<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $wa = app(App\Services\FonnteService::class);
    $skoring = App\Models\IkuSkoring::whereNotNull('indikator_id')->first();
    $indikator = $skoring->indikator;
    
    $skor = 9;
    $notes = 'Bagus sekali, test dari script backend.';
    
    $msg = "🏆 *Skor Akhir IKU Selesai (TEST)*\n\nHalo, Bupati telah memberikan skor final untuk IKU Anda.\n\nIndikator: *{$indikator->nama}*\nSkor Final: *{$skor}/10*\n\nCatatan Bupati: {$notes}\n\nTerima kasih atas kinerjanya.";

    echo "Mengirim pesan test ke 6282178535114...\n";
    $result = $wa->sendMessage('6282178535114', $msg);

    print_r($result);
    echo "\nSelesai test FonnteService!\n";
} catch (\Throwable $e) {
    echo "Error FonnteService: " . $e->getMessage() . "\n";
}
