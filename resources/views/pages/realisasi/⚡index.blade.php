<?php

use App\Livewire\Forms\RealisasiForm;
use App\Models\Indikator;
use App\Models\Realisasi;
use App\Models\TargetIndikator;
use App\Models\TahunAnggaran;
use App\Services\RealisasiService;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public RealisasiForm $form;

    public ?int $filterTahunAnggaranId = null;
    public ?int $filterOpdId = null;
    public int $filterBulan = 1;
    public bool $isEditing = false;

    private RealisasiService $service;

    public function boot(RealisasiService $service): void
    {
        $this->service = $service;
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['kepala_bidang', 'kabag', 'kepala_dinas', 'admin_super']), 403);
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

        return Indikator::with(['opd', 'bidang', 'realisasi' => fn ($q) => $q->where('bulan', $this->filterBulan)])
            ->where('tahun_anggaran_id', $this->filterTahunAnggaranId)
            ->where('category', 'utama')
            ->when($this->filterOpdId, function ($q) {
                $opdId = $this->filterOpdId;
                return $q->where(function ($q2) use ($opdId) {
                    $q2->where('opd_id', $opdId)
                       ->orWhere('asisten_id', $opdId)
                       ->orWhere('sekda_id', $opdId)
                       ->orWhere('kabag_id', $opdId);
                });
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
    public function indikatorOptions(): \Illuminate\Support\Collection
    {
        if (! $this->filterTahunAnggaranId) {
            return collect();
        }

        return Indikator::where('tahun_anggaran_id', $this->filterTahunAnggaranId)
            ->where('category', 'utama')
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

        $data = $this->form->toStoreData();

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

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3 max-w-4xl">
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

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full min-w-[600px] text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-xs">
                <tr>
                    <th class="px-3 py-3 text-left font-medium w-8">#</th>
                    <th class="px-3 py-3 text-left font-medium">Indikator</th>
                    <th class="px-3 py-3 text-right font-medium whitespace-nowrap hidden sm:table-cell">Target</th>
                    <th class="px-3 py-3 text-right font-medium whitespace-nowrap hidden sm:table-cell">Realisasi</th>
                    <th class="px-3 py-3 text-right font-medium">%</th>
                    <th class="px-3 py-3 text-left font-medium hidden lg:table-cell">Progres</th>
                    <th class="px-3 py-3 text-center font-medium hidden md:table-cell">Bukti</th>
                    <th class="px-3 py-3 text-center font-medium">Status</th>
                    <th class="px-3 py-3 text-center font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->indikators as $i => $indikator)
                    @php
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
                            'draft'       => 'zinc',
                            'diajukan'    => 'blue',
                            'diverifikasi' => 'green',
                            default       => 'zinc',
                        };
                    @endphp
                    <tr wire:key="row-{{ $indikator->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-3 py-3 text-zinc-500 text-xs w-8">{{ $i + 1 }}</td>
                        <td class="px-3 py-3">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100 text-sm line-clamp-2">{{ $indikator->nama }}</div>
                            <div class="text-xs text-zinc-400 mt-0.5">{{ $indikator->opd?->name ?? '-' }} · Bobot: {{ $indikator->bobot }}%</div>
                            {{-- Target/Realisasi di mobile --}}
                            <div class="flex gap-3 mt-1 sm:hidden text-xs text-zinc-500">
                                <span>Target: {{ $targetBulan ? number_format($targetBulan, 1) : '-' }}</span>
                                <span>Realisasi: {{ $nilaiRealisasi !== null ? number_format($nilaiRealisasi, 1) : '-' }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-right text-zinc-600 dark:text-zinc-300 hidden sm:table-cell whitespace-nowrap">
                            {{ $targetBulan ? number_format($targetBulan, 2) : '-' }}
                        </td>
                        <td class="px-3 py-3 text-right text-zinc-600 dark:text-zinc-300 hidden sm:table-cell whitespace-nowrap">
                            {{ $nilaiRealisasi !== null ? number_format($nilaiRealisasi, 2) : '-' }}
                        </td>
                        <td class="px-3 py-3 text-right whitespace-nowrap">
                            @if ($persentase !== null)
                                <span class="font-semibold text-sm {{ $persentase >= 80 ? 'text-green-600' : ($persentase >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($persentase, 1) }}%
                                </span>
                            @else
                                <span class="text-zinc-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 hidden lg:table-cell">
                            @if ($realisasi?->deskripsi_progres)
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 line-clamp-2 max-w-[180px]">{{ $realisasi->deskripsi_progres }}</p>
                            @else
                                <span class="text-zinc-300 dark:text-zinc-600 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center hidden md:table-cell">
                            <div class="flex items-center justify-center gap-1.5">
                                @if ($realisasi?->bukti_link)
                                    <a href="{{ $realisasi->bukti_link }}" target="_blank" rel="noopener" class="text-xs text-blue-600 hover:underline">Link</a>
                                @endif
                                @if ($realisasi?->foto_bukti)
                                    <a href="{{ Storage::url($realisasi->foto_bukti) }}" target="_blank" rel="noopener" class="text-xs text-emerald-600 hover:underline">Foto</a>
                                @endif
                                @if (! $realisasi?->bukti_link && ! $realisasi?->foto_bukti)
                                    <span class="text-zinc-300 text-xs">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center whitespace-nowrap">
                            @if ($status)
                                <flux:badge variant="{{ $badgeVariant }}" size="sm">{{ ucfirst($status) }}</flux:badge>
                            @else
                                <span class="text-zinc-400 text-xs">Belum</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
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
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-10 text-center text-zinc-400">
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
    <flux:modal name="realisasi-modal" class="w-full max-w-2xl">
        <div class="space-y-5">
            <flux:heading size="lg">
                {{ $isEditing ? 'Edit Realisasi' : 'Input Realisasi' }}
            </flux:heading>

            {{-- Indikator --}}
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

            {{-- Bulan --}}
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

            {{-- Nilai Realisasi --}}
            <flux:field>
                <flux:label>Nilai Realisasi <flux:badge size="sm" variant="blue">Wajib</flux:badge></flux:label>
                <flux:input type="number" wire:model="form.nilai" min="0" step="0.01" placeholder="0.00" />
                <flux:error name="form.nilai" />
            </flux:field>

            {{-- Keterangan --}}
            <flux:field>
                <flux:label>Keterangan Singkat</flux:label>
                <flux:textarea wire:model="form.keterangan" rows="2" placeholder="Catatan atau keterangan tambahan" />
                <flux:error name="form.keterangan" />
            </flux:field>

            {{-- Deskripsi Progres (baru) --}}
            <flux:field>
                <flux:label>
                    Deskripsi Progres / Capaian
                    <flux:badge size="sm" variant="zinc">Opsional</flux:badge>
                </flux:label>
                <flux:textarea
                    wire:model="form.deskripsi_progres"
                    rows="4"
                    placeholder="Jelaskan progres dan capaian secara narasi. Contoh: Pada bulan ini OPD telah menyelesaikan 3 dari 5 kegiatan utama, meliputi..."
                />
                <flux:error name="form.deskripsi_progres" />
            </flux:field>

            {{-- Bukti Link & Foto (baru) --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 space-y-4">
                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    Bukti Dukung
                    <flux:badge size="sm" variant="zinc">Opsional</flux:badge>
                </p>

                {{-- Link bukti --}}
                <flux:field>
                    <flux:label>Link Bukti (URL)</flux:label>
                    <flux:input
                        type="url"
                        wire:model="form.bukti_link"
                        placeholder="https://drive.google.com/... atau link laporan lainnya"
                        icon="link"
                    />
                    <flux:description>Link Google Drive, laporan online, atau tautan dokumen pendukung.</flux:description>
                    <flux:error name="form.bukti_link" />
                </flux:field>

                {{-- Upload foto --}}
                <flux:field>
                    <flux:label>Upload Foto Bukti</flux:label>

                    {{-- Preview foto existing saat edit --}}
                    @if ($isEditing && $form->foto_bukti_existing)
                        <div class="mb-2 flex items-center gap-3 rounded-lg bg-zinc-50 dark:bg-zinc-800 p-2">
                            <img
                                src="{{ Storage::url($form->foto_bukti_existing) }}"
                                alt="Foto bukti"
                                class="h-16 w-16 object-cover rounded-md border border-zinc-200 dark:border-zinc-700"
                            >
                            <div>
                                <p class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Foto saat ini</p>
                                <p class="text-xs text-zinc-400">Upload foto baru untuk mengganti</p>
                            </div>
                        </div>
                    @endif

                    {{-- Preview foto baru yang akan diupload --}}
                    @if ($form->foto_bukti)
                        <div class="mb-2 rounded-lg overflow-hidden border border-blue-200 dark:border-blue-800 max-w-xs">
                            <img src="{{ $form->foto_bukti->temporaryUrl() }}" alt="Preview" class="w-full h-40 object-cover">
                            <p class="text-xs text-center text-blue-600 dark:text-blue-400 py-1 bg-blue-50 dark:bg-blue-950">Preview foto baru</p>
                        </div>
                    @endif

                    <input
                        type="file"
                        wire:model="form.foto_bukti"
                        accept="image/*"
                        class="block w-full text-sm text-zinc-600 dark:text-zinc-400
                               file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                               file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700
                               hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300
                               cursor-pointer"
                    >
                    <flux:description>Foto kegiatan atau dokumentasi. Format JPG/PNG/WebP, maks. 5 MB.</flux:description>
                    <flux:error name="form.foto_bukti" />

                    {{-- Loading indicator --}}
                    <div wire:loading wire:target="form.foto_bukti" class="text-xs text-blue-500 flex items-center gap-1 mt-1">
                        <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Mengupload foto...
                    </div>
                </flux:field>
            </div>

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
