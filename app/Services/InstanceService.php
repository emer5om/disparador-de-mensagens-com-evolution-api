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

use App\Models\Instance;
use Exception;
use Illuminate\Support\Str;

class InstanceService
{
    private EvolutionApiService $evolutionApi;

    public function __construct(EvolutionApiService $evolutionApi)
    {
        $this->evolutionApi = $evolutionApi;
    }

    /**
     * Create a new instance
     */
    public function createInstance(array $data): Instance
    {
        // Generate unique instance key if not provided
        $instanceKey = $data['instance_key'] ?? $this->generateInstanceKey($data['name']);

        // Set webhook URL (optional for now)
        $webhookUrl = config('app.url') . '/api/webhooks/evolution/' . $instanceKey;

        try {
            // Create instance in Evolution API first (without webhook for now)
            $apiResponse = $this->evolutionApi->createInstance([
                'instance_key' => $instanceKey,
                // 'webhook_url' => $webhookUrl, // Disabled temporarily
            ]);

            // Create instance in database
            $instance = Instance::create([
                'name' => $data['name'],
                'instance_key' => $instanceKey,
                'evolution_api_url' => config('evolution.base_url'),
                'status' => 'disconnected',
                'webhook_url' => $webhookUrl,
            ]);

            // Try to get initial QR code
            $this->updateQRCode($instance);

            return $instance;
        } catch (Exception $e) {
            // If database record was created but API failed, clean up
            if (isset($instance)) {
                $instance->delete();
            }
            throw $e;
        }
    }

    /**
     * Update instance QR code
     */
    public function updateQRCode(Instance $instance): Instance
    {
        try {
            $response = $this->evolutionApi->getQRCode($instance->instance_key);
            
            if (isset($response['base64'])) {
                // Remove the data:image/png;base64, prefix if present
                $base64 = $response['base64'];
                if (strpos($base64, 'data:image/png;base64,') === 0) {
                    $base64 = substr($base64, strlen('data:image/png;base64,'));
                }
                
                $instance->update([
                    'qr_code' => $base64,
                    'status' => 'connecting'
                ]);
            }

            return $instance->fresh();
        } catch (Exception $e) {
            // Don't throw error for QR code updates, just log it
            logger()->warning("Failed to update QR code for instance {$instance->instance_key}: " . $e->getMessage());
            return $instance;
        }
    }

    /**
     * Update instance connection status
     */
    public function updateInstanceStatus(Instance $instance): Instance
    {
        try {
            $response = $this->evolutionApi->getInstanceStatus($instance->instance_key);
            
            $status = 'disconnected';
            if (isset($response['instance']['state'])) {
                switch ($response['instance']['state']) {
                    case 'open':
                        $status = 'connected';
                        break;
                    case 'connecting':
                        $status = 'connecting';
                        break;
                    default:
                        $status = 'disconnected';
                }
            }

            $instance->update(['status' => $status]);

            // Clear QR code if connected
            if ($status === 'connected') {
                $instance->update(['qr_code' => null]);
            }

            return $instance->fresh();
        } catch (Exception $e) {
            logger()->warning("Failed to update status for instance {$instance->instance_key}: " . $e->getMessage());
            return $instance;
        }
    }

    /**
     * Restart an instance
     */
    public function restartInstance(Instance $instance): Instance
    {
        try {
            $this->evolutionApi->restartInstance($instance->instance_key);
            
            $instance->update([
                'status' => 'connecting',
                'qr_code' => null
            ]);

            // Get new QR code after restart
            sleep(2); // Wait a moment for the instance to restart
            $this->updateQRCode($instance);

            return $instance->fresh();
        } catch (Exception $e) {
            throw new Exception("Failed to restart instance: " . $e->getMessage());
        }
    }

    /**
     * Delete an instance
     */
    public function deleteInstance(Instance $instance): bool
    {
        try {
            // Delete from Evolution API first
            $this->evolutionApi->deleteInstance($instance->instance_key);
        } catch (Exception $e) {
            // Log but continue with database deletion
            logger()->warning("Failed to delete instance from Evolution API: " . $e->getMessage());
        }

        // Delete from database
        return $instance->delete();
    }

    /**
     * Get all instances with updated status
     */
    public function getAllInstancesWithStatus(): \Illuminate\Database\Eloquent\Collection
    {
        $instances = Instance::all();

        foreach ($instances as $instance) {
            $this->updateInstanceStatus($instance);
        }

        return $instances->fresh();
    }

    /**
     * Generate unique instance key
     */
    private function generateInstanceKey(string $name): string
    {
        $slug = Str::slug($name);
        $key = $slug . '_' . Str::random(6);

        // Ensure uniqueness
        while (Instance::where('instance_key', $key)->exists()) {
            $key = $slug . '_' . Str::random(6);
        }

        return $key;
    }

    /**
     * Check if instance can send messages
     */
    public function canSendMessages(Instance $instance): bool
    {
        return $instance->status === 'connected';
    }

    /**
     * Send test message to verify instance connection
     */
    public function sendTestMessage(Instance $instance, ?string $phoneNumber = null): array
    {
        if (!$this->canSendMessages($instance)) {
            throw new Exception("Instance is not connected");
        }

        $testPhone = $phoneNumber ?? '5511999999999'; // Default test number
        $message = "🤖 Teste de conexão do Disparador\n\nSua instância está funcionando corretamente!";

        return $this->evolutionApi->sendTextMessage(
            $instance->instance_key,
            $testPhone,
            $message
        );
    }
}