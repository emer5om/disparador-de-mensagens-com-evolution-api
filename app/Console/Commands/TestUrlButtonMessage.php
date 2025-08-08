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

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Services\EvolutionApiService;

class TestUrlButtonMessage extends Command
{
    protected $signature = 'test:url-button-message {phone} {instance_id}';
    protected $description = 'Testa o envio de mensagem com botões URL clicáveis diretos';

    public function handle()
    {
        $phone = $this->argument('phone');
        $instanceId = $this->argument('instance_id');

        // Criar uma campanha de teste
        $campaign = Campaign::create([
            'title' => 'Teste de Botões URL Diretos',
            'message' => 'Escolha uma das opções abaixo para acessar diretamente:',
            'message_type' => 'url_button',
            'instance_id' => $instanceId,
            'created_by' => 1, // ID do usuário admin para teste
            'buttons' => json_encode([
                [
                    'type' => 'URL',
                    'text' => 'Acessar Site',
                    'url' => 'https://www.google.com'
                ],
                [
                    'type' => 'URL', 
                    'text' => 'Ver Produtos',
                    'url' => 'https://www.mercadolivre.com.br'
                ]
            ]),
            'delay_seconds' => 5,
            'status' => 'draft'
        ]);

        $this->info("Campanha criada: {$campaign->title}");

        // Buscar a instância
        $instance = \App\Models\Instance::find($instanceId);
        if (!$instance) {
            $this->error("Instância não encontrada!");
            return;
        }

        $this->info("Usando instância: {$instance->name} ({$instance->instance_key})");

        // Enviar mensagem usando o serviço
        $evolutionApi = new EvolutionApiService();
        $buttons = json_decode($campaign->buttons, true);
        
        $result = $evolutionApi->sendUrlButtonMessage(
            $instance->instance_key,
            $phone,
            $campaign->message,
            $buttons
        );

        if ($result['success']) {
            $this->info("Mensagem com botões URL enviada com sucesso!");
            $this->info("Dados da resposta: " . json_encode($result['data'], JSON_PRETTY_PRINT));
        } else {
            $this->error("Erro ao enviar mensagem: " . $result['message']);
        }

        // Limpar campanha de teste
        $campaign->delete();
    }
}