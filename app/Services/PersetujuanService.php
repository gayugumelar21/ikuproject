<?php

namespace App\Services;

use App\Models\Indikator;
use App\Models\Persetujuan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PersetujuanService
{
    public function getPending(string $level, ?int $userOpdId = null, ?int $filterOpdId = null): Collection
    {
        $query = Persetujuan::with(['indikator.opd', 'user'])
            ->where('level', $level)
            ->where('status', 'pending');

        if ($userOpdId) {
            $column = match ($level) {
                'kabag' => 'kabag_id',
                'asisten' => 'asisten_id',
                'sekda' => 'sekda_id',
                default => null,
            };

            if ($column) {
                $query->whereHas('indikator', fn ($q) => $q->where($column, $userOpdId));
            }
        }

        if ($filterOpdId) {
            $query->whereHas('indikator', function ($q) use ($filterOpdId) {
                $opd = \App\Models\Opd::find($filterOpdId);
                if (!$opd) return;
                match($opd->type) {
                    'sekda' => $q->where('sekda_id', $opd->id),
                    'asisten' => $q->where('asisten_id', $opd->id),
                    'opd' => $q->where('opd_id', $opd->id),
                    'kabag' => $q->where('kabag_id', $opd->id),
                    default => $q->where('opd_id', $opd->id),
                };
            });
        }

        return $query->latest()->get();
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
            'user_id' => Auth::id(),
        ]);

        $indikator = $persetujuan->indikator;
        if (! $indikator) {
            return;
        }

        $nextLevel = $this->nextApprovalLevel($persetujuan->level, $indikator);

        if ($nextLevel) {
            Persetujuan::create([
                'indikator_id' => $indikator->id,
                'user_id' => Auth::id(),
                'level' => $nextLevel,
                'status' => 'pending',
            ]);
        } else {
            $indikator->update(['status' => 'disetujui']);
        }
    }

    public function tolak(Persetujuan $persetujuan, string $catatan): void
    {
        $persetujuan->update([
            'status' => 'ditolak',
            'catatan' => $catatan,
            'user_id' => Auth::id(),
        ]);

        Indikator::find($persetujuan->indikator_id)?->update(['status' => 'ditolak']);
    }

    private function nextApprovalLevel(string $currentLevel, Indikator $indikator): ?string
    {
        return match ($currentLevel) {
            'kabag' => $indikator->asisten_id ? 'asisten' : 'sekda',
            'asisten' => 'sekda',
            default => null,
        };
    }
}
