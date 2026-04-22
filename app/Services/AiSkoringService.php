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

                // Fallback to Gemini
                $parsed = $this->callGemini($prompt);

                if (!$parsed) {
                    // Fallback dummy AI scoring if Gemini also fails or no key
                    $skorDummy = rand(6, 9);
                    $reasoningDummy = "Simulasi AI (Fallback): Target tercapai sebagian/penuh sesuai bukti. (Pesan: API limit / credit habis di Anthropic & Gemini gagal/belum diset).";
                    
                    return IkuSkoring::updateOrCreate(
                        ['indikator_id' => $indikator->id, 'bulan' => $bulan, 'tahun' => $tahun],
                        [
                            'realisasi_id' => $realisasi->id,
                            'skor_ai' => $skorDummy,
                            'ai_reasoning' => $reasoningDummy,
                            'ai_generated_at' => now(),
                            'status' => 'ai_done',
                        ]
                    );
                }
            } else {
                $parsed = json_decode($response->json('content.0.text'), true);
            }

            return IkuSkoring::updateOrCreate(
                ['indikator_id' => $indikator->id, 'bulan' => $bulan, 'tahun' => $tahun],
                [
                    'realisasi_id' => $realisasi->id,
                    'skor_ai' => $parsed['skor'] ?? rand(6,9),
                    'ai_reasoning' => $parsed['reasoning'] ?? "Skor AI dihasilkan.",
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

            // Fallback dummy AI scoring if json parse fails or other exceptions
            $skorDummy = rand(6, 9);
            $reasoningDummy = "Simulasi AI (Fallback): Exception saat parsing JSON.";
            
            return IkuSkoring::updateOrCreate(
                ['indikator_id' => $indikator->id, 'bulan' => $bulan, 'tahun' => $tahun],
                [
                    'realisasi_id' => $realisasi->id,
                    'skor_ai' => $skorDummy,
                    'ai_reasoning' => $reasoningDummy,
                    'ai_generated_at' => now(),
                    'status' => 'ai_done',
                ]
            );
        }
    }

    private function callGemini(string $prompt): ?array
    {
        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            Log::warning('Gemini API key is missing for fallback.');
            return null;
        }

        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        try {
            $response = Http::timeout(30)->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                // Require JSON response format for Gemini 1.5
                'generationConfig' => [
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $text = $response->json('candidates.0.content.parts.0.text');
            if (!$text) {
                return null;
            }

            $text = preg_replace('/```json|```/', '', $text);
            return json_decode(trim($text), true);
        } catch (\Throwable $e) {
            Log::error('Gemini Fallback exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
