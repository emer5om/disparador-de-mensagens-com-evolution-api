<?php

/**
 * Disparador WhatsApp - Sistema de Disparo de Mensagens
 * 
 * @package DisparadorWhatsApp
 * @author Emerson <https://github.com/emer5om>
 * @version 1.0.0
 * @license MIT
 * @link https://github.com/emer5om/disparador
 */

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadList;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;

class LeadImportService
{
    public function importFile(UploadedFile $file, string $name, ?string $description = null): LeadList
    {
        $filename = $file->store('lead_imports', 'local');
        $originalFilename = $file->getClientOriginalName();

        $leadList = LeadList::create([
            'name' => $name,
            'description' => $description,
            'original_filename' => $originalFilename,
            'mapping_config' => [],
            'status' => 'processing',
            'created_by' => auth()->id(),
        ]);

        // Store file path in mapping_config temporarily
        $leadList->update([
            'mapping_config' => ['file_path' => $filename]
        ]);

        return $leadList;
    }

    public function getPreviewData(LeadList $leadList, int $rows = 5): array
    {
        $filePath = $leadList->mapping_config['file_path'];
        $fullPath = Storage::disk('local')->path($filePath);

        try {
            $data = $this->readFileData($fullPath, $rows + 1); // +1 for potential header
            
            // If the file doesn't have headers, create generic column names
            $firstRow = $data[0] ?? [];
            $hasHeaders = $this->detectHeaders($firstRow);
            
            if ($hasHeaders) {
                return [
                    'headers' => $data[0] ?? [],
                    'rows' => array_slice($data, 1, $rows),
                    'total_rows' => count($data) - 1
                ];
            } else {
                // No headers detected, create generic column names
                $headers = [];
                for ($i = 0; $i < count($firstRow); $i++) {
                    $headers[] = "Coluna " . $i;
                }
                
                return [
                    'headers' => $headers,
                    'rows' => array_slice($data, 0, $rows),
                    'total_rows' => count($data)
                ];
            }
        } catch (Exception $e) {
            throw new Exception('Erro ao ler arquivo: ' . $e->getMessage());
        }
    }

    private function detectHeaders(array $firstRow): bool
    {
        // Simple heuristic: if the first row contains mostly text and no numbers that look like phone numbers
        foreach ($firstRow as $cell) {
            $clean = preg_replace('/\D/', '', (string)$cell);
            // If we find a cell with 10+ digits, it's probably data, not a header
            if (strlen($clean) >= 10) {
                return false;
            }
        }
        return true;
    }

    public function processWithMapping(LeadList $leadList, array $mapping): void
    {
        $filePath = $leadList->mapping_config['file_path'];
        $fullPath = Storage::disk('local')->path($filePath);

        DB::beginTransaction();
        try {
            $data = $this->readFileData($fullPath);
            
            // Check if we need to skip headers
            $firstRow = $data[0] ?? [];
            $hasHeaders = $this->detectHeaders($firstRow);
            
            if ($hasHeaders) {
                $headers = array_shift($data); // Remove header row
            } else {
                // Create generic headers for data without headers
                $headers = [];
                for ($i = 0; $i < count($firstRow); $i++) {
                    $headers[] = "Coluna " . $i;
                }
            }

            $validLeads = 0;
            $invalidLeads = 0;
            $totalLeads = count($data);

            foreach ($data as $row) {
                $name = $this->getFieldValue($row, $mapping['name'] ?? null);
                $phoneNumber = $this->getFieldValue($row, $mapping['phone_number'] ?? null);
                $product = $this->getFieldValue($row, $mapping['product'] ?? null);

                // Validate required fields
                if (empty($name) || empty($phoneNumber)) {
                    $invalidLeads++;
                    continue;
                }

                // Clean and validate phone number
                $cleanPhone = $this->formatPhoneNumber($phoneNumber);
                if (!$cleanPhone) {
                    $invalidLeads++;
                    continue;
                }

                // Collect extra data (all other columns)
                $extraData = [];
                foreach ($row as $index => $value) {
                    if ($index !== ($mapping['name'] ?? null) && 
                        $index !== ($mapping['phone_number'] ?? null) && 
                        $index !== ($mapping['product'] ?? null)) {
                        $extraData[$headers[$index] ?? "campo_$index"] = $value;
                    }
                }

                Lead::create([
                    'lead_list_id' => $leadList->id,
                    'name' => trim($name),
                    'phone_number' => $cleanPhone,
                    'product' => trim($product) ?: null,
                    'extra_data' => $extraData ?: null,
                ]);

                $validLeads++;
            }

            // Update lead list statistics
            $leadList->update([
                'total_leads' => $totalLeads,
                'valid_leads' => $validLeads,
                'invalid_leads' => $invalidLeads,
                'mapping_config' => $mapping,
                'status' => 'completed'
            ]);

            // Clean up file
            Storage::disk('local')->delete($filePath);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Erro ao processar leads: ' . $e->getMessage());
        }
    }

