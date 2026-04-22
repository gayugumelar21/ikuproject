<?php

namespace App\Services;

use App\Models\Indikator;
use App\Models\TargetIndikator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class IndikatorService
{
    public function getAll(int $tahunAnggaranId): Collection
    {
        return Indikator::with(['sekda', 'asisten', 'kabag', 'opd', 'bidang', 'dibuatOleh', 'owner'])
            ->where('tahun_anggaran_id', $tahunAnggaranId)
            ->orderBy('nama')
            ->get();
    }

    public function getByOpd(int $tahunAnggaranId, int $opdId): Collection
    {
        return Indikator::with(['opd', 'bidang'])
            ->where('tahun_anggaran_id', $tahunAnggaranId)
            ->where('opd_id', $opdId)
            ->orderBy('nama')
            ->get();
    }

    public function store(array $data): Indikator
    {
        $data['dibuat_oleh'] = Auth::id();

        return Indikator::create($data);
    }

    public function update(Indikator $indikator, array $data): Indikator
    {
        $indikator->update($data);

        return $indikator->fresh();
    }

    public function delete(Indikator $indikator): void
    {
        $indikator->delete();
    }

    public function ajukan(Indikator $indikator): void
    {
        $indikator->update(['status' => 'diajukan']);
    }

    public function simpanTargetBulanan(Indikator $indikator, array $targets): void
    {
        foreach ($targets as $bulan => $target) {
            TargetIndikator::updateOrCreate(
                ['indikator_id' => $indikator->id, 'bulan' => $bulan],
                ['target' => $target]
            );
        }
    }
}
