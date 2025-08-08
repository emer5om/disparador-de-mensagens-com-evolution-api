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

@section('title', 'Nova Campanha')
@section('page-title', 'Criar Nova Campanha')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.campaigns.index') }}" 
           class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Voltar para Campanhas
        </a>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Erro ao criar campanha
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Message -->
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.campaigns.store') }}" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Campaign Info Card -->
                <div class="bg-white rounded-lg shadow border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Informações da Campanha</h2>
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
                                   value="{{ old('title') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Ex: Promoção Black Friday 2024"
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
                                <option value="">Selecione uma instância</option>
                                @foreach($instances as $instance)
                                    <option value="{{ $instance->id }}">
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
                                      placeholder="Digite sua mensagem aqui..."
                                      required>{{ old('message') }}</textarea>
                            <div class="mt-1 flex justify-between text-sm text-gray-500">
                                <span>Máximo de 4000 caracteres</span>
                                <span id="char-count">0 / 4000</span>
                            </div>
                        </div>

                        <!-- Message Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Mensagem *
                            </label>
                            <select id="message_type" 
                                    name="message_type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                <option value="text" {{ old('message_type', 'text') == 'text' ? 'selected' : '' }}>
                                    📝 Texto Simples
                                </option>
                                <option value="button" {{ old('message_type') == 'button' ? 'selected' : '' }}>
                                    🔘 Botões (Descontinuado - não recomendado)
                                </option>
                                <option value="poll" {{ old('message_type') == 'poll' ? 'selected' : '' }}>
                                    📊 Enquete/Poll (Recomendado)
                                </option>
                                <option value="list" {{ old('message_type') == 'list' ? 'selected' : '' }}>
                                    📋 Lista de Opções (Recomendado)
                                </option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">
                                <strong>Enquetes</strong> e <strong>Listas</strong> são as melhores alternativas aos botões que foram descontinuados.
                            </p>
                        </div>

                        <!-- Buttons Section -->
                        <div id="buttons_section">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span id="buttons_label">Opções de Interação</span>
                            </label>
                            <div class="space-y-3">
                                @for($i = 0; $i < 4; $i++)
                                    <div class="flex gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <input type="text" 
                                                   name="buttons[{{ $i }}][text]" 
                                                   value="{{ old("buttons.{$i}.text") }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                                                   placeholder="Texto da opção">
                                        </div>
                                        <div class="flex-1" id="description_field_{{ $i }}" style="display: none;">
                                            <input type="text" 
                                                   name="buttons[{{ $i }}][description]" 
                                                   value="{{ old("buttons.{$i}.description") }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                                                   placeholder="Descrição (opcional)">
                                        </div>
                                        <div class="flex-1" id="url_field_{{ $i }}">
                                            <input type="url" 
                                                   name="buttons[{{ $i }}][url]" 
                                                   value="{{ old("buttons.{$i}.url") }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                                                   placeholder="https://exemplo.com">
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <p class="mt-1 text-sm text-gray-500" id="buttons_help">
                                Configure as opções de interação conforme o tipo selecionado.
                            </p>
                        </div>

                        <!-- Delay Between Messages -->
                        <div>
                            <label for="delay_seconds" class="block text-sm font-medium text-gray-700 mb-2">
                                Delay entre Mensagens (segundos) *
                            </label>
                            <input type="number" 
                                   id="delay_seconds" 
                                   name="delay_seconds" 
                                   value="{{ old('delay_seconds', 5) }}"
                                   min="1" 
                                   max="300"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="5"
                                   required>
                            <p class="mt-1 text-sm text-gray-500">
                                Tempo de espera entre o envio de cada mensagem (1 a 300 segundos)
                            </p>
                        </div>

                        <!-- Lead List Selection -->
                        <div>
                            <label for="lead_list_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Lista de Leads (Opcional)
                            </label>
                            <select id="lead_list_id" 
                                    name="lead_list_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Selecione uma lista ou use números manuais</option>
                                @foreach($leadLists as $leadList)
                                    <option value="{{ $leadList->id }}" {{ old('lead_list_id') == $leadList->id ? 'selected' : '' }}>
                                        {{ $leadList->name }} ({{ number_format($leadList->valid_leads) }} leads)
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">
                                Se selecionada, os números manuais serão ignorados
                            </p>
                        </div>

                        <!-- Phone Numbers -->
                        <div id="phone_numbers_section">
                            <label for="phone_numbers" class="block text-sm font-medium text-gray-700 mb-2">
                                Números de Telefone *
                            </label>
                            <textarea id="phone_numbers" 
                                      name="phone_numbers" 
                                      rows="8"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                      placeholder="Cole ou digite os números aqui:
