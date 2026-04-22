<?php

namespace App\Jobs;

use App\Models\IkuSkoring;
use App\Models\Indikator;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\AiSkorSelesai;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateAiScore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [60, 120, 300];

    public function __construct(
        public readonly int $indikatorId,
        public readonly int $bulan,
        public readonly int $tahun,
    ) {}

    public function handle(): void
    {
        if (! Setting::get('ai_enabled', false)) {
            return;
        }

        $indikator = Indikator::with(['opd', 'bidang'])->find($this->indikatorId);
        if (! $indikator) {
            return;
        }

        $realisasi = $indikator->realisasi()->where('bulan', $this->bulan)->first();
        if (! $realisasi) {
            return;
        }

        $apiKey = Setting::get('ai_api_key', '');
        if (! $apiKey) {
            Log::warning('GenerateAiScore: ai_api_key tidak dikonfigurasi.');
            return;
        }

        $target = $indikator->targetBulanan()->where('bulan', $this->bulan)->first();

        $prompt = "OPD: {$indikator->opd?->name}\n"
            ."Bidang: {$indikator->bidang?->name}\n"
            ."IKU: {$indikator->nama}\n"
            ."Deskripsi: {$indikator->definisi}\n"
            ."Tipe: {$indikator->measurement_type}\n"
            ."Target bulan ini: ".($target?->target ?? $target?->target_description ?? '-')."\n"
            ."Realisasi: {$realisasi->nilai}\n"
            .($realisasi->keterangan ? "Keterangan: {$realisasi->keterangan}\n" : '')
            ."\nBeri skor 1-10 dan reasoning singkat Bahasa Indonesia. Return HANYA JSON: {\"skor\": integer, \"reasoning\": \"string\"}";

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => Setting::get('ai_model', 'claude-sonnet-4-6'),
                    'max_tokens' => 500,
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                ]);

            if (! $response->successful()) {
                $this->fail(new \RuntimeException('API error: '.$response->status()));
                return;
            }

            $text = $response->json('content.0.text', '');
            preg_match('/\{.*\}/s', $text, $matches);
            $parsed = json_decode($matches[0] ?? '{}', true);

            $skor = (int) ($parsed['skor'] ?? 0);
            $reasoning = $parsed['reasoning'] ?? '';

            if ($skor < 1 || $skor > 10) {
                Log::warning('GenerateAiScore: skor tidak valid', ['skor' => $skor]);
                return;
            }

            IkuSkoring::updateOrCreate(
                ['indikator_id' => $this->indikatorId, 'bulan' => $this->bulan, 'tahun' => $this->tahun],
                [
                    'realisasi_id' => $realisasi->id,
                    'skor_ai' => $skor,
                    'ai_reasoning' => $reasoning,
                    'ai_generated_at' => now(),
                    'status' => 'ai_done',
                ]
            );

            User::role('admin_super')->get()->each(
                fn (User $u) => $u->notify(new AiSkorSelesai($indikator, $skor, $this->bulan, $this->tahun))
            );
        } catch (\Throwable $e) {
            Log::error('GenerateAiScore exception: '.$e->getMessage());
            throw $e;
        }
    }
}
