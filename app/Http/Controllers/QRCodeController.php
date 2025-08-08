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

namespace App\Http\Controllers;

use App\Models\Instance;
use App\Services\InstanceService;
use Exception;
use Illuminate\Http\Request;

class QRCodeController extends Controller
{
    private InstanceService $instanceService;

    public function __construct(InstanceService $instanceService)
    {
        $this->instanceService = $instanceService;
    }

    /**
     * Show public QR code page for an instance
     */
    public function show(Request $request, string $instanceKey)
    {
        try {
            $instance = Instance::where('instance_key', $instanceKey)->first();

            if (!$instance) {
                abort(404, 'Instância não encontrada');
            }

            // Update status and QR code
            $instance = $this->instanceService->updateInstanceStatus($instance);
            
            if ($instance->status !== 'connected') {
                $instance = $this->instanceService->updateQRCode($instance);
            }

            return view('qrcode.show', compact('instance'));
        } catch (Exception $e) {
            abort(500, 'Erro ao carregar QR Code: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to get QR code data (for auto-refresh)
     */
    public function api(Request $request, string $instanceKey)
    {
        try {
            $instance = Instance::where('instance_key', $instanceKey)->first();

            if (!$instance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instância não encontrada'
                ], 404);
            }

            // Update status first
            $instance = $this->instanceService->updateInstanceStatus($instance);
            
            // Get fresh QR code if not connected
            if ($instance->status !== 'connected') {
                $instance = $this->instanceService->updateQRCode($instance);
            }

            return response()->json([
                'success' => true,
                'status' => $instance->status,
                'qr_code' => $instance->qr_code,
                'instance_name' => $instance->name,
                'updated_at' => $instance->updated_at->toISOString()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter QR Code: ' . $e->getMessage()
            ], 500);
        }
    }
}