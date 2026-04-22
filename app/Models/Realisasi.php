<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Realisasi extends Model
{
    protected $table = 'realisasi';

    protected $fillable = [
        'indikator_id',
        'bulan',
        'nilai',
        'keterangan',
        'user_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'decimal:2',
            'bulan' => 'integer',
        ];
    }

    public function indikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeDiajukan($query)
    {
        return $query->where('status', 'diajukan');
    }

    public function scopeDiverifikasi($query)
    {
        return $query->where('status', 'diverifikasi');
    }
}
