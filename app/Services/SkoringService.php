<?php

namespace App\Services;

use App\Models\IkuSkoring;
use App\Models\Indikator;
use App\Models\IndikatorKerjasama;
use App\Models\User;
use App\Notifications\SkorBupatiFinalisasi;
use Illuminate\Database\Eloquent\Collection;

class SkoringService
{
    public function getOrCreateSkoring(int $indikatorId, int $bulan, int $tahun): IkuSkoring
    {
        return IkuSkoring::firstOrCreate(
            ['indikator_id' => $indikatorId, 'bulan' => $bulan, 'tahun' => $tahun],
            ['status' => 'pending']
        );
    }

    public function simpanSkorTa(IkuSkoring $skoring, int $skor, string $notes, User $scoredBy): IkuSkoring
    {
        $skoring->update([
            'skor_ta' => $skor,
            'ta_notes' => $notes,
            'ta_scored_by' => $scoredBy->id,
            'ta_scored_at' => now(),
            'status' => 'ta_done',
        ]);

        return $skoring->fresh();
    }

    public function simpanSkorBupati(IkuSkoring $skoring, int $skor, ?string $notes, User $bupati): IkuSkoring
    {
        $skoring->update([
            'skor_bupati' => $skor,
            'bupati_notes' => $notes,
            'bupati_scored_at' => now(),
            'status' => 'final',
            'is_final' => true,
            'finalized_by' => $bupati->id,
            'finalized_at' => now(),
        ]);

        $indikator = $skoring->indikator;

        // Notify owner of the OPD that their IKU score is finalized
        if ($indikator) {
            $indikator->loadMissing(['owner', 'kerjasamas.owner']);

            $penerima = collect([$indikator->owner])
                ->merge($indikator->kerjasamas->pluck('owner'))
                ->filter()
                ->unique('id');

            foreach ($penerima as $owner) {
                $owner->notify(new SkorBupatiFinalisasi($indikator, $skoring->fresh()));
            }
        }

        return $skoring->fresh();
    }

    /**
     * @return array{skor_total: float, jumlah_indikator: int, sudah_final: int, lengkap: bool}
     */
    public function hitungSkorTertimbangOpd(int $opdId, int $bulan, int $tahun): array
    {
        $indikators = Indikator::with(['tahunAnggaran', 'skorings' => fn ($q) => $q->where('bulan', $bulan)->where('tahun', $tahun)])
            ->where('opd_id', $opdId)
            ->disetujui()
            ->where('category', 'utama')
            ->whereHas('tahunAnggaran', fn ($q) => $q->where('tahun', $tahun))
            ->get();

        $kerjasamas = IndikatorKerjasama::with([
            'indikator' => fn ($q) => $q->with([
                'skorings' => fn ($sq) => $sq->where('bulan', $bulan)->where('tahun', $tahun),
            ]),
        ])
            ->where('opd_id', $opdId)
            ->disetujui()
            ->whereHas('indikator', fn ($q) => $q
                ->disetujui()
                ->where('category', 'utama')
                ->whereHas('tahunAnggaran', fn ($tq) => $tq->where('tahun', $tahun))
            )
            ->get();

        $skorTotal = 0.0;
        $sudahFinal = 0;

        foreach ($indikators as $indikator) {
            $skoring = $indikator->skorings->first();

            if ($skoring && $skoring->status === 'final' && $skoring->skor_bupati !== null) {
                $skorTotal += $skoring->skor_bupati * ((float) $indikator->bobot / 100);
                $sudahFinal++;
            }
        }

        foreach ($kerjasamas as $kerjasama) {
            $skoring = $kerjasama->indikator?->skorings?->first();

            if ($skoring && $skoring->status === 'final' && $skoring->skor_bupati !== null) {
                $skorTotal += $skoring->skor_bupati * ((float) $kerjasama->bobot / 100);
                $sudahFinal++;
            }
        }

        $jumlahIndikator = $indikators->count() + $kerjasamas->count();

        return [
            'skor_total' => round($skorTotal, 2),
            'jumlah_indikator' => $jumlahIndikator,
            'sudah_final' => $sudahFinal,
            'lengkap' => $jumlahIndikator > 0 && $sudahFinal === $jumlahIndikator,
        ];
    }

