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

@section('title', $campaign->title)
@section('page-title', 'Detalhes da Campanha')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.campaigns.index') }}" 
                   class="text-blue-600 hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $campaign->title }}</h1>
                    <div class="flex items-center space-x-3 mt-1">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            @switch($campaign->status)
                                @case('draft') bg-gray-100 text-gray-800 @break
                                @case('running') bg-blue-100 text-blue-800 @break
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
                            via {{ $campaign->instance->name }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 sm:mt-0 flex gap-2">
            @if($campaign->status === 'draft')
                <form method="POST" action="{{ route('admin.campaigns.start', $campaign) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Tem certeza que deseja iniciar esta campanha?')"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        🚀 Iniciar Campanha
                    </button>
                </form>
                <a href="{{ route('admin.campaigns.edit', $campaign) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    ✏️ Editar
                </a>
            @elseif($campaign->status === 'running')
                <form method="POST" action="{{ route('admin.campaigns.pause', $campaign) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                        ⏸️ Pausar
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.campaigns.stop', $campaign) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Tem certeza que deseja parar esta campanha?')"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                        ⏹️ Parar
                    </button>
                </form>
            @elseif($campaign->status === 'paused')
                <form method="POST" action="{{ route('admin.campaigns.resume', $campaign) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        ▶️ Retomar
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.campaigns.stop', $campaign) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Tem certeza que deseja parar esta campanha?')"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                        ⏹️ Parar
                    </button>
                </form>
            @endif
            
            <form method="POST" action="{{ route('admin.campaigns.duplicate', $campaign) }}" class="inline">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    📄 Duplicar
                </button>
            </form>
        </div>
    </div>

    <!-- Real-time Campaign Monitor -->
    <livewire:campaign-monitor :campaign="$campaign" />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Campaign Details -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Detalhes da Campanha</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mensagem</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $campaign->message }}</p>
                    </div>
                </div>

                @if($campaign->buttons && is_array($campaign->buttons) && count($campaign->buttons) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Botões</label>
                        <div class="mt-1 space-y-2">
                            @foreach($campaign->buttons as $button)
                                <div class="flex items-center justify-between p-2 bg-blue-50 rounded">
                                    <span class="text-sm font-medium text-blue-900">{{ $button['text'] ?? 'Botão' }}</span>
                                    @if(isset($button['url']) && $button['url'])
                                        <a href="{{ $button['url'] }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800">{{ $button['url'] }}</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="block font-medium text-gray-700">Instância</label>
                        <p class="text-gray-900">{{ $campaign->instance->name }}</p>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700">Status</label>
                        <p class="text-gray-900">{{ ucfirst($campaign->status) }}</p>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700">Criada em</label>
                        <p class="text-gray-900">{{ $campaign->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($campaign->started_at)
                        <div>
                            <label class="block font-medium text-gray-700">Iniciada em</label>
                            <p class="text-gray-900">{{ $campaign->started_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Messages Status -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Status das Mensagens</h3>
                @if($campaign->status === 'draft')
                    <button onclick="openAddNumbersModal()" 
                            class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                        + Adicionar Números
                    </button>
                @endif
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @php
                        $messages = $campaign->messages()->latest()->limit(10)->get();
                    @endphp
                    
                    @forelse($messages as $message)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded-full
                                    @switch($message->status)
                                        @case('sent') bg-green-500 @break
                                        @case('failed') bg-red-500 @break
                                        @case('sending') bg-blue-500 @break
                                        @default bg-gray-400 @break
                                    @endswitch"></div>
                                <span class="text-sm font-mono">{{ $message->phone_number }}</span>
                            </div>
                            <div class="flex items-center space-x-2 text-xs text-gray-500">
                                <span class="capitalize">{{ $message->status }}</span>
                                @if($message->sent_at)
                                    <span>{{ $message->sent_at->format('H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <p>Nenhum número adicionado ainda</p>
                        </div>
                    @endforelse
                    
                    @if($campaign->messages()->count() > 10)
                        <div class="text-center pt-3">
                            <span class="text-sm text-gray-500">
                                Mostrando 10 de {{ $campaign->messages()->count() }} números
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Numbers Modal -->
@if($campaign->status === 'draft')
    <div id="addNumbersModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Adicionar Números</h3>
            
            <form method="POST" action="{{ route('admin.campaigns.add-numbers', $campaign) }}">
                @csrf
                <div class="mb-4">
                    <label for="new_phone_numbers" class="block text-sm font-medium text-gray-700 mb-2">
                        Números de Telefone
                    </label>
                    <textarea id="new_phone_numbers" 
                              name="phone_numbers" 
                              rows="6"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg font-mono text-sm"
                              placeholder="Cole ou digite os números aqui..."
                              required></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeAddNumbersModal()" 
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Adicionar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

@push('scripts')
<script>
function openAddNumbersModal() {
    document.getElementById('addNumbersModal').classList.remove('hidden');
    document.getElementById('addNumbersModal').classList.add('flex');
}

function closeAddNumbersModal() {
    document.getElementById('addNumbersModal').classList.add('hidden');
    document.getElementById('addNumbersModal').classList.remove('flex');
}

// Auto refresh for running campaigns
@if(in_array($campaign->status, ['running', 'paused']))
    setInterval(() => {
        window.location.reload();
    }, 30000); // Refresh every 30 seconds
@endif
</script>
@endpush
@endsection