<?php

/**
 * Disparador WhatsApp - Sistema de Disparo de Mensagens
 * 
 * @package DisparadorWhatsApp
 * @author Emerson <https://github.com/emer5om>
 * @version 1.0.0
 * @license MIT
 * @link https://github.com/emer5om/disparador
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Instance;
use App\Models\Message;
use App\Services\CampaignService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct(
        private CampaignService $campaignService
    ) {}

    public function index(Request $request)
    {
        $period = $request->get('period', '30');
        $dateFrom = Carbon::now()->subDays($period);
        
        // Overall Statistics
        $stats = [
            'total_campaigns' => Campaign::count(),
            'active_campaigns' => Campaign::whereIn('status', ['running', 'paused'])->count(),
            'completed_campaigns' => Campaign::where('status', 'completed')->count(),
            'total_messages' => Message::count(),
            'sent_messages' => Message::where('status', 'sent')->count(),
            'failed_messages' => Message::where('status', 'failed')->count(),
            'success_rate' => 0,
        ];

        if ($stats['total_messages'] > 0) {
            $stats['success_rate'] = round(($stats['sent_messages'] / $stats['total_messages']) * 100, 2);
        }

        // Recent Campaign Performance
        $recentCampaigns = Campaign::with('instance')
            ->where('created_at', '>=', $dateFrom)
            ->latest()
            ->get()
            ->map(function ($campaign) {
                $campaign->stats = $this->campaignService->getCampaignStats($campaign);
                return $campaign;
            });

        // Messages sent over time
        $messagesOverTime = Message::select(
                DB::raw('DATE(sent_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
            )
            ->where('sent_at', '>=', $dateFrom)
            ->whereNotNull('sent_at')
            ->groupBy(DB::raw('DATE(sent_at)'))
            ->orderBy('date')
            ->get();

        // Instance Performance
        $instanceStats = Instance::with(['campaigns.messages'])
            ->get()
            ->map(function ($instance) {
                $totalMessages = $instance->campaigns->flatMap->messages->count();
                $sentMessages = $instance->campaigns->flatMap->messages->where('status', 'sent')->count();
                
                return [
                    'instance' => $instance,
                    'total_messages' => $totalMessages,
                    'sent_messages' => $sentMessages,
                    'success_rate' => $totalMessages > 0 ? round(($sentMessages / $totalMessages) * 100, 2) : 0,
                ];
            });

        // Top performing campaigns
        $topCampaigns = Campaign::with('instance')
            ->has('messages')
            ->get()
            ->map(function ($campaign) {
                $campaign->stats = $this->campaignService->getCampaignStats($campaign);
                return $campaign;
            })
            ->sortByDesc('stats.success_rate')
            ->take(10);

        return view('admin.reports.index', compact(
            'stats',
            'recentCampaigns',
            'messagesOverTime',
            'instanceStats',
            'topCampaigns',
            'period'
        ));
    }

    public function campaign(Campaign $campaign)
    {
        $stats = $this->campaignService->getCampaignStats($campaign);
        
        // Message status breakdown
        $messageStatusBreakdown = Message::where('campaign_id', $campaign->id)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Messages sent over time for this campaign
        $messagesTimeline = Message::where('campaign_id', $campaign->id)
            ->select(
                DB::raw('DATE_FORMAT(sent_at, "%Y-%m-%d %H:00:00") as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->whereNotNull('sent_at')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Failed messages with error details
        $failedMessages = Message::where('campaign_id', $campaign->id)
            ->where('status', 'failed')
            ->whereNotNull('error_message')
            ->select('error_message', DB::raw('COUNT(*) as count'))
            ->groupBy('error_message')
            ->orderByDesc('count')
            ->get();

        return view('admin.reports.campaign', compact(
            'campaign',
            'stats',
            'messageStatusBreakdown',
            'messagesTimeline',
            'failedMessages'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'campaigns');
        $period = $request->get('period', '30');
        $dateFrom = Carbon::now()->subDays($period);
        
        if ($type === 'campaigns') {
            return $this->exportCampaigns($dateFrom);
        } elseif ($type === 'messages') {
            return $this->exportMessages($dateFrom);
        }
        
        return redirect()->back()->with('error', 'Tipo de exportação inválido');
    }

    private function exportCampaigns(Carbon $dateFrom)
    {
        $campaigns = Campaign::with(['instance', 'messages'])
            ->where('created_at', '>=', $dateFrom)
            ->get();

        $csvData = [];
        $csvData[] = ['ID', 'Título', 'Instância', 'Status', 'Total Mensagens', 'Enviadas', 'Falharam', 'Taxa Sucesso', 'Criada em'];

        foreach ($campaigns as $campaign) {
            $stats = $this->campaignService->getCampaignStats($campaign);
            $csvData[] = [
                $campaign->id,
                $campaign->title,
                $campaign->instance->name,
                ucfirst($campaign->status),
                $stats['total'],
                $stats['sent'],
                $stats['failed'],
                $stats['success_rate'] . '%',
                $campaign->created_at->format('d/m/Y H:i:s')
            ];
        }

        $filename = 'campanhas_' . date('Y-m-d') . '.csv';
        
        return $this->downloadCsv($csvData, $filename);
    }

    private function exportMessages(Carbon $dateFrom)
    {
        $messages = Message::with(['campaign'])
            ->where('created_at', '>=', $dateFrom)
            ->get();

        $csvData = [];
        $csvData[] = ['ID', 'Campanha', 'Telefone', 'Status', 'Enviada em', 'Erro'];

        foreach ($messages as $message) {
            $csvData[] = [
                $message->id,
                $message->campaign->title,
                $message->phone_number,
                ucfirst($message->status),
                $message->sent_at ? $message->sent_at->format('d/m/Y H:i:s') : '-',
                $message->error_message ?? '-'
            ];
        }

        $filename = 'mensagens_' . date('Y-m-d') . '.csv';
        
        return $this->downloadCsv($csvData, $filename);
    }

    private function downloadCsv(array $data, string $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
