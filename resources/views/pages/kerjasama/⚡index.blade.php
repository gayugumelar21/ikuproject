<?php

use App\Models\Indikator;
use App\Models\IndikatorKerjasama;
use App\Models\Opd;
use App\Models\TahunAnggaran;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Flux\Flux;

new #[Title('IKU Kerjasama Lintas OPD')] class extends Component
{
    public ?int $filterTahunAnggaranId = null;
    public ?int $filterOpdId = null;
    public int $filterBulan = 1;

    public bool $isEditing = false;
    public ?int $kerjasamaId = null;

    public ?int $indikator_id = null;
    public ?int $sekda_id = null;
    public ?int $kabag_id = null;
    public ?int $asisten_id = null;
    public ?int $opd_id = null;
    public ?int $bidang_id = null;
    public ?int $owner_user_id = null;
    public string $peran = '';
    public float $bobot = 0;
    public string $status = 'draft';

    public function mount(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['kepala_bidang', 'kabag', 'kepala_dinas', 'asisten', 'sekda', 'bupati', 'admin_super']), 403);
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
    public function sekdas(): \Illuminate\Support\Collection
    {
        return Opd::sekda()->orderBy('name')->get();
    }

    #[Computed]
    public function asistens(): \Illuminate\Support\Collection
    {
        if (! $this->sekda_id) {
            return collect();
        }

        return Opd::asisten()->orderBy('name')->get();
    }

    #[Computed]
    public function opds(): \Illuminate\Support\Collection
    {
        if (! $this->asisten_id) {
            return collect();
        }

        return Opd::opd()->orderBy('name')->get();
    }

    #[Computed]
    public function bidangs(): \Illuminate\Support\Collection
    {
        if (! $this->opd_id) {
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

    #[Computed]
    public function indikatorSumberOptions(): \Illuminate\Support\Collection
    {
        if (! $this->filterTahunAnggaranId) {
            return collect();
        }

        return Indikator::with(['opd', 'bidang'])
            ->where('tahun_anggaran_id', $this->filterTahunAnggaranId)
            ->where('category', 'utama')
            ->where('status', 'disetujui')
            ->orderBy('nama')
            ->get();
    }

    #[Computed]
    public function ikuSumbers(): \Illuminate\Support\Collection
    {
        if (! $this->filterTahunAnggaranId) {
            return collect();
        }

        $tahun = TahunAnggaran::find($this->filterTahunAnggaranId)?->tahun ?? now()->year;

        return Indikator::with([
            'opd',
            'bidang',
            'skorings' => fn ($q) => $q
                ->where('bulan', $this->filterBulan)
                ->where('tahun', $tahun),
            'kerjasamas' => fn ($q) => $q
                ->with(['opd', 'bidang', 'owner'])
                ->orderBy('opd_id')
                ->orderBy('bidang_id'),
        ])
            ->where('tahun_anggaran_id', $this->filterTahunAnggaranId)
            ->where('category', 'utama')
            ->whereHas('kerjasamas')
            ->when($this->filterOpdId, function ($q) {
                $opdId = $this->filterOpdId;
                return $q->where(function ($q2) use ($opdId) {
                    $q2->where('opd_id', $opdId)
                       ->orWhere('asisten_id', $opdId)
                       ->orWhere('sekda_id', $opdId)
                       ->orWhere('kabag_id', $opdId)
                       ->orWhereHas('kerjasamas', fn($q3) => $q3->where('opd_id', $opdId));
                });
            })
            ->orderBy('nama')
            ->get();
    }

    #[Computed]
    public function filterOpds(): \Illuminate\Support\Collection
    {
        return Opd::whereIn('type', ['sekda', 'asisten', 'opd', 'kabag'])->orderBy('name')->get();
    }

    #[Computed]
    public function statistik(): array
    {
        if (! $this->filterTahunAnggaranId) {
            return [
                'total_kerjasama' => 0,
                'opd_terlibat' => 0,
                'total_iku_sumber' => 0,
            ];
        }

        $semua = IndikatorKerjasama::whereHas(
            'indikator',
            fn ($q) => $q
                ->where('tahun_anggaran_id', $this->filterTahunAnggaranId)
                ->where('category', 'utama')
        )->get();

        return [
            'total_kerjasama' => $semua->count(),
            'opd_terlibat' => $semua->pluck('opd_id')->filter()->unique()->count(),
            'total_iku_sumber' => $this->ikuSumbers->count(),
        ];
    }

    public function updatedSekdaId(): void
    {
        $this->asisten_id = null;
        $this->opd_id = null;
        $this->bidang_id = null;
        unset($this->asistens, $this->opds, $this->bidangs);
    }

    public function updatedAsistenId(): void
    {
        $this->opd_id = null;
        $this->bidang_id = null;
        unset($this->opds, $this->bidangs);
    }

    public function updatedOpdId(): void
    {
        $this->bidang_id = null;
        unset($this->bidangs);
    }

    public function bukaModalBuat(): void
    {
        $this->authorize('buat-indikator');
        $this->resetForm();
        $this->isEditing = false;
        Flux::modal('kerjasama-modal')->show();
    }

    public function bukaModalEdit(int $id): void
    {
        $this->authorize('edit-indikator');

        $kerjasama = IndikatorKerjasama::findOrFail($id);
        $this->isEditing = true;
        $this->kerjasamaId = $kerjasama->id;
        $this->indikator_id = $kerjasama->indikator_id;
        $this->sekda_id = $kerjasama->sekda_id;
        $this->kabag_id = $kerjasama->kabag_id;
        $this->asisten_id = $kerjasama->asisten_id;
        $this->opd_id = $kerjasama->opd_id;
        $this->bidang_id = $kerjasama->bidang_id;
        $this->owner_user_id = $kerjasama->owner_user_id;
        $this->peran = $kerjasama->peran ?? '';
        $this->bobot = (float) $kerjasama->bobot;
        $this->status = $kerjasama->status;
        unset($this->asistens, $this->opds, $this->bidangs);

        Flux::modal('kerjasama-modal')->show();
    }

    public function simpan(): void
    {
        if ($this->isEditing) {
            $this->authorize('edit-indikator');
        } else {
            $this->authorize('buat-indikator');
        }

        $data = $this->validate([
            'indikator_id' => ['required', 'exists:indikators,id'],
            'sekda_id' => ['nullable', 'exists:opds,id'],
            'kabag_id' => ['nullable', 'exists:opds,id'],
            'asisten_id' => ['nullable', 'exists:opds,id'],
            'opd_id' => ['required', 'exists:opds,id'],
            'bidang_id' => ['nullable', 'exists:opds,id'],
            'owner_user_id' => ['nullable', 'exists:users,id'],
            'peran' => ['nullable', 'string'],
            'bobot' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:draft,diajukan,disetujui,ditolak'],
        ]);

        $indikator = Indikator::findOrFail($data['indikator_id']);
        if ($indikator->category !== 'utama') {
            $this->addError('indikator_id', 'Pilih indikator utama sebagai sumber kerjasama.');

            return;
        }

        $uniqueRule = Rule::unique('indikator_kerjasamas')
            ->where('indikator_id', $data['indikator_id'])
            ->where('opd_id', $data['opd_id'])
            ->where(fn ($q) => $q->where('bidang_id', $data['bidang_id']));

        if ($this->isEditing && $this->kerjasamaId) {
            $uniqueRule->ignore($this->kerjasamaId);
        }

        $this->validate([
            'indikator_id' => [$uniqueRule],
        ], [
            'indikator_id.unique' => 'Relasi kerjasama untuk OPD/Bidang ini sudah ada.',
        ]);

        if ($this->isEditing) {
            $kerjasama = IndikatorKerjasama::findOrFail($this->kerjasamaId);
            $kerjasama->update($data);
            Flux::toast('Relasi kerjasama berhasil diperbarui.');
        } else {
            $data['dibuat_oleh'] = auth()->id();
            IndikatorKerjasama::create($data);
            Flux::toast('Relasi kerjasama berhasil ditambahkan.');
        }

        unset($this->ikuSumbers, $this->statistik);
        Flux::modal('kerjasama-modal')->close();
    }

    public function hapus(int $id): void
    {
        $this->authorize('hapus-indikator');
        $kerjasama = IndikatorKerjasama::findOrFail($id);
        $kerjasama->delete();
        unset($this->ikuSumbers, $this->statistik);
        Flux::toast('Relasi kerjasama berhasil dihapus.');
    }

    private function resetForm(): void
    {
        $this->kerjasamaId = null;
        $this->indikator_id = null;
        $this->sekda_id = null;
        $this->kabag_id = null;
        $this->asisten_id = null;
        $this->opd_id = null;
        $this->bidang_id = null;
        $this->owner_user_id = null;
        $this->peran = '';
        $this->bobot = 0;
        $this->status = 'draft';
        unset($this->asistens, $this->opds, $this->bidangs);
    }
};
?>

<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">IKU Kerjasama Lintas OPD</flux:heading>
            <flux:text class="mt-1 text-zinc-500">
                Satu indikator utama dapat dipetakan ke banyak OPD mitra tanpa membuat indikator baru.
            </flux:text>
        </div>
        @can('buat-indikator')
            <flux:button variant="primary" icon="plus" wire:click="bukaModalBuat">
                Tambah Relasi Kerjasama
            </flux:button>
        @endcan
    </div>

    <div class="flex flex-wrap gap-3">
        <flux:field>
            <flux:label>Tahun Anggaran</flux:label>
            <flux:select wire:model.live="filterTahunAnggaranId" class="w-40">
                <flux:select.option value="">-- Tahun --</flux:select.option>
                @foreach ($this->tahunAnggarans as $tahun)
                    <flux:select.option wire:key="tahun-{{ $tahun->id }}" value="{{ $tahun->id }}">
                        {{ $tahun->tahun }}{{ $tahun->is_active ? ' (Aktif)' : '' }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>Bulan</flux:label>
            <flux:select wire:model.live="filterBulan" class="w-40">
                @foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $idx => $nama)
                    <flux:select.option value="{{ $idx + 1 }}">{{ $nama }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>Filter Unit / OPD</flux:label>
            <flux:select wire:model.live="filterOpdId" class="w-64">
                <flux:select.option value="">-- Semua Unit --</flux:select.option>
                @foreach ($this->filterOpds as $opd)
                    <flux:select.option wire:key="filter-opd-{{ $opd->id }}" value="{{ $opd->id }}">
                        {{ $opd->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>
    </div>

    @if ($filterTahunAnggaranId)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-purple-200 dark:border-purple-800 bg-purple-50 dark:bg-purple-950 p-5">
                <div class="text-xs font-medium text-purple-500 uppercase tracking-wide mb-1">Total Relasi</div>
                <div class="text-3xl font-bold text-purple-700 dark:text-purple-300">{{ $this->statistik['total_kerjasama'] }}</div>
            </div>
            <div class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950 p-5">
                <div class="text-xs font-medium text-amber-500 uppercase tracking-wide mb-1">Indikator Sumber</div>
                <div class="text-3xl font-bold text-amber-700 dark:text-amber-300">{{ $this->statistik['total_iku_sumber'] }}</div>
            </div>
            <div class="rounded-xl border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-950 p-5">
                <div class="text-xs font-medium text-blue-500 uppercase tracking-wide mb-1">OPD Mitra</div>
                <div class="text-3xl font-bold text-blue-700 dark:text-blue-300">{{ $this->statistik['opd_terlibat'] }}</div>
            </div>
        </div>
    @endif

    @if ($this->ikuSumbers->isEmpty())
        <div class="rounded-xl border-2 border-dashed border-zinc-200 dark:border-zinc-700 py-14 text-center">
            <flux:heading size="sm" class="text-zinc-500">Belum ada relasi kerjasama</flux:heading>
            <flux:text class="text-zinc-400 mt-1 text-sm">
                Tambahkan relasi untuk mengaitkan IKU utama ke OPD mitra.
            </flux:text>
        </div>
    @else
        <div class="space-y-5">
            @foreach ($this->ikuSumbers as $sumber)
                @php
                    $skorSumber = $sumber->skorings->first();
                    $skorFinal = $skorSumber?->skor_bupati;
                    $isFinal = $skorSumber?->is_final;
                @endphp
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
                    <div class="p-5 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="text-xs text-zinc-500 mb-1">
                                    {{ $sumber->opd?->name ?? '-' }} @if($sumber->bidang) › {{ $sumber->bidang->name }} @endif
                                </div>
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $sumber->nama }}</div>
                                <div class="text-xs text-zinc-500 mt-1">Target: {{ number_format($sumber->target, 2) }} | Bobot Utama: {{ number_format($sumber->bobot, 2) }}%</div>
                            </div>
                            <div class="text-right">
                                @if ($isFinal && $skorFinal !== null)
                                    @php $color = $skorFinal >= 7 ? 'green' : ($skorFinal >= 5 ? 'yellow' : 'red'); @endphp
                                    <flux:badge variant="{{ $color }}" size="sm">{{ $skorFinal }}/10</flux:badge>
                                    <div class="text-xs text-zinc-500 mt-1">Skor sumber final</div>
                                @else
                                    <div class="text-xs text-zinc-400">Skor sumber belum final</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($sumber->kerjasamas as $mitra)
                            <div class="p-4 flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                        {{ $mitra->opd?->name ?? '-' }} @if($mitra->bidang) › {{ $mitra->bidang->name }} @endif
                                    </div>
                                    <div class="text-xs text-zinc-500 mt-0.5">
                                        Bobot Mitra: {{ number_format($mitra->bobot, 2) }}%
                                        @if ($mitra->owner)
                                            | Owner: {{ $mitra->owner->name }}
                                        @endif
                                        @if ($mitra->peran)
                                            | Peran: {{ \Illuminate\Support\Str::limit($mitra->peran, 90) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <flux:badge variant="{{ $mitra->status === 'disetujui' ? 'green' : ($mitra->status === 'diajukan' ? 'blue' : ($mitra->status === 'ditolak' ? 'red' : 'zinc')) }}" size="sm">
                                        {{ ucfirst($mitra->status) }}
                                    </flux:badge>
                                    @can('edit-indikator')
                                        <flux:button size="xs" icon="pencil" variant="ghost" wire:click="bukaModalEdit({{ $mitra->id }})" />
                                    @endcan
                                    @can('hapus-indikator')
                                        <flux:button
                                            size="xs"
                                            icon="trash"
                                            variant="ghost"
                                            class="text-red-500 hover:text-red-700"
                                            wire:click="hapus({{ $mitra->id }})"
                                            wire:confirm="Hapus relasi kerjasama ini?"
                                        />
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <flux:modal name="kerjasama-modal" class="w-full max-w-2xl">
        <div class="space-y-5">
            <flux:heading size="lg">
                {{ $isEditing ? 'Edit Relasi Kerjasama' : 'Tambah Relasi Kerjasama' }}
            </flux:heading>

            <flux:field>
                <flux:label>Indikator Sumber (IKU Utama)</flux:label>
                <flux:select wire:model="indikator_id">
                    <flux:select.option value="">-- Pilih Indikator --</flux:select.option>
                    @foreach ($this->indikatorSumberOptions as $indikator)
                        <flux:select.option value="{{ $indikator->id }}">
                            {{ $indikator->nama }} @if($indikator->opd) ({{ $indikator->opd->name }}) @endif
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="indikator_id" />
            </flux:field>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Sekda</flux:label>
                    <flux:select wire:model.live="sekda_id">
                        <flux:select.option value="">-- Pilih Sekda --</flux:select.option>
                        @foreach ($this->sekdas as $sekda)
                            <flux:select.option value="{{ $sekda->id }}">{{ $sekda->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="sekda_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Asisten</flux:label>
                    <flux:select wire:model.live="asisten_id" :disabled="! $sekda_id">
                        <flux:select.option value="">-- Pilih Asisten --</flux:select.option>
                        @foreach ($this->asistens as $asisten)
                            <flux:select.option value="{{ $asisten->id }}">{{ $asisten->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="asisten_id" />
                </flux:field>

                <flux:field>
                    <flux:label>OPD Mitra</flux:label>
                    <flux:select wire:model.live="opd_id" :disabled="! $asisten_id">
                        <flux:select.option value="">-- Pilih OPD --</flux:select.option>
                        @foreach ($this->opds as $opd)
                            <flux:select.option value="{{ $opd->id }}">{{ $opd->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="opd_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Bidang Mitra</flux:label>
                    <flux:select wire:model="bidang_id" :disabled="! $opd_id">
                        <flux:select.option value="">-- Pilih Bidang --</flux:select.option>
                        @foreach ($this->bidangs as $bidang)
                            <flux:select.option value="{{ $bidang->id }}">{{ $bidang->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="bidang_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Kabag</flux:label>
                    <flux:select wire:model="kabag_id">
                        <flux:select.option value="">-- Pilih Kabag --</flux:select.option>
                        @foreach ($this->kabags as $kabag)
                            <flux:select.option value="{{ $kabag->id }}">{{ $kabag->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="kabag_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Owner</flux:label>
                    <flux:select wire:model="owner_user_id">
                        <flux:select.option value="">-- Pilih Owner --</flux:select.option>
                        @foreach ($this->usersForSelect as $user)
                            <flux:select.option value="{{ $user->id }}">{{ $user->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="owner_user_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Bobot Mitra (%)</flux:label>
                    <flux:input type="number" wire:model="bobot" min="0" max="100" step="0.01" />
                    <flux:error name="bobot" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model="status">
                        <flux:select.option value="draft">Draft</flux:select.option>
                        <flux:select.option value="diajukan">Diajukan</flux:select.option>
                        <flux:select.option value="disetujui">Disetujui</flux:select.option>
                        <flux:select.option value="ditolak">Ditolak</flux:select.option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Peran Kerjasama</flux:label>
                <flux:textarea wire:model="peran" rows="3" placeholder="Contoh: peran OPD mitra dalam indikator ini." />
                <flux:error name="peran" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button variant="ghost" x-on:click="$flux.modal('kerjasama-modal').close()">
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
