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

namespace App\Livewire;

use App\Models\Campaign;
use App\Services\CampaignService;
use Livewire\Component;
use Livewire\Attributes\On;

class CampaignMonitor extends Component
{
    public Campaign $campaign;
    public array $stats = [];
    public int $refreshInterval = 5; // seconds
    
    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;
        $this->updateStats();
    }

    public function render()
    {
        return view('livewire.campaign-monitor');
    }

    public function updateStats()
    {
        $campaignService = app(CampaignService::class);
        $this->stats = $campaignService->getCampaignStats($this->campaign);
        $this->campaign->refresh();
    }

    #[On('echo:campaign-updates,CampaignUpdated')]
    public function handleCampaignUpdate($event)
    {
        if ($event['campaignId'] === $this->campaign->id) {
            $this->updateStats();
        }
    }

    public function startPolling()
    {
        $this->dispatch('start-polling', refreshInterval: $this->refreshInterval);
    }

    public function stopPolling()
    {
        $this->dispatch('stop-polling');
    }

    public function refreshData()
    {
        $this->updateStats();
    }
}
