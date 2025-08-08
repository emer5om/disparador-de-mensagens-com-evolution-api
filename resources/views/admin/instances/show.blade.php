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

@section('title', 'Instância: ' . $instance->name)
@section('page-title', 'Detalhes da Instância')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
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

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="ml-3 text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Instance Information Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $instance->name }}</h2>
                    <div class="flex items-center mt-2">
                        <div class="w-3 h-3 rounded-full mr-2
                            @if($instance->status === 'connected') bg-green-400 animate-pulse 
                            @elseif($instance->status === 'connecting') bg-yellow-400 animate-pulse 
                            @else bg-red-400 @endif"></div>
                        <span id="status-badge" class="px-2 py-1 text-xs font-medium rounded-full
                            @if($instance->status === 'connected') bg-green-100 text-green-800 
                            @elseif($instance->status === 'connecting') bg-yellow-100 text-yellow-800 
                            @else bg-red-100 text-red-800 @endif">
                            @if($instance->status === 'connected') Conectado
                            @elseif($instance->status === 'connecting') Conectando
                            @else Desconectado @endif
                        </span>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Instance Key -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chave da Instância</label>
                        <div class="flex items-center">
                            <code class="flex-1 px-2 py-1 bg-gray-100 rounded text-xs font-mono">{{ $instance->instance_key }}</code>
                            <button onclick="copyToClipboard('{{ $instance->instance_key }}')" 
                                    class="ml-2 p-1 text-gray-500 hover:text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- API URL -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL da Evolution API</label>
                        <code class="block px-2 py-1 bg-gray-100 rounded text-xs font-mono">{{ $instance->evolution_api_url }}</code>
                    </div>

                    <!-- Last Update -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Última Atualização</label>
                        <span id="last-update" class="text-sm text-gray-600">{{ $instance->updated_at->diffForHumans() }}</span>
                    </div>

                    <!-- Public QR Link -->
                    <div class="pt-4 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Link Público do QR Code</label>
                        <div class="flex items-center">
                            <input type="text" 
                                   id="public-qr-link" 
                                   value="{{ route('qrcode.show', $instance->instance_key) }}"
                                   class="flex-1 px-2 py-1 bg-gray-100 border border-gray-300 rounded text-xs font-mono cursor-pointer"
                                   readonly>
                            <button onclick="copyPublicLink()" 
                                    class="ml-2 p-1 text-gray-500 hover:text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                            <a href="{{ route('qrcode.show', $instance->instance_key) }}" 
                               target="_blank"
                               class="ml-2 p-1 text-blue-500 hover:text-blue-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Link público para compartilhar o QR Code (atualiza a cada 30 segundos)
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2 pt-4 border-t border-gray-200">
                        <button onclick="refreshStatus()" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Atualizar Status
                        </button>

                        @if($instance->status !== 'connected')
                            <button onclick="refreshQR()" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11a9 9 0 11-18 0 9 9 0 0118 0zm-9 8a1 1 0 100-2 1 1 0 000 2z"></path>
                                </svg>
                                Atualizar QR Code
                            </button>
                        @endif

                        <!-- QR Code Public Link Button -->
                        <a href="{{ route('qrcode.show', $instance->instance_key) }}" 
                           target="_blank"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v4a1 1 0 001 1h4m0-5a1 1 0 011-1h3m0 0a1 1 0 001 1v4a1 1 0 01-1 1h-3m0-8h3m0 0V3m4 4v4a1 1 0 001 1h4a1 1 0 001-1V7a1 1 0 00-1-1h-4z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Ver QR Code Público
                        </a>

                        <form method="POST" action="{{ route('admin.instances.restart', $instance) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Tem certeza que deseja reiniciar esta instância?')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reiniciar Instância
                            </button>
                        </form>

                        <a href="{{ route('admin.instances.edit', $instance) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar Instância
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code & Connection Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Conexão WhatsApp</h2>
                    <p class="text-gray-600 mt-1">Escaneie o QR Code com seu WhatsApp para conectar</p>
                </div>

                <div class="p-6">
                    @if($instance->status === 'connected')
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-green-900 mb-2">WhatsApp Conectado!</h3>
                            <p class="text-green-700 mb-6">Sua instância está conectada e pronta para enviar mensagens</p>
                            
                            <!-- Test Message Form -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 max-w-md mx-auto">
                                <h4 class="text-sm font-medium text-green-900 mb-3">Testar Conexão</h4>
                                <div class="flex gap-2">
                                    <input type="text" 
                                           id="test-phone" 
                                           placeholder="Ex: 5511999999999"
                                           class="flex-1 px-3 py-2 border border-green-300 rounded text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <button onclick="sendTestMessage()" 
                                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700">
                                        Testar
                                    </button>
                                </div>
                                <p class="text-xs text-green-600 mt-2">Enviar mensagem de teste para verificar a conexão</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center">
                            <div id="qr-container" class="mb-6">
                                @if($instance->qr_code)
                                    <div class="inline-block p-4 bg-white border-2 border-gray-200 rounded-lg">
                                        <img id="qr-image" src="data:image/png;base64,{{ $instance->qr_code }}" 
                                             alt="QR Code WhatsApp" 
                                             class="w-64 h-64 mx-auto">
                                    </div>
                                    <p class="text-sm text-gray-600 mt-3">Escaneie este código com seu WhatsApp</p>
                                @else
                                    <div class="w-64 h-64 mx-auto bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11a9 9 0 11-18 0 9 9 0 0118 0zm-9 8a1 1 0 100-2 1 1 0 000 2z"></path>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-900">QR Code não disponível</p>
                                            <p class="text-xs text-gray-500">Clique em "Atualizar QR Code"</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Connection Status -->
                            <div id="connection-status" class="max-w-md mx-auto">
                                @if($instance->status === 'connecting')
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex items-center justify-center">
                                            <svg class="w-5 h-5 text-yellow-500 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-yellow-800">Aguardando conexão do WhatsApp...</span>
                                        </div>
                                        <p class="text-xs text-yellow-700 mt-2 text-center">O status será atualizado automaticamente quando conectar</p>
                                    </div>
                                @else
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <div class="flex items-center justify-center">
                                            <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-red-800">WhatsApp desconectado</span>
                                        </div>
                                        <p class="text-xs text-red-700 mt-2 text-center">Clique em "Atualizar QR Code" para gerar um novo código</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Instructions -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6 max-w-md mx-auto">
                                <h4 class="text-sm font-medium text-blue-800 mb-2">Como conectar:</h4>
                                <ol class="text-sm text-blue-700 space-y-1 text-left">
                                    <li>1. Abra o WhatsApp no seu celular</li>
                                    <li>2. Vá em Menu → Dispositivos conectados</li>
                                    <li>3. Toque em "Conectar um dispositivo"</li>
                                    <li>4. Escaneie o QR Code acima</li>
                                </ol>
                            </div>

                            <!-- Public QR Code Link -->
                            <div class="mt-6">
                                <a href="{{ route('qrcode.show', $instance->instance_key) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-medium rounded-lg hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    🚀 Abrir em Tela Cheia
                                </a>
                                <p class="text-xs text-gray-500 mt-2 text-center">
                                    Link público que atualiza automaticamente a cada 30 segundos
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-refresh status every 10 seconds
let autoRefreshInterval;

