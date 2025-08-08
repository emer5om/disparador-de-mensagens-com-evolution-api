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

@section('title', 'Importar Lista de Leads')
@section('page-title', 'Importar Nova Lista')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.leads.index') }}" 
           class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Voltar para Listas
        </a>
    </div>

    <form method="POST" action="{{ route('admin.leads.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <!-- Upload Card -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Importar Arquivo</h2>
                <p class="text-gray-600 mt-1">Envie um arquivo CSV ou Excel com seus contatos</p>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome da Lista *
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Ex: Leads Black Friday 2024"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Descrição (Opcional)
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Descrição adicional sobre esta lista...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Upload -->
                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        Arquivo *
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Enviar arquivo</span>
                                    <input id="file" name="file" type="file" class="sr-only" accept=".csv,.xlsx,.xls,.txt" required>
                                </label>
                                <p class="pl-1">ou arraste e solte</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                CSV, XLSX, XLS, TXT até 10MB
                            </p>
                        </div>
                    </div>
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-sm font-medium text-blue-900 mb-2">📋 Instruções para o arquivo</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• <strong>Nome:</strong> Coluna com o nome completo do contato</li>
                <li>• <strong>Telefone:</strong> Número do WhatsApp (com ou sem código do país)</li>
                <li>• <strong>Produto (opcional):</strong> Nome do produto/serviço de interesse</li>
                <li>• Formatos aceitos: (11) 99999-9999, 11999999999, +5511999999999</li>
                <li>• A primeira linha deve conter os cabeçalhos das colunas</li>
                <li>• Campos adicionais serão preservados como informações extras</li>
            </ul>
        </div>

        <!-- Example -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-900 mb-3">📄 Exemplo de arquivo CSV:</h3>
            <div class="bg-white border rounded p-3 font-mono text-xs overflow-x-auto">
                <div class="text-gray-600">Nome,Telefone,Produto,Cidade</div>
                <div class="text-gray-800">João Silva,(11) 99999-1234,iPhone 15,São Paulo</div>
                <div class="text-gray-800">Maria Santos,11988887777,MacBook Pro,Rio de Janeiro</div>
                <div class="text-gray-800">Pedro Costa,+5511977776666,AirPods Pro,Belo Horizonte</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-3">
            <button type="submit" 
                    class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                Enviar Arquivo
            </button>
            
            <a href="{{ route('admin.leads.index') }}" 
               class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-400 transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const fileLabel = fileInput.parentElement;
    const fileText = fileLabel.querySelector('span');
    
    fileInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            fileText.textContent = this.files[0].name;
            fileLabel.classList.add('text-blue-600');
        }
    });
});
</script>
@endpush
@endsection