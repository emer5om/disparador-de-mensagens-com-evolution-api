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

class TestListMessage extends Command
{
    protected $signature = 'test:list-message {phone} {--instance=1} {--debug}';
    protected $description = 'Testa o envio de mensagem com lista como alternativa aos botões';

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

        // Criar campanha de teste com links
        $listOptions = [
            [
                'text' => 'Suporte Técnico',
                'description' => 'Preciso de ajuda com problemas técnicos',
                'url' => 'https://suporte.exemplo.com'
            ],
            [
                'text' => 'Vendas',
                'description' => 'Quero saber mais sobre produtos',
                'url' => 'https://vendas.exemplo.com'
            ],
            [
                'text' => 'Financeiro',
                'description' => 'Dúvidas sobre pagamentos e faturas',
                'url' => 'https://financeiro.exemplo.com'
            ],
            [
                'text' => 'Portal do Cliente',
                'description' => 'Acesse sua área exclusiva',
                'url' => 'https://portal.exemplo.com'
            ]
        ];

        $message = 'Escolha uma das opções abaixo para ser redirecionado:';

        // Create campaign with list type
        $campaign = Campaign::create([
            'title' => 'Teste de Lista com Links Clicáveis - ' . now()->format('d/m/Y H:i:s'),
            'message' => $message,
            'message_type' => 'list',
            'buttons' => json_encode([
                [
                    'text' => 'Suporte Técnico',
                    'description' => 'Obtenha ajuda técnica especializada',
                    'url' => 'https://suporte.exemplo.com'
                ],
                [
                    'text' => 'Vendas',
                    'description' => 'Fale com nossa equipe de vendas',
                    'url' => 'https://vendas.exemplo.com'
                ],
                [
                    'text' => 'Financeiro',
                    'description' => 'Questões sobre pagamentos e faturas',
                    'url' => 'https://financeiro.exemplo.com'
                ],
                [
                    'text' => 'Site Principal',
                    'description' => 'Acesse nosso site oficial',
                    'url' => 'https://www.exemplo.com'
                ]
            ]),
            'instance_id' => $instanceId,
            'status' => 'draft',
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
            // Enviar mensagem com lista
            $result = $evolutionService->sendMessage(
                $instance->instance_key,
                $phone,
                $campaign->message,
                json_decode($campaign->buttons, true),
                'list'
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

                Log::info('Teste de mensagem lista - SUCCESS', $logData);

                $this->info("✅ Lista enviada com sucesso!");
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
                
                Log::error('Teste de mensagem lista - FAILED', [
                    'message_id' => $message->id,
                    'campaign_id' => $campaign->id,
                    'phone' => $phone,
                    'error' => $result['message']
                ]);

                $this->error("❌ Falha ao enviar lista: " . $result['message']);
                return 1;
            }
        } catch (\Exception $e) {
            $message->update(['status' => 'failed']);
            
            Log::error('Teste de mensagem lista - EXCEPTION', [
                'message_id' => $message->id,
                'campaign_id' => $campaign->id,
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            $this->error("❌ Erro ao enviar lista: " . $e->getMessage());
            return 1;
        }
    }
}