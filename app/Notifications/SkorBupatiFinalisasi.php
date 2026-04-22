<?php

namespace App\Notifications;

use App\Models\IkuSkoring;
use App\Models\Indikator;
use Illuminate\Notifications\Notification;

class SkorBupatiFinalisasi extends Notification
{
    public function __construct(
        public readonly Indikator $indikator,
        public readonly IkuSkoring $skoring,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'bupati_finalize',
            'indikator_id' => $this->indikator->id,
            'indikator_nama' => $this->indikator->nama,
            'skor_final' => $this->skoring->skor_bupati,
            'bulan' => $this->skoring->bulan,
            'tahun' => $this->skoring->tahun,
            'message' => "Bupati memfinalisasi skor: {$this->indikator->nama} — Skor {$this->skoring->skor_bupati}/10",
        ];
    }
}
