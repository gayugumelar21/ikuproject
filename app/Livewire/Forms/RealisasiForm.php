<?php

namespace App\Livewire\Forms;

use App\Models\Realisasi;
use Livewire\Form;

class RealisasiForm extends Form
{
    public ?int $realisasiId = null;

    public ?int $indikator_id = null;

    public int $bulan = 1;

    public float $nilai = 0;

    public string $keterangan = '';

    public function setRealisasi(Realisasi $realisasi): void
    {
        $this->realisasiId = $realisasi->id;
        $this->indikator_id = $realisasi->indikator_id;
        $this->bulan = $realisasi->bulan;
        $this->nilai = (float) $realisasi->nilai;
        $this->keterangan = $realisasi->keterangan ?? '';
    }

    public function rules(): array
    {
        return [
            'indikator_id' => ['required', 'exists:indikators,id'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'nilai' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ];
    }

    public function namaBulan(): string
    {
        return match ($this->bulan) {
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            default => '-',
        };
    }
}
