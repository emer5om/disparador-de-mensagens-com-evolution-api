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

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    protected $fillable = [
        'lead_list_id',
        'name',
        'phone_number',
        'product',
        'extra_data'
    ];

    protected $casts = [
        'extra_data' => 'array'
    ];

    public function leadList(): BelongsTo
    {
        return $this->belongsTo(LeadList::class);
    }
}
