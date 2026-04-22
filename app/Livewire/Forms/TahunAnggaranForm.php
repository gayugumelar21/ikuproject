<?php

namespace App\Livewire\Forms;

use App\Models\TahunAnggaran;
use Livewire\Form;

class TahunAnggaranForm extends Form
{
    public ?int $tahunAnggaranId = null;

    public int $tahun = 0;

    public bool $is_active = false;

    public function setTahunAnggaran(TahunAnggaran $tahunAnggaran): void
    {
        $this->tahunAnggaranId = $tahunAnggaran->id;
        $this->tahun = $tahunAnggaran->tahun;
        $this->is_active = $tahunAnggaran->is_active;
    }

    public function rules(): array
    {
        return [
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100', 'unique:tahun_anggaran,tahun'.($this->tahunAnggaranId ? ",{$this->tahunAnggaranId}" : '')],
            'is_active' => ['boolean'],
        ];
    }
}
