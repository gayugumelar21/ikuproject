<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::find(1); // Admin Super
$user->phone = '6282178535114';
$user->save();

$indikator = App\Models\Indikator::find(1); // Usually Kabid PAUD Disdik is owner
if ($indikator && $indikator->owner) {
    $indikator->owner->phone = '6282178535114';
    $indikator->owner->save();
}

echo "Berhasil update nomor Admin Super dan Owner Indikator pertama!";
