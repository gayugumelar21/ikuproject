<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsAppMessage;
use App\Models\Indikator;
use App\Models\Setting;
use Illuminate\Console\Command;

class IkuSendReminders extends Command
{
    protected $signature = 'iku:send-reminders {month? : Bulan} {year? : Tahun}';

    protected $description = 'Kirim reminder WA ke pemilik indikator yang belum input realisasi';

    public function handle(): int
    {
        if (! Setting::get('wa_reminder_enabled', false)) {
            $this->info('WA reminder dinonaktifkan.');

            return self::SUCCESS;
        }

        $bulan = (int) ($this->argument('month') ?? Setting::get('current_scoring_month', now()->month));
        $tahun = (int) ($this->argument('year') ?? Setting::get('active_year', now()->year));

        $belumInput = Indikator::with(['owner'])
            ->where('status', 'disetujui')
            ->where('category', 'utama')
            ->whereNotExists(fn ($q) => $q->from('realisasi')
                ->whereColumn('realisasi.indikator_id', 'indikators.id')
                ->where('realisasi.bulan', $bulan))
            ->get();

        $count = 0;
        foreach ($belumInput as $indikator) {
            $user = $indikator->owner;
            if (! $user || ! $user->phone) {
                continue;
            }

            $pesan = "*Reminder IKU*\nYth. {$user->name},\n"
                ."Belum ada input realisasi bulan {$bulan}/{$tahun} untuk:\n"
                ."_{$indikator->nama}_\n\n"
                .'Mohon segera diinput. Terima kasih.';

            SendWhatsAppMessage::dispatch($user->phone, $pesan, 'reminder', $user->id);
            $count++;
        }

        $this->info("Reminder terkirim untuk {$count} indikator.");

        return self::SUCCESS;
    }
}
