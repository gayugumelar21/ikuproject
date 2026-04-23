<?php

use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts.auth')] #[Title('Ganti Password')] class extends Component
{
    #[Validate('required|min:8')]
    public string $password = '';

    #[Validate('required|same:password')]
    public string $passwordConfirmation = '';

    public function mount(): void
    {
        // Jika user tidak wajib ganti password, redirect ke dashboard
        if (! auth()->user()->must_change_password) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function save(): void
    {
        $this->validate();

        /** @var User $user */
        $user = auth()->user();

        if ($this->password === $user->username) {
            $this->addError('password', 'Password tidak boleh sama dengan username Anda.');

            return;
        }

        $user->update([
            'password' => $this->password,
            'must_change_password' => false,
        ]);

        Flux::toast('Password berhasil diperbarui. Selamat datang!', variant: 'success');

        // Redirect sesuai role setelah ganti password
        $redirect = match (true) {
            $user->hasRole('admin_super') => route('dashboard'),
            $user->hasRole('bupati') => route('skoring-bupati.index'),
            $user->hasRole('sekda') => route('rekap.index'),
            $user->hasRole('asisten') => route('rekap.index'),
            $user->hasRole('kepala_dinas') => route('indikator.index'),
            $user->hasRole('kepala_bidang') => route('realisasi.index'),
            $user->hasRole('kabag') => route('realisasi.index'),
            default => route('dashboard'),
        };

        $this->redirect($redirect, navigate: true);
    }
};
?>

<div class="flex items-center justify-center min-h-screen bg-zinc-50 dark:bg-zinc-900 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-8">
            <div class="text-center mb-6">
                <div class="inline-flex p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 mb-4">
                    <flux:icon name="key" class="size-8 text-yellow-600 dark:text-yellow-400" />
                </div>
                <flux:heading size="xl">Ganti Password</flux:heading>
                <flux:text class="text-zinc-500 mt-1">
                    Anda diwajibkan mengganti password sebelum melanjutkan.
                </flux:text>
            </div>

            <form wire:submit="save" class="space-y-5">
                <flux:field>
                    <flux:label>Password Baru</flux:label>
                    <flux:input
                        wire:model="password"
                        type="password"
                        placeholder="Minimal 8 karakter"
                        required
                    />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label>Konfirmasi Password</flux:label>
                    <flux:input
                        wire:model="passwordConfirmation"
                        type="password"
                        placeholder="Ulangi password baru"
                        required
                    />
                    <flux:error name="passwordConfirmation" />
                </flux:field>

                <flux:callout icon="information-circle" color="blue" class="text-sm">
                    <flux:callout.text>
                        Password minimal 8 karakter dan tidak boleh sama dengan NIP atau username Anda.
                    </flux:callout.text>
                </flux:callout>

                <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
                    <span wire:loading.remove>Simpan Password Baru</span>
                    <span wire:loading>Menyimpan...</span>
                </flux:button>
            </form>
        </div>
    </div>
</div>
