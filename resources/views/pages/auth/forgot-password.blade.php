<x-layouts::auth :title="__('Lupa Kata Sandi')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Lupa Kata Sandi')" :description="__('Masukkan email Anda untuk menerima tautan reset kata sandi')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <flux:field>
                <flux:label>Alamat Email</flux:label>
                <flux:input
                    name="email"
                    type="email"
                    required
                    autofocus
                    placeholder="email@example.com"
                />
                <flux:error name="email" />
            </flux:field>

            <flux:button variant="primary" type="submit" class="w-full">
                Kirim Tautan Reset
            </flux:button>
        </form>

        <div class="text-center text-sm text-zinc-400">
            <span>Atau, kembali ke</span>
            <flux:link :href="route('login')" wire:navigate>halaman masuk</flux:link>
        </div>
    </div>
</x-layouts::auth>