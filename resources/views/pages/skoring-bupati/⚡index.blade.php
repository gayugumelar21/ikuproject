<?php

use App\Models\IkuSkoring;
use App\Services\SkoringService;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Skoring Bupati')] class extends Component
{
    public int $bulan;
    public int $tahun;
    public ?int $filterOpdId = null;

    public ?int $selectedSkoringId = null;
    public int $skorBupati = 5;
    public string $bupatiNotes = '';

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
    public function skoringsBelumDinilai()
    {
        return $this->service->getPendingUntukBupati($this->bulan, $this->tahun, $this->filterOpdId)
            ->each(fn ($s) => $s->indikator->load(['kerjasamas.opd']));
    }

    #[Computed]
    public function skoringsSudahDinilai()
    {
        return $this->service->getSudahFinal($this->bulan, $this->tahun, $this->filterOpdId);
    }

    #[Computed]
    public function filterOpds(): \Illuminate\Support\Collection
    {
        return Opd::whereIn('type', ['sekda', 'asisten', 'opd', 'kabag'])->orderBy('name')->get();
    }

    public function openSkoring(int $id): void
    {
        $this->selectedSkoringId = $id;
        $skoring = IkuSkoring::find($id);
        $this->skorBupati = $skoring->skor_bupati ?? $skoring->skor_ta ?? $skoring->skor_ai ?? 5;
        $this->bupatiNotes = $skoring->bupati_notes ?? '';
        Flux::modal('modal-skoring-bupati')->show();
    }

    public function simpan(): void
    {
        $this->authorize('skoring-bupati');
        $this->validate([
            'skorBupati'   => ['required', 'integer', 'min:1', 'max:10'],
            'bupatiNotes'  => ['nullable', 'string', 'max:1000'],
        ]);

        $skoring = IkuSkoring::findOrFail($this->selectedSkoringId);
        $this->service->simpanSkorBupati($skoring, $this->skorBupati, $this->bupatiNotes ?: null, auth()->user());
        Flux::toast('Skor Bupati berhasil difinalisasi.');
        Flux::modal('modal-skoring-bupati')->close();
        unset($this->skoringsBelumDinilai, $this->skoringsSudahDinilai);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
?>

<div class="space-y-6" x-data="{ tab: 'belum' }">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Skoring Bupati</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Penilaian final IKU per indikator (1–10). Bersifat mutlak dan tidak dapat diubah.</flux:text>
        </div>

        {{-- Filter Bulan & Tahun --}}
        <div class="flex flex-wrap gap-3">
            <flux:field>
                <flux:label>Unit / OPD</flux:label>
                <flux:select wire:model.live="filterOpdId" class="w-64">
                    <flux:select.option value="">-- Semua Unit --</flux:select.option>
                    @foreach ($this->filterOpds as $opd)
                        <flux:select.option wire:key="filter-opd-{{ $opd->id }}" value="{{ $opd->id }}">
                            {{ $opd->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
            <flux:field>
                <flux:label>Bulan</flux:label>
                <flux:select wire:model.live="bulan" class="w-36">
                    @foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $idx => $nama)
                        <flux:select.option value="{{ $idx + 1 }}">{{ $nama }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
            <flux:field>
                <flux:label>Tahun</flux:label>
                <flux:input type="number" wire:model.live="tahun" class="w-24" min="2020" max="2030" />
            </flux:field>
        </div>
    </div>

    {{-- Warning Callout --}}
    <flux:callout variant="warning" icon="exclamation-triangle">
        <flux:callout.heading>Skor Bupati Bersifat FINAL</flux:callout.heading>
        <flux:callout.text>
            Setelah difinalisasi, skor tidak dapat diubah kecuali oleh Admin Super. Pertimbangkan skor AI dan TA sebelum memutuskan.
        </flux:callout.text>
    </flux:callout>

    {{-- Tab Navigation --}}
    <div class="border-b border-zinc-200 dark:border-zinc-700">
        <nav class="flex gap-6">
            <button
                x-on:click="tab = 'belum'"
                :class="tab === 'belum' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-zinc-500 hover:text-zinc-700'"
                class="pb-3 text-sm transition-colors"
            >
                Belum Dinilai
                <span class="ml-1 inline-flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 text-xs px-2 py-0.5">
                    {{ $this->skoringsBelumDinilai->count() }}
                </span>
            </button>
            <button
                x-on:click="tab = 'sudah'"
                :class="tab === 'sudah' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-zinc-500 hover:text-zinc-700'"
                class="pb-3 text-sm transition-colors"
            >
                Sudah Dinilai
                <span class="ml-1 inline-flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 text-xs px-2 py-0.5">
                    {{ $this->skoringsSudahDinilai->count() }}
                </span>
            </button>
        </nav>
    </div>

    {{-- Tab: Belum Dinilai (card layout) --}}
    <div x-show="tab === 'belum'">
        @if ($this->skoringsBelumDinilai->isNotEmpty())
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                @foreach ($this->skoringsBelumDinilai as $skoring)
                    <div wire:key="bupati-{{ $skoring->id }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 shadow-sm space-y-4">
                        {{-- OPD & Bobot --}}
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-xs text-zinc-400 uppercase tracking-wide">
                                    {{ $skoring->indikator->opd?->name ?? '-' }} › {{ $skoring->indikator->bidang?->name ?? '-' }}
                                </p>
                                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mt-0.5 leading-snug">
                                    {{ $skoring->indikator->nama }}
                                </h3>
                            </div>
                            <flux:badge variant="blue" size="sm">Bobot {{ $skoring->indikator->bobot }}%</flux:badge>
                        </div>

                        {{-- Realisasi --}}
                        @if ($skoring->realisasi)
                            <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-3 text-sm space-y-2">
                                <p class="text-xs font-medium text-zinc-500">Realisasi</p>
                                @if ($skoring->indikator->measurement_type === 'kuantitatif')
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $skoring->realisasi->nilai }} {{ $skoring->indikator->satuan }}
                                    </p>
                                @endif
                                @if ($skoring->realisasi->keterangan)
                                    <p class="text-zinc-500 text-xs">{{ $skoring->realisasi->keterangan }}</p>
                                @endif
                                @if ($skoring->realisasi->deskripsi_progres)
                                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2">
                                        <p class="text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">📝 Deskripsi Progres:</p>
                                        <p class="text-xs text-zinc-600 dark:text-zinc-400 italic line-clamp-4">{{ $skoring->realisasi->deskripsi_progres }}</p>
                                    </div>
                                @endif
                                <div class="flex items-center gap-3 pt-1">
                                    @if ($skoring->realisasi->bukti_link)
                                        <a href="{{ $skoring->realisasi->bukti_link }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                            🔗 Lihat Bukti Link
                                        </a>
                                    @endif
                                    @if ($skoring->realisasi->foto_bukti)
                                        <a href="{{ Storage::url($skoring->realisasi->foto_bukti) }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 hover:text-emerald-800 hover:underline">
                                            🖼 Lihat Foto Bukti
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif


                        {{-- Skor AI & TA --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-lg border border-blue-100 dark:border-blue-800 bg-blue-50 dark:bg-blue-950 p-3">
                                <p class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">
                                    Skor AI
                                    @if ($skoring->skor_ai)
                                        <span class="font-bold text-lg">{{ $skoring->skor_ai }}/10</span>
                                    @else
                                        <span class="text-zinc-400">Belum</span>
                                    @endif
                                </p>
                                @if ($skoring->ai_reasoning)
                                    <p class="text-xs text-blue-500 dark:text-blue-400 italic line-clamp-3">{{ $skoring->ai_reasoning }}</p>
                                @endif
                            </div>
                            <div class="rounded-lg border border-purple-100 dark:border-purple-800 bg-purple-50 dark:bg-purple-950 p-3">
                                <p class="text-xs font-medium text-purple-600 dark:text-purple-400 mb-1">
                                    Skor TA
                                    @if ($skoring->skor_ta)
                                        <span class="font-bold text-lg">{{ $skoring->skor_ta }}/10</span>
                                    @else
                                        <span class="text-zinc-400">Belum</span>
                                    @endif
                                </p>
                                @if ($skoring->ta_notes)
                                    <p class="text-xs text-purple-500 dark:text-purple-400 italic line-clamp-3">{{ $skoring->ta_notes }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Badge Kerjasama --}}
                        @if ($skoring->indikator->kerjasamas?->isNotEmpty())
                            <div class="rounded-lg bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 p-3">
                                <p class="text-xs font-semibold text-amber-700 dark:text-amber-300 flex items-center gap-1 mb-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                                    Skor ini akan otomatis diterapkan ke:
                                </p>
                                @foreach ($skoring->indikator->kerjasamas as $kerjasama)
                                    <p class="text-xs text-amber-600 dark:text-amber-400 ml-4">
                                        🤝 {{ $kerjasama->opd?->name ?? '-' }} (bobot {{ $kerjasama->bobot }}%)
                                    </p>
                                @endforeach
                            </div>
                        @endif

                        <flux:button
                            variant="primary"
                            class="w-full"
                            icon="trophy"
                            wire:click="openSkoring({{ $skoring->id }})"
                        >
                            Beri Skor Final
                        </flux:button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 py-16 text-center">
                <flux:icon name="trophy" class="h-12 w-12 text-zinc-300 dark:text-zinc-600 mb-3" />
                <flux:heading size="sm" class="text-zinc-500">Semua indikator sudah dinilai</flux:heading>
                <flux:text class="text-zinc-400 mt-1 text-sm">Tidak ada indikator yang menunggu penilaian Bupati bulan ini.</flux:text>
            </div>
        @endif
    </div>

    {{-- Tab: Sudah Dinilai (table) --}}
    <div x-show="tab === 'sudah'">
        @if ($this->skoringsSudahDinilai->isNotEmpty())
            <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">OPD / Bidang</th>
                            <th class="px-4 py-3 text-left font-medium">Indikator</th>
                            <th class="px-4 py-3 text-center font-medium">Bobot %</th>
                            <th class="px-4 py-3 text-center font-medium">Skor Final</th>
                            <th class="px-4 py-3 text-center font-medium">AI</th>
                            <th class="px-4 py-3 text-center font-medium">TA</th>
                            <th class="px-4 py-3 text-left font-medium">Catatan Bupati</th>
                            <th class="px-4 py-3 text-left font-medium">Finalisasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->skoringsSudahDinilai as $skoring)
                            <tr wire:key="final-{{ $skoring->id }}" class="bg-white dark:bg-zinc-900">
                                <td class="px-4 py-3">
                                    <div class="text-xs font-medium text-zinc-900 dark:text-zinc-100">{{ $skoring->indikator->opd?->name ?? '-' }}</div>
                                    <div class="text-xs text-zinc-500">{{ $skoring->indikator->bidang?->name ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100 max-w-xs">
                                    {{ $skoring->indikator->nama }}
                                </td>
                                <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-300">{{ $skoring->indikator->bobot }}%</td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $color = $skoring->skor_bupati >= 7 ? 'green' : ($skoring->skor_bupati >= 5 ? 'yellow' : 'red');
                                    @endphp
                                    <flux:badge variant="{{ $color }}" size="sm" class="text-base font-bold px-3 py-1">
                                        {{ $skoring->skor_bupati }}/10
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-3 text-center text-zinc-500">{{ $skoring->skor_ai ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-zinc-500">{{ $skoring->skor_ta ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs text-zinc-500 max-w-xs">{{ $skoring->bupati_notes ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs text-zinc-400">
                                    {{ $skoring->finalized_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 py-16 text-center">
                <flux:text class="text-zinc-400">Belum ada indikator yang sudah dinilai bulan ini.</flux:text>
            </div>
        @endif
    </div>

    {{-- Modal Skoring Bupati --}}
    <flux:modal name="modal-skoring-bupati" class="md:w-[540px]">
        @if ($selectedSkoringId)
            @php $skoring = IkuSkoring::find($selectedSkoringId); @endphp
            @if ($skoring)
                <div class="space-y-5">
                    <div>
                        <flux:heading>Skor Final Bupati</flux:heading>
                        <flux:text class="mt-1 text-zinc-500 text-sm font-medium">{{ $skoring->indikator->nama }}</flux:text>
                        <p class="text-xs text-zinc-400">
                            {{ $skoring->indikator->opd?->name }} › {{ $skoring->indikator->bidang?->name ?? '-' }}
                            &nbsp;|&nbsp; Bobot: {{ $skoring->indikator->bobot }}%
                        </p>
                    </div>

                    {{-- Info Mirror Kerjasama --}}
                    @if ($skoring->indikator->load('kerjasamas.opd')->kerjasamas->isNotEmpty())
                        <div class="rounded-lg bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 p-3">
                            <p class="text-xs font-semibold text-amber-700 dark:text-amber-300 mb-1">⚡ Skor ini akan diperluas ke OPD berikut (IKU Kerjasama):</p>
                            @foreach ($skoring->indikator->kerjasamas as $kerjasama)
                                <p class="text-xs text-amber-600 dark:text-amber-400 ml-2">• {{ $kerjasama->opd?->name ?? '-' }} — {{ $kerjasama->nama }} (bobot {{ $kerjasama->bobot }}%)</p>
                            @endforeach
                        </div>
                    @endif

                    {{-- Ringkasan Pertimbangan --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-lg bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 p-3">
                            <p class="text-xs font-medium text-blue-600 mb-1">Skor AI: <span class="font-bold">{{ $skoring->skor_ai ? $skoring->skor_ai.'/10' : 'N/A' }}</span></p>
                            @if ($skoring->ai_reasoning)
                                <p class="text-xs text-blue-500 italic line-clamp-3">{{ $skoring->ai_reasoning }}</p>
                            @endif
                        </div>
                        <div class="rounded-lg bg-purple-50 dark:bg-purple-950 border border-purple-200 dark:border-purple-800 p-3">
                            <p class="text-xs font-medium text-purple-600 mb-1">Skor TA: <span class="font-bold">{{ $skoring->skor_ta ? $skoring->skor_ta.'/10' : 'N/A' }}</span></p>
                            @if ($skoring->ta_notes)
                                <p class="text-xs text-purple-500 italic line-clamp-3">{{ $skoring->ta_notes }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Selector Skor 1-10 --}}
                    <div>
                        <flux:label class="mb-3 block font-semibold">Skor Final Bupati (1–10)</flux:label>
                        <div class="flex gap-2 flex-wrap">
                            @for ($s = 1; $s <= 10; $s++)
                                <button
                                    wire:click="$set('skorBupati', {{ $s }})"
                                    class="w-11 h-11 rounded-xl font-bold text-sm transition-all
                                        {{ $skorBupati === $s
                                            ? ($s >= 7 ? 'bg-green-600 text-white shadow-lg scale-110' : ($s >= 5 ? 'bg-yellow-500 text-white shadow-lg scale-110' : 'bg-red-500 text-white shadow-lg scale-110'))
                                            : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 hover:scale-105' }}"
                                >
                                    {{ $s }}
                                </button>
                            @endfor
                        </div>
                        <p class="mt-2 text-xs text-zinc-500">
                            🔴 1–4: Kurang &nbsp;|&nbsp; 🟡 5–6: Cukup &nbsp;|&nbsp; 🟢 7–10: Baik
                        </p>
                    </div>

                    <flux:field>
                        <flux:label>Catatan Bupati</flux:label>
                        <flux:textarea wire:model="bupatiNotes" rows="3" placeholder="Catatan atau arahan..." />
                        <flux:error name="bupatiNotes" />
                    </flux:field>

                    <div class="rounded-lg bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 p-3">
                        <p class="text-xs text-amber-700 dark:text-amber-300 font-medium">
                            ⚠ Skor ini bersifat FINAL dan tidak dapat diubah setelah disimpan.
                        </p>
                    </div>

                    <div class="flex justify-end gap-3 pt-1">
                        <flux:button variant="ghost" x-on:click="$flux.modal('modal-skoring-bupati').close()">Batal</flux:button>
                        <flux:button variant="primary" icon="trophy" wire:click="simpan" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="simpan">Finalisasi Skor</span>
                            <span wire:loading wire:target="simpan">Menyimpan...</span>
                        </flux:button>
                    </div>
                </div>
            @endif
        @endif
    </flux:modal>
</div>
