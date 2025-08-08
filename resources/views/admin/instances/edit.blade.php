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

@section('title', 'Editar Instância')
@section('page-title', 'Editar Instância')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.instances.show', $instance) }}" 
           class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Voltar para Detalhes
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Editar Instância</h2>
            <p class="text-gray-600 mt-1">Atualize as informações da instância {{ $instance->name }}</p>
        </div>

        <form method="POST" action="{{ route('admin.instances.update', $instance) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

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
                       value="{{ old('name', $instance->name) }}"
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

            <!-- Instance Key (Read Only) -->
            <div>
                <label for="instance_key" class="block text-sm font-medium text-gray-700 mb-2">
                    Chave da Instância
                </label>
                <div class="flex items-center">
                    <input type="text" 
                           id="instance_key" 
                           value="{{ $instance->instance_key }}"
                           class="flex-1 px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 font-mono text-sm cursor-not-allowed"
                           readonly>
                    <button type="button" 
                            onclick="copyToClipboard('{{ $instance->instance_key }}')" 
                            class="ml-2 p-2 text-gray-500 hover:text-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    A chave da instância não pode ser alterada após a criação
                </p>
            </div>

            <!-- Current Status Info -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Status Atual</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Status:</span>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full mr-2
                                @if($instance->status === 'connected') bg-green-400 
                                @elseif($instance->status === 'connecting') bg-yellow-400 
                                @else bg-red-400 @endif"></div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($instance->status === 'connected') bg-green-100 text-green-800 
                                @elseif($instance->status === 'connecting') bg-yellow-100 text-yellow-800 
                                @else bg-red-100 text-red-800 @endif">
                                @if($instance->status === 'connected') Conectado
                                @elseif($instance->status === 'connecting') Conectando
                                @else Desconectado @endif
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">API URL:</span>
                        <span class="font-mono text-xs">{{ $instance->evolution_api_url }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Criada em:</span>
                        <span>{{ $instance->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Última atualização:</span>
                        <span>{{ $instance->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Warning Box -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-yellow-800">Atenção</h4>
                        <p class="mt-1 text-sm text-yellow-700">
                            Alterar informações da instância não afeta a conexão WhatsApp existente. 
                            Para reconectar ou alterar configurações avançadas, use as opções na página de detalhes.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('admin.instances.show', $instance) }}" 
                   class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-400 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Salvar Alterações
                </button>
            </div>

            <!-- Advanced Actions -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Ações Avançadas:</h4>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('admin.instances.show', $instance) }}#connection" 
                       class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Ver QR Code
                    </a>
                    
                    <form method="POST" action="{{ route('admin.instances.restart', $instance) }}" class="flex-1">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Tem certeza que deseja reiniciar esta instância?')"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reiniciar
                        </button>
                    </form>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert('Chave copiada para a área de transferência!', 'success');
    }).catch(err => {
        console.error('Error copying to clipboard:', err);
        showAlert('Erro ao copiar chave', 'error');
    });
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-50 border border-green-200 text-green-700' :
        'bg-red-50 border border-red-200 text-red-700'
    }`;
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
@endpush
@endsection