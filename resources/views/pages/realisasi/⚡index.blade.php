<?php

use App\Livewire\Forms\RealisasiForm;
use App\Models\Indikator;
use App\Models\Realisasi;
use App\Models\TargetIndikator;
use App\Models\TahunAnggaran;
use App\Services\RealisasiService;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public RealisasiForm $form;

    public ?int $filterTahunAnggaranId = null;
    public int $filterBulan = 1;
    public bool $isEditing = false;

    private RealisasiService $service;

    public function boot(RealisasiService $service): void
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
    public function tahunAnggarans(): \Illuminate\Support\Collection
    {
        return TahunAnggaran::orderByDesc('tahun')->get();
    }

    #[Computed]
    public function indikators(): \Illuminate\Support\Collection
    {
        if (! $this->filterTahunAnggaranId) {
            return collect();
        }

        return Indikator::with(['opd', 'bidang', 'sourceIndikator.opd', 'realisasi' => fn ($q) => $q->where('bulan', $this->filterBulan)])
            ->where('tahun_anggaran_id', $this->filterTahunAnggaranId)
            ->orderBy('category')
            ->orderBy('nama')
            ->get();
    }

    #[Computed]
    public function indikatorOptions(): \Illuminate\Support\Collection
    {
        if (! $this->filterTahunAnggaranId) {
            return collect();
        }

        return Indikator::where('tahun_anggaran_id', $this->filterTahunAnggaranId)
            ->orderBy('nama')
            ->get();
    }

    public function bukaModalInput(int $indikatorId): void
    {
        $this->authorize('input-realisasi');
        $this->isEditing = false;
        $this->form->reset();
        $this->form->indikator_id = $indikatorId;
        $this->form->bulan = $this->filterBulan;
        Flux::modal('realisasi-modal')->show();
    }

    public function bukaModalEdit(int $id): void
    {
        $this->authorize('input-realisasi');
        $this->isEditing = true;
        $realisasi = Realisasi::findOrFail($id);
        $this->form->setRealisasi($realisasi);
        Flux::modal('realisasi-modal')->show();
    }

    public function simpan(): void
    {
        $this->authorize('input-realisasi');

        $data = $this->form->validate();

        if ($this->isEditing) {
            $realisasi = Realisasi::findOrFail($this->form->realisasiId);
            $this->service->update($realisasi, $data);
            Flux::toast('Realisasi berhasil diperbarui.');
        } else {
            $this->service->store($data);
            Flux::toast('Realisasi berhasil disimpan.');
        }

        unset($this->indikators);
        Flux::modal('realisasi-modal')->close();
    }

    public function ajukan(int $id): void
    {
        $this->authorize('input-realisasi');
        $realisasi = Realisasi::findOrFail($id);
        $this->service->ajukan($realisasi);
        unset($this->indikators);
        Flux::toast('Realisasi berhasil diajukan.');
    }

    public function verifikasi(int $id): void
    {
        $this->authorize('verifikasi-realisasi');
        $realisasi = Realisasi::findOrFail($id);
        $this->service->verifikasi($realisasi);
        unset($this->indikators);
        Flux::toast('Realisasi berhasil diverifikasi.');
    }

    private function namaBulan(int $bulan): string
    {
        return match ($bulan) {
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            default => '-',
        };
    }
};
?>

<div>
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">Input Realisasi</flux:heading>
    </div>

    {{-- Filter --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 max-w-xl">
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
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">#</th>
                    <th class="px-4 py-3 text-left font-medium">Nama Indikator</th>
                    <th class="px-4 py-3 text-center font-medium">Kategori</th>
                    <th class="px-4 py-3 text-left font-medium">OPD</th>
                    <th class="px-4 py-3 text-right font-medium">Target Bulan Ini</th>
                    <th class="px-4 py-3 text-right font-medium">Realisasi</th>
                    <th class="px-4 py-3 text-right font-medium">Persentase</th>
                    <th class="px-4 py-3 text-center font-medium">Status</th>
                    <th class="px-4 py-3 text-center font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->indikators as $i => $indikator)
                    @php
                        $isKerjasama = $indikator->category === 'kerjasama';
                        $realisasi = $indikator->realisasi->first();
                        $targetBulan = TargetIndikator::where('indikator_id', $indikator->id)
                            ->where('bulan', $filterBulan)
                            ->value('target') ?? 0;
                        $nilaiRealisasi = $realisasi?->nilai ?? null;
                        $persentase = ($targetBulan > 0 && $nilaiRealisasi !== null)
                            ? round(($nilaiRealisasi / $targetBulan) * 100, 2)
                            : null;
                        $status = $realisasi?->status ?? null;
                        $badgeVariant = match ($status) {
                            'draft' => 'zinc',
                            'diajukan' => 'blue',
                            'diverifikasi' => 'green',
                            default => 'zinc',
                        };
                    @endphp
                    <tr wire:key="row-{{ $indikator->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors {{ $isKerjasama ? 'border-l-4 border-l-purple-400' : '' }}">
                        <td class="px-4 py-3 text-zinc-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $indikator->nama }}</div>
                            @if ($isKerjasama && $indikator->sourceIndikator)
                                <div class="text-xs text-purple-500 mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    Mirror dari: {{ $indikator->sourceIndikator->opd?->name ?? '-' }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($isKerjasama)
                                <flux:badge variant="purple" size="sm">🤝 Kerjasama</flux:badge>
                            @else
                                <flux:badge variant="zinc" size="sm">Utama</flux:badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-500">{{ $indikator->opd?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-300">
                            {{ $targetBulan ? number_format($targetBulan, 2) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-300">
                            {{ $nilaiRealisasi !== null ? number_format($nilaiRealisasi, 2) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($persentase !== null)
                                <span class="font-semibold {{ $persentase >= 80 ? 'text-green-600' : ($persentase >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($persentase, 2) }}%
                                </span>
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($status)
                                <flux:badge variant="{{ $badgeVariant }}" size="sm">{{ ucfirst($status) }}</flux:badge>
                            @else
                                <span class="text-zinc-400 text-xs">Belum diinput</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                @if ($isKerjasama)
                                    {{-- IKU Kerjasama: tidak bisa diinput manual, skor otomatis dari IKU sumber --}}
                                    <span class="text-xs text-purple-400 italic flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        Auto Mirror
                                    </span>
                                @else
                                    @if (! $realisasi)
                                        @can('input-realisasi')
                                            <flux:button size="xs" variant="ghost" icon="plus" wire:click="bukaModalInput({{ $indikator->id }})">
                                                Input
                                            </flux:button>
                                        @endcan
                                    @else
                                        @can('input-realisasi')
                                            @if ($realisasi->status === 'draft')
                                                <flux:button size="xs" variant="ghost" icon="pencil" wire:click="bukaModalEdit({{ $realisasi->id }})" />
                                                <flux:button size="xs" variant="ghost" wire:click="ajukan({{ $realisasi->id }})" wire:confirm="Ajukan realisasi ini?">
                                                    Ajukan
                                                </flux:button>
                                            @endif
                                        @endcan
                                        @can('verifikasi-realisasi')
                                            @if ($realisasi->status === 'diajukan')
                                                <flux:button size="xs" variant="ghost" wire:click="verifikasi({{ $realisasi->id }})" wire:confirm="Verifikasi realisasi ini?">
                                                    Verifikasi
                                                </flux:button>
                                            @endif
                                        @endcan
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center text-zinc-400">
                            @if (! $filterTahunAnggaranId)
                                Pilih tahun anggaran dan bulan untuk melihat data realisasi.
                            @else
                                Tidak ada indikator pada tahun anggaran ini.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Input / Edit Realisasi --}}
    <flux:modal name="realisasi-modal" class="w-full max-w-lg">
        <div class="space-y-5">
            <flux:heading size="lg">
                {{ $isEditing ? 'Edit Realisasi' : 'Input Realisasi' }}
            </flux:heading>

            <flux:field>
                <flux:label>Indikator <flux:badge size="sm" variant="blue">Wajib</flux:badge></flux:label>
                <flux:select wire:model="form.indikator_id" :disabled="$isEditing">
                    <flux:select.option value="">-- Pilih Indikator --</flux:select.option>
                    @foreach ($this->indikatorOptions as $ind)
                        <flux:select.option wire:key="modal-ind-{{ $ind->id }}" value="{{ $ind->id }}">
                            {{ $ind->nama }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="form.indikator_id" />
            </flux:field>

            <flux:field>
                <flux:label>Bulan <flux:badge size="sm" variant="blue">Wajib</flux:badge></flux:label>
                <flux:select wire:model="form.bulan" :disabled="$isEditing">
                    @php
                        $bulanList = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                                      7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                    @endphp
                    @foreach ($bulanList as $num => $nama)
                        <flux:select.option wire:key="modal-bulan-{{ $num }}" value="{{ $num }}">{{ $nama }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="form.bulan" />
            </flux:field>

            <flux:field>
                <flux:label>Nilai Realisasi <flux:badge size="sm" variant="blue">Wajib</flux:badge></flux:label>
                <flux:input type="number" wire:model="form.nilai" min="0" step="0.01" placeholder="0.00" />
                <flux:error name="form.nilai" />
            </flux:field>

            <flux:field>
                <flux:label>Keterangan</flux:label>
                <flux:textarea wire:model="form.keterangan" rows="3" placeholder="Catatan atau keterangan tambahan" />
                <flux:error name="form.keterangan" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button variant="ghost" x-on:click="$flux.modal('realisasi-modal').close()">
                    Batal
                </flux:button>
                <flux:button variant="primary" wire:click="simpan" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="simpan">Simpan</span>
                    <span wire:loading wire:target="simpan">Menyimpan...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
