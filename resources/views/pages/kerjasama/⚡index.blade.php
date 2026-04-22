<?php

use App\Models\Indikator;
use App\Models\IkuSkoring;
use App\Models\TahunAnggaran;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('IKU Kerjasama Lintas OPD')] class extends Component
{
    public ?int $filterTahunAnggaranId = null;
    public int $filterBulan;

    public function mount(): void
    {
        $aktif = TahunAnggaran::aktif()->first();
        $this->filterTahunAnggaranId = $aktif?->id;
        $this->filterBulan = now()->month;
    }

    #[Computed]
    public function tahunAnggarans(): \Illuminate\Support\Collection
    {
        return TahunAnggaran::orderByDesc('tahun')->get();
    }

    /**
     * Semua IKU Utama yang memiliki kerjasama (IKU sumber).
     */
    #[Computed]
    public function ikuSumbers(): \Illuminate\Support\Collection
    {
        if (! $this->filterTahunAnggaranId) {
            return collect();
        }

        return Indikator::with([
            'opd',
            'bidang',
            'kerjasamas.opd',
            'kerjasamas.bidang',
            'kerjasamas' => fn ($q) => $q->with([
                'opd',
                'bidang',
                'skorings' => fn ($sq) => $sq
                    ->where('bulan', $this->filterBulan)
                    ->where('tahun', $this->filterTahunAnggaranId ? TahunAnggaran::find($this->filterTahunAnggaranId)?->tahun : now()->year),
            ]),
            'skorings' => fn ($q) => $q
                ->where('bulan', $this->filterBulan)
                ->where('tahun', $this->filterTahunAnggaranId ? TahunAnggaran::find($this->filterTahunAnggaranId)?->tahun : now()->year),
        ])
        ->where('tahun_anggaran_id', $this->filterTahunAnggaranId)
        ->where('category', 'utama')
        ->whereHas('kerjasamas')
        ->orderBy('nama')
        ->get();
    }

    /**
     * Statistik ringkasan.
     */
    #[Computed]
    public function statistik(): array
    {
        $semua = Indikator::where('tahun_anggaran_id', $this->filterTahunAnggaranId)
            ->where('category', 'kerjasama')
            ->get();

        $opds = $semua->pluck('opd_id')->unique()->count();

        return [
            'total_kerjasama'  => $semua->count(),
            'opd_terlibat'     => $opds,
            'total_iku_sumber' => $this->ikuSumbers->count(),
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
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">🤝 IKU Kerjasama Lintas OPD</flux:heading>
            <flux:text class="mt-1 text-zinc-500">
                Peta sinergi antar OPD — skor IKU sumber otomatis diperluas ke OPD mitra.
            </flux:text>
        </div>

        {{-- Filter --}}
        <div class="flex gap-3">
            <flux:field>
                <flux:select wire:model.live="filterTahunAnggaranId" class="w-36">
                    <flux:select.option value="">-- Tahun --</flux:select.option>
                    @foreach ($this->tahunAnggarans as $tahun)
                        <flux:select.option wire:key="tahun-{{ $tahun->id }}" value="{{ $tahun->id }}">
                            {{ $tahun->tahun }}{{ $tahun->is_active ? ' ✓' : '' }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
            <flux:field>
                <flux:select wire:model.live="filterBulan" class="w-36">
                    @foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $idx => $nama)
                        <flux:select.option value="{{ $idx + 1 }}">{{ $nama }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
        </div>
    </div>

    {{-- Callout penjelasan --}}
    <flux:callout variant="info" icon="information-circle">
        <flux:callout.heading>Prinsip IKU Kerjasama</flux:callout.heading>
        <flux:callout.text>
            Skor IKU Kerjasama <strong>identik dengan skor IKU sumber</strong> — jika OPD sumber mendapat nilai 7, OPD mitra juga mendapat nilai 7.
            Semangat bersama: untung bareng, rugi bareng. Ini mencegah ego sektoral.
        </flux:callout.text>
    </flux:callout>

    {{-- Kartu Statistik --}}
    @if ($filterTahunAnggaranId)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-purple-200 dark:border-purple-800 bg-purple-50 dark:bg-purple-950 p-5">
                <div class="text-xs font-medium text-purple-500 uppercase tracking-wide mb-1">Total IKU Kerjasama</div>
                <div class="text-3xl font-bold text-purple-700 dark:text-purple-300">{{ $this->statistik['total_kerjasama'] }}</div>
                <div class="text-xs text-purple-400 mt-1">di semua OPD mitra</div>
            </div>
            <div class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950 p-5">
                <div class="text-xs font-medium text-amber-500 uppercase tracking-wide mb-1">IKU Sumber (Utama)</div>
                <div class="text-3xl font-bold text-amber-700 dark:text-amber-300">{{ $this->statistik['total_iku_sumber'] }}</div>
                <div class="text-xs text-amber-400 mt-1">IKU yang menjadi referensi</div>
            </div>
            <div class="rounded-xl border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-950 p-5">
                <div class="text-xs font-medium text-blue-500 uppercase tracking-wide mb-1">OPD Terlibat</div>
                <div class="text-3xl font-bold text-blue-700 dark:text-blue-300">{{ $this->statistik['opd_terlibat'] }}</div>
                <div class="text-xs text-blue-400 mt-1">OPD mitra kerjasama</div>
            </div>
        </div>
    @endif

    {{-- Peta Kerjasama --}}
    @if ($this->ikuSumbers->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-zinc-200 dark:border-zinc-700 py-20 text-center">
            <div class="text-5xl mb-3">🤝</div>
            <flux:heading size="sm" class="text-zinc-500">Belum ada IKU Kerjasama</flux:heading>
            <flux:text class="text-zinc-400 mt-1 text-sm max-w-sm">
                @if (! $filterTahunAnggaranId)
                    Pilih tahun anggaran untuk melihat peta kerjasama.
                @else
                    Belum ada IKU yang memiliki mitra kerjasama untuk tahun ini.
                    Buat IKU dengan kategori "Kerjasama" dan pilih IKU sumber dari OPD lain.
                @endif
            </flux:text>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($this->ikuSumbers as $sumber)
                @php
                    $skorSumber = $sumber->skorings->first();
                    $skorFinal = $skorSumber?->skor_bupati;
                    $isFinal = $skorSumber?->is_final;
                @endphp

                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                    {{-- Header IKU Sumber --}}
                    <div class="p-5 bg-gradient-to-r from-zinc-50 to-white dark:from-zinc-800 dark:to-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <flux:badge variant="zinc" size="sm">📋 IKU Sumber</flux:badge>
                                    <span class="text-xs text-zinc-400">{{ $sumber->opd?->name ?? '-' }} › {{ $sumber->bidang?->name ?? '-' }}</span>
                                </div>
                                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 text-base leading-snug">
                                    {{ $sumber->nama }}
                                </h3>
                                <div class="flex items-center gap-3 mt-2 text-xs text-zinc-500">
                                    <span>Satuan: <strong>{{ $sumber->satuan }}</strong></span>
                                    <span>Target: <strong>{{ number_format($sumber->target, 2) }}</strong></span>
                                    <span>Bobot: <strong>{{ $sumber->bobot }}%</strong></span>
                                </div>
                            </div>

                            {{-- Skor Bupati --}}
                            <div class="text-right">
                                @if ($isFinal && $skorFinal !== null)
                                    @php $color = $skorFinal >= 7 ? 'green' : ($skorFinal >= 5 ? 'yellow' : 'red'); @endphp
                                    <div class="text-xs text-zinc-500 mb-1">Skor Final Bupati</div>
                                    <flux:badge variant="{{ $color }}" class="text-xl font-bold px-4 py-2">
                                        {{ $skorFinal }}/10
                                    </flux:badge>
                                @else
                                    <div class="text-xs text-zinc-400 italic">Skor belum difinalisasi</div>
                                    <div class="text-2xl font-bold text-zinc-300 dark:text-zinc-600 mt-1">—/10</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Daftar OPD Mitra --}}
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($sumber->kerjasamas as $mitra)
                            @php
                                $skorMitra = $mitra->skorings->first();
                                $skorMitraFinal = $skorMitra?->skor_bupati;
                                $isMitraFinal = $skorMitra?->is_final;
                            @endphp
                            <div class="p-5 flex flex-wrap items-center justify-between gap-4
                                {{ $isMitraFinal ? 'bg-green-50/30 dark:bg-green-950/20' : '' }}">
                                <div class="flex items-start gap-3">
                                    {{-- Connector visual --}}
                                    <div class="mt-1 flex flex-col items-center gap-0.5">
                                        <div class="w-px h-3 bg-purple-300 dark:bg-purple-600"></div>
                                        <div class="w-2.5 h-2.5 rounded-full bg-purple-400 dark:bg-purple-500 ring-2 ring-white dark:ring-zinc-900"></div>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 mb-0.5">
                                            <flux:badge variant="purple" size="sm">🤝 Mitra</flux:badge>
                                            <span class="text-xs text-zinc-400">{{ $mitra->opd?->name ?? '-' }} › {{ $mitra->bidang?->name ?? '-' }}</span>
                                        </div>
                                        <p class="font-medium text-zinc-800 dark:text-zinc-200 text-sm">{{ $mitra->nama }}</p>
                                        <p class="text-xs text-zinc-500 mt-0.5">
                                            Bobot: <strong>{{ $mitra->bobot }}%</strong>
                                            &nbsp;•&nbsp;
                                            Peran: {{ Str::limit($mitra->definisi ?? '-', 80) }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Status Mirror --}}
                                <div class="text-right min-w-[120px]">
                                    @if ($isMitraFinal && $skorMitraFinal !== null)
                                        @php $mc = $skorMitraFinal >= 7 ? 'green' : ($skorMitraFinal >= 5 ? 'yellow' : 'red'); @endphp
                                        <div class="text-xs text-zinc-500 mb-1">Skor Mirror</div>
                                        <flux:badge variant="{{ $mc }}" class="text-lg font-bold px-3 py-1">
                                            {{ $skorMitraFinal }}/10
                                        </flux:badge>
                                        <div class="text-xs text-green-500 mt-1 flex items-center justify-end gap-0.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Tersync
                                        </div>
                                    @elseif ($isFinal)
                                        <div class="text-xs text-amber-500 italic">Menunggu sync...</div>
                                    @else
                                        <div class="text-xs text-zinc-400 italic">Menunggu skor sumber</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
