<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapCapaian extends Model
{
    protected $table = 'rekap_capaian';

    protected $fillable = [
        'tahun_anggaran_id',
        'opd_id',
        'level',
        'bulan',
        'total_target',
        'total_realisasi',
        'persentase',
        'jumlah_indikator',
        'indikator_tercapai',
        'dihitung_pada',
    ];

    protected function casts(): array
    {
        return [
            'total_target' => 'decimal:2',
            'total_realisasi' => 'decimal:2',
            'persentase' => 'decimal:2',
            'bulan' => 'integer',
            'jumlah_indikator' => 'integer',
            'indikator_tercapai' => 'integer',
            'dihitung_pada' => 'datetime',
        ];
    }

    public function tahunAnggaran(): BelongsTo
    {
        return $this->belongsTo(TahunAnggaran::class);
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function scopeLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    public function scopeBulan($query, int $bulan)
    {
        return $query->where('bulan', $bulan);
    }
}
