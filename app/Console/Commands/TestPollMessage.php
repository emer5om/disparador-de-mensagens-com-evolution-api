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

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\Instance;
use App\Models\Message;
use App\Services\EvolutionApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestPollMessage extends Command
{
    protected $signature = 'test:poll-message {phone} {--instance=1} {--debug}';
    protected $description = 'Testa o envio de mensagem com poll (enquete) como alternativa aos botões';

    public function handle()
    {
        $phone = $this->argument('phone');
        $instanceId = $this->option('instance');
        $debug = $this->option('debug');

        if ($debug) {
            $this->info("🔍 Modo debug ativado");
            $this->info("📱 Telefone: {$phone}");
            $this->info("🏢 Instance ID: {$instanceId}");
        }

        // Buscar instância
        $instance = Instance::find($instanceId);
        if (!$instance) {
            $this->error("❌ Instância {$instanceId} não encontrada");
            return 1;
        }

        $this->info("🏢 Instância encontrada: {$instance->name}");

        // Create campaign with poll type
        $campaign = Campaign::create([
            'title' => 'Teste de Enquete - ' . now()->format('d/m/Y H:i:s'),
            'message' => 'Qual é a sua preferência?',
            'message_type' => 'poll',
            'buttons' => [
                ['text' => 'Opção A - Produto Premium'],
                ['text' => 'Opção B - Produto Básico'],
                ['text' => 'Opção C - Produto Intermediário'],
                ['text' => 'Opção D - Não tenho interesse']
            ],
            'instance_id' => $instance->id,
            'status' => 'running',
            'delay_seconds' => 5,
            'created_by' => 1,
        ]);

        $this->info("📋 Campanha criada: {$campaign->title} (ID: {$campaign->id})");

        // Criar mensagem
        $message = Message::create([
            'campaign_id' => $campaign->id,
            'phone_number' => $phone,
            'message_content' => $campaign->message,
            'buttons' => $campaign->buttons,
            'status' => 'pending',
        ]);

        $this->info("💬 Mensagem criada (ID: {$message->id})");

        // Inicializar serviço
        $evolutionService = new EvolutionApiService();

        try {
            // Enviar mensagem com poll
            $result = $evolutionService->sendMessage(
                $instance->instance_key,
                $phone,
                $campaign->message,
                $campaign->buttons,
                'poll'
            );

            if ($result['success']) {
                $message->update(['status' => 'sent', 'sent_at' => now()]);
                
                $logData = [
                    'message_id' => $message->id,
                    'campaign_id' => $campaign->id,
                    'phone' => $phone,
                    'instance_key' => $instance->instance_key,
                    'response' => $result['data'] ?? null
                ];

                Log::info('Teste de mensagem poll - SUCCESS', $logData);

                $this->info("✅ Poll enviado com sucesso!");
                $this->info("📊 Campanha: {$campaign->id}");
                $this->info("💬 Mensagem: {$message->id}");
                $this->info("📱 Telefone: {$phone}");
                $this->info("🏢 Instância: {$instance->name}");
                $this->info("🔗 Ver campanha: " . url("/admin/campaigns/{$campaign->id}"));

                if ($debug && isset($result['data'])) {
                    $this->info("📋 Resposta da API:");
                    $this->line(json_encode($result['data'], JSON_PRETTY_PRINT));
                }

                return 0;
            } else {
                $message->update(['status' => 'failed']);
                
                Log::error('Teste de mensagem poll - FAILED', [
                    'message_id' => $message->id,
                    'campaign_id' => $campaign->id,
                    'phone' => $phone,
                    'error' => $result['message']
                ]);

                $this->error("❌ Falha ao enviar poll: " . $result['message']);
                return 1;
            }
        } catch (\Exception $e) {
            $message->update(['status' => 'failed']);
            
            Log::error('Teste de mensagem poll - EXCEPTION', [
                'message_id' => $message->id,
                'campaign_id' => $campaign->id,
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            $this->error("❌ Erro ao enviar poll: " . $e->getMessage());
            return 1;
        }
    }
}