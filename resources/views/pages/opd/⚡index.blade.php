<?php

use App\Models\Opd;
use App\Services\OpdService;
use App\Livewire\Forms\OpdForm;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Flux\Flux;

new #[Title('Kelola OPD')] class extends Component {
    public OpdForm $form;

    public string $search = '';

    public bool $isEditing = false;

    private OpdService $service;

    public function boot(OpdService $service): void
    {
        $this->service = $service;
    }

    #[Computed]
    public function opds()
    {
        return Opd::with('parent')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('code', 'like', "%{$this->search}%"))
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function allOpdsForSelect()
    {
        return Opd::orderBy('name')->get();
    }

    public function openCreate(): void
    {
        $this->form->reset();
        $this->isEditing = false;
        Flux::modal('opd-modal')->show();
    }

    public function openEdit(int $id): void
    {
        $this->form->setOpd(Opd::findOrFail($id));
        $this->isEditing = true;
        Flux::modal('opd-modal')->show();
    }

    public function save(): void
    {
        $data = $this->form->validate();

        if ($this->isEditing) {
            $this->service->update(Opd::findOrFail($this->form->opdId), $data);
            Flux::toast('OPD berhasil diperbarui.');
        } else {
            $this->service->store($data);
            Flux::toast('OPD berhasil ditambahkan.');
        }

        Flux::modal('opd-modal')->close();
        $this->form->reset();
    }

    public function delete(int $id): void
    {
        $this->service->delete(Opd::findOrFail($id));
        Flux::toast('OPD berhasil dihapus.');
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Kelola OPD</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Manajemen Organisasi Perangkat Daerah</flux:text>
        </div>
        @can('kelola-opd')
            <flux:button wire:click="openCreate" variant="primary" icon="plus">Tambah OPD</flux:button>
        @endcan
    </div>

    <div class="flex gap-3">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Cari nama atau kode OPD..."
            icon="magnifying-glass"
            class="max-w-xs"
        />
    </div>

    <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm text-left">
            <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3">Induk</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->opds as $opd)
                    <tr wire:key="{{ $opd->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-zinc-700 dark:text-zinc-300">{{ $opd->code }}</td>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $opd->name }}</td>
                        <td class="px-4 py-3">
                            <flux:badge variant="outline" size="sm" class="capitalize">{{ $opd->type }}</flux:badge>
                        </td>
                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $opd->parent?->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @can('kelola-opd')
                                <div class="flex gap-2">
                                    <flux:button size="sm" wire:click="openEdit({{ $opd->id }})" icon="pencil">Edit</flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="danger"
                                        icon="trash"
                                        x-on:click="if(confirm('Hapus OPD ini?')) $wire.delete({{ $opd->id }})"
                                    >Hapus</flux:button>
                                </div>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-400 dark:text-zinc-500">
                            Tidak ada data OPD ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <flux:modal name="opd-modal" class="md:w-[480px]">
        <div class="space-y-5">
            <flux:heading>{{ $isEditing ? 'Edit OPD' : 'Tambah OPD' }}</flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>Nama OPD</flux:label>
                    <flux:input wire:model="form.name" placeholder="Nama unit organisasi" />
                    <flux:error name="form.name" />
                </flux:field>

                <flux:field>
                    <flux:label>Kode</flux:label>
                    <flux:input wire:model="form.code" placeholder="Contoh: DINKES" />
                    <flux:error name="form.code" />
                </flux:field>

                <flux:field>
                    <flux:label>Tipe</flux:label>
                    <flux:select wire:model="form.type">
                        <flux:select.option value="">-- Pilih Tipe --</flux:select.option>
                        <flux:select.option value="sekda">Sekda</flux:select.option>
                        <flux:select.option value="asisten">Asisten</flux:select.option>
                        <flux:select.option value="kabag">Kabag</flux:select.option>
                        <flux:select.option value="opd">OPD / Dinas</flux:select.option>
                        <flux:select.option value="bidang">Bidang</flux:select.option>
                    </flux:select>
                    <flux:error name="form.type" />
                </flux:field>

                <flux:field>
                    <flux:label>Induk (Opsional)</flux:label>
                    <flux:select wire:model="form.parent_id">
                        <flux:select.option value="">-- Tidak Ada --</flux:select.option>
                        @foreach ($this->allOpdsForSelect as $parent)
                            <flux:select.option value="{{ $parent->id }}">
                                {{ $parent->name }} ({{ $parent->type }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.parent_id" />
                </flux:field>

                <div class="flex justify-end gap-2 pt-2">
                    <flux:button type="button" x-on:click="$flux.modal('opd-modal').close()">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
