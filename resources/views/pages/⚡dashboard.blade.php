<?php

use App\Models\IkuSkoring;
use App\Models\Indikator;
use App\Models\MonthlySummary;
use App\Models\Opd;
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

    /** null = semua | opd-id = filter ke unit tertentu */
    public ?int $filterUnitId = null;

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

    /** Daftar unit level atas: Sekda + Asisten-asisten */
    #[Computed]
    public function unitOptions(): \Illuminate\Support\Collection
    {
        return Opd::whereIn('type', ['sekda', 'asisten'])->orderBy('type')->orderBy('name')->get();
    }

    /**
     * Summary OPD yang ditampilkan.
     * - filterUnitId null  → Sekda, Asisten I/II/III, lalu OPD/Bidang level dinas urut abjad
     * - filterUnitId = id  → Unit itu sendiri di baris 1, diikuti OPD bawahannya
     */
    #[Computed]
    public function summaries(): \Illuminate\Support\Collection
    {
        $base = MonthlySummary::with('opd')
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun);

        if ($this->filterUnitId) {
            // Baris pertama: unit itu sendiri
            $unitSummary = (clone $base)
                ->where('opd_id', $this->filterUnitId)
                ->get();

            // Cari OPD yang parent_id-nya = unit terpilih (anak langsung)
            $childIds = Opd::where('parent_id', $this->filterUnitId)->pluck('id');

            // Cari juga OPD yang opd_id-nya bukan asisten/sekda tapi
            // punya indikator dengan asisten_id = unit terpilih
            $linkedOpdIds = Indikator::where('asisten_id', $this->filterUnitId)
                ->whereNotNull('opd_id')
                ->pluck('opd_id')
                ->merge($childIds)
                ->unique();

            $childSummaries = (clone $base)
                ->whereIn('opd_id', $linkedOpdIds)
                ->whereHas('opd', fn ($q) => $q->whereIn('type', ['opd', 'kabag']))
                ->orderBy(Opd::select('name')->whereColumn('opds.id', 'monthly_summaries.opd_id')->limit(1))
                ->get();

            return $unitSummary->merge($childSummaries);
        }

        // Default: Sekda & Asisten di atas, kemudian OPD/Dinas urut abjad
        $unitRows = (clone $base)
            ->whereHas('opd', fn ($q) => $q->whereIn('type', ['sekda', 'asisten']))
            ->get()
            ->sortBy(fn ($s) => match ($s->opd?->type) {
                'sekda'   => '0_'.$s->opd->name,
                'asisten' => '1_'.$s->opd->name,
                default   => '9_'.$s->opd->name,
            });

        $opdRows = (clone $base)
            ->whereHas('opd', fn ($q) => $q->where('type', 'opd'))
            ->orderBy(Opd::select('name')->whereColumn('opds.id', 'monthly_summaries.opd_id')->limit(1))
            ->get();

        return $unitRows->merge($opdRows)->values();
    }

    #[Computed]
    public function totalIndikator(): int
    {
        return Indikator::where('status', 'disetujui')
            ->where('category', 'utama')
            ->count();
    }

    #[Computed]
    public function pendingTaCount(): int
    {
        return IkuSkoring::where('status', 'ai_done')
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->whereHas('indikator', fn ($q) => $q->where('category', 'utama'))
            ->count();
    }

    #[Computed]
    public function sudahFinalCount(): int
    {
        return IkuSkoring::where('status', 'final')
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->whereHas('indikator', fn ($q) => $q->where('category', 'utama'))
            ->count();
    }

    #[Computed]
    public function recentPendingSkoring(): \Illuminate\Support\Collection
    {
        return IkuSkoring::with(['indikator.opd', 'indikator.bidang'])
            ->where('status', 'ai_done')
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->whereHas('indikator', fn ($q) => $q->where('category', 'utama'))
            ->latest()
            ->limit(5)
            ->get();
    }

    /** Unit yang sedang aktif difilter (untuk header) */
    #[Computed]
    public function selectedUnit(): ?Opd
    {
        return $this->filterUnitId ? Opd::find($this->filterUnitId) : null;
    }

    public function updatedBulan(): void
    {
        unset($this->summaries, $this->pendingTaCount, $this->sudahFinalCount, $this->recentPendingSkoring);
    }

    public function updatedTahun(): void
    {
        unset($this->summaries, $this->pendingTaCount, $this->sudahFinalCount, $this->recentPendingSkoring);
    }

    public function updatedFilterUnitId(): void
    {
        unset($this->summaries, $this->selectedUnit);
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

    private function labelUnit(?Opd $opd): string
    {
        if (! $opd) return 'Semua OPD';
        return $opd->name;
    }
};
?>

