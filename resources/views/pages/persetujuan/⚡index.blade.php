<?php

use App\Livewire\Forms\PersetujuanForm;
use App\Models\Persetujuan;
use App\Services\PersetujuanService;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public PersetujuanForm $form;

    public ?int $selectedPersetujuanId = null;
    public ?int $detailIndikatorId = null;
    public string $filterLevel = '';

    private PersetujuanService $service;

    public function boot(PersetujuanService $service): void
    {
        $this->service = $service;
    }

    public function mount(): void
    {
        $this->filterLevel = $this->getLevelForCurrentUser();
    }

    public function getLevelForCurrentUser(): string
    {
        $role = auth()->user()->getRoleNames()->first() ?? '';

        return match ($role) {
            'kabag' => 'kabag',
            'asisten' => 'asisten',
            'sekda' => 'sekda',
            'bupati' => 'bupati',
            default => 'sekda',
        };
    }

    #[Computed]
    public function isAdminSuper(): bool
    {
        return auth()->user()->getRoleNames()->contains('admin_super');
    }

    #[Computed]
    public function persetujuans(): \Illuminate\Database\Eloquent\Collection
    {
        $level = $this->isAdminSuper ? $this->filterLevel : $this->getLevelForCurrentUser();

        if (! $level) {
            return collect();
        }

        return $this->service->getPending($level);
    }

    #[Computed]
    public function riwayat(): \Illuminate\Database\Eloquent\Collection
    {
        if (! $this->detailIndikatorId) {
            return collect();
        }

        return $this->service->getByIndikator($this->detailIndikatorId);
    }

    public function bukaSetujui(int $id): void
    {
        $this->selectedPersetujuanId = $id;
        $this->form->reset();
        $this->form->persetujuanId = $id;
        Flux::modal('setujui-modal')->show();
    }

    public function bukaTolak(int $id): void
    {
        $this->selectedPersetujuanId = $id;
        $this->form->reset();
        $this->form->persetujuanId = $id;
        Flux::modal('tolak-modal')->show();
    }

    public function setujui(): void
    {
        $this->form->validate();

        $persetujuan = Persetujuan::findOrFail($this->form->persetujuanId);
        $this->service->setujui($persetujuan, $this->form->catatan ?: null);

        unset($this->persetujuans, $this->riwayat);
        Flux::modal('setujui-modal')->close();
        Flux::toast('Persetujuan berhasil disetujui.');
    }

    public function tolak(): void
    {
        $this->validate(['form.catatan' => ['required', 'string', 'max:1000']]);

        $persetujuan = Persetujuan::findOrFail($this->form->persetujuanId);
        $this->service->tolak($persetujuan, $this->form->catatan);

        unset($this->persetujuans, $this->riwayat);
        Flux::modal('tolak-modal')->close();
        Flux::toast('Persetujuan ditolak.');
    }

    public function lihatDetail(int $indikatorId): void
    {
        $this->detailIndikatorId = $indikatorId;
        unset($this->riwayat);
    }
};
?>

