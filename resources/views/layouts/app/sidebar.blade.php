<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Master Data')" class="grid">
                    <flux:sidebar.item
                        icon="building-office"
                        :href="route('opd.index')"
                        :current="request()->routeIs('opd.index')"
                        wire:navigate
                    >
                        {{ __('OPD') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="calendar"
                        :href="route('tahun-anggaran.index')"
                        :current="request()->routeIs('tahun-anggaran.index')"
                        wire:navigate
                    >
                        {{ __('Tahun Anggaran') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Pengguna & Akses')" class="grid">
                    <flux:sidebar.item
                        icon="users"
                        :href="route('pengguna.index')"
                        :current="request()->routeIs('pengguna.index')"
                        wire:navigate
                    >
                        {{ __('Pengguna') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="shield-check"
                        :href="route('role.index')"
                        :current="request()->routeIs('role.index')"
                        wire:navigate
                    >
                        {{ __('Role') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="key"
                        :href="route('permission.index')"
                        :current="request()->routeIs('permission.index')"
                        wire:navigate
                    >
                        {{ __('Permission') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('IKU')" class="grid">
                    <flux:sidebar.item
                        icon="chart-bar"
                        :href="route('indikator.index')"
                        :current="request()->routeIs('indikator.index')"
                        wire:navigate
                    >
                        {{ __('Indikator') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="table-cells"
                        :href="route('target-indikator.index')"
                        :current="request()->routeIs('target-indikator.index')"
                        wire:navigate
                    >
                        {{ __('Target Indikator') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="arrow-trending-up"
                        :href="route('realisasi.index')"
                        :current="request()->routeIs('realisasi.index')"
                        wire:navigate
                    >
                        {{ __('Realisasi') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Monitoring')" class="grid">
                    <flux:sidebar.item
                        icon="check-badge"
                        :href="route('persetujuan.index')"
                        :current="request()->routeIs('persetujuan.index')"
                        wire:navigate
                    >
                        {{ __('Persetujuan') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="presentation-chart-line"
                        :href="route('rekap.index')"
                        :current="request()->routeIs('rekap.index')"
                        wire:navigate
                    >
                        {{ __('Rekap Capaian') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Skoring IKU')" class="grid">
                    <flux:sidebar.item
                        icon="star"
                        :href="route('skoring-ta.index')"
                        :current="request()->routeIs('skoring-ta.index')"
                        wire:navigate
                    >
                        {{ __('Skoring TA') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="trophy"
                        :href="route('skoring-bupati.index')"
                        :current="request()->routeIs('skoring-bupati.index')"
                        wire:navigate
                    >
                        {{ __('Skoring Bupati') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Sistem')" class="grid">
                    <flux:sidebar.item
                        icon="cog-6-tooth"
                        :href="route('pengaturan.index')"
                        :current="request()->routeIs('pengaturan.index')"
                        wire:navigate
                    >
                        {{ __('Pengaturan') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            {{-- Notification Bell --}}
            @php $notifCount = auth()->user()->unreadNotifications()->count(); @endphp
            @if($notifCount > 0)
                <flux:dropdown position="bottom" align="end">
                    <flux:button variant="ghost" size="sm" class="relative">
                        <flux:icon name="bell" class="size-5" />
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold">
                            {{ $notifCount > 9 ? '9+' : $notifCount }}
                        </span>
                    </flux:button>
                    <flux:menu class="w-80">
                        <div class="px-3 py-2 border-b border-zinc-200 dark:border-zinc-700">
                            <flux:heading size="sm">Notifikasi ({{ $notifCount }})</flux:heading>
                        </div>
                        @foreach(auth()->user()->unreadNotifications()->latest()->limit(5)->get() as $notif)
                            <flux:menu.item class="py-2">
                                <div class="text-xs text-zinc-700 dark:text-zinc-300">{{ $notif->data['message'] ?? '-' }}</div>
                                <div class="text-xs text-zinc-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</div>
                            </flux:menu.item>
                        @endforeach
                    </flux:menu>
                </flux:dropdown>
            @endif

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
