<x-layouts::auth :title="__('Masuk')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Sistem IKU Kabupaten Pringsewu')" :description="__('Masukkan username dan kata sandi untuk masuk')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        @if ($errors->any())
            <flux:callout variant="danger" icon="exclamation-triangle">
                <flux:callout.text>{{ $errors->first() }}</flux:callout.text>
            </flux:callout>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:field>
                <flux:label>Username</flux:label>
                <flux:input
                    name="username"
                    :value="old('username')"
                    type="text"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Masukkan username"
                />
                <flux:error name="username" />
            </flux:field>

            <flux:field>
                <div class="flex items-center justify-between">
                    <flux:label>Kata Sandi</flux:label>
                    @if (Route::has('password.request'))
                        <flux:link class="text-sm" :href="route('password.request')" wire:navigate>
                            Lupa kata sandi?
                        </flux:link>
                    @endif
                </div>
                <flux:input
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Kata sandi"
                    viewable
                />
                <flux:error name="password" />
            </flux:field>

            <flux:checkbox name="remember" label="Ingat saya" :checked="old('remember')" />

            <flux:button variant="primary" type="submit" class="w-full">
                Masuk
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
