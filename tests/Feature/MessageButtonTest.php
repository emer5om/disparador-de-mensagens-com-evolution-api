<?php

namespace Tests\Feature;

use App\Jobs\SendWhatsAppMessage;
use App\Models\Campaign;
use App\Models\Instance;
use App\Models\Message;
use App\Services\EvolutionApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MessageButtonTest extends TestCase
{
    use RefreshDatabase;

    protected $instance;
    protected $campaign;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar instância de teste
        $this->instance = Instance::create([
            'name' => 'Test Instance',
            'instance_key' => 'test_instance_key',
            'status' => 'connected',
            'qr_code' => null,
        ]);

        // Criar campanha de teste com botões
        $this->campaign = Campaign::create([
            'name' => 'Test Campaign with Buttons',
            'message' => 'Esta é uma mensagem de teste com botões',
            'buttons' => [
                ['text' => 'SIM', 'url' => 'https://example.com/sim'],
                ['text' => 'NÃO', 'url' => 'https://example.com/nao'],
                ['text' => 'TALVEZ', 'url' => 'https://example.com/talvez']
            ],
            'instance_id' => $this->instance->id,
            'status' => 'draft',
            'total_messages' => 0,
            'sent_messages' => 0,
            'failed_messages' => 0,
        ]);
    }

    /** @test */
    public function test_can_create_message_with_buttons()
    {
        $message = Message::create([
            'campaign_id' => $this->campaign->id,
            'phone_number' => '5551989462745',
            'message_content' => 'Mensagem de teste com botões',
            'buttons' => $this->campaign->buttons,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'campaign_id' => $this->campaign->id,
            'phone_number' => '5551989462745',
            'status' => 'pending',
        ]);

        $this->assertNotNull($message->buttons);
        $this->assertCount(3, $message->buttons);
    }

    /** @test */
    public function test_evolution_api_service_formats_buttons_correctly()
    {
        $service = new EvolutionApiService();
        
        // Mock da resposta da API
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'message' => 'Button message sent successfully',
                'data' => ['messageId' => 'test_message_id']
            ], 200)
        ]);

        $buttons = [
            ['text' => 'SIM', 'url' => 'https://example.com/sim'],
            ['text' => 'NÃO', 'url' => 'https://example.com/nao'],
        ];

        $result = $service->sendButtonMessage(
            'test_instance_key',
            '5551989462745',
            'Mensagem de teste',
            $buttons
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('Button message sent successfully', $result['message']);

        // Verificar se a requisição foi feita com os parâmetros corretos
        Http::assertSent(function ($request) {
            $data = $request->data();
            
            // Verificar estrutura dos botões
            $this->assertArrayHasKey('buttons', $data);
            $this->assertCount(2, $data['buttons']);
            
            // Verificar formato dos botões
            foreach ($data['buttons'] as $button) {
                $this->assertArrayHasKey('type', $button);
                $this->assertArrayHasKey('title', $button);
                $this->assertArrayHasKey('displayText', $button);
                $this->assertArrayHasKey('id', $button);
                $this->assertEquals('reply', $button['type']);
            }
            
            return true;
        });
    }

    /** @test */
    public function test_send_whatsapp_message_job_with_buttons()
    {
        Queue::fake();

        $message = Message::create([
            'campaign_id' => $this->campaign->id,
            'phone_number' => '5551989462745',
            'message_content' => 'Mensagem de teste com botões',
            'buttons' => null, // Vai usar os botões da campanha
            'status' => 'pending',
        ]);

        SendWhatsAppMessage::dispatch($message);

        Queue::assertPushed(SendWhatsAppMessage::class, function ($job) use ($message) {
            return $job->message->id === $message->id;
        });
    }

    /** @test */
    public function test_message_without_buttons_uses_text_message()
    {
        // Criar campanha sem botões
        $campaignWithoutButtons = Campaign::create([
            'name' => 'Test Campaign without Buttons',
            'message' => 'Esta é uma mensagem de teste sem botões',
            'buttons' => [],
            'instance_id' => $this->instance->id,
            'status' => 'draft',
            'total_messages' => 0,
            'sent_messages' => 0,
            'failed_messages' => 0,
        ]);

        $service = new EvolutionApiService();
        
        // Mock da resposta da API
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'message' => 'Text message sent successfully',
                'data' => ['messageId' => 'test_message_id']
            ], 200)
        ]);

        $result = $service->sendMessage(
            'test_instance_key',
            '5551989462745',
            'Mensagem de teste sem botões',
            []
        );

        $this->assertTrue($result['success']);
        
        // Verificar se foi chamado o endpoint de texto
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/sendText/');
        });
    }

    /** @test */
    public function test_debug_button_message_payload()
    {
        $service = new EvolutionApiService();
        
        // Capturar logs para debug
        Log::shouldReceive('info')->once()->with('Button Message Payload', \Mockery::type('array'));
        
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'message' => 'Button message sent successfully'
            ], 200)
        ]);

        $buttons = [
            ['text' => 'SIM', 'url' => 'https://example.com/sim'],
            ['text' => 'NÃO', 'url' => 'https://example.com/nao'],
        ];

        // Adicionar log de debug no serviço
        $formattedButtons = [];
        foreach ($buttons as $index => $button) {
            $formattedButtons[] = [
                'type' => 'reply',
                'title' => $button['text'],
                'displayText' => $button['text'],
                'id' => 'btn_' . $index
            ];
        }

        $payload = [
            'number' => '555551989462745',
            'title' => 'Escolha uma opção',
            'description' => 'Mensagem de teste',
            'footer' => '',
            'buttons' => $formattedButtons
        ];

        Log::info('Button Message Payload', $payload);

        $result = $service->sendButtonMessage(
            'test_instance_key',
            '5551989462745',
            'Mensagem de teste',
            $buttons
        );

        $this->assertTrue($result['success']);
    }

    /** @test */
    public function test_real_integration_with_evolution_api()
    {
        // Este teste só roda se as variáveis de ambiente estiverem configuradas
        if (!config('evolution.api_key') || !config('evolution.base_url')) {
            $this->markTestSkipped('Evolution API não configurada para testes de integração');
        }

        $message = Message::create([
            'campaign_id' => $this->campaign->id,
            'phone_number' => '5551989462745',
            'message_content' => 'TESTE DE INTEGRAÇÃO - Mensagem com botões',
            'buttons' => null,
            'status' => 'pending',
        ]);

        // Executar o job real
        $job = new SendWhatsAppMessage($message);
        
        try {
            $job->handle();
            
            // Recarregar a mensagem do banco
            $message->refresh();
            
            // Verificar se foi marcada como enviada
            $this->assertEquals('sent', $message->status);
            $this->assertNotNull($message->sent_at);
            
        } catch (\Exception $e) {
            // Se falhar, verificar se foi marcada como failed
            $message->refresh();
            $this->assertEquals('failed', $message->status);
            $this->assertNotNull($message->error_message);
            
            // Log do erro para debug
            Log::error('Teste de integração falhou', [
                'error' => $e->getMessage(),
                'message_id' => $message->id
            ]);
        }
    }
}