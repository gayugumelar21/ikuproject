<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Persetujuan extends Model
{
    protected $table = 'persetujuan';

    protected $fillable = [
        'indikator_id',
        'user_id',
        'level',
        'status',
        'catatan',
    ];

    public function indikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }
}
