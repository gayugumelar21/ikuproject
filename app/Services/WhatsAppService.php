<?php

namespace App\Services;

use App\Models\User;
use App\Models\WaLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $token;
    protected string $baseUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token', '');
    }

    /**
     * Normalisasi nomor ke format 628xxx
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone); // hapus non-angka
        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }
        return $phone;
    }

    /**
     * Kirim notifikasi ke user berdasarkan object User (menggunakan field phone)
     */
    public function notifyUser(User $user, string $message): bool
    {
        if (empty($user->phone)) {
            Log::info("Skipping WA notification for user {$user->name}: No phone number.");
            return false;
        }

        return $this->sendMessage($user->phone, $message);
    }

    /**
     * Method utama yang dipanggil oleh SendWhatsAppMessage Job
     * Mengirim pesan + mencatat ke wa_logs
     */
    public function kirim(
        string $phone,
        string $message,
        string $type = 'notification',
        ?int $recipientUserId = null,
        ?int $sentBy = null,
    ): bool {
        $result  = $this->sendMessage($phone, $message);
        $status  = $result ? 'sent' : 'failed';

        // Catat ke wa_logs
        try {
            WaLog::create([
                'recipient_user_id' => $recipientUserId,
                'recipient_phone'   => $this->normalizePhone($phone),
                'message_type'      => $type,
                'message'           => $message,
                'status'            => $status,
                'sent_by'           => $sentBy,
                'sent_at'           => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal menyimpan WaLog: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Kirim pesan teks ke nomor WA via Fonnte
     */
    public function sendMessage(string $to, string $message): bool
    {
        if (empty($this->token)) {
            Log::warning('Fonnte token is missing.');
            return false;
        }

        $to = $this->normalizePhone($to);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->baseUrl, [
                'target'  => $to,
                'message' => $message,
            ]);

            $body = $response->json();

            if ($response->failed() || ($body['status'] ?? false) === false) {
                Log::error('Fonnte API error', [
                    'status'   => $response->status(),
                    'response' => $body,
                    'target'   => $to,
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsappService exception', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
