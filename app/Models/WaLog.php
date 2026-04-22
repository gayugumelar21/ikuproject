<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaLog extends Model
{
    protected $fillable = [
        'recipient_user_id',
        'recipient_phone',
        'message_type',
        'message',
        'status',
        'provider_response',
        'sent_by',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'provider_response' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
