<x-layouts::auth :title="__('Konfirmasi Kata Sandi')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Konfirmasi Kata Sandi')"
            :description="__('Ini adalah area aman. Harap konfirmasi kata sandi sebelum melanjutkan.')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:field>
                <flux:label>Kata Sandi</flux:label>
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

            <flux:button variant="primary" type="submit" class="w-full">
                Konfirmasi
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
