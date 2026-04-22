<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TargetIndikator extends Model
{
    protected $fillable = [
        'indikator_id',
        'bulan',
        'target',
    ];

    protected function casts(): array
    {
        return [
            'target' => 'decimal:2',
            'bulan' => 'integer',
        ];
    }

    public function indikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class);
    }
}
