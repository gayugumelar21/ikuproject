<?php

namespace App\Livewire\Forms;

use App\Models\Indikator;
use Livewire\Attributes\Validate;
use Livewire\Form;

class IndikatorForm extends Form
{
    public ?int $indikatorId = null;

    #[Validate('nullable|exists:tahun_anggaran,id')]
    public ?int $tahun_anggaran_id = null;

    #[Validate('nullable|exists:opds,id')]
    public ?int $sekda_id = null;

    #[Validate('nullable|exists:opds,id')]
    public ?int $kabag_id = null;

    #[Validate('nullable|exists:opds,id')]
    public ?int $asisten_id = null;

    #[Validate('nullable|exists:opds,id')]
    public ?int $opd_id = null;

    #[Validate('nullable|exists:opds,id')]
    public ?int $bidang_id = null;

    #[Validate('nullable|exists:indikators,id')]
    public ?int $parent_indikator_id = null;

    #[Validate('nullable|exists:indikators,id')]
    public ?int $source_indikator_id = null;

    #[Validate('nullable|exists:users,id')]
    public ?int $owner_user_id = null;

    #[Validate('required|string|max:255')]
    public string $nama = '';

    #[Validate('nullable|string')]
    public string $definisi = '';

    #[Validate('nullable|string|max:50')]
    public string $satuan = '';

    #[Validate('required|in:utama,kerjasama')]
    public string $category = 'utama';

    #[Validate('required|in:kuantitatif,kualitatif')]
    public string $measurement_type = 'kuantitatif';

    #[Validate('nullable|numeric|min:0')]
    public float $target = 0;

    #[Validate('required|numeric|min:0|max:100')]
    public float $bobot = 0;

    public function setIndikator(Indikator $indikator): void
    {
        $this->indikatorId = $indikator->id;
        $this->tahun_anggaran_id = $indikator->tahun_anggaran_id;
        $this->sekda_id = $indikator->sekda_id;
        $this->kabag_id = $indikator->kabag_id;
        $this->asisten_id = $indikator->asisten_id;
        $this->opd_id = $indikator->opd_id;
        $this->bidang_id = $indikator->bidang_id;
        $this->parent_indikator_id = $indikator->parent_indikator_id;
        $this->source_indikator_id = $indikator->source_indikator_id;
        $this->owner_user_id = $indikator->owner_user_id;
        $this->nama = $indikator->nama;
        $this->definisi = $indikator->definisi ?? '';
        $this->satuan = $indikator->satuan ?? '';
        $this->category = $indikator->category ?? 'utama';
        $this->measurement_type = $indikator->measurement_type ?? 'kuantitatif';
        $this->target = (float) $indikator->target;
        $this->bobot = (float) $indikator->bobot;
    }

    public function rules(): array
    {
        return [
            'tahun_anggaran_id'   => ['required', 'exists:tahun_anggaran,id'],
            'sekda_id'            => ['required', 'exists:opds,id'],
            'kabag_id'            => ['nullable', 'exists:opds,id'],
            'asisten_id'          => ['nullable', 'exists:opds,id'],
            'opd_id'              => ['nullable', 'exists:opds,id'],
            'bidang_id'           => ['nullable', 'exists:opds,id'],
            'parent_indikator_id' => ['nullable', 'exists:indikators,id'],
            'source_indikator_id' => ['nullable', 'exists:indikators,id'],
            'owner_user_id'       => ['nullable', 'exists:users,id'],
            'nama'                => ['required', 'string', 'max:255'],
            'definisi'            => ['nullable', 'string'],
            'satuan'              => ['nullable', 'string', 'max:50'],
            'category'            => ['required', 'in:utama,kerjasama'],
            'measurement_type'    => ['required', 'in:kuantitatif,kualitatif'],
            'target'              => ['required_if:measurement_type,kuantitatif', 'nullable', 'numeric', 'min:0'],
            'bobot'               => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}