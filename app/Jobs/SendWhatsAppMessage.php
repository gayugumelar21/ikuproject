<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(
        public readonly string $phone,
        public readonly string $message,
        public readonly string $type = 'notification',
        public readonly ?int $recipientUserId = null,
        public readonly ?int $sentBy = null,
    ) {}

    public function handle(WhatsAppService $wa): void
    {
        $wa->kirim($this->phone, $this->message, $this->type, $this->recipientUserId, $this->sentBy);
    }
}
