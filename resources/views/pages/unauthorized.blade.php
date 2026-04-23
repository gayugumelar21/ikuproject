<x-layouts.app :title="'Akses Ditolak'">
    <flux:main>
        <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
            <div class="p-4 rounded-full bg-red-100 dark:bg-red-900/30 mb-6">
                <flux:icon name="shield-exclamation" class="size-16 text-red-500" />
            </div>
            <flux:heading size="xl" class="mb-2">Akses Ditolak</flux:heading>
            <flux:text class="text-zinc-500 max-w-md mb-8">
                Anda tidak memiliki izin untuk mengakses halaman ini.
                Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
            </flux:text>
            <div class="flex gap-3">
                <flux:button :href="route('dashboard')" wire:navigate>
                    Kembali ke Dashboard
                </flux:button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:button type="submit" variant="ghost">
                        Keluar
                    </flux:button>
                </form>
            </div>
        </div>
    </flux:main>
</x-layouts.app>