    private function readFileData(string $filePath, ?int $limit = null): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($extension === 'csv' || $extension === 'txt') {
            return $this->readCsvFile($filePath, $limit);
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            return $this->readExcelFile($filePath, $limit);
        }

        throw new Exception('Formato de arquivo não suportado');
    }

    private function readCsvFile(string $filePath, ?int $limit = null): array
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(null);
        
        // Try to detect delimiter
        $delimiters = [',', ';', '\t', '|'];
        $csv->setDelimiter($this->detectDelimiter($filePath, $delimiters));

        $data = [];
        $count = 0;
        foreach ($csv as $record) {
            if ($limit && $count >= $limit) break;
            $data[] = $record;
            $count++;
        }

        return $data;
    }

    private function readExcelFile(string $filePath, ?int $limit = null): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $data = [];
        $rowCount = 0;
        
        foreach ($worksheet->getRowIterator() as $row) {
            if ($limit && $rowCount >= $limit) break;
            
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }
            $data[] = $rowData;
            $rowCount++;
        }

        return $data;
    }

    private function detectDelimiter(string $filePath, array $delimiters): string
    {
        $file = fopen($filePath, 'r');
        $firstLine = fgets($file);
        $secondLine = fgets($file);
        fclose($file);

        $maxCount = 0;
        $bestDelimiter = ',';

        foreach ($delimiters as $delimiter) {
            $delimiter = $delimiter === '\t' ? "\t" : $delimiter;
            
            $firstLineCount = substr_count($firstLine, $delimiter);
            $secondLineCount = $secondLine ? substr_count($secondLine, $delimiter) : 0;
            
            // Use the average count and prefer consistent delimiters
            $avgCount = $secondLine ? ($firstLineCount + $secondLineCount) / 2 : $firstLineCount;
            
            if ($avgCount > $maxCount) {
                $maxCount = $avgCount;
                $bestDelimiter = $delimiter;
            }
        }

        return $bestDelimiter;
    }

    private function getFieldValue(array $row, ?int $index): string
    {
        if ($index === null || !isset($row[$index])) {
            return '';
        }

        return trim((string) $row[$index]);
    }

    private function formatPhoneNumber(string $phoneNumber): ?string
    {
        // Remove all non-numeric characters
        $clean = preg_replace('/\D/', '', $phoneNumber);
        
        // Must have at least 10 digits
        if (strlen($clean) < 10) {
            return null;
        }

        // Add country code if missing (default to Brazil +55)
        if (strlen($clean) === 11 && substr($clean, 0, 2) !== '55') {
            $clean = '55' . $clean;
        } elseif (strlen($clean) === 10 && substr($clean, 0, 2) !== '55') {
            $clean = '55' . $clean;
        }

        // Validate Brazilian phone number format
        if (strlen($clean) >= 12 && strlen($clean) <= 13) {
            return $clean;
        }

        return null;
    }
}