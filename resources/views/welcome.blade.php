<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Sistem IKU') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-50 dark:bg-neutral-950 text-neutral-800 dark:text-neutral-200 antialiased font-sans">

        {{-- Navbar --}}
        <header class="w-full border-b border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 sticky top-0 z-50">
            <div class="max-w-6xl mx-auto px-6 h-14 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="size-8 object-contain" />
                    <span class="font-semibold text-sm tracking-tight">{{ config('app.name', 'Sistem IKU') }}</span>
                </div>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center px-4 py-1.5 text-sm font-medium bg-zinc-900 dark:bg-white text-white dark:text-black rounded-md hover:bg-zinc-700 dark:hover:bg-neutral-200 transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="text-sm text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors">
                                Masuk
                            </a>
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        {{-- Hero Section --}}
        <main>
            <section class="max-w-6xl mx-auto px-4 sm:px-6 py-12 sm:py-20 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 dark:bg-neutral-800 text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-6">
                    <span class="size-1.5 rounded-full bg-green-500"></span>
                    Sistem Informasi Pemerintah Daerah
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold tracking-tight text-neutral-900 dark:text-white mb-5 leading-tight">
                    Sistem IKU<br>
                    <span class="text-neutral-500 dark:text-neutral-400">Kabupaten Pringsewu</span>
                </h1>

                <p class="max-w-xl mx-auto text-neutral-500 dark:text-neutral-400 text-base leading-relaxed mb-8">
                    Platform terintegrasi untuk pengelolaan, pemantauan, dan pelaporan Indikator Kinerja Utama (IKU) antar Organisasi Perangkat Daerah (OPD).
                </p>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-zinc-900 dark:bg-white text-white dark:text-black text-sm font-medium rounded-md hover:bg-zinc-700 dark:hover:bg-neutral-200 transition-colors">
                        Buka Dashboard
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                            <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-zinc-900 dark:bg-white text-white dark:text-black text-sm font-medium rounded-md hover:bg-zinc-700 dark:hover:bg-neutral-200 transition-colors">
                        Masuk ke Sistem
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                            <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endauth
            </section>

            {{-- Feature Grid --}}
            <section class="max-w-6xl mx-auto px-6 pb-20">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl p-5">
                        <div class="flex items-center justify-center size-9 rounded-lg bg-blue-50 dark:bg-blue-900/20 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-blue-600 dark:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-sm mb-1">Indikator & Target</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">Kelola indikator kinerja dan tetapkan target capaian per tahun anggaran untuk setiap OPD.</p>
                    </div>

                    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl p-5">
                        <div class="flex items-center justify-center size-9 rounded-lg bg-green-50 dark:bg-green-900/20 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-green-600 dark:text-green-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-sm mb-1">Realisasi & Persetujuan</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">Input realisasi capaian kinerja dan proses persetujuan bertahap oleh pejabat berwenang.</p>
                    </div>

                    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl p-5">
                        <div class="flex items-center justify-center size-9 rounded-lg bg-purple-50 dark:bg-purple-900/20 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-purple-600 dark:text-purple-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-sm mb-1">Skoring & Rekap</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">Hitung skoring otomatis, rekap capaian, dan perbandingan kinerja antar OPD secara komprehensif.</p>
                    </div>

                    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl p-5">
                        <div class="flex items-center justify-center size-9 rounded-lg bg-orange-50 dark:bg-orange-900/20 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-orange-600 dark:text-orange-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-sm mb-1">Manajemen OPD</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">Kelola data Organisasi Perangkat Daerah beserta pengguna dan hak akses masing-masing.</p>
                    </div>

                    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl p-5">
                        <div class="flex items-center justify-center size-9 rounded-lg bg-teal-50 dark:bg-teal-900/20 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-teal-600 dark:text-teal-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-sm mb-1">Kerjasama Lintas OPD</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">Fasilitasi kolaborasi dan kerjasama antar OPD dalam pencapaian target kinerja bersama.</p>
                    </div>

                    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl p-5">
                        <div class="flex items-center justify-center size-9 rounded-lg bg-rose-50 dark:bg-rose-900/20 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-rose-600 dark:text-rose-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-sm mb-1">Laporan & Ekspor</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">Unduh laporan rekap dan detail kinerja OPD dalam format Excel dan PDF.</p>
                    </div>

                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="border-t border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900">
            <div class="max-w-6xl mx-auto px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-xs text-neutral-400 dark:text-neutral-500">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Sistem IKU') }}. Dinas Komunikasi dan Informatika.
                </p>
                <p class="text-xs text-neutral-400 dark:text-neutral-500">
                    Sistem Informasi Indikator Kinerja Utama Kabupaten Pringsewu
                </p>
            </div>
        </footer>

    </body>
</html>
