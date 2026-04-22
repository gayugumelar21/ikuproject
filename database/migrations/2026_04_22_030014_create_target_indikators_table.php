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
        Schema::create('target_indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikators')->cascadeOnDelete();
            $table->tinyInteger('bulan');
            $table->decimal('target', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['indikator_id', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_indikators');
    }
};
