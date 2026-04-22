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
        Schema::create('iku_skorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikators')->cascadeOnDelete();
            $table->foreignId('realisasi_id')->nullable()->constrained('realisasi')->nullOnDelete();
            $table->tinyInteger('bulan');
            $table->smallInteger('tahun');

            // Skor AI (otomatis)
            $table->tinyInteger('skor_ai')->nullable();
            $table->text('ai_reasoning')->nullable();
            $table->timestamp('ai_generated_at')->nullable();

            // Skor Tenaga Ahli / Superadmin (pertimbangan)
            $table->tinyInteger('skor_ta')->nullable();
            $table->text('ta_notes')->nullable();
            $table->foreignId('ta_scored_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ta_scored_at')->nullable();

            // Skor Bupati (keputusan mutlak, 1–10)
            $table->tinyInteger('skor_bupati')->nullable();
            $table->text('bupati_notes')->nullable();
            $table->timestamp('bupati_scored_at')->nullable();

            $table->boolean('is_final')->default(false);
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalized_at')->nullable();

            $table->enum('status', ['pending', 'ai_done', 'ta_done', 'final'])->default('pending');

            $table->timestamps();

            $table->unique(['indikator_id', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iku_skorings');
    }
};
