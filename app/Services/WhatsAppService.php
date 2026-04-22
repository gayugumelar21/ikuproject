<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use App\Models\WaLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function kirim(string $phone, string $message, string $type = 'notification', ?int $recipientUserId = null, ?int $sentBy = null): WaLog
    {
        $log = WaLog::create([
            'recipient_user_id' => $recipientUserId,
            'recipient_phone' => $phone,
            'message_type' => $type,
            'message' => $message,
            'status' => 'pending',
            'sent_by' => $sentBy,
        ]);

        if (! Setting::get('wa_enabled', false)) {
            $log->update(['status' => 'failed', 'provider_response' => ['error' => 'WhatsApp dinonaktifkan']]);

            return $log;
        }

        $apiKey = Setting::get('wa_api_key', '');

        if (! $apiKey) {
            $log->update(['status' => 'failed', 'provider_response' => ['error' => 'API key tidak dikonfigurasi']]);

            return $log;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(['token' => $apiKey])
                ->post('https://fontee.io/api/send', [
                    'target' => $phone,
                    'message' => $message,
                ]);

            $log->update([
                'status' => $response->successful() ? 'sent' : 'failed',
                'provider_response' => $response->json(),
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp send error: '.$e->getMessage());
            $log->update(['status' => 'failed', 'provider_response' => ['error' => $e->getMessage()]]);
        }

        return $log;
    }

    public function kirimKeUser(User $user, string $message, string $type = 'notification', ?int $sentBy = null): ?WaLog
    {
        if (! $user->phone_wa) {
            return null;
        }

        return $this->kirim($user->phone_wa, $message, $type, $user->id, $sentBy);
    }
}
