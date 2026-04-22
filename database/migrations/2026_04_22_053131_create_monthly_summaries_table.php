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
        Schema::create('monthly_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opd_id')->constrained('opds')->cascadeOnDelete();
            $table->tinyInteger('bulan');
            $table->smallInteger('tahun');
            $table->decimal('skor_utama', 5, 2)->nullable();
            $table->decimal('skor_kerjasama', 5, 2)->nullable();
            $table->decimal('skor_total', 5, 2)->nullable();
            $table->boolean('is_complete')->default(false);
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['opd_id', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_summaries');
    }
};
