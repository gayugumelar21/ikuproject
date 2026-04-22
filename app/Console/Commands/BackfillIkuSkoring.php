<?php

namespace App\Console\Commands;

use App\Models\IkuSkoring;
use App\Models\Realisasi;
use Illuminate\Console\Command;

class BackfillIkuSkoring extends Command
{
    protected $signature   = 'iku:backfill-skoring';
    protected $description = 'Backfill iku_skorings dari realisasi yang sudah diverifikasi';

    public function handle(): int
    {
        $realisasis = Realisasi::where('status', 'diverifikasi')
            ->with(['indikator.tahunAnggaran'])
            ->get();

        $created = 0;

        foreach ($realisasis as $r) {
            $indikator = $r->indikator;
            if (! $indikator) {
                continue;
            }

            $tahun = (int) ($indikator->tahunAnggaran?->tahun ?? now()->year);

            IkuSkoring::firstOrCreate(
                [
                    'indikator_id' => $r->indikator_id,
                    'bulan'        => $r->bulan,
                    'tahun'        => $tahun,
                ],
                [
                    'realisasi_id' => $r->id,
                    'status'       => 'pending',
                ]
            );

            $created++;
        }

        $this->info("✅ Backfill selesai: {$created} realisasi diproses, total IkuSkoring: " . IkuSkoring::count());

        return self::SUCCESS;
    }
}
