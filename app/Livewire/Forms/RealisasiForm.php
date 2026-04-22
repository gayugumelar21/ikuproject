<?php

namespace App\Livewire\Forms;

use App\Models\Realisasi;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

class RealisasiForm extends Form
{
    public ?int $realisasiId = null;

    public ?int $indikator_id = null;

    public int $bulan = 1;

    public float $nilai = 0;

    public string $keterangan = '';

    public string $deskripsi_progres = '';

    public string $bukti_link = '';

    /** @var TemporaryUploadedFile|null */
    public $foto_bukti = null;

    public ?string $foto_bukti_existing = null;

    public function setRealisasi(Realisasi $realisasi): void
    {
        $this->realisasiId = $realisasi->id;
        $this->indikator_id = $realisasi->indikator_id;
        $this->bulan = $realisasi->bulan;
        $this->nilai = (float) $realisasi->nilai;
        $this->keterangan = $realisasi->keterangan ?? '';
        $this->deskripsi_progres = $realisasi->deskripsi_progres ?? '';
        $this->bukti_link = $realisasi->bukti_link ?? '';
        $this->foto_bukti_existing = $realisasi->foto_bukti;
        $this->foto_bukti = null; // reset upload field
    }

    public function rules(): array
    {
        return [
            'indikator_id' => ['required', 'exists:indikators,id'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'nilai' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
            'deskripsi_progres' => ['nullable', 'string', 'max:3000'],
            'bukti_link' => ['nullable', 'url', 'max:2048'],
            'foto_bukti' => ['nullable', 'image', 'max:5120'], // max 5MB
            'foto_bukti_existing' => ['nullable', 'string'],            // readonly, tidak divalidasi konten
        ];
    }

    /**
     * Return validated data + handle foto upload path.
     */
    public function toStoreData(): array
    {
        $data = $this->validate();

        // Handle foto upload
        if ($this->foto_bukti) {
            $data['foto_bukti'] = $this->foto_bukti->store('bukti-realisasi', 'public');
        } else {
            unset($data['foto_bukti']); // jangan overwrite dengan null
        }

        // Hapus field yang bukan kolom DB
        unset($data['foto_bukti_existing']);

        return $data;
    }

    public function namaBulan(): string
    {
        return match ($this->bulan) {
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',   9 => 'September',
            10 => 'Oktober', 11 => 'November',  12 => 'Desember',
            default => '-',
        };
    }
}