document.addEventListener('DOMContentLoaded', function() {
    // Start auto-refresh if not connected
    @if($instance->status !== 'connected')
        startAutoRefresh();
    @endif
});

function startAutoRefresh() {
    autoRefreshInterval = setInterval(refreshStatus, 10000);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
}

function refreshStatus() {
    fetch(`{{ route('admin.instances.status', $instance) }}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatusDisplay(data.status);
                document.getElementById('last-update').textContent = data.updated_at;
                
                // If connected, stop auto-refresh and reload page to show connected view
                if (data.status === 'connected') {
                    stopAutoRefresh();
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            }
        })
        .catch(error => {
            console.error('Error refreshing status:', error);
        });
}

function refreshQR() {
    fetch(`{{ route('admin.instances.refresh-qr', $instance) }}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.qr_code) {
                document.getElementById('qr-image').src = 'data:image/png;base64,' + data.qr_code;
                updateStatusDisplay(data.status);
                
                // Show success message
                showAlert('QR Code atualizado com sucesso!', 'success');
            } else {
                showAlert('Erro ao atualizar QR Code: ' + (data.message || 'Erro desconhecido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error refreshing QR:', error);
            showAlert('Erro ao atualizar QR Code', 'error');
        });
}

function updateStatusDisplay(status) {
    const badge = document.getElementById('status-badge');
    const statusClasses = {
        'connected': 'bg-green-100 text-green-800',
        'connecting': 'bg-yellow-100 text-yellow-800',
        'disconnected': 'bg-red-100 text-red-800'
    };
    
    const statusTexts = {
        'connected': 'Conectado',
        'connecting': 'Conectando',
        'disconnected': 'Desconectado'
    };
    
    // Update badge classes
    badge.className = 'px-2 py-1 text-xs font-medium rounded-full ' + statusClasses[status];
    badge.textContent = statusTexts[status];
}

function sendTestMessage() {
    const phoneInput = document.getElementById('test-phone');
    const phoneNumber = phoneInput.value.trim();
    
    if (!phoneNumber) {
        showAlert('Por favor, digite um número de telefone', 'error');
        return;
    }
    
    fetch(`{{ route('admin.instances.test', $instance) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            phone_number: phoneNumber
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Mensagem de teste enviada com sucesso!', 'success');
            phoneInput.value = '';
        } else {
            showAlert('Erro ao enviar mensagem: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error sending test message:', error);
        showAlert('Erro ao enviar mensagem de teste', 'error');
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert('Chave copiada para a área de transferência!', 'success');
    }).catch(err => {
        console.error('Error copying to clipboard:', err);
        showAlert('Erro ao copiar chave', 'error');
    });
}

function copyPublicLink() {
    const link = document.getElementById('public-qr-link').value;
    navigator.clipboard.writeText(link).then(() => {
        showAlert('Link público copiado para a área de transferência!', 'success');
    }).catch(err => {
        console.error('Error copying to clipboard:', err);
        showAlert('Erro ao copiar link', 'error');
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