    public function getPendingUntukTa(int $bulan, int $tahun, ?int $filterOpdId = null): Collection
    {
        return IkuSkoring::with(['indikator.opd', 'indikator.bidang', 'realisasi'])
            ->whereIn('status', ['pending', 'ai_done'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereHas('indikator', function ($q) use ($filterOpdId) {
                $q->where('category', 'utama');
                if ($filterOpdId) {
                    $opd = \App\Models\Opd::find($filterOpdId);
                    if ($opd) {
                        match($opd->type) {
                            'sekda' => $q->where('sekda_id', $opd->id),
                            'asisten' => $q->where('asisten_id', $opd->id),
                            'opd' => $q->where('opd_id', $opd->id),
                            'kabag' => $q->where('kabag_id', $opd->id),
                            default => $q->where('opd_id', $opd->id),
                        };
                    }
                }
            })
            ->whereHas('realisasi')
            ->get();
    }

    public function getPendingUntukBupati(int $bulan, int $tahun, ?int $filterOpdId = null): Collection
    {
        return IkuSkoring::with(['indikator.opd', 'indikator.bidang', 'realisasi'])
            ->whereIn('status', ['pending', 'ai_done', 'ta_done'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereHas('indikator', function ($q) use ($filterOpdId) {
                $q->where('category', 'utama');
                if ($filterOpdId) {
                    $opd = \App\Models\Opd::find($filterOpdId);
                    if ($opd) {
                        match($opd->type) {
                            'sekda' => $q->where('sekda_id', $opd->id),
                            'asisten' => $q->where('asisten_id', $opd->id),
                            'opd' => $q->where('opd_id', $opd->id),
                            'kabag' => $q->where('kabag_id', $opd->id),
                            default => $q->where('opd_id', $opd->id),
                        };
                    }
                }
            })
            ->whereHas('realisasi')
            ->get();
    }

    public function getSudahFinal(int $bulan, int $tahun, ?int $filterOpdId = null): Collection
    {
        return IkuSkoring::where('status', 'final')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereHas('indikator', function ($q) use ($filterOpdId) {
                $q->where('category', 'utama');
                if ($filterOpdId) {
                    $opd = \App\Models\Opd::find($filterOpdId);
                    if ($opd) {
                        match($opd->type) {
                            'sekda' => $q->where('sekda_id', $opd->id),
                            'asisten' => $q->where('asisten_id', $opd->id),
                            'opd' => $q->where('opd_id', $opd->id),
                            'kabag' => $q->where('kabag_id', $opd->id),
                            default => $q->where('opd_id', $opd->id),
                        };
                    }
                }
            })
            ->get();
    }

    public function getIndikatorBelumSkoring(int $bulan, int $tahun, ?int $filterOpdId = null): Collection
    {
        return Indikator::with([
            'opd',
            'bidang',
            'realisasi' => fn ($q) => $q->where('bulan', $bulan),
        ])
            ->where('category', 'utama')
            ->disetujui()
            ->when($filterOpdId, function ($q) use ($filterOpdId) {
                $opd = \App\Models\Opd::find($filterOpdId);
                if ($opd) {
                    match($opd->type) {
                        'sekda' => $q->where('sekda_id', $opd->id),
                        'asisten' => $q->where('asisten_id', $opd->id),
                        'opd' => $q->where('opd_id', $opd->id),
                        'kabag' => $q->where('kabag_id', $opd->id),
                        default => $q->where('opd_id', $opd->id),
                    };
                }
            })
            ->whereHas('realisasi', fn ($q) => $q->where('bulan', $bulan))
            ->whereHas('tahunAnggaran', fn ($q) => $q->where('tahun', $tahun))
            ->whereDoesntHave('skorings', fn ($q) => $q->where('bulan', $bulan)->where('tahun', $tahun))
            ->orderBy('nama')
            ->get();
    }

    public function getAllSkorings(int $bulan, int $tahun, ?int $filterOpdId = null): Collection
    {
        return IkuSkoring::with(['indikator.opd', 'indikator.bidang', 'realisasi', 'taScoredBy', 'finalizedBy'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereHas('indikator', function ($q) use ($filterOpdId) {
                $q->where('category', 'utama');
                if ($filterOpdId) {
                    $opd = \App\Models\Opd::find($filterOpdId);
                    if ($opd) {
                        match($opd->type) {
                            'sekda' => $q->where('sekda_id', $opd->id),
                            'asisten' => $q->where('asisten_id', $opd->id),
                            'opd' => $q->where('opd_id', $opd->id),
                            'kabag' => $q->where('kabag_id', $opd->id),
                            default => $q->where('opd_id', $opd->id),
                        };
                    }
                }
            })
            ->latest()
            ->get();
    }
}
