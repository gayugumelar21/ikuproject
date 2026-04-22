<?php

use App\Models\Indikator;
use App\Models\Opd;
use App\Models\RekapCapaian;
use App\Models\TahunAnggaran;
use App\Services\RekapCapaianService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Flux\Flux;

new class extends Component
{
    public ?int $filterTahunAnggaranId = null;
    public int  $filterBulan = 1;
    public string $filterLevel = 'opd';

    /** null = semua | opd-id = filter ke unit tertentu */
    public ?int $filterUnitId = null;

    private RekapCapaianService $service;

    public function boot(RekapCapaianService $service): void
    {
        $this->service = $service;
    }

    public function mount(): void
    {
        $aktif = TahunAnggaran::aktif()->first();
        $this->filterTahunAnggaranId = $aktif?->id;
        $this->filterBulan = now()->month;
    }

    #[Computed]
    public function tahunAnggarans(): \Illuminate\Database\Eloquent\Collection
    {
        return TahunAnggaran::orderByDesc('tahun')->get();
    }

    /** Opsi filter unit: Sekda + Asisten */
    #[Computed]
    public function unitOptions(): \Illuminate\Support\Collection
    {
        return Opd::whereIn('type', ['sekda', 'asisten'])->orderBy('type')->orderBy('name')->get();
    }

    /** Unit yang sedang difilter */
    #[Computed]
    public function selectedUnit(): ?Opd
    {
        return $this->filterUnitId ? Opd::find($this->filterUnitId) : null;
    }

    /**
     * Data rekap capaian.
     * - filterUnitId null  → mode normal: level terpilih, ordered by persentase desc
     * - filterUnitId ada   → unit itu di baris pertama, lalu OPD bawahannya
     */
    #[Computed]
    public function rekaps(): \Illuminate\Support\Collection
    {
        if (! $this->filterTahunAnggaranId) {
            return collect();
        }

        if ($this->filterUnitId) {
            // Baris 1: rekap unit itu sendiri
            $unitRow = RekapCapaian::with('opd')
                ->where('tahun_anggaran_id', $this->filterTahunAnggaranId)
                ->where('bulan', $this->filterBulan)
                ->where('opd_id', $this->filterUnitId)
                ->get();

            // OPD anak langsung (parent_id)
            $childIds = Opd::where('parent_id', $this->filterUnitId)->pluck('id');

            // OPD yang memiliki indikator dengan asisten_id = unit terpilih
            $linkedIds = Indikator::where('asisten_id', $this->filterUnitId)
                ->whereNotNull('opd_id')
                ->pluck('opd_id')
                ->merge($childIds)
                ->unique();

            $childRows = RekapCapaian::with('opd')
                ->where('tahun_anggaran_id', $this->filterTahunAnggaranId)
                ->where('bulan', $this->filterBulan)
                ->whereIn('opd_id', $linkedIds)
                ->whereHas('opd', fn ($q) => $q->whereIn('type', ['opd', 'kabag']))
                ->orderBy(Opd::select('name')->whereColumn('opds.id', 'rekap_capaian.opd_id')->limit(1))
                ->get();

            return $unitRow->merge($childRows)->values();
        }

        // Mode normal: Sekda & Asisten di atas, OPD diurutkan abjad
        if (in_array($this->filterLevel, ['sekda', 'asisten'])) {
            return RekapCapaian::with('opd')
                ->where('tahun_anggaran_id', $this->filterTahunAnggaranId)
                ->where('bulan', $this->filterBulan)
                ->where('level', $this->filterLevel)
                ->orderByDesc('persentase')
                ->get();
        }

        // Jika mode "opd" default, tampilkan Sekda+Asisten dulu, kemudian OPD abjad
        if ($this->filterLevel === 'opd' && ! $this->filterUnitId) {
            $unitRows = RekapCapaian::with('opd')
                ->where('tahun_anggaran_id', $this->filterTahunAnggaranId)
                ->where('bulan', $this->filterBulan)
                ->whereIn('level', ['sekda', 'asisten'])
                ->get()
                ->sortBy(fn ($r) => match ($r->opd?->type) {
                    'sekda'   => '0_'.$r->opd->name,
                    'asisten' => '1_'.$r->opd->name,
                    default   => '9_'.$r->opd->name,
                });

            $opdRows = RekapCapaian::with('opd')
                ->where('tahun_anggaran_id', $this->filterTahunAnggaranId)
                ->where('bulan', $this->filterBulan)
                ->where('level', 'opd')
                ->orderBy(Opd::select('name')->whereColumn('opds.id', 'rekap_capaian.opd_id')->limit(1))
                ->get();

            return $unitRows->merge($opdRows)->values();
        }

        return $this->service->getByLevel($this->filterLevel, $this->filterTahunAnggaranId, $this->filterBulan);
    }

    #[Computed]
    public function ringkasan(): array
    {
        $data = $this->rekaps;

        if ($data->isEmpty()) {
            return ['rata_persentase' => 0, 'total_indikator' => 0, 'total_target' => 0, 'total_realisasi' => 0];
        }

        return [
            'rata_persentase' => round($data->avg('persentase'), 2),
            'total_indikator' => $data->sum('jumlah_indikator'),
            'total_target'    => $data->sum('total_target'),
            'total_realisasi' => $data->sum('total_realisasi'),
        ];
    }

    public function updatedFilterUnitId(): void
    {
        unset($this->rekaps, $this->ringkasan, $this->selectedUnit);
        // Jika pilih unit, paksa level ke 'opd' supaya hierarki tampil
        if ($this->filterUnitId) {
            $this->filterLevel = 'opd';
        }
    }

    public function hitungUlang(): void
    {
        $this->authorize('lihat-laporan-semua');

        if (! $this->filterTahunAnggaranId) {
            Flux::toast('Pilih tahun anggaran terlebih dahulu.');
            return;
        }

        $this->service->hitung($this->filterTahunAnggaranId, $this->filterBulan);
        unset($this->rekaps, $this->ringkasan);
        Flux::toast('Rekap capaian berhasil dihitung ulang.');
    }
};
?>

