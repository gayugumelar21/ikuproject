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
        Schema::create('indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_anggaran_id')->constrained('tahun_anggaran')->cascadeOnDelete();
            $table->foreignId('sekda_id')->constrained('opds');
            $table->foreignId('kabag_id')->nullable()->constrained('opds')->nullOnDelete();
            $table->foreignId('asisten_id')->nullable()->constrained('opds')->nullOnDelete();
            $table->foreignId('opd_id')->nullable()->constrained('opds')->nullOnDelete();
            $table->foreignId('bidang_id')->nullable()->constrained('opds')->nullOnDelete();
            $table->foreignId('parent_indikator_id')->nullable()->constrained('indikators')->nullOnDelete();
            $table->string('nama');
            $table->text('definisi')->nullable();
            $table->string('satuan');
            $table->decimal('target', 15, 2)->default(0);
            $table->decimal('bobot', 5, 2)->default(0);
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'ditolak'])->default('draft');
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->timestamps();

            $table->index('tahun_anggaran_id');
            $table->index('sekda_id');
            $table->index(['asisten_id', 'opd_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikators');
    }
};
