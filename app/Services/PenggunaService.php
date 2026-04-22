<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class PenggunaService
{
    public function getAll(): Collection
    {
        return User::with(['opd', 'roles'])->orderBy('name')->get();
    }

    public function store(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'opd_id' => $data['opd_id'] ?? null,
        ]);

        if (! empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user;
    }

    public function update(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'opd_id' => $data['opd_id'] ?? null,
        ]);

        if (! empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        if (! empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user->fresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
