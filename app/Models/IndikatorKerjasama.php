<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndikatorKerjasama extends Model
{
    protected $fillable = [
        'indikator_id',
        'sekda_id',
        'kabag_id',
        'asisten_id',
        'opd_id',
        'bidang_id',
        'owner_user_id',
        'peran',
        'bobot',
        'status',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'decimal:2',
        ];
    }

    public function indikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class);
    }

    public function sekda(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'sekda_id');
    }

    public function kabag(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'kabag_id');
    }

    public function asisten(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'asisten_id');
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'bidang_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }
}
