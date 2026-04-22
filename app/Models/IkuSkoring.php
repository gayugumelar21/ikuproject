<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IkuSkoring extends Model
{
    protected $table = 'iku_skorings';

    protected $fillable = [
        'indikator_id',
        'realisasi_id',
        'bulan',
        'tahun',
        'skor_ai',
        'ai_reasoning',
        'ai_generated_at',
        'skor_ta',
        'ta_notes',
        'ta_scored_by',
        'ta_scored_at',
        'skor_bupati',
        'bupati_notes',
        'bupati_scored_at',
        'is_final',
        'finalized_by',
        'finalized_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'ai_generated_at' => 'datetime',
            'ta_scored_at' => 'datetime',
            'bupati_scored_at' => 'datetime',
            'finalized_at' => 'datetime',
            'is_final' => 'boolean',
        ];
    }

    public function indikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class);
    }

    public function realisasi(): BelongsTo
    {
        return $this->belongsTo(Realisasi::class);
    }

    public function taScoredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ta_scored_by');
    }

    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function getSkorFinal(): ?int
    {
        return $this->skor_bupati;
    }

    public function isSelesai(): bool
    {
        return $this->status === 'final' && $this->is_final;
    }
}
