<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indikator extends Model
{
    protected $fillable = [
        'tahun_anggaran_id',
        'sekda_id',
        'kabag_id',
        'asisten_id',
        'opd_id',
        'bidang_id',
        'parent_indikator_id',
        'nama',
        'definisi',
        'satuan',
        'target',
        'bobot',
        'status',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'target' => 'decimal:2',
            'bobot' => 'decimal:2',
        ];
    }

    public function tahunAnggaran(): BelongsTo
    {
        return $this->belongsTo(TahunAnggaran::class);
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

    public function parentIndikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class, 'parent_indikator_id');
    }

    public function childIndikators(): HasMany
    {
        return $this->hasMany(Indikator::class, 'parent_indikator_id');
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function targetBulanan(): HasMany
    {
        return $this->hasMany(TargetIndikator::class);
    }

    public function realisasi(): HasMany
    {
        return $this->hasMany(Realisasi::class);
    }

    public function persetujuan(): HasMany
    {
        return $this->hasMany(Persetujuan::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeDiajukan($query)
    {
        return $query->where('status', 'diajukan');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }
}
