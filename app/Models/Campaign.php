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
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'title',
        'message',
        'buttons',
        'message_type',
        'instance_id',
        'lead_list_id',
        'delay_seconds',
        'status',
        'scheduled_at',
        'started_at',
        'finished_at',
        'created_by',
        'error_message',
    ];

    protected $casts = [
        'buttons' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'status' => 'string',
    ];

    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class);
    }

    public function leadList(): BelongsTo
    {
        return $this->belongsTo(LeadList::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getTotalMessagesAttribute(): int
    {
        return $this->messages()->count();
    }

    public function getSentMessagesAttribute(): int
    {
        return $this->messages()->where('status', 'sent')->count();
    }

    public function getFailedMessagesAttribute(): int
    {
        return $this->messages()->where('status', 'failed')->count();
    }

    public function getStatsAttribute(): array
    {
        $total = $this->messages()->count();
        $sent = $this->messages()->where('status', 'sent')->count();
        $failed = $this->messages()->where('status', 'failed')->count();
        $pending = $this->messages()->where('status', 'pending')->count();
        
        $successRate = $total > 0 ? round(($sent / $total) * 100, 1) : 0;
        
        return [
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'pending' => $pending,
            'success_rate' => $successRate,
        ];
    }
}
