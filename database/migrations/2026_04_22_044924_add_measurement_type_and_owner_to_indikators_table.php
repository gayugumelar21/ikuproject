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
            $table->enum('measurement_type', ['kuantitatif', 'kualitatif'])
                ->default('kuantitatif')
                ->after('satuan');

            $table->foreignId('owner_user_id')
                ->nullable()
                ->after('dibuat_oleh')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('indikators', function (Blueprint $table) {
            $table->dropForeign(['owner_user_id']);
            $table->dropColumn(['measurement_type', 'owner_user_id']);
        });
    }
};
