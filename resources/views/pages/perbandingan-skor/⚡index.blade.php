<?php

use App\Models\IkuSkoring;
use App\Services\SkoringService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Perbandingan Skor IKU')] class extends Component
{
    public int $bulan;
    public int $tahun;
    public ?int $filterOpdId = null;

    private SkoringService $service;

    public function mount(): void
    {
        $this->bulan = (int) now()->format('n');
        $this->tahun = (int) now()->format('Y');
    }

    public function boot(SkoringService $service): void
    {
        $this->service = $service;
    }

    #[Computed]
    public function skorings()
    {
        return $this->service->getAllSkorings($this->bulan, $this->tahun, $this->filterOpdId);
    }

    #[Computed]
    public function filterOpds(): \Illuminate\Support\Collection
    {
        return \App\Models\Opd::whereIn('type', ['sekda', 'asisten', 'opd', 'kabag'])->orderBy('name')->get();
    }

    #[Computed]
    public function ringkasan(): array
    {
        $all = $this->skorings;
        $final = $all->where('status', 'final');

        return [
            'total' => $all->count(),
            'sudah_final' => $final->count(),
            'rata_ai' => $all->whereNotNull('skor_ai')->avg('skor_ai'),
            'rata_ta' => $all->whereNotNull('skor_ta')->avg('skor_ta'),
            'rata_bupati' => $final->whereNotNull('skor_bupati')->avg('skor_bupati'),
        ];
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
?>

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Perbandingan Skor IKU</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Bandingkan skor AI, Tenaga Ahli, dan Bupati per indikator.</flux:text>
        </div>

        <div class="flex flex-wrap gap-3 w-full sm:w-auto">
            <flux:field class="flex-1 min-w-[160px]">
                <flux:label>Unit / OPD</flux:label>
                <flux:select wire:model.live="filterOpdId">
                    <flux:select.option value="">-- Semua Unit --</flux:select.option>
                    @foreach ($this->filterOpds as $opd)
                        <flux:select.option wire:key="filter-opd-{{ $opd->id }}" value="{{ $opd->id }}">
                            {{ $opd->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
            <flux:field class="flex-1 min-w-[120px]">
                <flux:label>Bulan</flux:label>
                <flux:select wire:model.live="bulan">
                    @foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $idx => $nama)
                        <flux:select.option value="{{ $idx + 1 }}">{{ $nama }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
            <flux:field class="w-24">
                <flux:label>Tahun</flux:label>
                <flux:input type="number" wire:model.live="tahun" min="2020" max="2030" />
            </flux:field>
        </div>
    </div>

    {{-- Ringkasan Statistik --}}
    @if ($this->skorings->isNotEmpty())
        @php $r = $this->ringkasan; @endphp
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 text-center">
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $r['total'] }}</p>
                <p class="text-xs text-zinc-500 mt-1">Total Indikator</p>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $r['sudah_final'] }}</p>
                <p class="text-xs text-zinc-500 mt-1">Sudah Final</p>
            </div>
            <div class="rounded-xl border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-950 p-4 text-center">
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                    {{ $r['rata_ai'] ? number_format($r['rata_ai'], 1) : '—' }}
                </p>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Rata-rata AI</p>
            </div>
            <div class="rounded-xl border border-purple-200 dark:border-purple-700 bg-purple-50 dark:bg-purple-950 p-4 text-center">
                <p class="text-2xl font-bold text-purple-700 dark:text-purple-300">
                    {{ $r['rata_ta'] ? number_format($r['rata_ta'], 1) : '—' }}
                </p>
                <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">Rata-rata TA</p>
            </div>
            <div class="rounded-xl border border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-950 p-4 text-center">
                <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                    {{ $r['rata_bupati'] ? number_format($r['rata_bupati'], 1) : '—' }}
                </p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1">Rata-rata Bupati</p>
            </div>
        </div>
    @endif

    {{-- Tabel Perbandingan --}}
    @if ($this->skorings->isNotEmpty())
        <div class="overflow-x-auto">
        <table class="w-full min-w-[600px] text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-xs">
                    <tr>
                        <th class="px-3 py-3 text-left font-medium">Indikator / OPD</th>
                        <th class="px-3 py-3 text-center font-medium hidden sm:table-cell whitespace-nowrap">Bobot</th>
                        <th class="px-3 py-3 text-center font-medium hidden md:table-cell">Status</th>
                        <th class="px-3 py-3 text-center font-medium whitespace-nowrap"><span class="text-blue-600">AI</span></th>
                        <th class="px-3 py-3 text-center font-medium whitespace-nowrap"><span class="text-purple-600">TA</span></th>
                        <th class="px-3 py-3 text-center font-medium whitespace-nowrap"><span class="text-green-600">Bupati</span></th>
                        <th class="px-3 py-3 text-center font-medium hidden lg:table-cell whitespace-nowrap">Selisih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($this->skorings as $skoring)
                        @php
                            $statusVariant = match ($skoring->status) {
                                'final' => 'green', 'ta_done' => 'purple', 'ai_done' => 'blue', default => 'zinc',
                            };
                            $statusLabel = match ($skoring->status) {
                                'final' => 'Final', 'ta_done' => 'TA', 'ai_done' => 'AI', default => 'Pending',
                            };
                            $selisih = ($skoring->skor_ai && $skoring->skor_bupati)
                                ? abs($skoring->skor_ai - $skoring->skor_bupati) : null;
                            $selisihVariant = match (true) {
                                $selisih === null => 'zinc', $selisih <= 1 => 'green', $selisih <= 2 => 'yellow', default => 'red',
                            };
                            $selisihLabel = match (true) {
                                $selisih === null => '—', $selisih <= 1 => "±{$selisih}", $selisih <= 2 => "±{$selisih}", default => "±{$selisih}",
                            };
                        @endphp
                        <tr wire:key="perb-{{ $skoring->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                            <td class="px-3 py-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100 text-sm line-clamp-2">
                                    {{ $skoring->indikator->nama }}
                                </div>
                                <div class="text-xs text-zinc-500 mt-0.5">
                                    {{ $skoring->indikator->opd?->name ?? '-' }}
                                    @if($skoring->realisasi)
                                        · Realisasi: {{ $skoring->realisasi->nilai }} {{ $skoring->indikator->satuan }}
                                    @endif
                                </div>
                                <div class="mt-1 flex items-center gap-1.5 md:hidden">
                                    <flux:badge variant="{{ $statusVariant }}" size="sm">{{ $statusLabel }}</flux:badge>
                                    @if($skoring->indikator->bobot)<span class="text-xs text-zinc-400">{{ $skoring->indikator->bobot }}%</span>@endif
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center text-zinc-600 dark:text-zinc-300 text-xs hidden sm:table-cell whitespace-nowrap">
                                {{ $skoring->indikator->bobot }}%
                            </td>
                            <td class="px-3 py-3 text-center hidden md:table-cell">
                                <flux:badge variant="{{ $statusVariant }}" size="sm">{{ $statusLabel }}</flux:badge>
                            </td>
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                @if ($skoring->skor_ai)
                                    @php $c = $skoring->skor_ai >= 7 ? 'text-green-600' : ($skoring->skor_ai >= 5 ? 'text-yellow-600' : 'text-red-600'); @endphp
                                    <span class="font-bold {{ $c }}">{{ $skoring->skor_ai }}</span><span class="text-zinc-400 text-xs">/10</span>
                                @else
                                    <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                @if ($skoring->skor_ta)
                                    @php $c = $skoring->skor_ta >= 7 ? 'text-green-600' : ($skoring->skor_ta >= 5 ? 'text-yellow-600' : 'text-red-600'); @endphp
                                    <span class="font-bold {{ $c }}">{{ $skoring->skor_ta }}</span><span class="text-zinc-400 text-xs">/10</span>
                                @else
                                    <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                @if ($skoring->skor_bupati)
                                    @php $c = $skoring->skor_bupati >= 7 ? 'text-green-600' : ($skoring->skor_bupati >= 5 ? 'text-yellow-600' : 'text-red-600'); @endphp
                                    <span class="font-bold {{ $c }}">{{ $skoring->skor_bupati }}</span><span class="text-zinc-400 text-xs">/10</span>
                                @else
                                    <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center hidden lg:table-cell">
                                <flux:badge variant="{{ $selisihVariant }}" size="sm">{{ $selisihLabel }}</flux:badge>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Legenda --}}
        <div class="flex flex-wrap gap-4 text-xs text-zinc-500">
            <span class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-full bg-green-500"></span>
                Selaras (selisih ≤ 1)
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-full bg-yellow-400"></span>
                Sedikit berbeda (selisih 2)
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-full bg-red-500"></span>
                Beda signifikan (selisih ≥ 3)
            </span>
        </div>
    @else
        <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 py-16 text-center">
            <flux:icon name="chart-bar" class="h-12 w-12 text-zinc-300 dark:text-zinc-600 mb-3" />
            <flux:heading size="sm" class="text-zinc-500">Belum ada data skoring bulan ini</flux:heading>
            <flux:text class="text-zinc-400 mt-1 text-sm">
                Generate skor AI terlebih dahulu di halaman Skoring TA.
            </flux:text>
        </div>
    @endif
</div>
