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
        Schema::create('persetujuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikators')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('level', ['kabag', 'asisten', 'sekda', 'bupati']);
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['indikator_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persetujuan');
    }
};
