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

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionApiService
{
    private string $baseUrl;
    private ?string $apiKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('evolution.base_url');
        $this->apiKey = config('evolution.api_key');
        $this->timeout = config('evolution.timeout', 30);
    }

    /**
     * Create a new instance in Evolution API
     */
    public function createInstance(array $data): array
    {
        try {
            $payload = [
                'instanceName' => $data['instance_key'],
                'integration' => 'WHATSAPP-BAILEYS', // Required by Evolution API v2.2.3
            ];

            // Add webhook if provided and valid
            if (!empty($data['webhook_url']) && filter_var($data['webhook_url'], FILTER_VALIDATE_URL)) {
                $payload['webhook'] = [
                    'url' => $data['webhook_url']
                ];
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/instance/create", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("API Error: " . $response->body());
        } catch (Exception $e) {
            Log::error("Evolution API - Create Instance Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get QR Code for instance connection
     */
    public function getQRCode(string $instanceKey): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/instance/connect/{$instanceKey}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("Failed to get QR Code: " . $response->body());
        } catch (Exception $e) {
            Log::error("Evolution API - Get QR Code Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get instance connection status
     */
    public function getInstanceStatus(string $instanceKey): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/instance/connectionState/{$instanceKey}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("Failed to get instance status: " . $response->body());
        } catch (Exception $e) {
            Log::error("Evolution API - Get Status Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete an instance from Evolution API
     */
    public function deleteInstance(string $instanceKey): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->delete("{$this->baseUrl}/instance/delete/{$instanceKey}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("Failed to delete instance: " . $response->body());
        } catch (Exception $e) {
            Log::error("Evolution API - Delete Instance Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Restart an instance
     */
    public function restartInstance(string $instanceKey): array
    {
        try {
            // Evolution API uses DELETE for restart, not PUT
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->delete("{$this->baseUrl}/instance/logout/{$instanceKey}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("Failed to restart instance: " . $response->body());
        } catch (Exception $e) {
            Log::error("Evolution API - Restart Instance Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send a text message
     */
    /**
     * Send a message (text, buttons, poll, list, or url_button)
     */
    public function sendMessage(string $instanceKey, string $phoneNumber, string $message, array $buttons = [], string $type = 'text'): array
    {
        if (!empty($buttons) && count($buttons) > 0) {
            switch ($type) {
                case 'poll':
                    return $this->sendPollMessage($instanceKey, $phoneNumber, $message, $buttons);
                case 'list':
                    return $this->sendListMessage($instanceKey, $phoneNumber, $message, $buttons);
                case 'url_button':
                    return $this->sendUrlButtonMessage($instanceKey, $phoneNumber, $message, $buttons);
                case 'button':
                default:
                    return $this->sendButtonMessage($instanceKey, $phoneNumber, $message, $buttons);
            }
        }
        
        return $this->sendTextMessage($instanceKey, $phoneNumber, $message);
    }

    /**
     * Send a text message
     */
    public function sendTextMessage(string $instanceKey, string $phoneNumber, string $message): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/message/sendText/{$instanceKey}", [
                    'number' => $this->formatPhoneNumber($phoneNumber),
                    'text' => $message
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Message sent successfully'
                ];
            }

            throw new Exception("Failed to send message: " . $response->body());
        } catch (Exception $e) {
            Log::error("Evolution API - Send Message Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send a message with buttons
     */
    public function sendButtonMessage(string $instanceKey, string $phoneNumber, string $message, array $buttons): array
    {
        try {
            $formattedButtons = [];
            foreach ($buttons as $index => $button) {
                $buttonText = $button['text'] ?? $button['title'] ?? $button['displayText'] ?? 'Botão ' . ($index + 1);
                $formattedButtons[] = [
                    'type' => 'reply',
                    'title' => $buttonText,
                    'displayText' => $buttonText,
                    'id' => $button['id'] ?? 'btn_' . $index
                ];
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/message/sendButtons/{$instanceKey}", [
                    'number' => $this->formatPhoneNumber($phoneNumber),
                    'title' => 'Escolha uma opção',
                    'description' => $message,
                    'footer' => '',
                    'buttons' => $formattedButtons
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Button message sent successfully'
                ];
            }

            throw new Exception("Failed to send button message: " . $response->body());
        } catch (Exception $e) {
            Log::error("Evolution API - Send Button Message Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send a poll message (alternative to buttons)
     */
    public function sendPollMessage(string $instanceKey, string $phoneNumber, string $message, array $options): array
    {
        try {
            $formattedOptions = [];
            foreach ($options as $option) {
                $formattedOptions[] = $option['text'] ?? $option;
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/message/sendPoll/{$instanceKey}", [
                    'number' => $this->formatPhoneNumber($phoneNumber),
                    'name' => $message,
                    'selectableCount' => 1, // Permite apenas uma seleção
                    'values' => $formattedOptions
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Poll message sent successfully'
                ];
            }

            throw new Exception("Failed to send poll message: " . $response->body());
        } catch (Exception $e) {
            Log::error("Evolution API - Send Poll Message Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send a list message (alternative to buttons)
     */
    public function sendListMessage(string $instanceKey, string $phoneNumber, string $message, array $options): array
    {
        try {
            $formattedOptions = [];
            foreach ($options as $index => $option) {
                $listItem = [
                    'title' => $option['text'] ?? $option,
                    'description' => $option['description'] ?? '',
                    'rowId' => 'option_' . $index
                ];
                
                // Se há URL, adiciona como parte da descrição ou como um campo especial
                if (isset($option['url']) && !empty($option['url'])) {
                    $listItem['description'] = ($option['description'] ?? '') . "\n🔗 " . $option['url'];
                }
                
                $formattedOptions[] = $listItem;
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/message/sendList/{$instanceKey}", [
                    'number' => $this->formatPhoneNumber($phoneNumber),
                    'title' => 'Escolha uma opção',
                    'description' => $message,
                    'buttonText' => 'Ver opções',
                    'footerText' => 'Clique em uma opção para acessar o link',
                    'sections' => [
                        [
                            'title' => 'Opções disponíveis',
                            'rows' => $formattedOptions
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'List message sent successfully'
                ];
            }

            throw new Exception("Failed to send list message: " . $response->body());
        } catch (Exception $e) {
            Log::error("Evolution API - Send List Message Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send a message with URL buttons (call-to-action buttons)
     */
    public function sendUrlButtonMessage(string $instanceKey, string $phoneNumber, string $message, array $buttons): array
    {
        try {
            $formattedButtons = [];
            foreach ($buttons as $index => $button) {
                $formattedButtons[] = [
                    'type' => 'URL',
                    'text' => $button['text'],
                    'url' => $button['url']
                ];
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/message/sendButtonActions/{$instanceKey}", [
                    'phone' => $this->formatPhoneNumber($phoneNumber),
                    'message' => $message,
                    'buttonActions' => $formattedButtons
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'URL button message sent successfully'
                ];
            }

            throw new Exception("Failed to send URL button message: " . $response->body());
        } catch (Exception $e) {
            Log::error("Evolution API - Send URL Button Message Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get request headers for API calls
     */
    private function getHeaders(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if ($this->apiKey) {
            $headers['apikey'] = $this->apiKey;
        }

        return $headers;
    }

    /**
     * Format phone number for WhatsApp
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-numeric characters
        $clean = preg_replace('/\D/', '', $phoneNumber);
        
        // Add country code if missing (default to Brazil +55)
        if (strlen($clean) === 11 && substr($clean, 0, 1) !== '55') {
            $clean = '55' . $clean;
        }

        return $clean;
    }
}