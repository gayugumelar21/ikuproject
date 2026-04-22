<?php

namespace App\Services;

use App\Models\IkuSkoring;
use App\Models\Indikator;
use App\Models\Realisasi;
use App\Models\TargetIndikator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiSkoringService
{
    private array $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function generate(Indikator $indikator, int $bulan, int $tahun): ?IkuSkoring
    {
        $target = TargetIndikator::where('indikator_id', $indikator->id)
            ->where('bulan', $bulan)
            ->first();

        $realisasi = Realisasi::where('indikator_id', $indikator->id)
            ->where('bulan', $bulan)
            ->first();

        if (! $realisasi) {
            return null;
        }

        $indikator->loadMissing(['opd', 'bidang']);

        $opdName = $indikator->opd?->name ?? '-';
        $bidangName = $indikator->bidang?->name ?? '-';
        $tipePengukuran = $indikator->isKualitatif() ? 'Kualitatif' : 'Kuantitatif';
        $namaBulan = $this->namaBulan[$bulan] ?? $bulan;
        $satuan = $indikator->satuan ?? '';
        $targetNilai = $target?->target ?? '-';
        $targetDesc = $target?->target_description ?? '-';

        $prompt = <<<PROMPT
OPD: {$opdName}
Bidang: {$bidangName}
Indikator: {$indikator->nama}
Tipe Pengukuran: {$tipePengukuran}
Target bulan {$namaBulan}: {$targetNilai} {$satuan} / {$targetDesc}
Realisasi: {$realisasi->nilai} {$satuan} / {$realisasi->keterangan}

Berikan skor 1-10 berdasarkan tingkat ketercapaian target. Pertimbangkan konteks dan hambatan jika ada.
Balas HANYA dengan JSON: {"skor": integer, "reasoning": "string singkat bahasa Indonesia"}
PROMPT;

        $existing = IkuSkoring::where('indikator_id', $indikator->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-api-key' => config('services.anthropic.key'),
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => config('services.anthropic.model', 'claude-sonnet-4-6'),
                    'max_tokens' => 300,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Anthropic API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'indikator_id' => $indikator->id,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]);

                return $existing;
            }

            $parsed = json_decode($response->json('content.0.text'), true);

            return IkuSkoring::updateOrCreate(
                ['indikator_id' => $indikator->id, 'bulan' => $bulan, 'tahun' => $tahun],
                [
                    'realisasi_id' => $realisasi->id,
                    'skor_ai' => $parsed['skor'],
                    'ai_reasoning' => $parsed['reasoning'],
                    'ai_generated_at' => now(),
                    'status' => 'ai_done',
                ]
            );
        } catch (\Throwable $e) {
            Log::error('AiSkoringService exception', [
                'message' => $e->getMessage(),
                'indikator_id' => $indikator->id,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ]);

            return $existing;
        }
    }
}
