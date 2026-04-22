<?php

use App\Models\Indikator;
use App\Models\TargetIndikator;
use App\Models\TahunAnggaran;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Flux\Flux;

new class extends Component
{
    public ?int $tahunAnggaranId = null;
    public ?int $filterOpdId = null;
    public ?int $indikatorId = null;

    /** @var array<int, array{nilai: string, deskripsi: string}> */
    public array $targets = [];

    public function mount(): void
    {
        for ($b = 1; $b <= 12; $b++) {
            $this->targets[$b] = ['nilai' => '', 'deskripsi' => ''];
        }
        $aktif = TahunAnggaran::aktif()->first();
        $this->tahunAnggaranId = $aktif?->id;
    }

    #[Computed]
    public function tahunAnggarans(): \Illuminate\Support\Collection
    {
        return TahunAnggaran::orderByDesc('tahun')->get();
    }

    #[Computed]
    public function indikators(): \Illuminate\Support\Collection
    {
        if (! $this->tahunAnggaranId) {
            return collect();
        }

        return Indikator::where('tahun_anggaran_id', $this->tahunAnggaranId)
            ->where('category', 'utama')
            ->when($this->filterOpdId, function ($q) {
                $opd = \App\Models\Opd::find($this->filterOpdId);
                if (!$opd) return $q;
                return match($opd->type) {
                    'sekda' => $q->where('sekda_id', $opd->id),
                    'asisten' => $q->where('asisten_id', $opd->id),
                    'opd' => $q->where('opd_id', $opd->id),
                    'kabag' => $q->where('kabag_id', $opd->id),
                    default => $q->where('opd_id', $opd->id),
                };
            })
            ->orderBy('nama')
            ->get();
    }

    #[Computed]
    public function filterOpds(): \Illuminate\Support\Collection
    {
        return \App\Models\Opd::whereIn('type', ['sekda', 'asisten', 'opd', 'kabag'])->orderBy('name')->get();
    }

    #[Computed]
    public function indikatorTerpilih(): ?Indikator
    {
        if (! $this->indikatorId) {
            return null;
        }

        return Indikator::find($this->indikatorId);
    }

    public function updatedTahunAnggaranId(): void
    {
        $this->filterOpdId = null;
        $this->indikatorId = null;
        for ($b = 1; $b <= 12; $b++) {
            $this->targets[$b] = ['nilai' => '', 'deskripsi' => ''];
        }
        unset($this->indikators, $this->indikatorTerpilih);
    }

    public function updatedFilterOpdId(): void
    {
        $this->indikatorId = null;
        for ($b = 1; $b <= 12; $b++) {
            $this->targets[$b] = ['nilai' => '', 'deskripsi' => ''];
        }
        unset($this->indikators, $this->indikatorTerpilih);
    }

    public function updatedIndikatorId(): void
    {
        for ($b = 1; $b <= 12; $b++) {
            $this->targets[$b] = ['nilai' => '', 'deskripsi' => ''];
        }
        unset($this->indikatorTerpilih);

        if (! $this->indikatorId) {
            return;
        }

        $existing = TargetIndikator::where('indikator_id', $this->indikatorId)->get();

        foreach ($existing as $t) {
            $this->targets[$t->bulan] = [
                'nilai'    => $t->target ?? '',
                'deskripsi' => $t->target_description ?? '',
            ];
        }
    }

    public function simpanTarget(): void
    {
        $this->authorize('buat-indikator');

        $this->validate([
            'indikatorId'       => ['required', 'exists:indikators,id'],
            'targets'           => ['required', 'array'],
            'targets.*.nilai'   => ['nullable', 'numeric', 'min:0'],
            'targets.*.deskripsi' => ['nullable', 'string'],
        ]);

        $indikator = Indikator::findOrFail($this->indikatorId);
        $isKualitatif = $indikator->measurement_type === 'kualitatif';

        foreach ($this->targets as $bulan => $data) {
            TargetIndikator::updateOrCreate(
                ['indikator_id' => $this->indikatorId, 'bulan' => $bulan],
                [
                    'target'             => $isKualitatif ? null : ($data['nilai'] !== '' ? (float) $data['nilai'] : null),
                    'target_description' => $isKualitatif ? ($data['deskripsi'] ?? '') : null,
                ]
            );
        }

        Flux::toast('Target bulanan berhasil disimpan.');
    }
};
?>

