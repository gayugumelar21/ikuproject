<?php

use App\Livewire\Forms\IndikatorForm;
use App\Models\Indikator;
use App\Models\Opd;
use App\Models\TahunAnggaran;
use App\Models\User;
use App\Services\IndikatorService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Flux\Flux;

new class extends Component {
    public IndikatorForm $form;

    public ?int $filterTahunAnggaranId = null;
    public ?int $filterOpdId = null;
    public bool $isEditing = false;

    private IndikatorService $service;

    public function boot(IndikatorService $service): void
    {
        $this->service = $service;
    }

    public function mount(): void
    {
        abort_unless(
            auth()
                ->user()
                ->hasAnyRole(['kepala_bidang', 'kabag', 'kepala_dinas', 'asisten', 'sekda', 'bupati', 'admin_super']),
            403,
        );
        $aktif = TahunAnggaran::aktif()->first();
        $this->filterTahunAnggaranId = $aktif?->id;
    }

    #[Computed]
    public function tahunAnggarans(): \Illuminate\Support\Collection
    {
        return TahunAnggaran::orderByDesc('tahun')->get();
    }

    #[Computed]
    public function indikators(): \Illuminate\Support\Collection
    {
        if (!$this->filterTahunAnggaranId) {
            return collect();
        }

        return Indikator::with(['sekda', 'asisten', 'kabag', 'opd', 'bidang', 'dibuatOleh', 'owner', 'kerjasamas.opd'])
            ->where('tahun_anggaran_id', $this->filterTahunAnggaranId)
            ->where('category', 'utama')
            ->when($this->filterOpdId, function ($q) {
                $opd = Opd::find($this->filterOpdId);
                if (!$opd) {
                    return $q;
                }
                return match ($opd->type) {
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
        return Opd::whereIn('type', ['sekda', 'asisten', 'opd', 'kabag'])
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function sekdas(): \Illuminate\Support\Collection
    {
        return Opd::sekda()->orderBy('name')->get();
    }

    #[Computed]
    public function asistens(): \Illuminate\Support\Collection
    {
        if (!$this->form->sekda_id) {
            return collect();
        }

        return Opd::asisten()->orderBy('name')->get();
    }

    #[Computed]
    public function opds(): \Illuminate\Support\Collection
    {
        if (!$this->form->asisten_id) {
            return collect();
        }

        return Opd::opd()->orderBy('name')->get();
    }

    #[Computed]
    public function bidangs(): \Illuminate\Support\Collection
    {
        if (!$this->form->opd_id) {
            return collect();
        }

        return Opd::bidang()->orderBy('name')->get();
    }

    #[Computed]
    public function kabags(): \Illuminate\Support\Collection
    {
        return Opd::kabag()->orderBy('name')->get();
    }

    #[Computed]
    public function usersForSelect(): \Illuminate\Support\Collection
    {
        return User::orderBy('name')->get();
    }

    public function updatedFormSekdaId(): void
    {
        $this->form->asisten_id = null;
        $this->form->opd_id = null;
        $this->form->bidang_id = null;
        unset($this->asistens, $this->opds, $this->bidangs);
    }

    public function updatedFormAsistenId(): void
    {
        $this->form->opd_id = null;
        $this->form->bidang_id = null;
        unset($this->opds, $this->bidangs);
    }

    public function updatedFormOpdId(): void
    {
        $this->form->bidang_id = null;
        unset($this->bidangs);
    }

    public function bukaModalBuat(): void
    {
        $this->authorize('buat-indikator');
        $this->isEditing = false;
        $this->form->reset();
        $this->form->tahun_anggaran_id = $this->filterTahunAnggaranId;
        $this->form->category = 'utama';
        $this->form->source_indikator_id = null;
        Flux::modal('indikator-modal')->show();
    }

    public function bukaModalEdit(int $id): void
    {
        $this->authorize('edit-indikator');
        $this->isEditing = true;
        $indikator = Indikator::findOrFail($id);
        $this->form->setIndikator($indikator);
        unset($this->asistens, $this->opds, $this->bidangs);
        Flux::modal('indikator-modal')->show();
    }

    public function simpan(): void
    {
        if ($this->isEditing) {
            $this->authorize('edit-indikator');
        } else {
            $this->authorize('buat-indikator');
        }

        $data = $this->form->validate();
        $data['category'] = 'utama';
        $data['source_indikator_id'] = null;

        if ($this->isEditing) {
            $indikator = Indikator::findOrFail($this->form->indikatorId);
            $this->service->update($indikator, $data);
            Flux::toast('Indikator berhasil diperbarui.');
        } else {
            $this->service->store($data);
            Flux::toast('Indikator berhasil dibuat.');
        }

        unset($this->indikators);
        Flux::modal('indikator-modal')->close();
    }

    public function hapus(int $id): void
    {
        $this->authorize('hapus-indikator');
        $indikator = Indikator::findOrFail($id);
        $this->service->delete($indikator);
        unset($this->indikators);
        Flux::toast('Indikator berhasil dihapus.');
    }

    public function ajukan(int $id): void
    {
        $this->authorize('ajukan-indikator');
        $indikator = Indikator::findOrFail($id);
        $this->service->ajukan($indikator);
        unset($this->indikators);
        Flux::toast('Indikator berhasil diajukan.');
    }
};
?>

<div>
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">Manajemen Indikator Kinerja Utama</flux:heading>
        @can('buat-indikator')
            <flux:button variant="primary" icon="plus" wire:click="bukaModalBuat">
                Tambah Indikator
            </flux:button>
        @endcan
    </div>

    <div class="mb-6 flex flex-wrap gap-4">
        <flux:field class="max-w-xs w-full">
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

        <flux:field class="max-w-xs w-full">
            <flux:label>Filter Unit / OPD</flux:label>
            <flux:select wire:model.live="filterOpdId">
                <flux:select.option value="">-- Semua Unit --</flux:select.option>
                @foreach ($this->filterOpds as $opd)
                    <flux:select.option wire:key="filter-opd-{{ $opd->id }}" value="{{ $opd->id }}">
                        {{ $opd->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>
    </div>

    {{-- Tabel Indikator --}}
    <div class="overflow-x-auto">
        <table class="w-full min-w-[600px] text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-xs">
                <tr>
                    <th class="px-3 py-3 text-left font-medium w-8">#</th>
                    <th class="px-3 py-3 text-left font-medium">Nama Indikator</th>
                    <th class="px-3 py-3 text-left font-medium hidden md:table-cell">Tipe</th>
                    <th class="px-3 py-3 text-right font-medium hidden sm:table-cell whitespace-nowrap">Target</th>
                    <th class="px-3 py-3 text-right font-medium whitespace-nowrap">Bobot</th>
                    <th class="px-3 py-3 text-left font-medium hidden lg:table-cell">Jalur</th>
                    <th class="px-3 py-3 text-center font-medium">Status</th>
                    <th class="px-3 py-3 text-center font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->indikators as $i => $indikator)
                    <tr wire:key="indikator-{{ $indikator->id }}"
                        class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-3 py-3 text-zinc-500 text-xs w-8">{{ $i + 1 }}</td>
                        <td class="px-3 py-3">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100 text-sm line-clamp-2">
                                {{ $indikator->nama }}</div>
                            <div class="text-xs text-zinc-500 mt-0.5">
                                {{ $indikator->opd?->name ?? '-' }}
                                @if ($indikator->satuan)
                                    · {{ $indikator->satuan }}
                                @endif
                            </div>
                            @if ($indikator->kerjasamas->isNotEmpty())
                                <div class="text-xs text-amber-500 mt-0.5">
                                    Kerjasama: {{ $indikator->kerjasamas->pluck('opd.name')->filter()->implode(', ') }}
                                </div>
                            @endif
                            <div class="flex gap-2 mt-1 md:hidden">
                                @if ($indikator->measurement_type === 'kualitatif')
                                    <flux:badge variant="blue" size="sm">Kualitatif</flux:badge>
                                @else
                                    <flux:badge variant="green" size="sm">Kuantitatif</flux:badge>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 py-3 hidden md:table-cell">
                            @if ($indikator->measurement_type === 'kualitatif')
                                <flux:badge variant="blue" size="sm">Kualitatif</flux:badge>
                            @else
                                <flux:badge variant="green" size="sm">Kuantitatif</flux:badge>
                            @endif
                        </td>
                        <td
                            class="px-3 py-3 text-right text-zinc-600 dark:text-zinc-300 hidden sm:table-cell whitespace-nowrap text-xs">
                            @if ($indikator->measurement_type === 'kuantitatif')
                                {{ number_format($indikator->target, 2) }}
                            @else
                                <span class="text-zinc-400 italic">-</span>
                            @endif
                        </td>
                        <td
                            class="px-3 py-3 text-right font-semibold text-zinc-700 dark:text-zinc-200 whitespace-nowrap text-xs">
                            {{ number_format($indikator->bobot, 1) }}%</td>
                        <td class="px-3 py-3 text-xs text-zinc-500 dark:text-zinc-400 hidden lg:table-cell">
                            @if ($indikator->asisten)
                                <div class="truncate max-w-[140px]">{{ $indikator->asisten->name }}</div>
                            @endif
                            @if ($indikator->opd)
                                <div class="truncate max-w-[140px] text-zinc-400">{{ $indikator->opd->name }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center whitespace-nowrap">
                            @php
                                $badgeVariant = match ($indikator->status) {
                                    'draft' => 'zinc',
                                    'diajukan' => 'blue',
                                    'disetujui' => 'green',
                                    'ditolak' => 'red',
                                    default => 'zinc',
                                };
                            @endphp
                            <flux:badge variant="{{ $badgeVariant }}" size="sm">{{ ucfirst($indikator->status) }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                @can('ajukan-indikator')
                                    @if ($indikator->status === 'draft')
                                        <flux:button size="xs" variant="ghost"
                                            wire:click="ajukan({{ $indikator->id }})" wire:confirm="Ajukan indikator ini?">
                                            Ajukan
                                        </flux:button>
                                    @endif
                                @endcan
                                @can('edit-indikator')
                                    <flux:button size="xs" icon="pencil" variant="ghost"
                                        wire:click="bukaModalEdit({{ $indikator->id }})" />
                                @endcan
                                @can('hapus-indikator')
                                    <flux:button size="xs" icon="trash" variant="ghost"
                                        class="text-red-500 hover:text-red-700" wire:click="hapus({{ $indikator->id }})"
                                        wire:confirm="Yakin ingin menghapus indikator ini?" />
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-4 py-10 text-center text-zinc-400">
                            @if (!$this->filterTahunAnggaranId)
                                Pilih tahun anggaran untuk melihat data indikator.
                            @else
                                Belum ada indikator untuk tahun anggaran ini.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Buat / Edit --}}
    <flux:modal name="indikator-modal" class="w-full max-w-2xl">
        <div class="space-y-5">
            <flux:heading size="lg">
                {{ $isEditing ? 'Edit Indikator' : 'Tambah Indikator Baru' }}
            </flux:heading>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Tahun Anggaran <flux:badge size="sm" variant="blue">Wajib</flux:badge>
                    </flux:label>
                    <flux:select wire:model="form.tahun_anggaran_id">
                        <flux:select.option value="">-- Pilih Tahun --</flux:select.option>
                        @foreach ($this->tahunAnggarans as $tahun)
                            <flux:select.option wire:key="modal-tahun-{{ $tahun->id }}"
                                value="{{ $tahun->id }}">
                                {{ $tahun->tahun }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.tahun_anggaran_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Sekda <flux:badge size="sm" variant="blue">Wajib</flux:badge>
                    </flux:label>
                    <flux:select wire:model.live="form.sekda_id">
                        <flux:select.option value="">-- Pilih Sekda --</flux:select.option>
                        @foreach ($this->sekdas as $sekda)
                            <flux:select.option wire:key="sekda-{{ $sekda->id }}" value="{{ $sekda->id }}">
                                {{ $sekda->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.sekda_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Asisten</flux:label>
                    <flux:select wire:model.live="form.asisten_id" :disabled="! $form->sekda_id">
                        <flux:select.option value="">-- Pilih Asisten --</flux:select.option>
                        @foreach ($this->asistens as $asisten)
                            <flux:select.option wire:key="asisten-{{ $asisten->id }}" value="{{ $asisten->id }}">
                                {{ $asisten->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.asisten_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Kabag</flux:label>
                    <flux:select wire:model="form.kabag_id">
                        <flux:select.option value="">-- Pilih Kabag --</flux:select.option>
                        @foreach ($this->kabags as $kabag)
                            <flux:select.option wire:key="kabag-{{ $kabag->id }}" value="{{ $kabag->id }}">
                                {{ $kabag->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.kabag_id" />
                </flux:field>

                <flux:field>
                    <flux:label>OPD</flux:label>
                    <flux:select wire:model.live="form.opd_id" :disabled="! $form->asisten_id">
                        <flux:select.option value="">-- Pilih OPD --</flux:select.option>
                        @foreach ($this->opds as $opd)
                            <flux:select.option wire:key="opd-{{ $opd->id }}" value="{{ $opd->id }}">
                                {{ $opd->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.opd_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Bidang</flux:label>
                    <flux:select wire:model="form.bidang_id" :disabled="! $form->opd_id">
                        <flux:select.option value="">-- Pilih Bidang --</flux:select.option>
                        @foreach ($this->bidangs as $bidang)
                            <flux:select.option wire:key="bidang-{{ $bidang->id }}" value="{{ $bidang->id }}">
                                {{ $bidang->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.bidang_id" />
                </flux:field>
            </div>

            {{-- Owner Indikator --}}
            <flux:field>
                <flux:label>Pemilik Indikator (Kabid/Kabag) <flux:badge size="sm" variant="blue">Wajib
                    </flux:badge>
                </flux:label>
                <flux:select wire:model="form.owner_user_id">
                    <flux:select.option value="">-- Pilih Owner --</flux:select.option>
                    @foreach ($this->usersForSelect as $user)
                        <flux:select.option value="{{ $user->id }}">
                            {{ $user->name }} ({{ $user->getRoleNames()->first() ?? '-' }})
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:description>Pilih Kepala Bidang atau Kabag yang bertanggung jawab atas indikator ini.
                </flux:description>
                <flux:error name="form.owner_user_id" />
            </flux:field>

            <flux:field>
                <flux:label>Nama Indikator <flux:badge size="sm" variant="blue">Wajib</flux:badge>
                </flux:label>
                <flux:input wire:model="form.nama" placeholder="Nama indikator kinerja utama" />
                <flux:error name="form.nama" />
            </flux:field>

            <flux:field>
                <flux:label>Definisi</flux:label>
                <flux:textarea wire:model="form.definisi" rows="3"
                    placeholder="Deskripsi / definisi indikator" />
                <flux:error name="form.definisi" />
            </flux:field>

            <flux:field>
                <flux:label>Tipe Pengukuran <flux:badge size="sm" variant="blue">Wajib</flux:badge>
                </flux:label>
                <flux:select wire:model.live="form.measurement_type">
                    <flux:select.option value="kuantitatif">Kuantitatif (Angka)</flux:select.option>
                    <flux:select.option value="kualitatif">Kualitatif (Deskripsi)</flux:select.option>
                </flux:select>
                <flux:error name="form.measurement_type" />
            </flux:field>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Satuan</flux:label>
                    <flux:input wire:model="form.satuan" placeholder="Persen, Dokumen, Indeks, dll." />
                    <flux:error name="form.satuan" />
                </flux:field>
            </div>

            @if ($form->measurement_type === 'kuantitatif')
                <flux:field>
                    <flux:label>Target Tahunan <flux:badge size="sm" variant="blue">Wajib</flux:badge>
                    </flux:label>
                    <flux:input type="number" wire:model="form.target" min="0" step="0.01" />
                    <flux:error name="form.target" />
                </flux:field>
            @endif

            <flux:field>
                <flux:label>Bobot (%) <flux:badge size="sm" variant="blue">Wajib</flux:badge>
                </flux:label>
                <flux:input type="number" wire:model="form.bobot" min="0" max="100" step="0.01" />
                <flux:description>Total bobot semua indikator dalam 1 OPD harus = 100%.</flux:description>
                <flux:error name="form.bobot" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button variant="ghost" x-on:click="$flux.modal('indikator-modal').close()">
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
