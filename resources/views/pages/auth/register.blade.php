<x-layouts::auth :title="__('Daftar')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Buat Akun')" :description="__('Masukkan detail di bawah ini untuk membuat akun')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:field>
                <flux:label>Nama Lengkap</flux:label>
                <flux:input
                    name="name"
                    :value="old('name')"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Nama lengkap"
                />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Username</flux:label>
                <flux:input
                    name="username"
                    :value="old('username')"
                    type="text"
                    required
                    autocomplete="username"
                    placeholder="Contoh: john_doe"
                />
                <flux:description>Hanya huruf, angka, dan underscore (_). Min. 3 karakter.</flux:description>
                <flux:error name="username" />
            </flux:field>

            <flux:field>
                <flux:label>Alamat Email</flux:label>
                <flux:input
                    name="email"
                    :value="old('email')"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="email@example.com"
                />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>Kata Sandi</flux:label>
                <flux:input
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Kata sandi"
                    viewable
                />
                <flux:error name="password" />
            </flux:field>

            <flux:field>
                <flux:label>Konfirmasi Kata Sandi</flux:label>
                <flux:input
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi"
                    viewable
                />
                <flux:error name="password_confirmation" />
            </flux:field>

            <flux:button type="submit" variant="primary" class="w-full">
                Buat Akun
            </flux:button>
        </form>

        <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>Sudah punya akun?</span>
            <flux:link :href="route('login')" wire:navigate>Masuk</flux:link>
        </div>
    </div>
</x-layouts::auth>
