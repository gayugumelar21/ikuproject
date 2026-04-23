<?php

use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/unauthorized', 'pages::unauthorized')->name('unauthorized');

Route::middleware(['auth', 'verified'])->group(function () {
    // Ganti password (wajib jika must_change_password = true)
    Route::livewire('ganti-password', 'pages::ganti-password')->name('ganti-password');

    // Dashboard — semua role yang sudah login
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');

    // Rekap & perbandingan — semua role
    Route::livewire('rekap', 'pages::rekap.index')->name('rekap.index');
    Route::livewire('perbandingan-skor', 'pages::perbandingan-skor.index')->name('perbandingan-skor.index');
    Route::livewire('persetujuan', 'pages::persetujuan.index')->name('persetujuan.index');

    // Export — semua role
    Route::get('export/rekap', [ExportController::class, 'excelRekap'])->name('export.rekap');
    Route::get('export/detail', [ExportController::class, 'excelDetail'])->name('export.detail');
    Route::get('export/pdf/{opdId}', [ExportController::class, 'pdfOpd'])->name('export.pdf-opd');

    // IKU — kepala_bidang, kabag, kepala_dinas, asisten, sekda, bupati, admin_super
    Route::middleware(['role:kepala_bidang|kabag|kepala_dinas|asisten|sekda|bupati|admin_super'])->group(function () {
        Route::livewire('indikator', 'pages::indikator.index')->name('indikator.index');
        Route::livewire('kerjasama', 'pages::kerjasama.index')->name('kerjasama.index');
        Route::livewire('target-indikator', 'pages::target-indikator.index')->name('target-indikator.index');
    });

    // Realisasi — kepala_bidang, kabag, kepala_dinas, admin_super
    Route::middleware(['role:kepala_bidang|kabag|kepala_dinas|admin_super'])->group(function () {
        Route::livewire('realisasi', 'pages::realisasi.index')->name('realisasi.index');
    });

    // Skoring TA — admin_super saja (Tenaga Ahli)
    Route::middleware(['role:admin_super'])->group(function () {
        Route::livewire('skoring-ta', 'pages::skoring-ta.index')->name('skoring-ta.index');
        Route::livewire('opd', 'pages::opd.index')->name('opd.index');
        Route::livewire('pengguna', 'pages::pengguna.index')->name('pengguna.index');
        Route::livewire('tahun-anggaran', 'pages::tahun-anggaran.index')->name('tahun-anggaran.index');
        Route::livewire('role', 'pages::role.index')->name('role.index');
        Route::livewire('permission', 'pages::permission.index')->name('permission.index');
        Route::livewire('pengaturan', 'pages::pengaturan.index')->name('pengaturan.index');
    });

    // Skoring Bupati — bupati dan admin_super
    Route::middleware(['role:bupati|admin_super'])->group(function () {
        Route::livewire('skoring-bupati', 'pages::skoring-bupati.index')->name('skoring-bupati.index');
    });
});

require __DIR__.'/settings.php';
