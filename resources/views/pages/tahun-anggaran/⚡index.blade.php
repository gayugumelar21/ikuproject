<?php

use App\Models\TahunAnggaran;
use App\Services\TahunAnggaranService;
use App\Livewire\Forms\TahunAnggaranForm;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Flux\Flux;

new #[Title('Tahun Anggaran')] class extends Component {
    public TahunAnggaranForm $form;

    public bool $isEditing = false;

    private TahunAnggaranService $service;

    public function boot(TahunAnggaranService $service): void
    {
        $this->service = $service;
    }

    #[Computed]
    public function tahunAnggarans()
    {
        return TahunAnggaran::orderBy('tahun', 'desc')->get();
    }

    public function openCreate(): void
    {
        $this->form->reset();
        $this->isEditing = false;
        Flux::modal('tahun-modal')->show();
    }

    public function openEdit(int $id): void
    {
        $this->form->setTahunAnggaran(TahunAnggaran::findOrFail($id));
        $this->isEditing = true;
        Flux::modal('tahun-modal')->show();
    }

    public function save(): void
    {
        $data = $this->form->validate();

        if ($this->isEditing) {
            $this->service->update(TahunAnggaran::findOrFail($this->form->tahunAnggaranId), $data);
            Flux::toast('Tahun anggaran berhasil diperbarui.');
        } else {
            $this->service->store($data);
            Flux::toast('Tahun anggaran berhasil ditambahkan.');
        }

        Flux::modal('tahun-modal')->close();
        $this->form->reset();
    }

    public function setActive(int $id): void
    {
        $tahunAnggaran = TahunAnggaran::findOrFail($id);
        $this->service->setActive($tahunAnggaran);
        Flux::toast("Tahun {$tahunAnggaran->tahun} kini menjadi tahun anggaran aktif.");
    }

    public function delete(int $id): void
    {
        $tahunAnggaran = TahunAnggaran::findOrFail($id);

        if ($tahunAnggaran->is_active) {
            Flux::toast('Tidak dapat menghapus tahun anggaran yang sedang aktif.', variant: 'danger');

            return;
        }

        $this->service->delete($tahunAnggaran);
        Flux::toast('Tahun anggaran berhasil dihapus.');
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
            <flux:heading size="xl">Tahun Anggaran</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Manajemen periode tahun anggaran sistem</flux:text>
        </div>
        @if (auth()->user()->hasRole('admin_super'))
            <flux:button wire:click="openCreate" variant="primary" icon="plus">Tambah Tahun</flux:button>
        @endif
    </div>

    <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm text-left">
            <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3">Tahun</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->tahunAnggarans as $tahun)
                    <tr wire:key="{{ $tahun->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100 text-base">
                            {{ $tahun->tahun }}
                        </td>
                        <td class="px-4 py-3">
                            @if ($tahun->is_active)
                                <flux:badge color="green" size="sm" icon="check-circle">Aktif</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">Tidak Aktif</flux:badge>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if (auth()->user()->hasRole('admin_super'))
                                <div class="flex gap-2">
                                    @unless ($tahun->is_active)
                                        <flux:button
                                            size="sm"
                                            variant="filled"
                                            icon="check"
                                            wire:click="setActive({{ $tahun->id }})"
                                            wire:confirm="Aktifkan tahun anggaran {{ $tahun->tahun }}?"
                                        >Aktifkan</flux:button>
                                    @endunless
                                    <flux:button size="sm" wire:click="openEdit({{ $tahun->id }})" icon="pencil">Edit</flux:button>
                                    @unless ($tahun->is_active)
                                        <flux:button
                                            size="sm"
                                            variant="danger"
                                            icon="trash"
                                            x-on:click="if(confirm('Hapus tahun anggaran {{ $tahun->tahun }}?')) $wire.delete({{ $tahun->id }})"
                                        >Hapus</flux:button>
                                    @endunless
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-10 text-center text-zinc-400 dark:text-zinc-500">
                            Belum ada data tahun anggaran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (auth()->user()->hasRole('admin_super'))
        <flux:modal name="tahun-modal" class="md:w-96">
            <div class="space-y-5">
                <flux:heading>{{ $isEditing ? 'Edit Tahun Anggaran' : 'Tambah Tahun Anggaran' }}</flux:heading>

                <form wire:submit="save" class="space-y-4">
                    <flux:field>
                        <flux:label>Tahun</flux:label>
                        <flux:input
                            type="number"
                            wire:model="form.tahun"
                            placeholder="Contoh: 2025"
                            min="2000"
                            max="2100"
                        />
                        <flux:error name="form.tahun" />
                    </flux:field>

                    <flux:field>
                        <div class="flex items-center gap-3">
                            <flux:checkbox wire:model="form.is_active" id="is_active" />
                            <flux:label for="is_active">Jadikan tahun aktif</flux:label>
                        </div>
                        <flux:error name="form.is_active" />
                    </flux:field>

                    <div class="flex justify-end gap-2 pt-2">
                        <flux:button type="button" x-on:click="$flux.modal('tahun-modal').close()">Batal</flux:button>
                        <flux:button type="submit" variant="primary">Simpan</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>
