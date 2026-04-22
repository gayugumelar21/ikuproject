<?php

namespace App\Services;

use App\Models\TahunAnggaran;
use Illuminate\Database\Eloquent\Collection;

class TahunAnggaranService
{
    public function getAll(): Collection
    {
        return TahunAnggaran::orderBy('tahun', 'desc')->get();
    }

    public function getActive(): ?TahunAnggaran
    {
        return TahunAnggaran::where('is_active', true)->first();
    }

    public function store(array $data): TahunAnggaran
    {
        return TahunAnggaran::create($data);
    }

    public function update(TahunAnggaran $tahunAnggaran, array $data): TahunAnggaran
    {
        $tahunAnggaran->update($data);

        return $tahunAnggaran->fresh();
    }

    public function setActive(TahunAnggaran $tahunAnggaran): void
    {
        TahunAnggaran::query()->update(['is_active' => false]);
        $tahunAnggaran->update(['is_active' => true]);
    }

    public function delete(TahunAnggaran $tahunAnggaran): void
    {
        $tahunAnggaran->delete();
    }
}
