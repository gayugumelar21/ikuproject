<x-layouts::auth :title="__('Reset Kata Sandi')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Reset Kata Sandi')" :description="__('Masukkan kata sandi baru Anda di bawah ini')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <flux:field>
                <flux:label>Alamat Email</flux:label>
                <flux:input
                    name="email"
                    value="{{ request('email') }}"
                    type="email"
                    required
                    autocomplete="email"
                />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>Kata Sandi Baru</flux:label>
                <flux:input
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Kata sandi baru"
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
                    placeholder="Ulangi kata sandi baru"
                    viewable
                />
                <flux:error name="password_confirmation" />
            </flux:field>

            <flux:button type="submit" variant="primary" class="w-full">
                Reset Kata Sandi
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