<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Rekap Capaian</flux:heading>
            <flux:text class="mt-1 text-zinc-500">
                @if($this->selectedUnit)
                    Overview <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $this->selectedUnit->name }}</span>
                    dan OPD di bawahnya.
                @else
                    Rangkuman capaian indikator kinerja — Sekda, Asisten, dan seluruh OPD.
                @endif
            </flux:text>
        </div>
        @can('lihat-laporan-semua')
            <flux:button
                variant="primary"
                icon="arrow-path"
                wire:click="hitungUlang"
                wire:loading.attr="disabled"
                wire:confirm="Hitung ulang rekap capaian untuk bulan ini?"
            >
                <span wire:loading.remove wire:target="hitungUlang">Hitung Ulang</span>
                <span wire:loading wire:target="hitungUlang">Menghitung...</span>
            </flux:button>
        @endcan
    </div>

    {{-- Filter Baris 1: Unit Pills --}}
    <div class="mb-4 flex items-center gap-2 flex-wrap">
        <span class="text-xs text-zinc-500 font-medium mr-1">Filter Unit:</span>
        <button
            wire:click="$set('filterUnitId', null)"
            class="px-3 py-1.5 rounded-full text-xs font-medium transition-all
                {{ $filterUnitId === null
                    ? 'bg-blue-600 text-white shadow'
                    : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
        >
            Semua
        </button>
        @foreach($this->unitOptions as $unit)
            <button
                wire:click="$set('filterUnitId', {{ $unit->id }})"
                class="px-3 py-1.5 rounded-full text-xs font-medium transition-all
                    {{ (int)$filterUnitId === $unit->id
                        ? 'bg-blue-600 text-white shadow'
                        : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
            >
                {{ $unit->type === 'sekda' ? '🏛' : '📋' }} {{ Str::limit($unit->name, 22) }}
            </button>
        @endforeach
    </div>

    {{-- Filter Baris 2: Tahun, Bulan, Level (hanya tampil jika tidak ada filter unit) --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3 max-w-2xl">
        <flux:field>
            <flux:label>Tahun Anggaran</flux:label>
            <flux:select wire:model.live="filterTahunAnggaranId">
                <flux:select.option value="">-- Pilih Tahun --</flux:select.option>
                @foreach ($this->tahunAnggarans as $tahun)
                    <flux:select.option wire:key="tahun-{{ $tahun->id }}" value="{{ $tahun->id }}">
                        {{ $tahun->tahun }}{{ $tahun->is_active ? ' (Aktif)' : '' }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>Bulan</flux:label>
            <flux:select wire:model.live="filterBulan">
                @php
                    $bulanList = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                                  7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                @endphp
                @foreach ($bulanList as $num => $nama)
                    <flux:select.option wire:key="bulan-{{ $num }}" value="{{ $num }}">{{ $nama }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        @if(! $filterUnitId)
        <flux:field>
            <flux:label>Level</flux:label>
            <flux:select wire:model.live="filterLevel">
                <flux:select.option value="opd">Default (Sekda + Asisten + OPD)</flux:select.option>
                <flux:select.option value="sekda">Sekda saja</flux:select.option>
                <flux:select.option value="asisten">Asisten saja</flux:select.option>
                <flux:select.option value="kabag">Kabag saja</flux:select.option>
                <flux:select.option value="bidang">Bidang saja</flux:select.option>
            </flux:select>
        </flux:field>
        @endif
    </div>

    {{-- Banner jika filter unit aktif --}}
    @if($this->selectedUnit)
        <div class="mb-4 rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-950/40 px-4 py-3 flex items-center gap-3">
            <span class="text-blue-600 text-2xl">
                {{ $this->selectedUnit->type === 'sekda' ? '🏛' : '📋' }}
            </span>
            <div>
                <p class="font-semibold text-blue-700 dark:text-blue-300 text-sm">
                    {{ $this->selectedUnit->name }}
                </p>
                <p class="text-xs text-blue-500 dark:text-blue-400">
                    Baris pertama = skor agregat unit ini. Baris berikutnya = OPD / Bagian di bawah koordinasinya.
                </p>
            </div>
        </div>
    @endif

    {{-- Kartu Ringkasan --}}
    @if ($filterTahunAnggaranId && $this->rekaps->isNotEmpty())
        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Rata-rata Capaian</div>
                @php
                    $rataPersentase = $this->ringkasan['rata_persentase'];
                    $persentaseClass = $rataPersentase >= 80 ? 'text-green-600' : ($rataPersentase >= 60 ? 'text-yellow-600' : 'text-red-600');
                @endphp
                <div class="text-2xl font-bold {{ $persentaseClass }}">
                    {{ number_format($rataPersentase, 2) }}%
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Total Indikator</div>
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ number_format($this->ringkasan['total_indikator']) }}
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Total Target</div>
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ number_format($this->ringkasan['total_target'], 2) }}
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Total Realisasi</div>
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ number_format($this->ringkasan['total_realisasi'], 2) }}
                </div>
            </div>
        </div>
    @endif

    {{-- Tabel Rekap --}}
    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">#</th>
                    <th class="px-4 py-3 text-left font-medium">Unit / OPD</th>
                    <th class="px-4 py-3 text-right font-medium">Jml. Indikator</th>
                    <th class="px-4 py-3 text-right font-medium">Indikator Tercapai</th>
                    <th class="px-4 py-3 text-right font-medium">Total Target</th>
                    <th class="px-4 py-3 text-right font-medium">Total Realisasi</th>
                    <th class="px-4 py-3 text-right font-medium">Persentase</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->rekaps as $i => $rekap)
                    @php
                        $pct        = (float) $rekap->persentase;
                        $pctClass   = $pct >= 80
                            ? 'text-green-600 dark:text-green-400'
                            : ($pct >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400');
                        $isTopUnit  = $filterUnitId && $i === 0;
                        $opdType    = $rekap->opd?->type;
                        $isUnitLvl  = in_array($opdType, ['sekda', 'asisten', 'kabag']);
                    @endphp
                    <tr
                        wire:key="rekap-{{ $rekap->id }}"
                        class="
                            {{ $isTopUnit
                                ? 'bg-blue-50 dark:bg-blue-950/40 border-l-4 border-l-blue-500'
                                : ($isUnitLvl
                                    ? 'bg-zinc-50/70 dark:bg-zinc-800/40'
                                    : 'bg-white dark:bg-zinc-900') }}
                            hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors
                        "
                    >
                        <td class="px-4 py-3 text-zinc-500">
                            @if($isTopUnit)
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-600 text-white text-xs font-bold">★</span>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($isTopUnit)
                                    <span class="text-blue-500">{{ $rekap->opd?->type === 'sekda' ? '🏛' : '📋' }}</span>
                                @elseif($isUnitLvl)
                                    <span class="text-zinc-400 text-xs">📋</span>
                                @endif
                                <div>
                                    <span class="font-{{ $isTopUnit ? 'bold' : 'medium' }} {{ $isTopUnit ? 'text-blue-700 dark:text-blue-300' : 'text-zinc-900 dark:text-zinc-100' }}">
                                        {{ $rekap->opd?->name ?? '-' }}
                                    </span>
                                    @if($isTopUnit)
                                        <div class="text-xs text-blue-500 dark:text-blue-400">Skor Agregat Unit</div>
                                    @elseif($isUnitLvl)
                                        <div class="text-xs text-zinc-400">{{ ucfirst($opdType) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-300">{{ number_format($rekap->jumlah_indikator) }}</td>
                        <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-300">{{ number_format($rekap->indikator_tercapai) }}</td>
                        <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-300">{{ number_format($rekap->total_target, 2) }}</td>
                        <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-300">{{ number_format($rekap->total_realisasi, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold {{ $pctClass }}">
                                {{ number_format($pct, 2) }}%
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-zinc-400">
                            @if (! $filterTahunAnggaranId)
                                Pilih tahun anggaran dan bulan untuk melihat rekap capaian.
                            @else
                                Belum ada data rekap untuk filter yang dipilih. Klik "Hitung Ulang" untuk menghitung.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
