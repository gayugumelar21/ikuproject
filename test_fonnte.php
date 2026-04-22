<?php

/**
 * Test Fonnte WhatsApp API
 * Jalankan: php test_fonnte.php 6281234567890
 */

$token = 'J3VtsYMinDL1wbQscAiQ';

// Ambil nomor dari argumen CLI
if (empty($argv[1])) {
    echo "❌ Masukkan nomor tujuan sebagai argumen!\n";
    echo "   Contoh: php test_fonnte.php 6281234567890\n";
    echo "   Atau  : php test_fonnte.php 081234567890\n";
    exit(1);
}

$input = trim($argv[1]);

// Auto-konversi: 08xxx → 628xxx
if (str_starts_with($input, '08')) {
    $nomor628 = '62' . substr($input, 1);
} elseif (str_starts_with($input, '628')) {
    $nomor628 = $input;
} elseif (str_starts_with($input, '+62')) {
    $nomor628 = substr($input, 1); // hapus +
} else {
    $nomor628 = $input;
}

echo "=== Fonnte API Test ===\n";
echo "Token  : " . substr($token, 0, 8) . "...\n";
echo "Input  : $input\n";
echo "Dikirim: $nomor628\n\n";

// ---- Step 1: Cek status device ----
echo "--- [1] Cek status device / token ---\n";
$ch = curl_init('https://api.fonnte.com/device');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,          // Fonnte butuh POST
    CURLOPT_HTTPHEADER     => ["Authorization: $token"],
    CURLOPT_POSTFIELDS     => [],
]);
$res  = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP: $http\n";
$dev = json_decode($res, true);
echo "Response: " . json_encode($dev, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Cek apakah device connected
$connected = $dev['status'] ?? false;
if (!$connected) {
    echo "⚠️  Device tidak terhubung atau token salah. Periksa dashboard Fonnte.\n\n";
}

// ---- Step 2: Kirim pesan test ----
echo "--- [2] Kirim pesan test ke $nomor628 ---\n";
$ch = curl_init('https://api.fonnte.com/send');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ["Authorization: $token"],
    CURLOPT_POSTFIELDS     => [
        'target'  => $nomor628,
        'message' => "[TEST] Halo dari Sistem IKU Diskominfo 🚀\nNotifikasi WA berhasil dikonfigurasi!\n\n" . now()->format('d/m/Y H:i'),
    ],
]);
$res  = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP: $http\n";
$send = json_decode($res, true);
echo "Response: " . json_encode($send, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// ---- Kesimpulan ----
echo "=== HASIL ===\n";
if (($send['status'] ?? false) === true) {
    echo "✅ Berhasil! Pesan terkirim ke $nomor628\n";
    echo "   Format yang benar: gunakan 628xxx (tanpa +, tanpa 0 di awal)\n";
} else {
    $reason = $send['reason'] ?? 'unknown';
    echo "❌ Gagal. Alasan: $reason\n";
    if ($reason === 'target input invalid') {
        echo "   Coba format nomor berbeda: 628xxxxxxxxxx\n";
    }
}

function now(): \DateTime {
    return new \DateTime('now', new \DateTimeZone('Asia/Makassar'));
}
