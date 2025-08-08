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

@section('title', 'Configurar Mapeamento - ' . $leadList->name)
@section('page-title', 'Configurar Mapeamento de Campos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Configurar Mapeamento</h2>
            <p class="text-gray-600">Configure como os campos do arquivo serão mapeados para os dados dos leads</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.leads.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <!-- Lead List Info -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ $leadList->name }}</h3>
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                🔄 Aguardando Configuração
            </span>
        </div>
        
        @if($leadList->description)
            <p class="text-gray-600 mb-4">{{ $leadList->description }}</p>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Arquivo:</span>
                <span class="font-medium text-gray-900 ml-2">{{ $leadList->original_filename }}</span>
            </div>
            <div>
                <span class="text-gray-500">Total de registros:</span>
                <span class="font-medium text-gray-900 ml-2">{{ number_format($leadList->total_leads) }}</span>
            </div>
            <div>
                <span class="text-gray-500">Importado em:</span>
                <span class="font-medium text-gray-900 ml-2">{{ $leadList->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Preview Data -->
    @if(isset($preview) && !empty($preview['rows']))
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Prévia dos Dados</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($preview['headers'] as $index => $header)
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Coluna {{ $index }} @if($header)({{ Str::limit($header, 20) }})@endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($preview['rows'] as $row)
                            <tr>
                                @foreach($row as $cell)
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                        {{ Str::limit($cell ?? '', 30) }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($preview['total_rows'] > count($preview['rows']))
                <p class="text-sm text-gray-500 mt-2">Mostrando apenas as primeiras {{ count($preview['rows']) }} linhas de {{ $preview['total_rows'] }} registros</p>
            @endif
        </div>
    @endif

    <!-- Mapping Form -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Configurar Mapeamento de Campos</h3>
        
        <form method="POST" action="{{ route('admin.leads.save-mapping', $leadList) }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Nome Field -->
                <div>
                    <label for="mapping_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome do Lead <span class="text-red-500">*</span>
                    </label>
                    <select name="mapping[name]" id="mapping_name" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            required>
                        <option value="">Selecione a coluna</option>
                        @if(isset($preview) && !empty($preview['headers']))
                            @foreach($preview['headers'] as $index => $header)
                                <option value="{{ $index }}" {{ old('mapping.name') == $index ? 'selected' : '' }}>
                                    Coluna {{ $index }} @if($header)({{ Str::limit($header, 20) }})@endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('mapping.name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Telefone Field -->
                <div>
                    <label for="mapping_phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Número de Telefone <span class="text-red-500">*</span>
                    </label>
                    <select name="mapping[phone_number]" id="mapping_phone_number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            required>
                        <option value="">Selecione a coluna</option>
                        @if(isset($preview) && !empty($preview['headers']))
                            @foreach($preview['headers'] as $index => $header)
                                <option value="{{ $index }}" {{ old('mapping.phone_number') == $index ? 'selected' : '' }}>
                                    Coluna {{ $index }} @if($header)({{ Str::limit($header, 20) }})@endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('mapping.phone_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Produto Field -->
                <div>
                    <label for="mapping_product" class="block text-sm font-medium text-gray-700 mb-2">
                        Produto (Opcional)
                    </label>
                    <select name="mapping[product]" id="mapping_product" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Não mapear</option>
                        @if(isset($preview) && !empty($preview['headers']))
                            @foreach($preview['headers'] as $index => $header)
                                <option value="{{ $index }}" {{ old('mapping.product') == $index ? 'selected' : '' }}>
                                    Coluna {{ $index }} @if($header)({{ Str::limit($header, 20) }})@endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('mapping.product')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Instruções</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li><strong>Nome:</strong> Campo obrigatório que identifica o lead</li>
                                <li><strong>Telefone:</strong> Campo obrigatório com o número para contato (será validado automaticamente)</li>
                                <li><strong>Produto:</strong> Campo opcional para segmentação das campanhas</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-4 space-y-3 sm:space-y-0">
                <a href="{{ route('admin.leads.index') }}" 
                   class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="inline-flex justify-center items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Processar Leads
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-suggest mapping based on column content
    const nameSelect = document.getElementById('mapping_name');
    const phoneSelect = document.getElementById('mapping_phone_number');
    const productSelect = document.getElementById('mapping_product');
    
    // Simple heuristics for auto-mapping
    const options = Array.from(nameSelect.options);
    
    options.forEach((option, index) => {
        if (index === 0) return; // Skip the first "Select column" option
        
        const text = option.textContent.toLowerCase();
        
        // Auto-select name field
        if (!nameSelect.value && (text.includes('nome') || text.includes('name'))) {
            nameSelect.value = option.value;
        }
        
        // Auto-select phone field
        if (!phoneSelect.value && (text.includes('telefone') || text.includes('phone') || text.includes('celular') || text.includes('whatsapp'))) {
            phoneSelect.value = option.value;
        }
        
        // Auto-select product field
        if (!productSelect.value && (text.includes('produto') || text.includes('product') || text.includes('serviço') || text.includes('service'))) {
            productSelect.value = option.value;
        }
    });
});
</script>
@endpush