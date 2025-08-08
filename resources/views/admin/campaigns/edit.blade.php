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

@section('title', 'Editar Campanha')
@section('page-title', 'Editar Campanha')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.campaigns.show', $campaign) }}" 
           class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Voltar para Detalhes
        </a>
    </div>

    <form method="POST" action="{{ route('admin.campaigns.update', $campaign) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Campaign Info Card -->
                <div class="bg-white rounded-lg shadow border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Editar Campanha</h2>
                        <p class="text-gray-600 mt-1">{{ $campaign->title }}</p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Título da Campanha *
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $campaign->title) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>

                        <!-- Instance Selection -->
                        <div>
                            <label for="instance_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Instância WhatsApp *
                            </label>
                            <select id="instance_id" 
                                    name="instance_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                @foreach($instances as $instance)
                                    <option value="{{ $instance->id }}" {{ $campaign->instance_id == $instance->id ? 'selected' : '' }}>
                                        {{ $instance->name }} ({{ $instance->instance_key }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                Mensagem *
                            </label>
                            <textarea id="message" 
                                      name="message" 
                                      rows="6"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      required>{{ old('message', $campaign->message) }}</textarea>
                            <div class="mt-1 flex justify-between text-sm text-gray-500">
                                <span>Máximo de 4000 caracteres</span>
                                <span id="char-count">0 / 4000</span>
                            </div>
                        </div>

                        <!-- Buttons Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Botões de Ação (Opcional)
                            </label>
                            <div class="space-y-3">
                                @for($i = 0; $i < 3; $i++)
                                    <div class="flex gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <input type="text" 
                                                   name="buttons[{{ $i }}][text]" 
                                                   value="{{ old("buttons.{$i}.text", $campaign->buttons[$i]['text'] ?? '') }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                                                   placeholder="Texto do botão (máx. 20 chars)">
                                        </div>
                                        <div class="flex-1">
                                            <input type="url" 
                                                   name="buttons[{{ $i }}][url]" 
                                                   value="{{ old("buttons.{$i}.url", $campaign->buttons[$i]['url'] ?? '') }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                                                   placeholder="https://exemplo.com">
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                Os botões aparecerão como links clicáveis na mensagem. Deixe em branco se não quiser usar.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Campaign Stats -->
                <div class="bg-white rounded-lg shadow border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Estatísticas Atuais</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <label class="block font-medium text-gray-700">Total de Números</label>
                                <p class="text-2xl font-semibold text-gray-900">{{ $campaign->messages()->count() }}</p>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Status</label>
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    @switch($campaign->status)
                                        @case('draft') bg-gray-100 text-gray-800 @break
                                        @case('running') bg-blue-100 text-blue-800 @break
                                        @case('paused') bg-yellow-100 text-yellow-800 @break
                                        @case('completed') bg-green-100 text-green-800 @break
                                        @case('stopped') bg-red-100 text-red-800 @break
                                    @endswitch">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Preview Card -->
                <div class="bg-white rounded-lg shadow border border-gray-200">
                    <div class="px-4 py-3 border-b border-gray-200 bg-green-50">
                        <h3 class="text-sm font-semibold text-green-900">📱 Preview WhatsApp</h3>
                    </div>
                    <div class="p-4">
                        <div class="bg-green-100 rounded-lg p-3 mb-3 max-w-xs ml-auto">
                            <div id="message-preview" class="text-sm text-gray-800 whitespace-pre-wrap">
                                {{ $campaign->message }}
                            </div>
                            <div id="buttons-preview" class="mt-2 space-y-1">
                                @if($campaign->buttons)
                                    @foreach($campaign->buttons as $button)
                                        @if(!empty($button['text']) && !empty($button['url']))
                                            <div class="inline-block px-3 py-1 bg-blue-500 text-white text-xs rounded-full mr-1">
                                                {{ $button['text'] }}
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warning -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-yellow-800 mb-2">⚠️ Atenção</h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Apenas campanhas em rascunho podem ser editadas</li>
                        <li>• Alterar a mensagem atualizará todos os números pendentes</li>
                        <li>• Para adicionar números, use o botão na página de detalhes</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Salvar Alterações
                    </button>
                    
                    <a href="{{ route('admin.campaigns.show', $campaign) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-400 transition-colors">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('message');
    const messagePreview = document.getElementById('message-preview');
    const charCount = document.getElementById('char-count');
    const buttonsPreview = document.getElementById('buttons-preview');
    
    function updatePreview() {
        const message = messageTextarea.value;
        const count = message.length;
        
        charCount.textContent = `${count} / 4000`;
        charCount.className = count > 4000 ? 'text-red-500' : 'text-gray-500';
        
        messagePreview.textContent = message;
        updateButtonsPreview();
    }
    
    function updateButtonsPreview() {
        const buttonInputs = document.querySelectorAll('input[name*="[text]"]');
        buttonsPreview.innerHTML = '';
        
        buttonInputs.forEach((input, index) => {
            const text = input.value.trim();
            const urlInput = document.querySelector(`input[name="buttons[${index}][url]"]`);
            const url = urlInput ? urlInput.value.trim() : '';
            
            if (text && url) {
                const buttonDiv = document.createElement('div');
                buttonDiv.className = 'inline-block px-3 py-1 bg-blue-500 text-white text-xs rounded-full cursor-pointer hover:bg-blue-600 mr-1';
                buttonDiv.textContent = text;
                buttonsPreview.appendChild(buttonDiv);
            }
        });
    }
    
    // Event listeners
    messageTextarea.addEventListener('input', updatePreview);
    
    // Button inputs listeners
    document.querySelectorAll('input[name*="buttons"]').forEach(input => {
        input.addEventListener('input', updateButtonsPreview);
    });
    
    // Initial update
    updatePreview();
});
</script>
@endpush
@endsection