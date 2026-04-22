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
        Schema::create('rekap_capaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_anggaran_id')->constrained('tahun_anggaran')->cascadeOnDelete();
            $table->foreignId('opd_id')->constrained('opds')->cascadeOnDelete();
            $table->enum('level', ['bidang', 'opd', 'asisten', 'kabag', 'sekda']);
            $table->tinyInteger('bulan');
            $table->decimal('total_target', 15, 2)->default(0);
            $table->decimal('total_realisasi', 15, 2)->default(0);
            $table->decimal('persentase', 5, 2)->default(0);
            $table->integer('jumlah_indikator')->default(0);
            $table->integer('indikator_tercapai')->default(0);
            $table->timestamp('dihitung_pada')->nullable();
            $table->timestamps();

            $table->unique(['tahun_anggaran_id', 'opd_id', 'bulan', 'level']);
            $table->index(['tahun_anggaran_id', 'level', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_capaian');
    }
};
