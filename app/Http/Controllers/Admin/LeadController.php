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

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadList;
use App\Services\LeadImportService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function __construct(
        private LeadImportService $leadImportService
    ) {}

    public function index()
    {
        $leadLists = LeadList::with('creator')
            ->latest()
            ->paginate(20);

        return view('admin.leads.index', compact('leadLists'));
    }

    public function create()
    {
        return view('admin.leads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:csv,xlsx,xls,txt|max:10240', // 10MB max
        ]);

        try {
            $leadList = $this->leadImportService->importFile(
                $request->file('file'),
                $request->name,
                $request->description
            );

            return redirect()
                ->route('admin.leads.mapping', $leadList)
                ->with('success', 'Arquivo carregado com sucesso! Configure o mapeamento dos campos.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao processar arquivo: ' . $e->getMessage());
        }
    }

    public function mapping(LeadList $leadList)
    {
        if ($leadList->status === 'completed') {
            return redirect()->route('admin.leads.show', $leadList);
        }

        $preview = $this->leadImportService->getPreviewData($leadList);
        
        return view('admin.leads.mapping', compact('leadList', 'preview'));
    }

    public function saveMapping(Request $request, LeadList $leadList)
    {
        $request->validate([
            'mapping.name' => 'required|integer|min:0',
            'mapping.phone_number' => 'required|integer|min:0',
            'mapping.product' => 'nullable|integer|min:0',
        ]);

        try {
            $this->leadImportService->processWithMapping($leadList, $request->mapping);

            return redirect()
                ->route('admin.leads.show', $leadList)
                ->with('success', 'Leads importados com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao processar leads: ' . $e->getMessage());
        }
    }

    public function show(LeadList $leadList)
    {
        $leadList->load(['leads' => function($query) {
            $query->latest()->limit(100);
        }]);

        $stats = [
            'total' => $leadList->total_leads,
            'valid' => $leadList->valid_leads,
            'invalid' => $leadList->invalid_leads,
        ];

        return view('admin.leads.show', compact('leadList', 'stats'));
    }

    public function destroy(LeadList $leadList)
    {
        $leadList->delete();

        return redirect()
            ->route('admin.leads.index')
            ->with('success', 'Lista de leads excluída com sucesso.');
    }

    public function export(LeadList $leadList)
    {
        $leads = $leadList->leads()->get();

        $csvData = [];
        $csvData[] = ['Nome', 'Telefone', 'Produto'];

        foreach ($leads as $lead) {
            $csvData[] = [
                $lead->name,
                $lead->phone_number,
                $lead->product ?? '',
            ];
        }

        $filename = 'leads_' . $leadList->name . '_' . date('Y-m-d') . '.csv';
        
        return $this->downloadCsv($csvData, $filename);
    }

    private function downloadCsv(array $data, string $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
