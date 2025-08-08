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

@section('title', 'Listas de Leads')
@section('page-title', 'Gerenciar Listas de Leads')

@section('content')
<div class="space-y-6">
    <!-- Header with Create Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Listas de Leads</h2>
            <p class="text-gray-600">Importe e gerencie suas listas de contatos para as campanhas</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.leads.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                Importar Lista
            </a>
        </div>
    </div>

    @if($leadLists->count() > 0)
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lista
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Leads
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Arquivo Original
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Criada em
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($leadLists as $leadList)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $leadList->name }}
                                        </div>
                                        @if($leadList->description)
                                            <div class="text-sm text-gray-500">
                                                {{ Str::limit($leadList->description, 50) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @switch($leadList->status)
                                            @case('processing') bg-yellow-100 text-yellow-800 @break
                                            @case('completed') bg-green-100 text-green-800 @break
                                            @case('failed') bg-red-100 text-red-800 @break
                                        @endswitch">
                                        @switch($leadList->status)
                                            @case('processing') 🔄 Processando @break
                                            @case('completed') ✅ Concluída @break
                                            @case('failed') ❌ Falhou @break
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium text-green-600">{{ number_format($leadList->valid_leads) }}</span>
                                        @if($leadList->invalid_leads > 0)
                                            <span class="text-gray-400">/</span>
                                            <span class="text-red-500">{{ number_format($leadList->invalid_leads) }}</span>
                                        @endif
                                        <span class="text-gray-400">de {{ number_format($leadList->total_leads) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $leadList->original_filename }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $leadList->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end items-center space-x-2">
                                        @if($leadList->status === 'processing')
                                            <a href="{{ route('admin.leads.mapping', $leadList) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                Configurar
                                            </a>
                                        @else
                                            <a href="{{ route('admin.leads.show', $leadList) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                Ver
                                            </a>
                                        @endif
                                        
                                        @if($leadList->status === 'completed')
                                            <a href="{{ route('admin.leads.export', $leadList) }}" 
                                               class="text-green-600 hover:text-green-900">
                                                Exportar
                                            </a>
                                        @endif
                                        
                                        <form method="POST" action="{{ route('admin.leads.destroy', $leadList) }}" 
                                              class="inline" 
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta lista?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Mostrando {{ $leadLists->firstItem() }} a {{ $leadLists->lastItem() }} de {{ $leadLists->total() }} resultados
            </div>
            {{ $leadLists->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhuma lista importada</h3>
            <p class="text-gray-600 mb-6">Importe sua primeira lista de leads para começar a criar campanhas direcionadas</p>
            <a href="{{ route('admin.leads.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                Importar primeira lista
            </a>
        </div>
    @endif
</div>
@endsection