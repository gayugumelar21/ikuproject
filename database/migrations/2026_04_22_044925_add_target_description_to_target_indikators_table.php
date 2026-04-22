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
        Schema::table('target_indikators', function (Blueprint $table) {
            $table->text('target_description')->nullable()->after('target');
        });
    }

    public function down(): void
    {
        Schema::table('target_indikators', function (Blueprint $table) {
            $table->dropColumn('target_description');
        });
    }
};
