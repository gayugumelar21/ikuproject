<?php

use App\Models\RekapCapaian;
use App\Models\TahunAnggaran;
use App\Services\RekapCapaianService;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public ?int $filterTahunAnggaranId = null;
    public int $filterBulan = 1;
    public string $filterLevel = 'opd';

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

    #[Computed]
    public function rekaps(): \Illuminate\Database\Eloquent\Collection
    {
        if (! $this->filterTahunAnggaranId) {
            return collect();
        }

        return $this->service->getByLevel($this->filterLevel, $this->filterTahunAnggaranId, $this->filterBulan);
    }

    #[Computed]
    public function ringkasan(): array
    {
        $data = $this->rekaps;

        if ($data->isEmpty()) {
            return [
                'rata_persentase' => 0,
                'total_indikator' => 0,
                'total_target' => 0,
                'total_realisasi' => 0,
            ];
        }

        return [
            'rata_persentase' => round($data->avg('persentase'), 2),
            'total_indikator' => $data->sum('jumlah_indikator'),
            'total_target' => $data->sum('total_target'),
            'total_realisasi' => $data->sum('total_realisasi'),
        ];
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
            <flux:text class="mt-1 text-zinc-500">Rangkuman capaian indikator kinerja per OPD.</flux:text>
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

    {{-- Filter --}}
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

        <flux:field>
            <flux:label>Level</flux:label>
            <flux:select wire:model.live="filterLevel">
                <flux:select.option value="sekda">Sekda</flux:select.option>
                <flux:select.option value="asisten">Asisten</flux:select.option>
                <flux:select.option value="kabag">Kabag</flux:select.option>
                <flux:select.option value="opd">OPD</flux:select.option>
                <flux:select.option value="bidang">Bidang</flux:select.option>
            </flux:select>
        </flux:field>
    </div>

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
                    <th class="px-4 py-3 text-left font-medium">Nama OPD</th>
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
                        $pct = (float) $rekap->persentase;
                        $pctClass = $pct >= 80
                            ? 'text-green-600 dark:text-green-400'
                            : ($pct >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400');
                    @endphp
                    <tr wire:key="rekap-{{ $rekap->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-4 py-3 text-zinc-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $rekap->opd?->name ?? '-' }}</td>
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
