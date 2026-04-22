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
        Schema::table('realisasi', function (Blueprint $table) {
            // Deskripsi progres/capaian (naratif)
            $table->text('deskripsi_progres')->nullable()->after('keterangan');

            // Link bukti dukung (URL eksternal, misalnya Google Drive, laporan online)
            $table->string('bukti_link', 2048)->nullable()->after('deskripsi_progres');

            // Path foto bukti yang diupload (disimpan di storage)
            $table->string('foto_bukti')->nullable()->after('bukti_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi', function (Blueprint $table) {
            $table->dropColumn(['deskripsi_progres', 'bukti_link', 'foto_bukti']);
        });
    }
};
