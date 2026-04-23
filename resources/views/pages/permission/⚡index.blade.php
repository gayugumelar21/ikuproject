<?php

use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Flux\Flux;

new #[Title('Kelola Permission')] class extends Component {
    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('admin_super'), 403);
    }

    public string $search = '';

    public string $permissionName = '';

    #[Computed]
    public function permissions()
    {
        return Permission::with('roles')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();
    }

    public function openCreate(): void
    {
        $this->permissionName = '';
        $this->resetValidation();
        Flux::modal('permission-modal')->show();
    }

    public function save(): void
    {
        $this->validate([
            'permissionName' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:permissions,name'],
        ]);

        Permission::create([
            'name' => $this->permissionName,
            'guard_name' => 'web',
        ]);

        Flux::toast('Permission berhasil ditambahkan.');
        Flux::modal('permission-modal')->close();
        $this->permissionName = '';
    }

    public function delete(int $id): void
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        Flux::toast('Permission berhasil dihapus.');
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
            <flux:heading size="xl">Kelola Permission</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Manajemen izin akses dalam sistem</flux:text>
        </div>
        @if (auth()->user()->hasRole('admin_super'))
            <flux:button wire:click="openCreate" variant="primary" icon="plus">Tambah Permission</flux:button>
        @endif
    </div>

    <div class="flex gap-3">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Cari nama permission..."
            icon="magnifying-glass"
            class="max-w-sm"
        />
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[600px] text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3">Nama Permission</th>
                    <th class="px-4 py-3">Digunakan oleh Role</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->permissions as $permission)
                    <tr wire:key="{{ $permission->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-zinc-800 dark:text-zinc-200">
                            {{ $permission->name }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($permission->roles as $role)
                                    <flux:badge wire:key="{{ $role->id }}" color="blue" size="sm">
                                        {{ $role->name }}
                                    </flux:badge>
                                @empty
                                    <span class="text-zinc-400 dark:text-zinc-500 text-xs italic">Belum digunakan</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if (auth()->user()->hasRole('admin_super'))
                                <flux:button
                                    size="sm"
                                    variant="danger"
                                    icon="trash"
                                    x-on:click="if(confirm(`Hapus permission '{{ $permission->name }}'?`)) $wire.delete({{ $permission->id }})"
                                >Hapus</flux:button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-10 text-center text-zinc-400 dark:text-zinc-500">
                            Tidak ada data permission ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (auth()->user()->hasRole('admin_super'))
        <flux:modal name="permission-modal" class="md:w-96">
            <div class="space-y-5">
                <div>
                    <flux:heading>Tambah Permission</flux:heading>
                    <flux:text class="mt-1 text-zinc-500 text-sm">
                        Gunakan format slug, contoh: <code class="font-mono">kelola-opd</code>
                    </flux:text>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <flux:field>
                        <flux:label>Nama Permission</flux:label>
                        <flux:input
                            wire:model="permissionName"
                            placeholder="contoh: kelola-opd"
                        />
                        <flux:error name="permissionName" />
                    </flux:field>

                    <div class="flex justify-end gap-2 pt-2">
                        <flux:button type="button" x-on:click="$flux.modal('permission-modal').close()">Batal</flux:button>
                        <flux:button type="submit" variant="primary">Simpan</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>
