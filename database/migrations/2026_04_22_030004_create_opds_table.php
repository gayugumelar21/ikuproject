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
        Schema::create('opds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('opds')->nullOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['sekda', 'asisten', 'kabag', 'opd', 'bidang']);
            $table->timestamps();

            $table->index('parent_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opds');
    }
};
