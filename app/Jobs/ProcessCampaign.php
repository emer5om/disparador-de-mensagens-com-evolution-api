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

namespace App\Jobs;

use App\Models\Campaign;
use App\Services\CampaignService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCampaign implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public function __construct(
        protected Campaign $campaign
    ) {}

    public function handle(CampaignService $campaignService): void
    {
        $campaignService->processCampaign($this->campaign);
    }

    public function failed(\Throwable $exception): void
    {
        $this->campaign->update([
            'status' => 'stopped',
            'error_message' => $exception->getMessage()
        ]);
    }
}
