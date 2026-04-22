<?php

namespace App\Services;

use App\Models\Indikator;
use App\Models\Persetujuan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PersetujuanService
{
    public function getPending(string $level): Collection
    {
        return Persetujuan::with(['indikator.opd', 'user'])
            ->where('level', $level)
            ->where('status', 'pending')
            ->latest()
            ->get();
    }

    public function getByIndikator(int $indikatorId): Collection
    {
        return Persetujuan::with('user')
            ->where('indikator_id', $indikatorId)
            ->orderBy('created_at')
            ->get();
    }

    public function ajukan(Indikator $indikator, string $level): Persetujuan
    {
        return Persetujuan::create([
            'indikator_id' => $indikator->id,
            'user_id' => Auth::id(),
            'level' => $level,
            'status' => 'pending',
        ]);
    }

    public function setujui(Persetujuan $persetujuan, ?string $catatan = null): void
    {
        $persetujuan->update([
            'status' => 'disetujui',
            'catatan' => $catatan,
        ]);

        $this->updateStatusIndikator($persetujuan->indikator_id);
    }

    public function tolak(Persetujuan $persetujuan, string $catatan): void
    {
        $persetujuan->update([
            'status' => 'ditolak',
            'catatan' => $catatan,
        ]);

        Indikator::find($persetujuan->indikator_id)?->update(['status' => 'ditolak']);
    }

    private function updateStatusIndikator(int $indikatorId): void
    {
        $semuaDisetujui = Persetujuan::where('indikator_id', $indikatorId)
            ->where('status', '!=', 'disetujui')
            ->doesntExist();

        if ($semuaDisetujui) {
            Indikator::find($indikatorId)?->update(['status' => 'disetujui']);
        }
    }
}
