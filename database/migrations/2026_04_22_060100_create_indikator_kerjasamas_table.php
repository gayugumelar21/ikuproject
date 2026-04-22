<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('indikator_kerjasamas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikators')->cascadeOnDelete();
            $table->foreignId('sekda_id')->nullable()->constrained('opds')->nullOnDelete();
            $table->foreignId('kabag_id')->nullable()->constrained('opds')->nullOnDelete();
            $table->foreignId('asisten_id')->nullable()->constrained('opds')->nullOnDelete();
            $table->foreignId('opd_id')->constrained('opds')->cascadeOnDelete();
            $table->foreignId('bidang_id')->nullable()->constrained('opds')->nullOnDelete();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('peran')->nullable();
            $table->decimal('bobot', 5, 2)->default(0);
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'ditolak'])->default('draft');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['indikator_id', 'opd_id', 'bidang_id'], 'indikator_kerjasama_unique');
            $table->index(['opd_id', 'status']);
        });

        // Migrasi data lama: IKU kerjasama yang sebelumnya berupa row indikator terpisah.
        $legacyKerjasamas = DB::table('indikators')
            ->where('category', 'kerjasama')
            ->whereNotNull('source_indikator_id')
            ->orderBy('id')
            ->get([
                'source_indikator_id',
                'sekda_id',
                'kabag_id',
                'asisten_id',
                'opd_id',
                'bidang_id',
                'owner_user_id',
                'definisi',
                'bobot',
                'status',
                'dibuat_oleh',
                'created_at',
                'updated_at',
            ]);

        foreach ($legacyKerjasamas as $legacy) {
            if (! $legacy->source_indikator_id || ! $legacy->opd_id) {
                continue;
            }

            DB::table('indikator_kerjasamas')->updateOrInsert(
                [
                    'indikator_id' => $legacy->source_indikator_id,
                    'opd_id' => $legacy->opd_id,
                    'bidang_id' => $legacy->bidang_id,
                ],
                [
                    'sekda_id' => $legacy->sekda_id,
                    'kabag_id' => $legacy->kabag_id,
                    'asisten_id' => $legacy->asisten_id,
                    'owner_user_id' => $legacy->owner_user_id,
                    'peran' => $legacy->definisi,
                    'bobot' => $legacy->bobot ?? 0,
                    'status' => $legacy->status ?? 'draft',
                    'dibuat_oleh' => $legacy->dibuat_oleh,
                    'created_at' => $legacy->created_at ?? now(),
                    'updated_at' => $legacy->updated_at ?? now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_kerjasamas');
    }
};
