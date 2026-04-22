<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Form;

class PenggunaForm extends Form
{
    public ?int $userId = null;

    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?int $opd_id = null;

    public string $role = '';

    public function setUser(User $user): void
    {
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->opd_id = $user->opd_id;
        $this->role = $user->roles->first()?->name ?? '';
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9_]+$/'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'opd_id' => ['nullable', 'exists:opds,id'],
            'role' => ['required', 'string'],
        ];

        if ($this->userId) {
            $rules['username'][] = "unique:users,username,{$this->userId}";
            $rules['email'][] = "unique:users,email,{$this->userId}";
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['username'][] = 'unique:users,username';
            $rules['email'][] = 'unique:users,email';
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }
}
