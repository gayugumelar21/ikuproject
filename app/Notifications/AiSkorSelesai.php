<?php

namespace App\Notifications;

use App\Models\Indikator;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

class AiSkorSelesai extends Notification
{
    public function __construct(
        public readonly Indikator $indikator,
        public readonly int $skor,
        public readonly int $bulan,
        public readonly int $tahun,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $namaBulan = Carbon::create($this->tahun, $this->bulan)->translatedFormat('F');

        return [
            'type' => 'ai_score',
            'indikator_id' => $this->indikator->id,
            'indikator_nama' => $this->indikator->nama,
            'skor' => $this->skor,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'message' => "AI selesai scoring: {$this->indikator->nama} — Skor {$this->skor}/10 ({$namaBulan} {$this->tahun})",
        ];
    }
}
