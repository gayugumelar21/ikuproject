<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');

    Route::livewire('opd', 'pages::opd.index')->name('opd.index');
    Route::livewire('pengguna', 'pages::pengguna.index')->name('pengguna.index');
    Route::livewire('tahun-anggaran', 'pages::tahun-anggaran.index')->name('tahun-anggaran.index');
    Route::livewire('role', 'pages::role.index')->name('role.index');
    Route::livewire('permission', 'pages::permission.index')->name('permission.index');
    Route::livewire('indikator', 'pages::indikator.index')->name('indikator.index');
    Route::livewire('target-indikator', 'pages::target-indikator.index')->name('target-indikator.index');
    Route::livewire('realisasi', 'pages::realisasi.index')->name('realisasi.index');
    Route::livewire('persetujuan', 'pages::persetujuan.index')->name('persetujuan.index');
    Route::livewire('rekap', 'pages::rekap.index')->name('rekap.index');
    Route::livewire('kerjasama', 'pages::kerjasama.index')->name('kerjasama.index');
    Route::livewire('skoring-ta', 'pages::skoring-ta.index')->name('skoring-ta.index');
    Route::livewire('skoring-bupati', 'pages::skoring-bupati.index')->name('skoring-bupati.index');
    Route::livewire('pengaturan', 'pages::pengaturan.index')->name('pengaturan.index');

    Route::get('export/rekap', [App\Http\Controllers\ExportController::class, 'excelRekap'])->name('export.rekap');
    Route::get('export/detail', [App\Http\Controllers\ExportController::class, 'excelDetail'])->name('export.detail');
    Route::get('export/pdf/{opdId}', [App\Http\Controllers\ExportController::class, 'pdfOpd'])->name('export.pdf-opd');
});

require __DIR__.'/settings.php';
