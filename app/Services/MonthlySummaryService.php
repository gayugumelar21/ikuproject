<?php

namespace App\Services;

use App\Models\IkuSkoring;
use App\Models\Indikator;
use App\Models\IndikatorKerjasama;
use App\Models\MonthlySummary;
use App\Models\Opd;
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

        // 1. Hitung seluruh skor OPD pertama kali (termasuk anak OPD)
        foreach ($opdIds as $opdId) {
            $this->hitungOpd($opdId, $bulan, $tahun);
        }

        // 2. Sinkronisasikan skor agregat OPD anak ke Indikator Kontribusi Asisten
        $this->sinkronSkorKontribusi($bulan, $tahun);

        // 3. Hitung ulang khusus untuk level Asisten agar nilai kontribusi yang baru masuk terakumulasi
        $asistenIds = Opd::where('type', 'asisten')->pluck('id');
        $asistenToRecalculate = $opdIds->intersect($asistenIds);
        foreach ($asistenToRecalculate as $opdId) {
            $this->hitungOpd($opdId, $bulan, $tahun);
        }

        // 4. Hitung agregat skor Sekda berdasarkan anak-anaknya (Asisten/Kabag)
        $this->hitungSkorSekda($bulan, $tahun);
    }

    public function getSkorOpdBulan(int $bulan, int $tahun): Collection
    {
        return MonthlySummary::with('opd')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderByDesc('skor_total')
            ->get();
    }

    /**
     * Sinkronisasi skor kontribusi OPD anak ke indikator kontribusi di Asisten.
     *
     * Ketika suatu OPD (misal Disdik) menjadi anak dari Asisten I, maka skor Disdik
     * harus otomatis dicerminkan ke indikator "[Kontribusi] ..." di Asisten I.
     *
     * Contoh:
     *   - Indikator "[Kontribusi] Capaian ... Dinas Pendidikan" (opd_id = Asisten I)
     *     harus menggunakan skor dari "Dinas Pendidikan dan Kebudayaan" (opd_id = DISDIK)
     *
     * Mapping dilakukan berdasarkan kecoccokan nama OPD dengan nama dalam definisi indikator.
     */
    public function sinkronSkorKontribusi(int $bulan, int $tahun): void
    {
        // Ambil semua indikator kontribusi (nama mengandung "[Kontribusi]")
        $indikatorKontribusi = Indikator::where('status', 'disetujui')
            ->where('category', 'utama')
            ->where('nama', 'like', '%[Kontribusi]%')
            ->whereHas('tahunAnggaran', fn ($q) => $q->where('tahun', $tahun))
            ->get();

        foreach ($indikatorKontribusi as $kontribusi) {
            $childOpd = $this->findChildOpdFromKontribusi($kontribusi);

            if (! $childOpd) {
                continue;
            }

            // Ambil skor bulanan dari MonthlySummary OPD anak
            $childSummary = MonthlySummary::where('opd_id', $childOpd->id)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();

            if (! $childSummary || $childSummary->skor_total === null) {
                continue;
            }

            $roundedSkor = (int) round($childSummary->skor_total, 0);

            // Mirror skor rata-rata (skor_total) anak ke indikator kontribusi
            IkuSkoring::updateOrCreate(
                [
                    'indikator_id' => $kontribusi->id,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ],
                [
                    'skor_ai' => $roundedSkor,
                    'skor_ta' => $roundedSkor,
                    'skor_bupati' => $roundedSkor,
                    'ta_notes' => "Otomatis tersinkronisasi dari rata-rata capaian {$childOpd->name}",
                    'bupati_notes' => "Otomatis tersinkronisasi dari rata-rata capaian {$childOpd->name}",
                    'status' => 'final',
                    'is_final' => true,
                    'finalized_at' => now(),
                ]
            );
        }
    }

    /**
     * Cari OPD anak yang bersesuaian dengan indikator kontribusi.
     *
     * Mencari OPD berdasarkan kecoccokan nama dalam definisi atau nama indikator.
     * Contoh: "[Kontribusi] ... Dinas Pendidikan dan Kebudayaan"
     *         → cari OPD dengan nama mengandung "Pendidikan"
     */
    private function findChildOpdFromKontribusi(Indikator $indikator): ?Opd
    {
        $searchText = $indikator->definisi ?? $indikator->nama;

        // Cari OPD yang parent_id-nya = asisten_id indikator
        $parentOpd = $indikator->asisten; // asisten_id adalah parent

        if (! $parentOpd) {
            return null;
        }

        // Cari OPD anak dengan kecoccokan nama
        // Prioritas pencarian: Disdik → Dinas Pendidikan, Kominfo → Komunikasi dan Informatika
        $keywords = [
            'Pendidikan' => ['pendidikan', 'paud', 'dikdas', 'dikmen'],
            'Komunikasi' => ['komunikasi', 'informatika', 'spbe'],
            'Kesehatan' => ['kesehatan'],
            'Sosial' => ['sosial', 'pppa'],
        ];

        foreach ($keywords as $opdType => $searchWords) {
            foreach ($searchWords as $word) {
                if (stripos($searchText, $word) !== false) {
                    // Cari OPD dengan nama mengandung kata kunci
                    $childOpd = Opd::where('parent_id', $parentOpd->id)
                        ->where('type', 'opd')
                        ->whereRaw('LOWER(name) LIKE LOWER(?)', ["%{$word}%"])
                        ->first();

                    if ($childOpd) {
                        return $childOpd;
                    }
                }
            }
        }

        // Fallback: cari OPD anak pertama yang punya indikator
        $childOpdIds = Opd::where('parent_id', $parentOpd->id)
            ->where('type', 'opd')
            ->pluck('id');

        return Indikator::whereIn('opd_id', $childOpdIds)
            ->where('status', 'disetujui')
            ->where('category', 'utama')
            ->orderBy('id')
            ->first()
            ?->opd;
    }

    /**
     * Menghitung skor agregat Sekda dari unit-unit di bawahnya (Asisten/Kabag).
     */
    public function hitungSkorSekda(int $bulan, int $tahun): void
    {
        $sekdas = Opd::where('type', 'sekda')->get();
        
        foreach ($sekdas as $sekda) {
            $childrenIds = Opd::where('parent_id', $sekda->id)->pluck('id');
            
            if ($childrenIds->isEmpty()) {
                continue;
            }

            $childrenSummaries = MonthlySummary::whereIn('opd_id', $childrenIds)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->get();

            if ($childrenSummaries->isEmpty()) {
                continue;
            }

            $validScores = $childrenSummaries->pluck('skor_total')->filter(fn ($skor) => $skor !== null);
            
            if ($validScores->isEmpty()) {
                continue;
            }

            $skorSekda = $validScores->avg();

            MonthlySummary::updateOrCreate(
                ['opd_id' => $sekda->id, 'bulan' => $bulan, 'tahun' => $tahun],
                [
                    'skor_utama' => round($skorSekda, 2),
                    'skor_kerjasama' => null,
                    'skor_total' => round($skorSekda, 2),
                    'is_complete' => $childrenSummaries->where('is_complete', false)->isEmpty(),
                    'calculated_at' => now(),
                ]
            );
        }
    }
}
