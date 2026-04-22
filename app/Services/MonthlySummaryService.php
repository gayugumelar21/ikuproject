<?php

namespace App\Services;

use App\Models\Indikator;
use App\Models\IndikatorKerjasama;
use App\Models\MonthlySummary;
use Illuminate\Support\Collection;

class MonthlySummaryService
{
    public function hitungOpd(int $opdId, int $bulan, int $tahun): MonthlySummary
    {
        $indikatorUtama = Indikator::with(['skorings' => fn ($q) => $q->where('bulan', $bulan)->where('tahun', $tahun)])
            ->where('opd_id', $opdId)
            ->where('status', 'disetujui')
            ->where('category', 'utama')
            ->whereHas('tahunAnggaran', fn ($q) => $q->where('tahun', $tahun))
            ->get();

        $indikatorKerjasama = IndikatorKerjasama::with([
            'indikator' => fn ($q) => $q->with([
                'skorings' => fn ($sq) => $sq->where('bulan', $bulan)->where('tahun', $tahun),
            ]),
        ])
            ->where('opd_id', $opdId)
            ->where('status', 'disetujui')
            ->whereHas('indikator', fn ($q) => $q
                ->where('status', 'disetujui')
                ->where('category', 'utama')
                ->whereHas('tahunAnggaran', fn ($tq) => $tq->where('tahun', $tahun))
            )
            ->get();

        $totalBobotUtama = 0;
        $totalSkorUtama = 0;
        $totalBobotKerjasama = 0;
        $totalSkorKerjasama = 0;
        $isComplete = true;

        foreach ($indikatorUtama as $indikator) {
            $skoring = $indikator->skorings->first();
            $skor = $skoring?->skor_bupati;
            $bobot = (float) $indikator->bobot;

            if ($skor === null) {
                $isComplete = false;
            }

            $totalBobotUtama += $bobot;
            $totalSkorUtama += ($skor ?? 0) * ($bobot / 100);
        }

        foreach ($indikatorKerjasama as $kerjasama) {
            $skoring = $kerjasama->indikator?->skorings?->first();
            $skor = $skoring?->skor_bupati;
            $bobot = (float) $kerjasama->bobot;

            if ($skor === null) {
                $isComplete = false;
            }

            $totalBobotKerjasama += $bobot;
            $totalSkorKerjasama += ($skor ?? 0) * ($bobot / 100);
        }

        $skorUtama = $totalBobotUtama > 0 ? ($totalSkorUtama / ($totalBobotUtama / 100)) : null;
        $skorKerjasama = $totalBobotKerjasama > 0 ? ($totalSkorKerjasama / ($totalBobotKerjasama / 100)) : null;
        $hasData = $indikatorUtama->isNotEmpty() || $indikatorKerjasama->isNotEmpty();

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
                'is_complete' => $isComplete && $hasData,
                'calculated_at' => now(),
            ]
        );
    }

    public function hitungSemua(int $bulan, int $tahun): void
    {
        // OPD/unit yang punya IKU utama sendiri (termasuk asisten/kabag yang punya IKU langsung)
        $opdUtamaIds = Indikator::where('status', 'disetujui')
            ->where('category', 'utama')
            ->whereNotNull('opd_id')
            ->whereHas('tahunAnggaran', fn ($q) => $q->where('tahun', $tahun))
            ->distinct()
            ->pluck('opd_id');

        // OPD yang terlibat sebagai mitra kerjasama
        $opdKerjasamaIds = IndikatorKerjasama::where('status', 'disetujui')
            ->whereHas('indikator', fn ($q) => $q
                ->where('status', 'disetujui')
                ->where('category', 'utama')
                ->whereHas('tahunAnggaran', fn ($tq) => $tq->where('tahun', $tahun))
            )
            ->distinct()
            ->pluck('opd_id');

        $opdIds = $opdUtamaIds->merge($opdKerjasamaIds)->filter()->unique()->values();

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
