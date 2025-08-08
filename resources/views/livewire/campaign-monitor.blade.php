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
<div wire:poll.5s="updateStats" class="space-y-6">
    <!-- Real-time Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Enviadas</p>
                    <p class="text-2xl font-semibold text-green-600">{{ $stats['sent'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Falharam</p>
                    <p class="text-2xl font-semibold text-red-600">{{ $stats['failed'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pendentes</p>
                    <p class="text-2xl font-semibold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    @if(in_array($campaign->status, ['running', 'paused', 'completed']))
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-medium text-gray-900">Progresso</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">{{ $stats['progress'] ?? 0 }}%</span>
                    <button wire:click="refreshData" class="text-blue-600 hover:text-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="h-3 rounded-full transition-all duration-500 ease-out
                    @if($campaign->status === 'completed') bg-green-500
                    @elseif($campaign->status === 'paused') bg-yellow-500
                    @else bg-blue-500 @endif"
                     style="width: {{ $stats['progress'] ?? 0 }}%"></div>
            </div>
            <div class="mt-2 text-sm text-gray-600">
                Taxa de sucesso: {{ $stats['success_rate'] ?? 0 }}%
            </div>
        </div>
    @endif

    <!-- Campaign Status and Actions -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 text-sm font-medium rounded-full
                    @switch($campaign->status)
                        @case('draft') bg-gray-100 text-gray-800 @break
                        @case('running') bg-blue-100 text-blue-800 animate-pulse @break
                        @case('paused') bg-yellow-100 text-yellow-800 @break
                        @case('completed') bg-green-100 text-green-800 @break
                        @case('stopped') bg-red-100 text-red-800 @break
                    @endswitch">
                    @switch($campaign->status)
                        @case('draft') 📝 Rascunho @break
                        @case('running') 🚀 Executando @break
                        @case('paused') ⏸️ Pausada @break
                        @case('completed') ✅ Concluída @break
                        @case('stopped') ⏹️ Parada @break
                    @endswitch
                </span>
                <span class="text-sm text-gray-500">
                    Última atualização: {{ now()->format('H:i:s') }}
                </span>
            </div>
            
            @if($campaign->status === 'running')
                <div class="flex items-center space-x-2 text-green-600">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-ping"></div>
                    <span class="text-sm font-medium">Em execução</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Mensagens Recentes</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @php
                    $recentMessages = $campaign->messages()->latest()->limit(5)->get();
                @endphp
                
                @forelse($recentMessages as $message)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded" 
                         wire:key="message-{{ $message->id }}">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 rounded-full
                                @switch($message->status)
                                    @case('sent') bg-green-500 @break
                                    @case('failed') bg-red-500 @break
                                    @case('sending') bg-blue-500 animate-pulse @break
                                    @default bg-gray-400 @break
                                @endswitch"></div>
                            <span class="text-sm font-mono">{{ $message->phone_number }}</span>
                        </div>
                        <div class="flex items-center space-x-2 text-xs text-gray-500">
                            <span class="capitalize">{{ $message->status }}</span>
                            @if($message->sent_at)
                                <span>{{ $message->sent_at->format('H:i:s') }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <p>Nenhuma mensagem encontrada</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Auto-refresh functionality
    let pollInterval;
    
    $wire.on('start-polling', (event) => {
        if (pollInterval) clearInterval(pollInterval);
        
        pollInterval = setInterval(() => {
            $wire.call('updateStats');
        }, event.refreshInterval * 1000);
    });
    
    $wire.on('stop-polling', () => {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    });
    
    // Start polling when campaign is running
    @if(in_array($campaign->status, ['running', 'paused']))
        $wire.call('startPolling');
    @endif
    
    // Clean up on page unload
    window.addEventListener('beforeunload', () => {
        if (pollInterval) {
            clearInterval(pollInterval);
        }
    });
</script>
@endscript