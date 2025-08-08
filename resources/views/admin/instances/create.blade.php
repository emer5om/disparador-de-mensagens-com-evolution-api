{{--
/**
 * Disparador WhatsApp - Sistema de Disparo de Mensagens
 * 
 * @package DisparadorWhatsApp
 * @author Emerson <https://github.com/emer5om>
 * @version 1.0.0
 * @license MIT
 * @link https://github.com/emer5om/disparador
 */
--}}
@extends('layouts.admin')

@section('title', 'Nova Instância')
@section('page-title', 'Criar Nova Instância')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.instances.index') }}" 
           class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Voltar para Instâncias
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Criar Nova Instância</h2>
            <p class="text-gray-600 mt-1">Configure uma nova conexão com o WhatsApp através da Evolution API</p>
        </div>

        <form method="POST" action="{{ route('admin.instances.store') }}" class="p-6 space-y-6">
            @csrf

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="ml-3 text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Instance Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nome da Instância *
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror"
                       placeholder="Ex: WhatsApp Principal"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">
                    Nome para identificar esta instância no sistema
                </p>
            </div>

            <!-- Instance Key (Optional) -->
            <div>
                <label for="instance_key" class="block text-sm font-medium text-gray-700 mb-2">
                    Chave da Instância (Opcional)
                </label>
                <input type="text" 
                       id="instance_key" 
                       name="instance_key" 
                       value="{{ old('instance_key') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm @error('instance_key') border-red-300 @enderror"
                       placeholder="Deixe vazio para gerar automaticamente">
                @error('instance_key')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">
                    Identificador único para a API. Se não informado, será gerado automaticamente baseado no nome.
                </p>
            </div>

            <!-- Information Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800">Como funciona?</h4>
                        <div class="mt-2 text-sm text-blue-700 space-y-2">
                            <p>1. <strong>Criar Instância:</strong> Uma nova instância será criada na Evolution API</p>
                            <p>2. <strong>QR Code:</strong> Será gerado um QR Code para conectar seu WhatsApp</p>
                            <p>3. <strong>Conectar:</strong> Escaneie o QR Code com seu WhatsApp para ativar</p>
                            <p>4. <strong>Pronto:</strong> Sua instância estará pronta para enviar mensagens</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuration Info -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Configurações da Evolution API</h4>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>URL da API:</span>
                        <span class="font-mono text-xs">{{ config('evolution.base_url') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Webhook:</span>
                        <span class="text-xs">Configurado automaticamente</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Status:</span>
                        <span class="text-green-600">{{ config('evolution.base_url') ? '✓ Configurado' : '✗ Não configurado' }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('admin.instances.index') }}" 
                   class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-400 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Criar Instância
                </button>
            </div>

            <!-- Requirements -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Requisitos:</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Evolution API configurada e funcionando
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        WhatsApp instalado no dispositivo móvel
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Acesso à câmera para escanear QR Code
                    </li>
                </ul>
            </div>
        </form>
    </div>
</div>
@endsection