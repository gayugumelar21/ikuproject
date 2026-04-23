@props([
    'sidebar' => false,
])

@if ($sidebar)
    <flux:sidebar.brand name="Sistem Informasi IKU" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center">
            <img src="{{ asset('img/logo.png') }}" alt="Logo IKU Pringsewu" class="size-8 object-contain" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Sistem IKU Pringsewu" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center">
            <img src="{{ asset('img/logo.png') }}" alt="Logo IKU Pringsewu" class="size-8 object-contain" />
        </x-slot>
    </flux:brand>
@endif
