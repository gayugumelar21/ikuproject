<?php

namespace App\Services;

use App\Models\Realisasi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class RealisasiService
{
    public function getByIndikator(int $indikatorId): Collection
    {
        return Realisasi::where('indikator_id', $indikatorId)
            ->orderBy('bulan')
            ->get();
    }

    public function getByOpdDanTahun(int $opdId, int $tahunAnggaranId): Collection
    {
        return Realisasi::with(['indikator'])
            ->whereHas('indikator', fn ($q) => $q
                ->where('opd_id', $opdId)
                ->where('tahun_anggaran_id', $tahunAnggaranId)
            )
            ->orderBy('bulan')
            ->get();
    }

    public function store(array $data): Realisasi
    {
        $data['user_id'] = Auth::id();

        return Realisasi::create($data);
    }

    public function update(Realisasi $realisasi, array $data): Realisasi
    {
        $realisasi->update($data);

        return $realisasi->fresh();
    }

    public function ajukan(Realisasi $realisasi): void
    {
        $realisasi->update(['status' => 'diajukan']);
    }

    public function verifikasi(Realisasi $realisasi): void
    {
        $realisasi->update(['status' => 'diverifikasi']);
    }

    public function hitungPersentase(Realisasi $realisasi): float
    {
        $target = $realisasi->indikator->targetBulanan()
            ->where('bulan', $realisasi->bulan)
            ->value('target');

        if (! $target || $target == 0) {
            return 0;
        }

        return round(($realisasi->nilai / $target) * 100, 2);
    }
}
