<?php

namespace App\Services;

use App\Models\Indikator;
use App\Models\Opd;
use App\Models\Realisasi;
use App\Models\RekapCapaian;
use App\Models\TargetIndikator;
use Illuminate\Database\Eloquent\Collection;

class RekapCapaianService
{
    public function getByLevel(string $level, int $tahunAnggaranId, int $bulan): Collection
    {
        return RekapCapaian::with('opd')
            ->where('level', $level)
            ->where('tahun_anggaran_id', $tahunAnggaranId)
            ->where('bulan', $bulan)
            ->orderByDesc('persentase')
            ->get();
    }

    public function hitungSemuaBulan(int $tahunAnggaranId): void
    {
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $this->hitung($tahunAnggaranId, $bulan);
        }
    }

    public function hitung(int $tahunAnggaranId, int $bulan): void
    {
        // Ambil semua OPD/unit yang memiliki setidaknya satu IKU terkait
        $relevantOpdIds = Indikator::where('tahun_anggaran_id', $tahunAnggaranId)
            ->where('category', 'utama')
            ->where(fn ($q) => $q
                ->whereNotNull('opd_id')
                ->orWhereNotNull('asisten_id')
                ->orWhereNotNull('sekda_id')
                ->orWhereNotNull('kabag_id')
            )
            ->get(['opd_id', 'asisten_id', 'sekda_id', 'kabag_id'])
            ->flatMap(fn ($i) => array_filter([
                $i->opd_id, $i->asisten_id, $i->sekda_id, $i->kabag_id,
            ]))
            ->unique()
            ->values();

        $opds = Opd::whereIn('id', $relevantOpdIds)->get();

        foreach ($opds as $opd) {
            $this->hitungPerOpd($opd, $tahunAnggaranId, $bulan);
        }
    }

    private function hitungPerOpd(Opd $opd, int $tahunAnggaranId, int $bulan): void
    {
        $indikatorIds = Indikator::where('tahun_anggaran_id', $tahunAnggaranId)
            ->where('category', 'utama')
            ->where(fn ($q) => $q
                ->where('opd_id', $opd->id)
                ->orWhere('bidang_id', $opd->id)
                ->orWhere('asisten_id', $opd->id)
                ->orWhere('kabag_id', $opd->id)
                ->orWhere('sekda_id', $opd->id)
            )
            ->pluck('id');

        if ($indikatorIds->isEmpty()) {
            return;
        }

        $totalTarget = TargetIndikator::whereIn('indikator_id', $indikatorIds)
            ->where('bulan', $bulan)
            ->sum('target');

        $totalRealisasi = Realisasi::whereIn('indikator_id', $indikatorIds)
            ->where('bulan', $bulan)
            ->sum('nilai');

        $persentase = $totalTarget > 0
            ? round(($totalRealisasi / $totalTarget) * 100, 2)
            : 0;

        RekapCapaian::updateOrCreate(
            [
                'tahun_anggaran_id' => $tahunAnggaranId,
                'opd_id' => $opd->id,
                'bulan' => $bulan,
                'level' => $opd->type,
            ],
            [
                'total_target' => $totalTarget,
                'total_realisasi' => $totalRealisasi,
                'persentase' => $persentase,
                'jumlah_indikator' => $indikatorIds->count(),
                'indikator_tercapai' => 0,
                'dihitung_pada' => now(),
            ]
        );
    }
}
