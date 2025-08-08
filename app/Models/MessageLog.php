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

class MessageLog extends Model
{
    protected $fillable = [
        'message_id',
        'status',
        'response',
        'error_message',
    ];

    protected $casts = [
        'response' => 'array',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public static function createLog(int $messageId, string $status, array $response, ?string $errorMessage = null): self
    {
        return self::create([
            'message_id' => $messageId,
            'status' => $status,
            'response' => $response,
            'error_message' => $errorMessage,
        ]);
    }
}
