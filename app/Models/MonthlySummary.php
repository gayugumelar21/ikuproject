<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlySummary extends Model
{
    protected $fillable = [
        'opd_id',
        'bulan',
        'tahun',
        'skor_utama',
        'skor_kerjasama',
        'skor_total',
        'is_complete',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'skor_utama' => 'decimal:2',
            'skor_kerjasama' => 'decimal:2',
            'skor_total' => 'decimal:2',
            'is_complete' => 'boolean',
            'calculated_at' => 'datetime',
        ];
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function getBadgeColor(): string
    {
        if ($this->skor_total === null) {
            return 'zinc';
        }

        return match (true) {
            $this->skor_total >= 8 => 'green',
            $this->skor_total >= 6 => 'yellow',
            default => 'red',
        };
    }
}
