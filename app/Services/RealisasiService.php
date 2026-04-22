<?php

namespace App\Services;

use App\Models\IkuSkoring;
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
                ->where('category', 'utama')
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

        // Otomatis buat/update record iku_skorings agar masuk antrian scoring
        $tahun = (int) now()->format('Y');
        // Cari tahun dari tahun_anggaran indikator jika ada
        $indikator = $realisasi->indikator;
        if ($indikator) {
            $indikator->loadMissing('tahunAnggaran');
            if ($indikator->tahunAnggaran) {
                $tahun = (int) $indikator->tahunAnggaran->tahun;
            }
        }

        IkuSkoring::firstOrCreate(
            [
                'indikator_id' => $realisasi->indikator_id,
                'bulan' => $realisasi->bulan,
                'tahun' => $tahun,
            ],
            [
                'realisasi_id' => $realisasi->id,
                'status' => 'pending',
            ]
        );
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
