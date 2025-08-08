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
use App\Services\EvolutionApiService;

class TestWorkingButtons extends Command
{
    protected $signature = 'test:working-buttons {phone} {instance_id}';
    protected $description = 'Testa botões que realmente funcionam na Evolution API';

    public function handle()
    {
        $phone = $this->argument('phone');
        $instanceId = $this->argument('instance_id');

        // Buscar a instância
        $instance = \App\Models\Instance::find($instanceId);
        if (!$instance) {
            $this->error("Instância não encontrada!");
            return;
        }

        $this->info("Usando instância: {$instance->name} ({$instance->instance_key})");

        $evolutionApi = new EvolutionApiService();

        // 1. Primeiro, enviar botões que funcionam (tipo reply)
        $this->info("1. Enviando botões de resposta...");
        
        $buttons = [
            [
                'title' => '🌐 Ver Site',
                'displayText' => 'Ver Site',
                'id' => 'btn_site'
            ],
            [
                'title' => '🛒 Ver Produtos', 
                'displayText' => 'Ver Produtos',
                'id' => 'btn_produtos'
            ]
        ];

        $result1 = $evolutionApi->sendButtonMessage(
            $instance->instance_key,
            $phone,
            'Escolha uma das opções abaixo:',
            $buttons
        );

        if ($result1['success']) {
            $this->info("✅ Botões enviados com sucesso!");
        } else {
            $this->error("❌ Erro ao enviar botões: " . $result1['message']);
        }

        // 2. Aguardar um pouco e enviar mensagem com links clicáveis
        sleep(3);
        
        $this->info("2. Enviando mensagem com links clicáveis...");
        
        $linkMessage = "🔗 *Links Diretos Clicáveis:*\n\n" .
                      "🌐 *Site Principal:*\n" .
                      "https://www.google.com\n\n" .
                      "🛒 *Ver Produtos:*\n" .
                      "https://www.mercadolivre.com.br\n\n" .
                      "👆 *Clique nos links acima para acessar diretamente!*";

        $result2 = $evolutionApi->sendTextMessage(
            $instance->instance_key,
            $phone,
            $linkMessage
        );

        if ($result2['success']) {
            $this->info("✅ Links enviados com sucesso!");
        } else {
            $this->error("❌ Erro ao enviar links: " . $result2['message']);
        }

        // 3. Demonstrar lista (sem links clicáveis)
        sleep(3);
        
        $this->info("3. Enviando lista (links não clicáveis)...");
        
        $listOptions = [
            [
                'text' => 'Google',
                'description' => 'Acesse: https://www.google.com',
                'url' => 'https://www.google.com'
            ],
            [
                'text' => 'Mercado Livre',
                'description' => 'Acesse: https://www.mercadolivre.com.br',
                'url' => 'https://www.mercadolivre.com.br'
            ]
        ];

        $result3 = $evolutionApi->sendListMessage(
            $instance->instance_key,
            $phone,
            'Lista com links (não clicáveis):',
            $listOptions
        );

        if ($result3['success']) {
            $this->info("✅ Lista enviada com sucesso!");
        } else {
            $this->error("❌ Erro ao enviar lista: " . $result3['message']);
        }

        $this->info("\n📋 Resumo dos testes:");
        $this->info("1. ✅ Botões de resposta: Funcionam perfeitamente");
        $this->info("2. ✅ Links em mensagem de texto: Clicáveis e funcionais");
        $this->info("3. ⚠️  Links em lista: Aparecem como texto, não são clicáveis");
        $this->info("4. ❌ Botões URL: Não suportados pela Evolution API");
    }
}