<div>
    {{-- Header & Filter --}}
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <flux:heading size="xl">Dashboard Monitoring IKU</flux:heading>
            <flux:text class="text-zinc-500 mt-1">
                Rekap skor kinerja
                @if($this->selectedUnit)
                    <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $this->selectedUnit->name }}</span>
                @else
                    seluruh OPD
                @endif
                — {{ $this->namaBulan($bulan) }} {{ $tahun }}
            </flux:text>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            {{-- Filter Unit --}}
            <flux:select wire:model.live="filterUnitId" class="w-52" placeholder="Semua OPD">
                <flux:select.option value="">— Semua OPD —</flux:select.option>
                @foreach($this->unitOptions as $unit)
                    <flux:select.option value="{{ $unit->id }}">
                        {{ $unit->type === 'sekda' ? '🏛 ' : '📋 ' }}{{ $unit->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            {{-- Filter Bulan --}}
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

    {{-- Filter Unit Pills --}}
    <div class="flex items-center gap-2 flex-wrap mb-6">
        <button
            wire:click="$set('filterUnitId', null)"
            class="px-3 py-1.5 rounded-full text-xs font-medium transition-all
                {{ $filterUnitId === null
                    ? 'bg-blue-600 text-white shadow'
                    : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
        >
            Semua OPD
        </button>
        @foreach($this->unitOptions as $unit)
            <button
                wire:click="$set('filterUnitId', {{ $unit->id }})"
                class="px-3 py-1.5 rounded-full text-xs font-medium transition-all
                    {{ (int)$filterUnitId === $unit->id
                        ? 'bg-blue-600 text-white shadow'
                        : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
            >
                {{ $unit->type === 'sekda' ? '🏛' : '📋' }} {{ Str::limit($unit->name, 24) }}
            </button>
        @endforeach
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
                    <div class="text-xs text-zinc-500">Unit Terhitung</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Rekap --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden mb-6">
        <div class="bg-zinc-50 dark:bg-zinc-800 px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
            <div>
                <flux:heading size="sm">
                    @if($this->selectedUnit)
                        Overview: {{ $this->selectedUnit->name }}
                    @else
                        Rekap Skor OPD
                    @endif
                    — {{ $this->namaBulan($bulan) }} {{ $tahun }}
                </flux:heading>
                @if($this->selectedUnit)
                    <p class="text-xs text-zinc-400 mt-0.5">
                        Baris pertama = skor agregat unit, baris berikutnya = OPD/bagian di bawahnya
                    </p>
                @endif
            </div>
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
                        Klik tombol <strong>Hitung Ulang</strong> untuk menghitung rekap skor bulan ini.
                    </flux:callout.text>
                </flux:callout>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">#</th>
                        <th class="px-4 py-3 text-left font-medium">Unit / OPD</th>
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
                            $skor        = $summary->skor_total;
                            $color       = $summary->getBadgeColor();
                            $pct         = $skor ? min(100, ($skor / 10) * 100) : 0;
                            $isTopUnit   = $filterUnitId && $i === 0;
                            $opdType     = $summary->opd?->type;
                            $isUnitLevel = in_array($opdType, ['sekda', 'asisten', 'kabag']);
                        @endphp
                        <tr
                            wire:key="dash-{{ $summary->id }}"
                            class="
                                {{ $isTopUnit
                                    ? 'bg-blue-50 dark:bg-blue-950/40 border-l-4 border-l-blue-500'
                                    : ($isUnitLevel
                                        ? 'bg-zinc-50/70 dark:bg-zinc-800/50'
                                        : 'bg-white dark:bg-zinc-900') }}
                                hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors
                            "
                        >
                            <td class="px-4 py-3 text-zinc-500 text-xs">
                                @if($isTopUnit)
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-600 text-white text-xs font-bold">★</span>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($isTopUnit)
                                        <span class="text-blue-600 text-base">🏛</span>
                                    @elseif($isUnitLevel)
                                        <span class="text-zinc-400 text-xs">📋</span>
                                    @endif
                                    <div>
                                        <div class="font-{{ $isTopUnit ? 'bold' : 'medium' }} text-zinc-900 dark:text-zinc-100 text-sm {{ $isTopUnit ? 'text-blue-700 dark:text-blue-300' : '' }}">
                                            {{ $summary->opd?->name ?? '-' }}
                                        </div>
                                        @if($isTopUnit)
                                            <div class="text-xs text-blue-500 dark:text-blue-400">Skor Agregat Unit</div>
                                        @elseif($isUnitLevel)
                                            <div class="text-xs text-zinc-400">{{ ucfirst($opdType) }}</div>
                                        @endif
                                    </div>
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
