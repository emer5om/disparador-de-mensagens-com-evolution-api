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

class TestButtonMessage extends Command
{
    protected $signature = 'test:button-message 
                          {phone : Número de telefone para enviar o teste}
                          {--instance= : ID da instância (opcional)}
                          {--debug : Ativar modo debug}';

    protected $description = 'Testa o envio de mensagem com botões para identificar problemas de integração';

    public function handle()
    {
        $phone = $this->argument('phone');
        $instanceId = $this->option('instance');
        $debug = $this->option('debug');

        $this->info('🧪 Iniciando teste de mensagem com botões...');
        $this->info("📱 Telefone: {$phone}");

        // Buscar instância
        if ($instanceId) {
            $instance = Instance::find($instanceId);
        } else {
            $instance = Instance::where('status', 'connected')->first();
        }

        if (!$instance) {
            $this->error('❌ Nenhuma instância conectada encontrada!');
            return 1;
        }

        $this->info("🔗 Usando instância: {$instance->name} ({$instance->instance_key})");

        // Criar campanha de teste
        $campaign = Campaign::create([
            'title' => 'Teste Botões - ' . now()->format('Y-m-d H:i:s'),
            'message' => 'Esta é uma mensagem de TESTE com botões. Por favor, responda clicando em um dos botões abaixo.',
            'buttons' => [
                ['text' => '✅ FUNCIONOU', 'url' => 'https://example.com/funcionou'],
                ['text' => '❌ NÃO FUNCIONOU', 'url' => 'https://example.com/nao-funcionou'],
                ['text' => '🤔 PARCIALMENTE', 'url' => 'https://example.com/parcialmente']
            ],
            'instance_id' => $instance->id,
            'status' => 'running',
            'created_by' => 1, // Assumindo usuário admin
        ]);

        $this->info("📋 Campanha criada: {$campaign->title} (ID: {$campaign->id})");

        // Criar mensagem
        $message = Message::create([
            'campaign_id' => $campaign->id,
            'phone_number' => $phone,
            'message_content' => $campaign->message,
            'buttons' => null, // Vai usar os botões da campanha
            'status' => 'pending',
        ]);

        $this->info("💬 Mensagem criada (ID: {$message->id})");

        // Inicializar serviço
        $service = new EvolutionApiService();
        $this->info("📞 Telefone: {$phone}");

        // Preparar dados dos botões
        $buttons = $campaign->buttons ?? [];
        $this->info("🔘 Botões configurados: " . count($buttons));

        if ($debug) {
            $this->info("🐛 Dados dos botões:");
            foreach ($buttons as $index => $button) {
                $this->line("  [{$index}] {$button['text']} -> {$button['url']}");
            }
        }

        // Testar envio via API
        $this->info("🚀 Enviando mensagem via Evolution API...");

        try {
            if (!empty($buttons)) {
                $result = $service->sendButtonMessage(
                    $instance->instance_key,
                    $phone,
                    $campaign->message,
                    $buttons
                );
            } else {
                $result = $service->sendTextMessage(
                    $instance->instance_key,
                    $phone,
                    $campaign->message
                );
            }

            if ($debug) {
                $this->info("🐛 Resposta da API:");
                $this->line(json_encode($result, JSON_PRETTY_PRINT));
            }

            if ($result['success']) {
                $this->info("✅ Mensagem enviada com sucesso!");
                $this->info("📨 ID da mensagem: " . ($result['data']['messageId'] ?? 'N/A'));
                
                // Atualizar status da mensagem
                $message->markAsSent();
                
                $this->info("💾 Status atualizado no banco de dados");
                
                // Log de sucesso
                Log::info('Teste de mensagem com botões - SUCESSO', [
                    'message_id' => $message->id,
                    'campaign_id' => $campaign->id,
                    'phone' => $phone,
                    'instance_key' => $instance->instance_key,
                    'api_response' => $result
                ]);
                
            } else {
                $this->error("❌ Falha no envio da mensagem!");
                $this->error("Erro: " . ($result['message'] ?? 'Erro desconhecido'));
                
                // Atualizar status da mensagem
                $message->markAsFailed($result['message'] ?? 'Erro desconhecido');
                
                // Log de erro
                Log::error('Teste de mensagem com botões - ERRO', [
                    'message_id' => $message->id,
                    'campaign_id' => $campaign->id,
                    'phone' => $phone,
                    'instance_key' => $instance->instance_key,
                    'error' => $result['message'] ?? 'Erro desconhecido',
                    'api_response' => $result
                ]);
                
                return 1;
            }

        } catch (\Exception $e) {
            $this->error("💥 Exceção durante o envio:");
            $this->error($e->getMessage());
            
            if ($debug) {
                $this->error("Stack trace:");
                $this->error($e->getTraceAsString());
            }
            
            // Atualizar status da mensagem
            $message->markAsFailed($e->getMessage());
            
            // Log de exceção
            Log::error('Teste de mensagem com botões - EXCEÇÃO', [
                'message_id' => $message->id,
                'campaign_id' => $campaign->id,
                'phone' => $phone,
                'instance_key' => $instance->instance_key,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }

        $this->newLine();
        $this->info("📊 Resumo do teste:");
        $this->info("  • Campanha ID: {$campaign->id}");
        $this->info("  • Mensagem ID: {$message->id}");
        $this->info("  • Telefone: {$phone}");
        $this->info("  • Instância: {$instance->name}");
        $this->info("  • Status: " . ($result['success'] ? 'SUCESSO' : 'FALHA'));
        
        $this->newLine();
        $this->info("🔍 Para verificar os logs:");
        $this->info("  tail -f storage/logs/laravel.log | grep 'Teste de mensagem'");
        
        $this->newLine();
        $this->info("🌐 Para ver a campanha no admin:");
        $this->info("  http://localhost:8000/admin/campaigns/{$campaign->id}");

        return 0;
    }
}