<div>
    <div class="mb-6">
        <flux:heading size="xl">Target Indikator Bulanan</flux:heading>
        <flux:text class="mt-1 text-zinc-500">Atur target per bulan untuk setiap indikator kinerja utama.</flux:text>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3 max-w-4xl">
        <flux:field>
            <flux:label>Tahun Anggaran</flux:label>
            <flux:select wire:model.live="tahunAnggaranId">
                <flux:select.option value="">-- Pilih Tahun --</flux:select.option>
                @foreach ($this->tahunAnggarans as $tahun)
                    <flux:select.option wire:key="tahun-{{ $tahun->id }}" value="{{ $tahun->id }}">
                        {{ $tahun->tahun }}{{ $tahun->is_active ? ' (Aktif)' : '' }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>Unit / OPD</flux:label>
            <flux:select wire:model.live="filterOpdId" :disabled="! $tahunAnggaranId">
                <flux:select.option value="">-- Semua Unit --</flux:select.option>
                @foreach ($this->filterOpds as $opd)
                    <flux:select.option wire:key="filter-opd-{{ $opd->id }}" value="{{ $opd->id }}">
                        {{ $opd->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>Indikator</flux:label>
            <flux:select wire:model.live="indikatorId" :disabled="! $tahunAnggaranId">
                <flux:select.option value="">-- Pilih Indikator --</flux:select.option>
                @foreach ($this->indikators as $indikator)
                    <flux:select.option wire:key="ind-{{ $indikator->id }}" value="{{ $indikator->id }}">
                        {{ $indikator->nama }} [{{ $indikator->measurement_type === 'kualitatif' ? 'Kualitatif' : 'Kuantitatif' }}]
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>
    </div>

    @if ($indikatorId && $this->indikatorTerpilih)
        {{-- Info Indikator --}}
        <div class="mb-6 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-sm">
                <div>
                    <span class="font-medium text-zinc-500 dark:text-zinc-400">Nama:</span>
                    <span class="ml-1 text-zinc-900 dark:text-zinc-100">{{ $this->indikatorTerpilih->nama }}</span>
                </div>
                <div>
                    <span class="font-medium text-zinc-500 dark:text-zinc-400">Satuan:</span>
                    <span class="ml-1 text-zinc-900 dark:text-zinc-100">{{ $this->indikatorTerpilih->satuan ?: '-' }}</span>
                </div>
                <div>
                    <span class="font-medium text-zinc-500 dark:text-zinc-400">Tipe:</span>
                    <span class="ml-1">
                        @if ($this->indikatorTerpilih->measurement_type === 'kualitatif')
                            <flux:badge variant="blue" size="sm">Kualitatif</flux:badge>
                        @else
                            <flux:badge variant="green" size="sm">Kuantitatif</flux:badge>
                        @endif
                    </span>
                </div>
                <div>
                    <span class="font-medium text-zinc-500 dark:text-zinc-400">Bobot:</span>
                    <span class="ml-1 font-semibold text-zinc-900 dark:text-zinc-100">{{ $this->indikatorTerpilih->bobot }}%</span>
                </div>
            </div>
        </div>

        {{-- Form 12 Bulan --}}
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
            <flux:heading size="lg" class="mb-5">Input Target Per Bulan</flux:heading>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @for ($bulan = 1; $bulan <= 12; $bulan++)
                    <flux:field wire:key="bulan-field-{{ $bulan }}">
                        <flux:label>
                            {{ match ($bulan) {
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            } }}
                        </flux:label>
                        @if ($this->indikatorTerpilih->measurement_type === 'kualitatif')
                            <flux:textarea
                                wire:model="targets.{{ $bulan }}.deskripsi"
                                rows="2"
                                placeholder="Deskripsi target bulan ini..."
                            />
                            <flux:error name="targets.{{ $bulan }}.deskripsi" />
                        @else
                            <flux:input
                                type="number"
                                wire:model="targets.{{ $bulan }}.nilai"
                                min="0"
                                step="0.01"
                                placeholder="0"
                            />
                            <flux:error name="targets.{{ $bulan }}.nilai" />
                        @endif
                    </flux:field>
                @endfor
            </div>

            @can('buat-indikator')
                <div class="mt-6 flex justify-end">
                    <flux:button
                        variant="primary"
                        icon="check"
                        wire:click="simpanTarget"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="simpanTarget">Simpan Semua Target</span>
                        <span wire:loading wire:target="simpanTarget">Menyimpan...</span>
                    </flux:button>
                </div>
            @endcan
        </div>
    @else
        <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 py-16 text-center">
            <flux:icon name="chart-bar" class="h-12 w-12 text-zinc-300 dark:text-zinc-600 mb-3" />
            <flux:text class="text-zinc-400">
                Pilih tahun anggaran dan indikator untuk mengatur target bulanan.
            </flux:text>
        </div>
    @endif
</div>