11999999999
(11) 99999-9999
+55 11 99999-9999
5511999999999">{{ old('phone_numbers') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">
                                Um número por linha. Será ignorado se uma lista de leads for selecionada.
                            </p>
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
                                Digite uma mensagem para ver o preview...
                            </div>
                            <div id="buttons-preview" class="mt-2 space-y-1">
                                <!-- Buttons will appear here dynamically -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Criar Campanha
                    </button>
                    
                    <a href="{{ route('admin.campaigns.index') }}" 
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
    const leadListSelect = document.getElementById('lead_list_id');
    const phoneNumbersSection = document.getElementById('phone_numbers_section');
    const phoneNumbersTextarea = document.getElementById('phone_numbers');
    const messageTypeSelect = document.getElementById('message_type');
    const buttonsSection = document.getElementById('buttons_section');
    const buttonsLabel = document.getElementById('buttons_label');
    const buttonsHelp = document.getElementById('buttons_help');
    
    function updateMessageTypeUI() {
        const messageType = messageTypeSelect.value;
        
        // Update labels and help text
        switch(messageType) {
            case 'text':
                buttonsSection.style.display = 'none';
                break;
            case 'button':
                buttonsSection.style.display = 'block';
                buttonsLabel.textContent = 'Botões de Ação (Descontinuado)';
                buttonsHelp.innerHTML = '⚠️ <strong>Atenção:</strong> Botões foram descontinuados pela Evolution API. Use Enquetes ou Listas.';
                buttonsHelp.className = 'mt-1 text-sm text-red-600';
                showUrlFields();
                hideDescriptionFields();
                break;
            case 'poll':
                buttonsSection.style.display = 'block';
                buttonsLabel.textContent = 'Opções da Enquete';
                buttonsHelp.innerHTML = '📊 Configure as opções que aparecerão na enquete. Os usuários poderão votar em uma opção.';
                buttonsHelp.className = 'mt-1 text-sm text-green-600';
                hideUrlFields();
                hideDescriptionFields();
                break;
            case 'list':
                buttonsSection.style.display = 'block';
                buttonsLabel.textContent = 'Itens da Lista';
                buttonsHelp.innerHTML = '📋 Configure os itens que aparecerão na lista. Adicione descrições para mais clareza.';
                buttonsHelp.className = 'mt-1 text-sm text-blue-600';
                hideUrlFields();
                showDescriptionFields();
                break;
        }
        
        updatePreview();
    }
    
    function showUrlFields() {
        for(let i = 0; i < 4; i++) {
            const urlField = document.getElementById(`url_field_${i}`);
            if(urlField) urlField.style.display = 'block';
        }
    }
    
    function hideUrlFields() {
        for(let i = 0; i < 4; i++) {
            const urlField = document.getElementById(`url_field_${i}`);
            if(urlField) urlField.style.display = 'none';
        }
    }
    
    function showDescriptionFields() {
        for(let i = 0; i < 4; i++) {
            const descField = document.getElementById(`description_field_${i}`);
            if(descField) descField.style.display = 'block';
        }
    }
    
    function hideDescriptionFields() {
        for(let i = 0; i < 4; i++) {
            const descField = document.getElementById(`description_field_${i}`);
            if(descField) descField.style.display = 'none';
        }
    }
    
    function updatePreview() {
        const message = messageTextarea.value;
        const count = message.length;
        const messageType = messageTypeSelect.value;
        
        charCount.textContent = `${count} / 4000`;
        charCount.className = count > 4000 ? 'text-red-500' : 'text-gray-500';
        
        messagePreview.textContent = message || 'Digite uma mensagem para ver o preview...';
        messagePreview.className = message ? 'text-sm text-gray-800 whitespace-pre-wrap' : 'text-sm text-gray-400 whitespace-pre-wrap';
        updateButtonsPreview();
    }
    
    function updateButtonsPreview() {
        const buttonInputs = document.querySelectorAll('input[name*="[text]"]');
        const messageType = messageTypeSelect.value;
        buttonsPreview.innerHTML = '';
        
        if (messageType === 'text') {
            return;
        }
        
        buttonInputs.forEach((input, index) => {
            const text = input.value.trim();
            if (!text) return;
            
            const buttonDiv = document.createElement('div');
            
            switch(messageType) {
                case 'button':
                    const urlInput = document.querySelector(`input[name="buttons[${index}][url]"]`);
                    const url = urlInput ? urlInput.value.trim() : '';
                    if (text && url) {
                        buttonDiv.className = 'inline-block px-3 py-1 bg-blue-500 text-white text-xs rounded-full cursor-pointer hover:bg-blue-600 mr-1 mb-1';
                        buttonDiv.textContent = text;
                        buttonsPreview.appendChild(buttonDiv);
                    }
                    break;
                case 'poll':
                    buttonDiv.className = 'block p-2 bg-gray-100 border border-gray-300 rounded text-sm mb-1';
                    buttonDiv.innerHTML = `<span class="inline-block w-4 h-4 border border-gray-400 rounded-full mr-2"></span>${text}`;
                    buttonsPreview.appendChild(buttonDiv);
                    break;
                case 'list':
                    const descInput = document.querySelector(`input[name="buttons[${index}][description]"]`);
                    const description = descInput ? descInput.value.trim() : '';
                    buttonDiv.className = 'block p-2 bg-gray-100 border border-gray-300 rounded text-sm mb-1';
                    buttonDiv.innerHTML = `<div class="font-medium">${text}</div>${description ? `<div class="text-xs text-gray-600">${description}</div>` : ''}`;
                    buttonsPreview.appendChild(buttonDiv);
                    break;
            }
        });
        
        if (buttonsPreview.children.length === 0 && messageType !== 'text') {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'text-xs text-gray-400 italic';
            emptyDiv.textContent = 'Configure as opções acima para ver o preview';
            buttonsPreview.appendChild(emptyDiv);
        }
    }
    
    function togglePhoneNumbersSection() {
        const hasLeadList = leadListSelect.value !== '';
        if (hasLeadList) {
            phoneNumbersSection.style.opacity = '0.5';
            phoneNumbersTextarea.disabled = true;
            phoneNumbersTextarea.required = false;
        } else {
            phoneNumbersSection.style.opacity = '1';
            phoneNumbersTextarea.disabled = false;
            phoneNumbersTextarea.required = true;
        }
    }
    
    // Event listeners
    messageTextarea.addEventListener('input', updatePreview);
    messageTypeSelect.addEventListener('change', updateMessageTypeUI);
    leadListSelect.addEventListener('change', togglePhoneNumbersSection);
    
    // Button inputs listeners
    document.querySelectorAll('input[name*="buttons"]').forEach(input => {
        input.addEventListener('input', updateButtonsPreview);
    });
    
    // Initial updates
    updateMessageTypeUI();
    updatePreview();
    togglePhoneNumbersSection();
});
</script>
@endpush
@endsection