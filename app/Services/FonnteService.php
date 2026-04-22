<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FonnteService
{
    protected $apiToken;

    public function __construct()
    {
        $this->apiToken = config('services.fonnte.token');
    }
    
    public function sendMessage($to, $message, $mediaUrl = null)
    {
        $url = "https://api.fonnte.com/send";

        // Normalisasi nomor 08... menjadi 628...
        $to = preg_replace('/\D/', '', $to); // hapus non-angka
        if (str_starts_with($to, '0')) {
            $to = '62' . substr($to, 1);
        }

        $payload = [
            'target' => $to,
            'message' => $message,
        ];

        if ($mediaUrl) {
            $payload['url'] = $mediaUrl;
        }

        $response = Http::asForm()->withHeaders([
            'Authorization' => $this->apiToken,
        ])->post($url, $payload);

        return $response->json();
    }
}
