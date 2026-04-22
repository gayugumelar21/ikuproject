<?php

namespace App\Services;

use App\Models\Indikator;
use App\Models\Persetujuan;
use App\Models\TargetIndikator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class IndikatorService
{
    public function getAll(int $tahunAnggaranId): Collection
    {
        return Indikator::with(['sekda', 'asisten', 'kabag', 'opd', 'bidang', 'dibuatOleh', 'owner', 'kerjasamas.opd'])
            ->where('tahun_anggaran_id', $tahunAnggaranId)
            ->where('category', 'utama')
            ->orderBy('nama')
            ->get();
    }

    public function getByOpd(int $tahunAnggaranId, int $opdId): Collection
    {
        return Indikator::with(['opd', 'bidang'])
            ->where('tahun_anggaran_id', $tahunAnggaranId)
            ->where('opd_id', $opdId)
            ->where('category', 'utama')
            ->orderBy('nama')
            ->get();
    }

    public function store(array $data): Indikator
    {
        $data['dibuat_oleh'] = Auth::id();
        $data['category'] = 'utama';
        $data['source_indikator_id'] = null;

        return Indikator::create($data);
    }

    public function update(Indikator $indikator, array $data): Indikator
    {
        $data['category'] = 'utama';
        $data['source_indikator_id'] = null;
        $indikator->update($data);

        return $indikator->fresh();
    }

    public function delete(Indikator $indikator): void
    {
        $indikator->delete();
    }

    public function ajukan(Indikator $indikator): void
    {
        $indikator->update(['status' => 'diajukan']);

        $firstLevel = $this->firstApprovalLevel($indikator);

        $alreadyPending = Persetujuan::where('indikator_id', $indikator->id)
            ->where('level', $firstLevel)
            ->where('status', 'pending')
            ->exists();

        if (! $alreadyPending) {
            Persetujuan::create([
                'indikator_id' => $indikator->id,
                'user_id' => Auth::id(),
                'level' => $firstLevel,
                'status' => 'pending',
            ]);
        }
    }

    private function firstApprovalLevel(Indikator $indikator): string
    {
        if ($indikator->kabag_id) {
            return 'kabag';
        }

        if ($indikator->asisten_id) {
            return 'asisten';
        }

        return 'sekda';
    }

    public function simpanTargetBulanan(Indikator $indikator, array $targets): void
    {
        foreach ($targets as $bulan => $target) {
            TargetIndikator::updateOrCreate(
                ['indikator_id' => $indikator->id, 'bulan' => $bulan],
                ['target' => $target]
            );
        }
    }
}
