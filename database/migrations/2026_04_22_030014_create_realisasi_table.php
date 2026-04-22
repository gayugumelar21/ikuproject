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
        Schema::create('realisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikators')->cascadeOnDelete();
            $table->tinyInteger('bulan');
            $table->decimal('nilai', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('status', ['draft', 'diajukan', 'diverifikasi'])->default('draft');
            $table->timestamps();

            $table->unique(['indikator_id', 'bulan']);
            $table->index(['indikator_id', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisasi');
    }
};
