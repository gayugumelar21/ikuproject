<?php

namespace App\Services;

use App\Models\IkuSkoring;
use App\Models\Indikator;
use App\Models\MonthlySummary;
use Illuminate\Support\Collection;

class MonthlySummaryService
{
    public function hitungOpd(int $opdId, int $bulan, int $tahun): MonthlySummary
    {
        $indikators = Indikator::with(['skorings' => fn ($q) => $q->where('bulan', $bulan)->where('tahun', $tahun)])
            ->where('opd_id', $opdId)
            ->where('status', 'disetujui')
            ->get();

        $totalBobotUtama = 0;
        $totalSkorUtama = 0;
        $totalBobotKerjasama = 0;
        $totalSkorKerjasama = 0;
        $isComplete = true;

        foreach ($indikators as $indikator) {
            if ($indikator->category === 'kerjasama' && $indikator->source_indikator_id) {
                $skoring = IkuSkoring::where('indikator_id', $indikator->source_indikator_id)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->first();
            } else {
                $skoring = $indikator->skorings->first();
            }

            $skor = $skoring?->skor_bupati;
            $bobot = (float) $indikator->bobot;

            if ($skor === null) {
                $isComplete = false;
            }

            if ($indikator->category === 'kerjasama') {
                $totalBobotKerjasama += $bobot;
                $totalSkorKerjasama += ($skor ?? 0) * ($bobot / 100);
            } else {
                $totalBobotUtama += $bobot;
                $totalSkorUtama += ($skor ?? 0) * ($bobot / 100);
            }
        }

        $skorUtama = $totalBobotUtama > 0 ? ($totalSkorUtama / ($totalBobotUtama / 100)) : null;
        $skorKerjasama = $totalBobotKerjasama > 0 ? ($totalSkorKerjasama / ($totalBobotKerjasama / 100)) : null;

        if ($skorUtama !== null && $skorKerjasama !== null) {
            $skorTotal = ($skorUtama * 0.7) + ($skorKerjasama * 0.3);
        } elseif ($skorUtama !== null) {
            $skorTotal = $skorUtama;
        } else {
            $skorTotal = $skorKerjasama;
        }

        return MonthlySummary::updateOrCreate(
            ['opd_id' => $opdId, 'bulan' => $bulan, 'tahun' => $tahun],
            [
                'skor_utama' => $skorUtama !== null ? round($skorUtama, 2) : null,
                'skor_kerjasama' => $skorKerjasama !== null ? round($skorKerjasama, 2) : null,
                'skor_total' => $skorTotal !== null ? round($skorTotal, 2) : null,
                'is_complete' => $isComplete && $indikators->isNotEmpty(),
                'calculated_at' => now(),
            ]
        );
    }

    public function hitungSemua(int $bulan, int $tahun): void
    {
        $opdIds = Indikator::where('status', 'disetujui')
            ->whereNotNull('opd_id')
            ->distinct()
            ->pluck('opd_id');

        foreach ($opdIds as $opdId) {
            $this->hitungOpd($opdId, $bulan, $tahun);
        }
    }

    public function getSkorOpdBulan(int $bulan, int $tahun): Collection
    {
        return MonthlySummary::with('opd')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderByDesc('skor_total')
            ->get();
    }
}
