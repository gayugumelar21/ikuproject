<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$opd2 = App\Models\Opd::find(2);
echo "OPD 2 Name: " . ($opd2 ? $opd2->name : 'None') . "\n";