<div>
    <div class="mb-6">
        <flux:heading size="xl">Persetujuan Indikator</flux:heading>
        <flux:text class="mt-1 text-zinc-500">Kelola persetujuan indikator kinerja berdasarkan level jabatan.</flux:text>
    </div>

    {{-- Filter Level (hanya untuk admin_super) --}}
    @if ($this->isAdminSuper)
        <div class="mb-6 max-w-xs">
            <flux:field>
                <flux:label>Filter Level Persetujuan</flux:label>
                <flux:select wire:model.live="filterLevel">
                    <flux:select.option value="kabag">Kabag</flux:select.option>
                    <flux:select.option value="asisten">Asisten</flux:select.option>
                    <flux:select.option value="sekda">Sekda</flux:select.option>
                    <flux:select.option value="bupati">Bupati</flux:select.option>
                </flux:select>
            </flux:field>
        </div>
    @endif

    {{-- Tabel Persetujuan --}}
    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">#</th>
                    <th class="px-4 py-3 text-left font-medium">Nama Indikator</th>
                    <th class="px-4 py-3 text-left font-medium">OPD</th>
                    <th class="px-4 py-3 text-left font-medium">Satuan</th>
                    <th class="px-4 py-3 text-right font-medium">Target</th>
                    <th class="px-4 py-3 text-left font-medium">Diajukan Oleh</th>
                    <th class="px-4 py-3 text-left font-medium">Tanggal</th>
                    <th class="px-4 py-3 text-center font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->persetujuans as $i => $item)
                    <tr wire:key="persetujuan-{{ $item->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-4 py-3 text-zinc-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3">
                            <button
                                class="font-medium text-blue-600 dark:text-blue-400 hover:underline text-left"
                                wire:click="lihatDetail({{ $item->indikator?->id }})"
                            >
                                {{ $item->indikator?->nama ?? '-' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-zinc-500">{{ $item->indikator?->opd?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $item->indikator?->satuan ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-300">
                            {{ $item->indikator ? number_format($item->indikator->target, 2) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-zinc-500">{{ $item->user?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $item->created_at?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <flux:button
                                    size="xs"
                                    variant="ghost"
                                    class="text-green-600 hover:text-green-800"
                                    wire:click="bukaSetujui({{ $item->id }})"
                                >
                                    Setujui
                                </flux:button>
                                <flux:button
                                    size="xs"
                                    variant="ghost"
                                    class="text-red-600 hover:text-red-800"
                                    wire:click="bukaTolak({{ $item->id }})"
                                >
                                    Tolak
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-zinc-400">
                            Tidak ada persetujuan yang menunggu untuk level ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Riwayat Persetujuan (Detail) --}}
    @if ($detailIndikatorId && $this->riwayat->isNotEmpty())
        <div class="mt-8">
            <flux:heading size="lg" class="mb-4">Riwayat Persetujuan</flux:heading>
            <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Level</th>
                            <th class="px-4 py-3 text-left font-medium">Diproses Oleh</th>
                            <th class="px-4 py-3 text-center font-medium">Status</th>
                            <th class="px-4 py-3 text-left font-medium">Catatan</th>
                            <th class="px-4 py-3 text-left font-medium">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->riwayat as $riwayatItem)
                            @php
                                $statusVariant = match ($riwayatItem->status) {
                                    'pending' => 'zinc',
                                    'disetujui' => 'green',
                                    'ditolak' => 'red',
                                    default => 'zinc',
                                };
                            @endphp
                            <tr wire:key="riwayat-{{ $riwayatItem->id }}" class="bg-white dark:bg-zinc-900">
                                <td class="px-4 py-3 font-medium text-zinc-700 dark:text-zinc-300 capitalize">{{ $riwayatItem->level }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ $riwayatItem->user?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <flux:badge variant="{{ $statusVariant }}" size="sm">{{ ucfirst($riwayatItem->status) }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-500">{{ $riwayatItem->catatan ?? '-' }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ $riwayatItem->updated_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Modal Setujui --}}
    <flux:modal name="setujui-modal" class="w-full max-w-md">
        <div class="space-y-5">
            <flux:heading size="lg">Setujui Persetujuan</flux:heading>
            <flux:text class="text-zinc-500">Indikator ini akan ditandai sebagai disetujui pada level Anda.</flux:text>

            <flux:field>
                <flux:label>Catatan (opsional)</flux:label>
                <flux:textarea wire:model="form.catatan" rows="3" placeholder="Catatan persetujuan..." />
                <flux:error name="form.catatan" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button variant="ghost" x-on:click="$flux.modal('setujui-modal').close()">Batal</flux:button>
                <flux:button variant="primary" wire:click="setujui" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="setujui">Setujui</span>
                    <span wire:loading wire:target="setujui">Memproses...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Tolak --}}
    <flux:modal name="tolak-modal" class="w-full max-w-md">
        <div class="space-y-5">
            <flux:heading size="lg">Tolak Persetujuan</flux:heading>
            <flux:text class="text-zinc-500">Berikan alasan penolakan yang jelas.</flux:text>

            <flux:field>
                <flux:label>Alasan Penolakan <flux:badge size="sm" variant="red">Wajib</flux:badge></flux:label>
                <flux:textarea wire:model="form.catatan" rows="4" placeholder="Tuliskan alasan penolakan..." />
                <flux:error name="form.catatan" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button variant="ghost" x-on:click="$flux.modal('tolak-modal').close()">Batal</flux:button>
                <flux:button variant="danger" wire:click="tolak" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="tolak">Tolak</span>
                    <span wire:loading wire:target="tolak">Memproses...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
