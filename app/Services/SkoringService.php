<?php

namespace App\Services;

use App\Models\IkuSkoring;
use App\Models\Indikator;
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

        // Mirror score to all kerjasama IKUs that reference this as source
        $kerjasamas = Indikator::where('source_indikator_id', $skoring->indikator_id)
            ->where('category', 'kerjasama')
            ->get();

        foreach ($kerjasamas as $kerjasamaIndikator) {
            IkuSkoring::updateOrCreate(
                [
                    'indikator_id' => $kerjasamaIndikator->id,
                    'bulan' => $skoring->bulan,
                    'tahun' => $skoring->tahun,
                ],
                [
                    'skor_bupati' => $skor,
                    'bupati_notes' => "Skor otomatis dari IKU sumber: {$indikator?->nama}",
                    'bupati_scored_at' => now(),
                    'status' => 'final',
                    'is_final' => true,
                    'finalized_by' => $bupati->id,
                    'finalized_at' => now(),
                ]
            );
        }

        // Notify owner of the OPD that their IKU score is finalized
        if ($indikator?->owner) {
            $indikator->owner->notify(new SkorBupatiFinalisasi($indikator, $skoring->fresh()));
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
            ->whereHas('tahunAnggaran', fn ($q) => $q->where('tahun', $tahun))
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

        $jumlahIndikator = $indikators->count();

        return [
            'skor_total' => round($skorTotal, 2),
            'jumlah_indikator' => $jumlahIndikator,
            'sudah_final' => $sudahFinal,
            'lengkap' => $jumlahIndikator > 0 && $sudahFinal === $jumlahIndikator,
        ];
    }

    public function getPendingUntukTa(int $bulan, int $tahun): Collection
    {
        return IkuSkoring::with(['indikator.opd', 'indikator.bidang', 'realisasi'])
            ->where('status', 'ai_done')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();
    }

    public function getPendingUntukBupati(int $bulan, int $tahun): Collection
    {
        return IkuSkoring::with(['indikator.opd', 'indikator.bidang', 'realisasi'])
            ->where('status', 'ta_done')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();
    }

    public function getSudahFinal(int $bulan, int $tahun): Collection
    {
        return IkuSkoring::where('status', 'final')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();
    }
}
