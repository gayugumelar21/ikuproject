<?php

namespace App\Services;

use App\Models\IkuSkoring;
use App\Models\Realisasi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class RealisasiService
{
    public function getByIndikator(int $indikatorId): Collection
    {
        return Realisasi::where('indikator_id', $indikatorId)
            ->orderBy('bulan')
            ->get();
    }

    public function getByOpdDanTahun(int $opdId, int $tahunAnggaranId): Collection
    {
        return Realisasi::with(['indikator'])
            ->whereHas('indikator', fn ($q) => $q
                ->where('opd_id', $opdId)
                ->where('tahun_anggaran_id', $tahunAnggaranId)
                ->where('category', 'utama')
            )
            ->orderBy('bulan')
            ->get();
    }

    public function store(array $data): Realisasi
    {
        $data['user_id'] = Auth::id();

        return Realisasi::create($data);
    }

    public function update(Realisasi $realisasi, array $data): Realisasi
    {
        $realisasi->update($data);

        return $realisasi->fresh();
    }

    public function ajukan(Realisasi $realisasi): void
    {
        $realisasi->update(['status' => 'diajukan']);

        // Kirim Notifikasi ke Kepala Dinas
        try {
            $wa = app(WhatsAppService::class);
            $indikator = $realisasi->indikator;
            $opd = $indikator?->opd;

            if ($opd) {
                $approver = \App\Models\User::where('opd_id', $opd->id)
                    ->whereHas('roles', fn($q) => $q->where('name', 'kepala_dinas'))
                    ->first();

                if ($approver && $approver->phone) {
                    $msg = "🔔 *Pemberitahuan IKU*\n\nHalo {$approver->name}, ada data Realisasi baru yang perlu diverifikasi.\n\nIndikator: *{$indikator->nama}*\nBulan: {$realisasi->bulan}\nOleh: " . auth()->user()->name . "\n\nSilakan cek di aplikasi IKU.";
                    $wa->notifyUser($approver, $msg);
                }
            }
        } catch (\Exception $e) {
            \Log::error("Gagal kirim WA ajukan: " . $e->getMessage());
        }
    }

    public function verifikasi(Realisasi $realisasi): void
    {
        $realisasi->update(['status' => 'diverifikasi']);

        // Kirim Notifikasi ke Tenaga Ahli (Admin Super)
        try {
            $wa = app(WhatsAppService::class);
            $indikator = $realisasi->indikator;

            $admins = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'admin_super'))
                ->whereNotNull('phone')
                ->get();

            foreach ($admins as $admin) {
                $msg = "✅ *Realisasi Diverifikasi*\n\nHalo {$admin->name}, satu realisasi telah diverifikasi dan siap diberi Skor AI/TA.\n\nOPD: {$indikator->opd?->name}\nIndikator: *{$indikator->nama}*\n\nSegera lakukan skoring di menu Skoring TA.";
                $wa->notifyUser($admin, $msg);
            }
        } catch (\Exception $e) {
            \Log::error("Gagal kirim WA verifikasi: " . $e->getMessage());
        }

        // Otomatis buat/update record iku_skorings agar masuk antrian scoring
        $tahun = (int) now()->format('Y');
        // Cari tahun dari tahun_anggaran indikator jika ada
        $indikator = $realisasi->indikator;
        if ($indikator) {
            $indikator->loadMissing('tahunAnggaran');
            if ($indikator->tahunAnggaran) {
                $tahun = (int) $indikator->tahunAnggaran->tahun;
            }
        }

        IkuSkoring::firstOrCreate(
            [
                'indikator_id' => $realisasi->indikator_id,
                'bulan' => $realisasi->bulan,
                'tahun' => $tahun,
            ],
            [
                'realisasi_id' => $realisasi->id,
                'status' => 'pending',
            ]
        );
    }

    public function hitungPersentase(Realisasi $realisasi): float
    {
        $target = $realisasi->indikator->targetBulanan()
            ->where('bulan', $realisasi->bulan)
            ->value('target');

        if (! $target || $target == 0) {
            return 0;
        }

        return round(($realisasi->nilai / $target) * 100, 2);
    }
}
