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

namespace App\Jobs;

use App\Models\Message;
use App\Services\EvolutionApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessage implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        protected Message $message
    ) {}

    public function handle(EvolutionApiService $evolutionApi): void
    {
        try {
            $this->message->update(['status' => 'sending']);
            
            $response = $evolutionApi->sendMessage(
                $this->message->campaign->instance->instance_key,
                $this->message->phone_number,
                $this->message->campaign->message,
                $this->message->campaign->buttons ?? []
            );

            if ($response['success']) {
                $this->message->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'evolution_message_id' => $response['data']['id'] ?? null
                ]);
                
                Log::info('Message sent successfully', [
                    'message_id' => $this->message->id,
                    'phone' => $this->message->phone_number
                ]);
            } else {
                throw new \Exception($response['message'] ?? 'Failed to send message');
            }
        } catch (\Exception $e) {
            $this->message->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            
            Log::error('Failed to send WhatsApp message', [
                'message_id' => $this->message->id,
                'phone' => $this->message->phone_number,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->message->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage()
        ]);
    }
}
