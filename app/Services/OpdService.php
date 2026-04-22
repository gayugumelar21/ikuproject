<?php

namespace App\Services;

use App\Models\Opd;
use Illuminate\Database\Eloquent\Collection;

class OpdService
{
    public function getAll(): Collection
    {
        return Opd::with('parent')->orderBy('type')->orderBy('name')->get();
    }

    public function getByType(string $type): Collection
    {
        return Opd::where('type', $type)->orderBy('name')->get();
    }

    public function getChildren(int $parentId): Collection
    {
        return Opd::where('parent_id', $parentId)->orderBy('name')->get();
    }

    public function getForSelect(?string $type = null): Collection
    {
        return Opd::when($type, fn ($q) => $q->where('type', $type))
            ->orderBy('type')->orderBy('name')->get();
    }

    public function store(array $data): Opd
    {
        return Opd::create($data);
    }

    public function update(Opd $opd, array $data): Opd
    {
        $opd->update($data);

        return $opd->fresh();
    }

    public function delete(Opd $opd): void
    {
        $opd->delete();
    }
}
