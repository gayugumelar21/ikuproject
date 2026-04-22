<?php

use App\Models\IkuSkoring;
use App\Models\Indikator;
use App\Models\MonthlySummary;
use App\Models\Setting;
use App\Services\MonthlySummaryService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Flux\Flux;

new #[Title('Dashboard IKU')] class extends Component
{
    public int $bulan;
    public int $tahun;

    private MonthlySummaryService $summaryService;

    public function boot(MonthlySummaryService $summaryService): void
    {
        $this->summaryService = $summaryService;
    }

    public function mount(): void
    {
        $this->bulan = (int) (Setting::get('current_scoring_month') ?? now()->month);
        $this->tahun = (int) (Setting::get('active_year') ?? now()->year);
    }

    #[Computed]
    public function summaries(): \Illuminate\Support\Collection
    {
        return MonthlySummary::with('opd')
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->orderByDesc('skor_total')
            ->get();
    }

    #[Computed]
    public function totalIndikator(): int
    {
        return Indikator::where('status', 'disetujui')->count();
    }

    #[Computed]
    public function pendingTaCount(): int
    {
        return IkuSkoring::where('status', 'ai_done')
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->count();
    }

    #[Computed]
    public function sudahFinalCount(): int
    {
        return IkuSkoring::where('status', 'final')
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->count();
    }

    #[Computed]
    public function recentPendingSkoring(): \Illuminate\Support\Collection
    {
        return IkuSkoring::with(['indikator.opd', 'indikator.bidang'])
            ->where('status', 'ai_done')
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->latest()
            ->limit(5)
            ->get();
    }

    public function updatedBulan(): void
    {
        unset($this->summaries, $this->pendingTaCount, $this->sudahFinalCount, $this->recentPendingSkoring);
    }

    public function updatedTahun(): void
    {
        unset($this->summaries, $this->pendingTaCount, $this->sudahFinalCount, $this->recentPendingSkoring);
    }

    public function hitungUlang(): void
    {
        $this->summaryService->hitungSemua($this->bulan, $this->tahun);
        unset($this->summaries);
        Flux::toast('Rekap berhasil dihitung ulang.');
    }

    private function namaBulan(int $b): string
    {
        return ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$b];
    }
};
?>

