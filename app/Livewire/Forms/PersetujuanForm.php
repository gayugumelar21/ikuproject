<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class PersetujuanForm extends Form
{
    public ?int $persetujuanId = null;

    public string $catatan = '';

    public function rules(): array
    {
        return [
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function rulesUntukTolak(): array
    {
        return [
            'catatan' => ['required', 'string', 'max:1000'],
        ];
    }
}
