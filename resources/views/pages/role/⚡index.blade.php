<?php

use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Flux\Flux;

new #[Title('Kelola Role')] class extends Component {
    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('admin_super'), 403);
    }

    public string $roleName = '';

    public string $roleDisplayName = '';

    public array $selectedPermissions = [];

    public ?int $editingRoleId = null;

    public bool $isEditing = false;

    #[Computed]
    public function roles()
    {
        return Role::withCount('permissions')->orderBy('name')->get();
    }

    #[Computed]
    public function allPermissions()
    {
        return Permission::orderBy('name')->get();
    }

    public function openCreate(): void
    {
        $this->roleName = '';
        $this->roleDisplayName = '';
        $this->selectedPermissions = [];
        $this->editingRoleId = null;
        $this->isEditing = false;
        $this->resetValidation();
        Flux::modal('role-modal')->show();
    }

    public function openEdit(int $id): void
    {
        $role = Role::with('permissions')->findOrFail($id);
        $this->editingRoleId = $role->id;
        $this->roleName = $role->name;
        $this->roleDisplayName = $role->display_name ?? '';
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isEditing = true;
        $this->resetValidation();
        Flux::modal('role-modal')->show();
    }

    public function save(): void
    {
        $rules = [
            'roleName' => ['required', 'string', 'max:100', 'alpha_dash'],
            'roleDisplayName' => ['nullable', 'string', 'max:255'],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string', 'exists:permissions,name'],
        ];

        if ($this->isEditing) {
            $rules['roleName'][] = "unique:roles,name,{$this->editingRoleId}";
        } else {
            $rules['roleName'][] = 'unique:roles,name';
        }

        $this->validate($rules);

        if ($this->isEditing) {
            $role = Role::findOrFail($this->editingRoleId);
            $role->update([
                'name' => $this->roleName,
                'display_name' => $this->roleDisplayName ?: null,
            ]);
            $role->syncPermissions($this->selectedPermissions);
            Flux::toast('Role berhasil diperbarui.');
        } else {
            $role = Role::create([
                'name' => $this->roleName,
                'display_name' => $this->roleDisplayName ?: null,
                'guard_name' => 'web',
            ]);
            $role->syncPermissions($this->selectedPermissions);
            Flux::toast('Role berhasil ditambahkan.');
        }

        Flux::modal('role-modal')->close();
        $this->roleName = '';
        $this->roleDisplayName = '';
        $this->selectedPermissions = [];
        $this->editingRoleId = null;
        $this->isEditing = false;
    }

    public function delete(int $id): void
    {
        $role = Role::findOrFail($id);
        $role->delete();
        Flux::toast('Role berhasil dihapus.');
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
            <flux:heading size="xl">Kelola Role</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Manajemen peran dan hak akses pengguna</flux:text>
        </div>
        @if (auth()->user()->hasRole('admin_super'))
            <flux:button wire:click="openCreate" variant="primary" icon="plus">Tambah Role</flux:button>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[600px] text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3">Nama Role</th>
                    <th class="px-4 py-3">Display Name</th>
                    <th class="px-4 py-3">Jumlah Permission</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->roles as $role)
                    <tr wire:key="{{ $role->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-zinc-800 dark:text-zinc-200">{{ $role->name }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $role->display_name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <flux:badge color="blue" size="sm">{{ $role->permissions_count }} permission</flux:badge>
                        </td>
                        <td class="px-4 py-3">
                            @if (auth()->user()->hasRole('admin_super'))
                                <div class="flex gap-2">
                                    <flux:button size="sm" wire:click="openEdit({{ $role->id }})" icon="pencil">Edit</flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="danger"
                                        icon="trash"
                                        x-on:click="if(confirm('Hapus role {{ $role->name }}?')) $wire.delete({{ $role->id }})"
                                    >Hapus</flux:button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-zinc-400 dark:text-zinc-500">
                            Belum ada data role.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (auth()->user()->hasRole('admin_super'))
        <flux:modal name="role-modal" class="md:w-[560px]">
            <div class="space-y-5">
                <flux:heading>{{ $isEditing ? 'Edit Role' : 'Tambah Role' }}</flux:heading>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Nama Role</flux:label>
                            <flux:input wire:model="roleName" placeholder="contoh: admin_opd" />
                            <flux:error name="roleName" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Display Name</flux:label>
                            <flux:input wire:model="roleDisplayName" placeholder="contoh: Admin OPD" />
                            <flux:error name="roleDisplayName" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>Permissions</flux:label>
                        <div class="mt-2 max-h-64 overflow-y-auto rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 space-y-2">
                            @forelse ($this->allPermissions as $permission)
                                <label wire:key="{{ $permission->id }}" class="flex items-center gap-2 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800 px-2 py-1 rounded">
                                    <flux:checkbox
                                        wire:model="selectedPermissions"
                                        value="{{ $permission->name }}"
                                    />
                                    <span class="text-sm text-zinc-700 dark:text-zinc-300 font-mono">{{ $permission->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-zinc-400 text-center py-2">Belum ada permission terdaftar.</p>
                            @endforelse
                        </div>
                        <flux:error name="selectedPermissions" />
                    </flux:field>

                    <div class="flex justify-end gap-2 pt-2">
                        <flux:button type="button" x-on:click="$flux.modal('role-modal').close()">Batal</flux:button>
                        <flux:button type="submit" variant="primary">Simpan</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>
