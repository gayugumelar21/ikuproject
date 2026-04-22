<?php

namespace App\Livewire\Forms;

use App\Models\Opd;
use Livewire\Attributes\Validate;
use Livewire\Form;

class OpdForm extends Form
{
    public ?int $opdId = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:20')]
    public string $code = '';

    #[Validate('required|in:sekda,asisten,kabag,opd,bidang')]
    public string $type = '';

    #[Validate('nullable|exists:opds,id')]
    public ?int $parent_id = null;

    public function setOpd(Opd $opd): void
    {
        $this->opdId = $opd->id;
        $this->name = $opd->name;
        $this->code = $opd->code;
        $this->type = $opd->type;
        $this->parent_id = $opd->parent_id;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:opds,code'.($this->opdId ? ",{$this->opdId}" : '')],
            'type' => ['required', 'in:sekda,asisten,kabag,opd,bidang'],
            'parent_id' => ['nullable', 'exists:opds,id'],
        ];
    }
}
