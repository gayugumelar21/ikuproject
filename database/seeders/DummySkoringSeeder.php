<?php

namespace Database\Seeders;

use App\Models\IkuSkoring;
use App\Models\Realisasi;
use App\Models\TahunAnggaran;
use App\Models\User;
use App\Services\MonthlySummaryService;
use Illuminate\Database\Seeder;

/**
 * DummySkoringSeeder
 *
 * Membuat record IkuSkoring (skor_ai, skor_ta, skor_bupati) secara otomatis
 * dari data realisasi yang sudah ada, sehingga MonthlySummary bisa dihitung.
 *
 * Formula skor (skala 1–10):
 *   pct   = realisasi / target * 100
 *   skor  = clamp(round(pct / 10), 1, 10)
 *   Lalu diberi sedikit variasi realistis (±0–1 poin).
 */
class DummySkoringSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = TahunAnggaran::where('tahun', 2026)->first();
        if (! $tahun) {
            $this->command->warn('Tahun anggaran 2026 tidak ditemukan.');

            return;
        }

        $admin = User::where('username', 'admin')->first();
        $finalizedBy = $admin?->id ?? 1;

        // Ambil semua realisasi yang sudah diverifikasi/diajukan untuk tahun 2026
        $realisasiList = Realisasi::with(['indikator.targetBulanan'])
            ->whereIn('status', ['diverifikasi', 'diajukan', 'draft'])
            ->whereHas('indikator', fn ($q) => $q
                ->where('tahun_anggaran_id', $tahun->id)
                ->where('status', 'disetujui')
                ->where('category', 'utama')
            )
            ->get();

        $count = 0;

        foreach ($realisasiList as $realisasi) {
            $indikator = $realisasi->indikator;

            // Cari target bulanan
            $targetRow = $indikator->targetBulanan
                ->firstWhere('bulan', $realisasi->bulan);

            if (! $targetRow || $targetRow->target <= 0) {
                continue;
            }

            $pct = ($realisasi->nilai / $targetRow->target) * 100;

            // Konversi ke skala 1–10
            $skorRaw = round($pct / 10);
            $skor = max(1, min(10, (int) $skorRaw));

            // Variasi realistis kecil berdasarkan bulan
            $variasi = match ($realisasi->bulan) {
                1 => 0,
                2 => 0,
                3 => ($pct >= 100 ? 0 : ($pct >= 90 ? 0 : -1)),
                4 => 0,
                default => 0,
            };
            $skor = max(1, min(10, $skor + $variasi));

            // skor_ai sedikit lebih rendah dari bupati (AI lebih konservatif)
            $skorAi = max(1, min(10, $skor - (rand(0, 1))));
            $skorTa = max(1, min(10, $skor));

            // Status: bulan 1-2 sudah final, bulan 3 ta_done, bulan 4+ ai_done
            $status = match (true) {
                $realisasi->bulan <= 2 => 'final',
                $realisasi->bulan === 3 => 'ta_done',
                default => 'ai_done',
            };

            $isFinal = $status === 'final';
            $skorBupati = $isFinal ? $skor : null;
            $skorTaVal = in_array($status, ['ta_done', 'final']) ? $skorTa : null;

            IkuSkoring::updateOrCreate(
                [
                    'indikator_id' => $indikator->id,
                    'bulan' => $realisasi->bulan,
                    'tahun' => 2026,
                ],
                [
                    'realisasi_id' => $realisasi->id,
                    'skor_ai' => $skorAi,
                    'ai_reasoning' => $this->generateReasoning($pct, $indikator->nama, $realisasi->bulan),
                    'ai_generated_at' => now()->subDays(30 - $realisasi->bulan * 5),
                    'skor_ta' => $skorTaVal,
                    'ta_notes' => $skorTaVal ? 'Skor TA dikonfirmasi berdasarkan data realisasi dan tren capaian.' : null,
                    'ta_scored_by' => $skorTaVal ? $finalizedBy : null,
                    'ta_scored_at' => $skorTaVal ? now()->subDays(20 - $realisasi->bulan * 3) : null,
                    'skor_bupati' => $skorBupati,
                    'bupati_notes' => $isFinal ? 'Skor difinalisasi berdasarkan evaluasi capaian kinerja.' : null,
                    'bupati_scored_at' => $isFinal ? now()->subDays(15 - $realisasi->bulan * 2) : null,
                    'is_final' => $isFinal,
                    'finalized_by' => $isFinal ? $finalizedBy : null,
                    'finalized_at' => $isFinal ? now()->subDays(14 - $realisasi->bulan * 2) : null,
                    'status' => $status,
                ]
            );

            $count++;
        }

        $this->command->info('');
        $this->command->info("✅  DummySkoringSeeder selesai: {$count} record IkuSkoring dibuat/diperbarui.");
        $this->command->info('   Status distribusi:');
        $this->command->info('   - Bulan 1-2 → FINAL (skor_bupati terisi)');
        $this->command->info('   - Bulan 3   → ta_done (skor_ta terisi, menunggu Bupati)');
        $this->command->info('   - Bulan 4   → ai_done (menunggu TA)');
        $this->command->info('');

        // Hitung MonthlySummary untuk semua OPD TERLEBIH DAHULU (termasuk Disdik)
        $this->command->info('📊 Menghitung MonthlySummary untuk semua OPD...');
        $summaryService = app(MonthlySummaryService::class);
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $summaryService->hitungSemua($bulan, 2026);
        }
        $this->command->info('✅ MonthlySummary berhasil dihitung.');

        // KEMUDIAN sinkronisasi skor kontribusi OPD ke indikator Asisten
        $this->command->info('🔄 Menyinkronisasi skor kontribusi OPD ke indikator Asisten...');
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $summaryService->sinkronSkorKontribusi($bulan, 2026);
        }
        $this->command->info('✅ Sinkronisasi selesai.');

        // Terakhir, hitung ulang MonthlySummary untuk Asisten (dengan kontribusi yang sudah tersinkronisasi)
        $this->command->info('📊 Menghitung ulang MonthlySummary Asisten dengan kontribusi...');
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $summaryService->hitungSemua($bulan, 2026);
        }
        $this->command->info('✅ Semua perhitungan selesai!');
    }

    private function generateReasoning(float $pct, string $namaIndikator, int $bulan): string
    {
        $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April'][min($bulan, 4)];
        $pctFmt = number_format($pct, 1);

        return match (true) {
            $pct >= 100 => "Capaian {$namaIndikator} pada {$bulanNama} 2026 sebesar {$pctFmt}% melebihi target bulanan. Kinerja sangat baik.",
            $pct >= 90 => "Capaian {$namaIndikator} pada {$bulanNama} 2026 sebesar {$pctFmt}% mendekati target bulanan dengan baik.",
            $pct >= 75 => "Capaian {$namaIndikator} pada {$bulanNama} 2026 sebesar {$pctFmt}% cukup baik namun masih ada ruang peningkatan.",
            $pct >= 60 => "Capaian {$namaIndikator} pada {$bulanNama} 2026 sebesar {$pctFmt}% di bawah target. Perlu perhatian dan tindak lanjut.",
            default => "Capaian {$namaIndikator} pada {$bulanNama} 2026 sebesar {$pctFmt}% jauh dari target. Diperlukan evaluasi dan intervensi segera.",
        };
    }
}
