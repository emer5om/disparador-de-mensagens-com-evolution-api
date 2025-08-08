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

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use App\Services\InstanceService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InstanceController extends Controller
{
    private InstanceService $instanceService;

    public function __construct(InstanceService $instanceService)
    {
        $this->instanceService = $instanceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $instances = $this->instanceService->getAllInstancesWithStatus();
            return view('admin.instances.index', compact('instances'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar instâncias: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.instances.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:instances,name',
            'instance_key' => 'nullable|string|max:255|unique:instances,instance_key',
        ], [
            'name.required' => 'O nome da instância é obrigatório.',
            'name.unique' => 'Já existe uma instância com este nome.',
            'instance_key.unique' => 'Esta chave de instância já está em uso.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $instance = $this->instanceService->createInstance($request->only(['name', 'instance_key']));
            
            return redirect()
                ->route('admin.instances.show', $instance)
                ->with('success', 'Instância criada com sucesso! Use o QR Code para conectar seu WhatsApp.');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar instância: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Instance $instance)
    {
        try {
            // Update status and QR code before showing
            $instance = $this->instanceService->updateInstanceStatus($instance);
            
            if ($instance->status !== 'connected') {
                $instance = $this->instanceService->updateQRCode($instance);
            }

            return view('admin.instances.show', compact('instance'));
        } catch (Exception $e) {
            return redirect()->route('admin.instances.index')
                ->with('error', 'Erro ao carregar instância: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instance $instance)
    {
        return view('admin.instances.edit', compact('instance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Instance $instance)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:instances,name,' . $instance->id,
        ], [
            'name.required' => 'O nome da instância é obrigatório.',
            'name.unique' => 'Já existe uma instância com este nome.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $instance->update($request->only(['name']));
            
            return redirect()
                ->route('admin.instances.show', $instance)
                ->with('success', 'Instância atualizada com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar instância: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instance $instance)
    {
        try {
            $this->instanceService->deleteInstance($instance);
            
            return redirect()
                ->route('admin.instances.index')
                ->with('success', 'Instância excluída com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao excluir instância: ' . $e->getMessage());
        }
    }

    /**
     * Restart an instance
     */
    public function restart(Instance $instance)
    {
        try {
            $instance = $this->instanceService->restartInstance($instance);
            
            return redirect()
                ->route('admin.instances.show', $instance)
                ->with('success', 'Instância reiniciada! Use o novo QR Code para reconectar.');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao reiniciar instância: ' . $e->getMessage());
        }
    }

    /**
     * Refresh QR Code
     */
    public function refreshQR(Instance $instance)
    {
        try {
            $instance = $this->instanceService->updateQRCode($instance);
            
            return response()->json([
                'success' => true,
                'qr_code' => $instance->qr_code,
                'status' => $instance->status
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get instance status
     */
    public function status(Instance $instance)
    {
        try {
            $instance = $this->instanceService->updateInstanceStatus($instance);
            
            return response()->json([
                'success' => true,
                'status' => $instance->status,
                'updated_at' => $instance->updated_at->diffForHumans()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test message
     */
    public function test(Request $request, Instance $instance)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Número de telefone é obrigatório.'
            ], 400);
        }

        try {
            $response = $this->instanceService->sendTestMessage($instance, $request->phone_number);
            
            return response()->json([
                'success' => true,
                'message' => 'Mensagem de teste enviada com sucesso!',
                'response' => $response
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar mensagem: ' . $e->getMessage()
            ], 500);
        }
    }
}
