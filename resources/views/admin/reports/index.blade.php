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

@section('title', 'Relatórios')
@section('page-title', 'Relatórios e Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header with Filters -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Relatórios de Performance</h2>
            <p class="text-gray-600">Análise detalhada das suas campanhas e mensagens</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <!-- Period Filter -->
            <form method="GET" class="flex items-center space-x-2">
                <select name="period" onchange="this.form.submit()" 
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="7" {{ $period == '7' ? 'selected' : '' }}>Últimos 7 dias</option>
                    <option value="30" {{ $period == '30' ? 'selected' : '' }}>Últimos 30 dias</option>
                    <option value="90" {{ $period == '90' ? 'selected' : '' }}>Últimos 90 dias</option>
                    <option value="365" {{ $period == '365' ? 'selected' : '' }}>Último ano</option>
                </select>
            </form>
            
            <!-- Export Buttons -->
            <div class="flex space-x-2">
                <a href="{{ route('admin.reports.export', ['type' => 'campaigns', 'period' => $period]) }}" 
                   class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Exportar Campanhas
                </a>
                <a href="{{ route('admin.reports.export', ['type' => 'messages', 'period' => $period]) }}" 
                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Exportar Mensagens
                </a>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total de Campanhas</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_campaigns'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Mensagens Enviadas</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($stats['sent_messages']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Campanhas Ativas</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['active_campaigns'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Taxa de Sucesso</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['success_rate'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Messages Over Time -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Mensagens por Dia</h3>
            </div>
            <div class="p-6">
                @if($messagesOverTime->count() > 0)
                    <div class="h-64 flex items-end justify-between space-x-2">
                        @foreach($messagesOverTime as $day)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-gray-200 rounded-t" style="height: {{ ($day->total / $messagesOverTime->max('total')) * 200 }}px">
                                    <div class="w-full bg-green-500 rounded-t" style="height: {{ ($day->sent / $day->total) * 100 }}%"></div>
                                </div>
                                <div class="text-xs text-gray-600 mt-2">{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}</div>
                                <div class="text-xs font-medium text-gray-900">{{ $day->total }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex items-center justify-center space-x-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded mr-2"></div>
                            <span>Enviadas</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-200 rounded mr-2"></div>
                            <span>Total</span>
                        </div>
                    </div>
                @else
                    <div class="text-center text-gray-500 py-12">
                        <p>Nenhum dado disponível para o período selecionado</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Performing Campaigns -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Top Campanhas</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($topCampaigns->take(5) as $campaign)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 truncate">{{ $campaign->title }}</h4>
                                <p class="text-sm text-gray-600">{{ $campaign->instance->name }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-semibold text-green-600">{{ $campaign->stats['success_rate'] ?? 0 }}%</div>
                                <div class="text-xs text-gray-500">{{ $campaign->stats['sent'] ?? 0 }}/{{ $campaign->stats['total'] ?? 0 }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-8">
                            <p>Nenhuma campanha encontrada</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Instance Performance -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Performance por Instância</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instância</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Mensagens</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enviadas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taxa de Sucesso</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($instanceStats as $stat)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $stat['instance']->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $stat['instance']->instance_key }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($stat['instance']->status === 'connected') bg-green-100 text-green-800
                                    @elseif($stat['instance']->status === 'connecting') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($stat['instance']->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($stat['total_messages']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                {{ number_format($stat['sent_messages']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $stat['success_rate'] }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $stat['success_rate'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Nenhuma instância encontrada
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Campaigns -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Campanhas Recentes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campanha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enviadas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taxa Sucesso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criada em</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentCampaigns as $campaign)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $campaign->title }}</div>
                                    <div class="text-sm text-gray-500">via {{ $campaign->instance->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @switch($campaign->status)
                                        @case('draft') bg-gray-100 text-gray-800 @break
                                        @case('running') bg-blue-100 text-blue-800 @break
                                        @case('paused') bg-yellow-100 text-yellow-800 @break
                                        @case('completed') bg-green-100 text-green-800 @break
                                        @case('stopped') bg-red-100 text-red-800 @break
                                    @endswitch">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $campaign->stats['total'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                {{ $campaign->stats['sent'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $campaign->stats['success_rate'] }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $campaign->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.campaigns.show', $campaign) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                Nenhuma campanha encontrada
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection