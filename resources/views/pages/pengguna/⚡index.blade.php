<?php

use App\Models\Opd;
use App\Models\User;
use App\Services\PenggunaService;
use App\Livewire\Forms\PenggunaForm;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Role as SpatieRole;
use Flux\Flux;

new #[Title('Kelola Pengguna')] class extends Component {
    public PenggunaForm $form;

    public string $search = '';

    public bool $isEditing = false;

    private PenggunaService $service;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('admin_super'), 403);
    }

    public function boot(PenggunaService $service): void
    {
        $this->service = $service;
    }

    #[Computed]
    public function pengguna()
    {
        return User::with(['opd', 'roles'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('username', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function opds()
    {
        return Opd::orderBy('name')->get();
    }

    #[Computed]
    public function roles()
    {
        return SpatieRole::orderBy('name')->get();
    }

    public function openCreate(): void
    {
        $this->form->reset();
        $this->isEditing = false;
        Flux::modal('pengguna-modal')->show();
    }

    public function openEdit(int $id): void
    {
        $this->form->setUser(User::with('roles')->findOrFail($id));
        $this->isEditing = true;
        Flux::modal('pengguna-modal')->show();
    }

    public function save(): void
    {
        $data = $this->form->validate();

        if ($this->isEditing) {
            $this->service->update(User::findOrFail($this->form->userId), $data);
            Flux::toast('Pengguna berhasil diperbarui.');
        } else {
            $this->service->store($data);
            Flux::toast('Pengguna berhasil ditambahkan.');
        }

        Flux::modal('pengguna-modal')->close();
        $this->form->reset();
    }

    public function delete(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            Flux::toast('Tidak dapat menghapus akun Anda sendiri.', variant: 'danger');

            return;
        }

        $this->service->delete($user);
        Flux::toast('Pengguna berhasil dihapus.');
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
            <flux:heading size="xl">Kelola Pengguna</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Manajemen akun pengguna sistem</flux:text>
        </div>
        @can('kelola-pengguna')
            <flux:button wire:click="openCreate" variant="primary" icon="plus">Tambah Pengguna</flux:button>
        @endcan
    </div>

    <div class="flex gap-3">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Cari nama, username, atau email..."
            icon="magnifying-glass"
            class="max-w-sm"
        />
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[600px] text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Username</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">No. WA</th>
                    <th class="px-4 py-3">OPD</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->pengguna as $user)
                    <tr wire:key="{{ $user->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</td>
                        <td class="px-4 py-3 font-mono text-zinc-600 dark:text-zinc-400">{{ $user->username }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $user->phone ?? '-' }}</td>
                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $user->opd?->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @forelse ($user->roles as $role)
                                <flux:badge color="blue" size="sm">{{ $role->name }}</flux:badge>
                            @empty
                                <span class="text-zinc-400">-</span>
                            @endforelse
                        </td>
                        <td class="px-4 py-3">
                            @can('kelola-pengguna')
                                <div class="flex gap-2">
                                    <flux:button size="sm" wire:click="openEdit({{ $user->id }})" icon="pencil">Edit</flux:button>
                                    @if ($user->id !== auth()->id())
                                        <flux:button
                                            size="sm"
                                            variant="danger"
                                            icon="trash"
                                            x-on:click="if(confirm('Hapus pengguna ini?')) $wire.delete({{ $user->id }})"
                                        >Hapus</flux:button>
                                    @endif
                                </div>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-zinc-400 dark:text-zinc-500">
                            Tidak ada data pengguna ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <flux:modal name="pengguna-modal" class="md:w-[520px]">
        <div class="space-y-5">
            <flux:heading>{{ $isEditing ? 'Edit Pengguna' : 'Tambah Pengguna' }}</flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>Nama Lengkap</flux:label>
                    <flux:input wire:model="form.name" placeholder="Nama lengkap pengguna" />
                    <flux:error name="form.name" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Username</flux:label>
                        <flux:input wire:model="form.username" placeholder="username_unik" />
                        <flux:error name="form.username" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Email</flux:label>
                        <flux:input type="email" wire:model="form.email" placeholder="email@contoh.com" />
                        <flux:error name="form.email" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>No. WhatsApp (Fonnte)</flux:label>
                    <flux:input wire:model="form.phone" placeholder="Contoh: 08123456789" />
                    <flux:error name="form.phone" />
                    <flux:description>Nomor HP untuk pengiriman notifikasi via Fonnte.</flux:description>
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ $isEditing ? 'Password Baru (opsional)' : 'Password' }}</flux:label>
                        <flux:input type="password" wire:model="form.password" placeholder="Minimal 8 karakter" />
                        <flux:error name="form.password" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Konfirmasi Password</flux:label>
                        <flux:input type="password" wire:model="form.password_confirmation" placeholder="Ulangi password" />
                        <flux:error name="form.password_confirmation" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>OPD (Opsional)</flux:label>
                    <flux:select wire:model="form.opd_id">
                        <flux:select.option value="">-- Tidak Ada --</flux:select.option>
                        @foreach ($this->opds as $opd)
                            <flux:select.option value="{{ $opd->id }}">{{ $opd->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.opd_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Role</flux:label>
                    <flux:select wire:model="form.role">
                        <flux:select.option value="">-- Pilih Role --</flux:select.option>
                        @foreach ($this->roles as $role)
                            <flux:select.option value="{{ $role->name }}">{{ $role->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.role" />
                </flux:field>

                <div class="flex justify-end gap-2 pt-2">
                    <flux:button type="button" x-on:click="$flux.modal('pengguna-modal').close()">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