<div>
    {{-- Header & Filter --}}
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Dashboard Monitoring IKU</flux:heading>
            <flux:text class="text-zinc-500 mt-1">
                Rekap skor kinerja OPD bulan {{ $this->namaBulan($bulan) }} {{ $tahun }}
            </flux:text>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <flux:select wire:model.live="bulan" class="w-36">
                @foreach(range(1,12) as $b)
                    <flux:select.option value="{{ $b }}">{{ $this->namaBulan($b) }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input type="number" wire:model.live="tahun" class="w-24" min="2020" max="2100" />
            <flux:button icon="arrow-path" wire:click="hitungUlang" wire:loading.attr="disabled">
                Hitung Ulang
            </flux:button>
            <flux:button
                icon="arrow-down-tray"
                variant="ghost"
                :href="route('export.rekap', ['bulan' => $bulan, 'tahun' => $tahun])"
            >
                Export CSV
            </flux:button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 gap-4 mb-6 sm:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900">
                    <flux:icon name="chart-bar" class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->totalIndikator }}</div>
                    <div class="text-xs text-zinc-500">Total Indikator Aktif</div>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-yellow-100 dark:bg-yellow-900">
                    <flux:icon name="clock" class="size-5 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div>
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->pendingTaCount }}</div>
                    <div class="text-xs text-zinc-500">Menunggu Skoring TA</div>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900">
                    <flux:icon name="check-badge" class="size-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->sudahFinalCount }}</div>
                    <div class="text-xs text-zinc-500">Sudah Final Bulan Ini</div>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900">
                    <flux:icon name="building-office" class="size-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->summaries->count() }}</div>
                    <div class="text-xs text-zinc-500">OPD Terhitung</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Rekap OPD --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden mb-6">
        <div class="bg-zinc-50 dark:bg-zinc-800 px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
            <flux:heading size="sm">Rekap Skor OPD — {{ $this->namaBulan($bulan) }} {{ $tahun }}</flux:heading>
            <flux:button
                size="sm"
                variant="ghost"
                icon="arrow-down-tray"
                :href="route('export.detail', ['bulan' => $bulan, 'tahun' => $tahun])"
            >
                Detail CSV
            </flux:button>
        </div>

        @if($this->summaries->isEmpty())
            <div class="p-8 text-center">
                <flux:callout icon="information-circle" color="blue">
                    <flux:callout.heading>Data rekap belum tersedia</flux:callout.heading>
                    <flux:callout.text>
                        Klik tombol <strong>Hitung Ulang</strong> untuk menghitung rekap skor OPD bulan ini.
                    </flux:callout.text>
                </flux:callout>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">#</th>
                        <th class="px-4 py-3 text-left font-medium">OPD</th>
                        <th class="px-4 py-3 text-center font-medium">Skor Utama</th>
                        <th class="px-4 py-3 text-center font-medium">Skor Kerjasama</th>
                        <th class="px-4 py-3 text-center font-medium">Skor Total</th>
                        <th class="px-4 py-3 text-center font-medium">Performa</th>
                        <th class="px-4 py-3 text-center font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($this->summaries as $i => $summary)
                        @php
                            $skor = $summary->skor_total;
                            $color = $summary->getBadgeColor();
                            $pct = $skor ? min(100, ($skor / 10) * 100) : 0;
                        @endphp
                        <tr class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                            <td class="px-4 py-3 text-zinc-500 text-xs">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100 text-sm">
                                    {{ $summary->opd?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($summary->skor_utama !== null)
                                    <span class="font-semibold text-zinc-700 dark:text-zinc-300">
                                        {{ number_format($summary->skor_utama, 1) }}
                                    </span>
                                @else
                                    <span class="text-zinc-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($summary->skor_kerjasama !== null)
                                    <span class="font-semibold text-zinc-700 dark:text-zinc-300">
                                        {{ number_format($summary->skor_kerjasama, 1) }}
                                    </span>
                                @else
                                    <span class="text-zinc-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($skor !== null)
                                    <span class="text-xl font-bold {{ $color === 'green' ? 'text-green-600' : ($color === 'yellow' ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ number_format($skor, 1) }}
                                    </span>
                                    <span class="text-zinc-400 text-xs">/10</span>
                                @else
                                    <span class="text-zinc-400 text-xs">Belum dihitung</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($skor !== null)
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                            <div
                                                class="h-2 rounded-full {{ $color === 'green' ? 'bg-green-500' : ($color === 'yellow' ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                style="width: {{ $pct }}%"
                                            ></div>
                                        </div>
                                        <flux:badge variant="{{ $color }}" size="sm">
                                            {{ $color === 'green' ? 'Baik' : ($color === 'yellow' ? 'Cukup' : 'Kurang') }}
                                        </flux:badge>
                                    </div>
                                @else
                                    <flux:badge variant="zinc" size="sm">Pending</flux:badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <flux:button
                                    size="xs"
                                    variant="ghost"
                                    icon="arrow-down-tray"
                                    :href="route('export.pdf-opd', ['opdId' => $summary->opd_id, 'bulan' => $bulan, 'tahun' => $tahun])"
                                >
                                    PDF
                                </flux:button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- IKU Pending Skoring TA --}}
    @if($this->recentPendingSkoring->isNotEmpty())
        <div class="rounded-xl border border-yellow-200 dark:border-yellow-800 overflow-hidden">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 px-4 py-3 border-b border-yellow-200 dark:border-yellow-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon name="clock" class="size-4 text-yellow-600" />
                    <flux:heading size="sm">IKU Menunggu Skoring TA</flux:heading>
                </div>
                <flux:button size="sm" variant="ghost" :href="route('skoring-ta.index')" wire:navigate>
                    Lihat Semua →
                </flux:button>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-yellow-50/50 dark:bg-yellow-900/10 text-xs text-zinc-600 dark:text-zinc-400">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">Indikator</th>
                        <th class="px-4 py-2 text-left font-medium">OPD</th>
                        <th class="px-4 py-2 text-center font-medium">Skor AI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($this->recentPendingSkoring as $sk)
                        <tr class="bg-white dark:bg-zinc-900">
                            <td class="px-4 py-2.5 font-medium text-zinc-800 dark:text-zinc-200">
                                {{ $sk->indikator?->nama }}
                            </td>
                            <td class="px-4 py-2.5 text-xs text-zinc-500">
                                {{ $sk->indikator?->opd?->name }} / {{ $sk->indikator?->bidang?->name }}
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <flux:badge variant="{{ $sk->skor_ai >= 7 ? 'green' : ($sk->skor_ai >= 5 ? 'yellow' : 'red') }}" size="sm">
                                    {{ $sk->skor_ai }}/10
                                </flux:badge>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
