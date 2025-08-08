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

use App\Jobs\SendWhatsAppMessage;
use App\Models\Campaign;
use App\Models\Instance;
use App\Models\Message;
use App\Models\MessageLog;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CampaignService
{
    private EvolutionApiService $evolutionApi;

    public function __construct(EvolutionApiService $evolutionApi)
    {
        $this->evolutionApi = $evolutionApi;
    }

    /**
     * Create a new campaign
     */
    public function createCampaign(array $data): Campaign
    {
        // Validate instance exists and is connected
        $instance = Instance::findOrFail($data['instance_id']);
        
        if (!$this->canSendMessages($instance)) {
            throw new Exception("A instância selecionada não está conectada");
        }

        $campaign = Campaign::create([
            'title' => $data['title'],
            'message' => $data['message'],
            'message_type' => $data['message_type'] ?? 'text',
            'buttons' => $data['buttons'] ?? null,
            'instance_id' => $data['instance_id'],
            'lead_list_id' => $data['lead_list_id'] ?? null,
            'delay_seconds' => $data['delay_seconds'] ?? 5,
            'status' => 'draft',
            'created_by' => $data['created_by'] ?? 1,
        ]);

        return $campaign;
    }

    /**
     * Add phone numbers to campaign
     */
    public function addPhoneNumbers(Campaign $campaign, array $phoneNumbers): int
    {
        $added = 0;
        
        foreach ($phoneNumbers as $phoneNumber) {
            $cleanNumber = $this->formatPhoneNumber($phoneNumber);
            
            // Check if already exists
            if (!$campaign->messages()->where('phone_number', $cleanNumber)->exists()) {
                Message::create([
                    'campaign_id' => $campaign->id,
                    'phone_number' => $cleanNumber,
                    'message_content' => $campaign->message,
                    'status' => 'pending'
                ]);
                $added++;
            }
        }

        return $added;
    }

    /**
     * Add leads from lead list to campaign
     */
    public function addLeadsFromList(Campaign $campaign, \App\Models\LeadList $leadList): int
    {
        $added = 0;
        
        // Get all valid leads from the list
        $leads = $leadList->leads()->whereNotNull('phone_number')->get();
        
        foreach ($leads as $lead) {
            $cleanNumber = $this->formatPhoneNumber($lead->phone_number);
            
            // Check if already exists
            if (!$campaign->messages()->where('phone_number', $cleanNumber)->exists()) {
                Message::create([
                    'campaign_id' => $campaign->id,
                    'phone_number' => $cleanNumber,
                    'message_content' => $campaign->message,
                    'status' => 'pending'
                ]);
                $added++;
            }
        }

        return $added;
    }

    /**
     * Start campaign execution
     */
    public function startCampaign(Campaign $campaign): Campaign
    {
        if ($campaign->status !== 'draft') {
            throw new Exception("Apenas campanhas em rascunho podem ser iniciadas");
        }

        // Check if instance is still connected
        if (!$this->canSendMessages($campaign->instance)) {
            throw new Exception("A instância não está conectada");
        }

        // Check if campaign has messages
        if ($campaign->messages()->count() === 0) {
            throw new Exception("A campanha não possui números de telefone");
        }

        $campaign->update([
            'status' => 'running',
            'started_at' => now()
        ]);

        // Queue messages for processing
        $this->queueMessages($campaign);

        return $campaign->fresh();
    }

    /**
     * Queue campaign messages for processing
     */
    private function queueMessages(Campaign $campaign): void
    {
        $pendingMessages = $campaign->messages()
            ->where('status', 'pending')
            ->get();

        foreach ($pendingMessages as $message) {
            SendWhatsAppMessage::dispatch($message)
                ->delay(now()->addSeconds($message->id * 3)); // Stagger messages
        }
        
        Log::info('Queued messages for campaign', [
            'campaign_id' => $campaign->id,
            'message_count' => $pendingMessages->count()
        ]);
    }

    /**
     * Process campaign directly (for testing or small batches)
     */
    public function processCampaign(Campaign $campaign): void
    {
        $pendingMessages = $campaign->messages()
            ->where('status', 'pending')
            ->limit(50) // Process in batches
            ->get();

        foreach ($pendingMessages as $message) {
            $this->sendMessage($message);
            
            // Add delay between messages to avoid rate limiting
            sleep(2);
        }

        // Update campaign status if all messages are processed
        $this->updateCampaignStatus($campaign);
    }

    /**
     * Send individual message
     */
    private function sendMessage(Message $message): void
    {
        try {
            $message->update(['status' => 'sending']);

            $campaign = $message->campaign;
            $instance = $campaign->instance;

            // Determine message type and send accordingly
            $messageType = $campaign->message_type ?? 'text';
            
            if ($campaign->buttons && is_array($campaign->buttons) && count($campaign->buttons) > 0) {
                // Send message with buttons/options based on type
                $response = $this->evolutionApi->sendMessage(
                    $instance->instance_key,
                    $message->phone_number,
                    $message->message_content,
                    $campaign->buttons,
                    $messageType
                );
            } else {
                // Send text message
                $response = $this->evolutionApi->sendTextMessage(
                    $instance->instance_key,
                    $message->phone_number,
                    $message->message_content
                );
            }

            // Log success
            MessageLog::create([
                'message_id' => $message->id,
                'status' => 'success',
                'api_response' => json_encode($response),
                'sent_at' => now()
            ]);

            $message->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

        } catch (Exception $e) {
            // Log error
            MessageLog::create([
                'message_id' => $message->id,
                'status' => 'error',
                'error_message' => $e->getMessage(),
                'api_response' => null,
                'sent_at' => now()
            ]);

            $message->update(['status' => 'failed']);
        }
    }

    /**
     * Pause campaign
     */
    public function pauseCampaign(Campaign $campaign): Campaign
    {
        if ($campaign->status !== 'running') {
            throw new Exception("Apenas campanhas em execução podem ser pausadas");
        }

        $campaign->update(['status' => 'paused']);

        return $campaign;
    }

    /**
     * Resume campaign
     */
    public function resumeCampaign(Campaign $campaign): Campaign
    {
        if ($campaign->status !== 'paused') {
            throw new Exception("Apenas campanhas pausadas podem ser retomadas");
        }

        $campaign->update(['status' => 'running']);

        // Continue processing by queueing pending messages
        $this->queueMessages($campaign);

        return $campaign;
    }

    /**
     * Stop campaign
     */
    public function stopCampaign(Campaign $campaign): Campaign
    {
        if (!in_array($campaign->status, ['running', 'paused'])) {
            throw new Exception("Campanha não pode ser parada");
        }

        $campaign->update([
            'status' => 'stopped',
            'finished_at' => now()
        ]);

        return $campaign;
    }

    /**
     * Get campaign statistics
     */
    public function getCampaignStats(Campaign $campaign): array
    {
        $total = $campaign->messages()->count();
        $sent = $campaign->messages()->where('status', 'sent')->count();
        $failed = $campaign->messages()->where('status', 'failed')->count();
        $pending = $campaign->messages()->where('status', 'pending')->count();

        return [
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'pending' => $pending,
            'success_rate' => $total > 0 ? round(($sent / $total) * 100, 2) : 0,
            'progress' => $total > 0 ? round((($sent + $failed) / $total) * 100, 2) : 0
        ];
    }

    /**
     * Update campaign status based on message status
     */
    private function updateCampaignStatus(Campaign $campaign): void
    {
        $stats = $this->getCampaignStats($campaign);

        if ($stats['pending'] === 0 && $campaign->status === 'running') {
            $campaign->update([
                'status' => 'completed',
                'finished_at' => now()
            ]);
        }
    }

    /**
     * Check if instance can send messages
     */
    private function canSendMessages(Instance $instance): bool
    {
        return $instance->status === 'connected';
    }

    /**
     * Format phone number for WhatsApp
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-numeric characters
        $clean = preg_replace('/\D/', '', $phoneNumber);
        
        // Add country code if missing (default to Brazil +55)
        if (strlen($clean) === 11 && substr($clean, 0, 2) !== '55') {
            $clean = '55' . $clean;
        } elseif (strlen($clean) === 10 && substr($clean, 0, 2) !== '55') {
            $clean = '55' . $clean;
        }

        return $clean;
    }

    /**
     * Import phone numbers from text
     */
    public function importPhoneNumbers(string $text): array
    {
        // Extract phone numbers from text (supports various formats)
        preg_match_all('/(?:\+?55\s?)?(?:\(?\d{2}\)?\s?)?(?:9\s?)?\d{4}[-\s]?\d{4}/', $text, $matches);
        
        $phoneNumbers = [];
        foreach ($matches[0] as $match) {
            $cleaned = $this->formatPhoneNumber($match);
            if (strlen($cleaned) >= 12 && strlen($cleaned) <= 13) { // Valid Brazilian number
                $phoneNumbers[] = $cleaned;
            }
        }

        return array_unique($phoneNumbers);
    }

    /**
     * Get all campaigns with stats
     */
    public function getAllCampaignsWithStats(): Collection
    {
        return Campaign::with('instance')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($campaign) {
                $campaign->stats = $this->getCampaignStats($campaign);
                return $campaign;
            });
    }

    /**
     * Duplicate campaign
     */
    public function duplicateCampaign(Campaign $originalCampaign): Campaign
    {
        $newCampaign = Campaign::create([
            'title' => $originalCampaign->title . ' (Cópia)',
            'message' => $originalCampaign->message,
            'message_type' => $originalCampaign->message_type ?? 'text',
            'buttons' => $originalCampaign->buttons,
            'instance_id' => $originalCampaign->instance_id,
            'status' => 'draft',
            'created_by' => 1,
        ]);

        // Copy phone numbers
        foreach ($originalCampaign->messages as $message) {
            Message::create([
                'campaign_id' => $newCampaign->id,
                'phone_number' => $message->phone_number,
                'message_content' => $newCampaign->message,
                'status' => 'pending'
            ]);
        }

        return $newCampaign;
    }
}