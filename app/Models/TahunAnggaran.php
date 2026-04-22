<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAnggaran extends Model
{
    protected $table = 'tahun_anggaran';

    protected $fillable = [
        'tahun',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function indikators(): HasMany
    {
        return $this->hasMany(Indikator::class);
    }

    public function rekapCapaian(): HasMany
    {
        return $this->hasMany(RekapCapaian::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
