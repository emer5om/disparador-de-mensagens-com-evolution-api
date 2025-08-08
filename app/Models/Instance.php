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
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instance extends Model
{
    protected $fillable = [
        'name',
        'instance_key',
        'evolution_api_url',
        'status',
        'qr_code',
        'webhook_url',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    public function isConnecting(): bool
    {
        return $this->status === 'connecting';
    }

    public function isDisconnected(): bool
    {
        return $this->status === 'disconnected';
    }
}
