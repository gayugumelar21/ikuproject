<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wa_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipient_user_id')->nullable();
            $table->string('recipient_phone', 20);
            $table->enum('message_type', ['report', 'reminder', 'blast', 'notification'])->default('notification');
            $table->text('message');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->json('provider_response')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('recipient_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('sent_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_logs');
    }
};
