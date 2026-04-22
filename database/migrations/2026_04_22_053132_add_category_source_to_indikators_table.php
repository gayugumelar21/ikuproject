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
        Schema::table('indikators', function (Blueprint $table) {
            $table->enum('category', ['utama', 'kerjasama'])->default('utama')->after('nama');
            $table->unsignedBigInteger('source_indikator_id')->nullable()->after('parent_indikator_id');
            $table->foreign('source_indikator_id')->references('id')->on('indikators')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('indikators', function (Blueprint $table) {
            $table->dropForeign(['source_indikator_id']);
            $table->dropColumn(['category', 'source_indikator_id']);
        });
    }
};
