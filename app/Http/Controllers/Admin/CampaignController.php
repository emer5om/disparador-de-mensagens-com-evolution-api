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
use App\Services\CampaignService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    private CampaignService $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    /**
     * Display a listing of campaigns
     */
    public function index()
    {
        try {
            $campaigns = $this->campaignService->getAllCampaignsWithStats();
            return view('admin.campaigns.index', compact('campaigns'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar campanhas: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new campaign
     */
    public function create()
    {
        $instances = Instance::where('status', 'connected')->get();
        $leadLists = \App\Models\LeadList::where('status', 'completed')
            ->where('valid_leads', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($instances->isEmpty()) {
            return redirect()->route('admin.instances.index')
                ->with('error', 'Você precisa ter pelo menos uma instância conectada para criar campanhas.');
        }

        return view('admin.campaigns.create', compact('instances', 'leadLists'));
    }

    /**
     * Store a newly created campaign
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:4000',
            'message_type' => 'required|in:text,button,poll,list,url_button',
            'instance_id' => 'required|exists:instances,id',
            'lead_list_id' => 'nullable|exists:lead_lists,id',
            'delay_seconds' => 'required|integer|min:1|max:300',
            'buttons' => 'sometimes|array|max:4',
            'buttons.*.text' => 'required_with:buttons|string|max:50',
            'buttons.*.url' => 'required_if:message_type,button|url|max:255',
            'buttons.*.description' => 'nullable|string|max:100',
            'phone_numbers' => 'required_without:lead_list_id|string',
        ], [
            'title.required' => 'O título da campanha é obrigatório.',
            'message.required' => 'A mensagem é obrigatória.',
            'message.max' => 'A mensagem não pode ter mais de 4000 caracteres.',
            'message_type.required' => 'O tipo de mensagem é obrigatório.',
            'message_type.in' => 'Tipo de mensagem inválido.',
            'instance_id.required' => 'Selecione uma instância.',
            'instance_id.exists' => 'Instância selecionada não existe.',
            'lead_list_id.exists' => 'Lista de leads selecionada não existe.',
            'delay_seconds.required' => 'O delay entre mensagens é obrigatório.',
            'delay_seconds.min' => 'O delay mínimo é 1 segundo.',
            'delay_seconds.max' => 'O delay máximo é 300 segundos (5 minutos).',
            'buttons.max' => 'Máximo de 4 opções permitidas.',
            'buttons.*.text.required_with' => 'Texto da opção é obrigatório.',
            'buttons.*.text.max' => 'Texto da opção não pode ter mais de 50 caracteres.',
            'buttons.*.url.required_if' => 'URL do botão é obrigatória para botões.',
            'buttons.*.url.url' => 'URL do botão deve ser válida.',
            'buttons.*.description.max' => 'Descrição não pode ter mais de 100 caracteres.',
            'phone_numbers.required_without' => 'Selecione uma lista de leads ou adicione números manualmente.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create campaign
            $campaignData = $request->only(['title', 'message', 'message_type', 'instance_id', 'lead_list_id', 'delay_seconds']);
            $campaignData['created_by'] = 1; // Default user ID
            
            // Process buttons/options based on message type
            if ($request->has('buttons') && is_array($request->buttons)) {
                $buttons = array_filter($request->buttons, function($button) use ($request) {
                    if (empty($button['text'])) return false;
                    
                    // For buttons, URL is required
                    if ($request->message_type === 'button') {
                        return !empty($button['url']);
                    }
                    
                    // For polls and lists, only text is required
                    return true;
                });
                $campaignData['buttons'] = array_values($buttons);
            }

            $campaign = $this->campaignService->createCampaign($campaignData);

            // Add contacts based on source
            if ($request->lead_list_id) {
                // Use lead list
                $leadList = \App\Models\LeadList::findOrFail($request->lead_list_id);
                $added = $this->campaignService->addLeadsFromList($campaign, $leadList);
            } else {
                // Manual phone numbers
                $phoneNumbers = $this->campaignService->importPhoneNumbers($request->phone_numbers);
                
                if (empty($phoneNumbers)) {
                    $campaign->delete();
                    return redirect()->back()
                        ->with('error', 'Nenhum número de telefone válido encontrado.')
                        ->withInput();
                }

                $added = $this->campaignService->addPhoneNumbers($campaign, $phoneNumbers);
            }

            return redirect()
                ->route('admin.campaigns.show', $campaign)
                ->with('success', "Campanha criada com sucesso! {$added} contatos adicionados.");

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar campanha: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified campaign
     */
    public function show(Campaign $campaign)
    {
        try {
            $campaign->load('instance', 'messages.logs');
            $stats = $this->campaignService->getCampaignStats($campaign);
            
            return view('admin.campaigns.show', compact('campaign', 'stats'));
        } catch (Exception $e) {
            return redirect()->route('admin.campaigns.index')
                ->with('error', 'Erro ao carregar campanha: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the campaign
     */
    public function edit(Campaign $campaign)
    {
        if ($campaign->status !== 'draft') {
            return redirect()->route('admin.campaigns.show', $campaign)
                ->with('error', 'Apenas campanhas em rascunho podem ser editadas.');
        }

        $instances = Instance::where('status', 'connected')->get();
        return view('admin.campaigns.edit', compact('campaign', 'instances'));
    }

    /**
     * Update the campaign
     */
    public function update(Request $request, Campaign $campaign)
    {
        if ($campaign->status !== 'draft') {
            return redirect()->route('admin.campaigns.show', $campaign)
                ->with('error', 'Apenas campanhas em rascunho podem ser editadas.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:4000',
            'instance_id' => 'required|exists:instances,id',
            'buttons' => 'sometimes|array|max:3',
            'buttons.*.text' => 'required_with:buttons|string|max:20',
            'buttons.*.url' => 'required_with:buttons|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = $request->only(['title', 'message', 'instance_id']);
            
            // Process buttons
            if ($request->has('buttons') && is_array($request->buttons)) {
                $buttons = array_filter($request->buttons, function($button) {
                    return !empty($button['text']) && !empty($button['url']);
                });
                $updateData['buttons'] = array_values($buttons);
            } else {
                $updateData['buttons'] = null;
            }

            $campaign->update($updateData);

            // Update message content for all pending messages
            $campaign->messages()->where('status', 'pending')
                ->update(['message_content' => $campaign->message]);

            return redirect()
                ->route('admin.campaigns.show', $campaign)
                ->with('success', 'Campanha atualizada com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar campanha: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Start campaign
     */
    public function start(Campaign $campaign)
    {
        try {
            $campaign = $this->campaignService->startCampaign($campaign);
            
            return redirect()->back()
                ->with('success', 'Campanha iniciada com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao iniciar campanha: ' . $e->getMessage());
        }
    }

    /**
     * Pause campaign
     */
    public function pause(Campaign $campaign)
    {
        try {
            $campaign = $this->campaignService->pauseCampaign($campaign);
            
            return redirect()->back()
                ->with('success', 'Campanha pausada com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao pausar campanha: ' . $e->getMessage());
        }
    }

    /**
     * Resume campaign
     */
    public function resume(Campaign $campaign)
    {
        try {
            $campaign = $this->campaignService->resumeCampaign($campaign);
            
            return redirect()->back()
                ->with('success', 'Campanha retomada com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao retomar campanha: ' . $e->getMessage());
        }
    }

    /**
     * Stop campaign
     */
    public function stop(Campaign $campaign)
    {
        try {
            $campaign = $this->campaignService->stopCampaign($campaign);
            
            return redirect()->back()
                ->with('success', 'Campanha parada com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao parar campanha: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate campaign
     */
    public function duplicate(Campaign $campaign)
    {
        try {
            $newCampaign = $this->campaignService->duplicateCampaign($campaign);
            
            return redirect()
                ->route('admin.campaigns.show', $newCampaign)
                ->with('success', 'Campanha duplicada com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao duplicar campanha: ' . $e->getMessage());
        }
    }

    /**
     * Delete campaign
     */
    public function destroy(Campaign $campaign)
    {
        try {
            if (in_array($campaign->status, ['running'])) {
                return redirect()->back()
                    ->with('error', 'Não é possível excluir campanhas em execução.');
            }

            $campaign->delete();
            
            return redirect()
                ->route('admin.campaigns.index')
                ->with('success', 'Campanha excluída com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao excluir campanha: ' . $e->getMessage());
        }
    }

    /**
     * Add phone numbers to existing campaign
     */
    public function addNumbers(Request $request, Campaign $campaign)
    {
        if ($campaign->status !== 'draft') {
            return redirect()->route('admin.campaigns.show', $campaign)
                ->with('error', 'Apenas campanhas em rascunho podem receber novos números.');
        }

        $validator = Validator::make($request->all(), [
            'phone_numbers' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $phoneNumbers = $this->campaignService->importPhoneNumbers($request->phone_numbers);
            
            if (empty($phoneNumbers)) {
                return redirect()->back()
                    ->with('error', 'Nenhum número de telefone válido encontrado.');
            }

            $added = $this->campaignService->addPhoneNumbers($campaign, $phoneNumbers);

            return redirect()
                ->route('admin.campaigns.show', $campaign)
                ->with('success', "{$added} números adicionados com sucesso!");

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao adicionar números: ' . $e->getMessage());
        }
    }
}