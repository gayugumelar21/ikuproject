<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- BUPATI USERS ---\n";
foreach(App\Models\User::role('bupati')->get() as $u) {
    echo "{$u->name} (Phone: {$u->phone})\n";
}
echo "\n--- USERS WITH PHONE ---\n";
foreach(App\Models\User::whereNotNull('phone')->get() as $u) {
    echo "{$u->name} - Roles: {$u->roles->pluck('name')->join(', ')}\n";
}

echo "\n--- INDIKATORS IN SKORING ---\n";
$skorings = App\Models\IkuSkoring::take(5)->get();
foreach($skorings as $s) {
    $ind = $s->indikator;
    $owner = $ind->owner;
    echo "Skoring ID {$s->id} | Indikator: {$ind->nama} | Owner: " . ($owner ? "{$owner->name} ({$owner->phone})" : "NULL") . "\n";
}
