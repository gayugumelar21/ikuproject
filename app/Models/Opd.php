<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opd extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'code',
        'type',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Opd::class, 'parent_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function rekapCapaian(): HasMany
    {
        return $this->hasMany(RekapCapaian::class);
    }

    public function scopeSekda($query)
    {
        return $query->where('type', 'sekda');
    }

    public function scopeAsisten($query)
    {
        return $query->where('type', 'asisten');
    }

    public function scopeKabag($query)
    {
        return $query->where('type', 'kabag');
    }

    public function scopeOpd($query)
    {
        return $query->where('type', 'opd');
    }

    public function scopeBidang($query)
    {
        return $query->where('type', 'bidang');
    }
}
