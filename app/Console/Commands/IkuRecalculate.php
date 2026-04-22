<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\MonthlySummaryService;
use Illuminate\Console\Command;

class IkuRecalculate extends Command
{
    protected $signature = 'iku:recalculate {month? : Bulan (1-12)} {year? : Tahun}';

    protected $description = 'Hitung ulang monthly summary skor IKU semua OPD';

    public function handle(MonthlySummaryService $service): int
    {
        $bulan = (int) ($this->argument('month') ?? Setting::get('current_scoring_month', now()->month));
        $tahun = (int) ($this->argument('year') ?? Setting::get('active_year', now()->year));

        $this->info("Menghitung skor bulan {$bulan}/{$tahun}...");
        $service->hitungSemua($bulan, $tahun);
        $this->info('Selesai.');

        return self::SUCCESS;
    }
}
