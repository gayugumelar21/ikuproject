<?php

namespace Database\Seeders;

use App\Models\Indikator;
use App\Models\Opd;
use App\Models\Realisasi;
use App\Models\TahunAnggaran;
use App\Models\TargetIndikator;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DummyAsisten1Seeder
 *
 * Membuat IKU Utama untuk Asisten I (Pemerintahan & Kesra).
 * Komposisi bobot IKU Asisten I:
 *   - IKU Kabag Tata Pemerintahan   : 10%
 *   - IKU Kabag Kesejahteraan Rakyat: 10%
 *   - IKU Kabag Hukum               : 10%
 *   - Kontribusi Disdik (via child)  : 35%  (50% dari sisa 70% → 35%)
 *   - Kontribusi Diskominfo (via child): 25% (sisa → 25%)
 *   - IKU Lintas Koordinasi          : 10%
 *   ──────────────────────────────────────
 *   Total                            : 100%
 *
 * Sederhananya: sementara 50:50 antara Disdik dan Diskominfo dari porsi OPD,
 * namun karena Diskominfo berada di bawah Asisten II, bobotnya disesuaikan:
 *   - Kabag Tapem   10%, Kabag Kesra 10%, Kabag Hukum 10%  → 30%
 *   - Disdik         35%  (porsi OPD Asisten I, bobot terbesar)
 *   - Diskominfo     25%  (koordinasi lintas Asisten, lebih kecil)
 *   - IKU Koordinasi 10%
 */
class DummyAsisten1Seeder extends Seeder
{
    public function run(): void
    {
        $tahun = TahunAnggaran::where('tahun', 2026)->first();
        if (! $tahun) {
            $this->command->warn('Tahun anggaran 2026 tidak ditemukan.');

            return;
        }

        // ── Resolve OPD yang sudah ada ────────────────────────────────────
        $sekda = Opd::where('code', 'SEKDA')->first();
        $asisten1 = Opd::where('code', 'ASISTEN-I')->first();
        $disdik = Opd::where('code', 'DISDIK')->first();
        $diskominfo = Opd::where('code', 'DISKOMINFO')->first();

        if (! $asisten1) {
            $this->command->error('Asisten I tidak ditemukan. Jalankan DummyDisdikKerjasamaSeeder terlebih dahulu.');

            return;
        }
        if (! $disdik || ! $diskominfo) {
            $this->command->error('Disdik/Diskominfo tidak ditemukan. Pastikan seeder sebelumnya sudah dijalankan.');

            return;
        }

        // ── 1. Kabag-kabag di bawah Asisten I ────────────────────────────
        $kabagTapem = Opd::firstOrCreate(
            ['code' => 'KABAG-TAPEM'],
            [
                'name' => 'Bagian Tata Pemerintahan',
                'type' => 'kabag',
                'parent_id' => $asisten1->id,
            ]
        );

        $kabagKesra = Opd::firstOrCreate(
            ['code' => 'KABAG-KESRA'],
            [
                'name' => 'Bagian Kesejahteraan Rakyat',
                'type' => 'kabag',
                'parent_id' => $asisten1->id,
            ]
        );

        $kabagHukum = Opd::firstOrCreate(
            ['code' => 'KABAG-HUKUM'],
            [
                'name' => 'Bagian Hukum',
                'type' => 'kabag',
                'parent_id' => $asisten1->id,
            ]
        );

        // ── 2. User Asisten I & Kabag ─────────────────────────────────────
        $admin = User::where('username', 'admin')->first();

        $userAsisten1 = User::firstOrCreate(
            ['username' => 'asisten1'],
            [
                'name' => 'Asisten I Pemerintahan & Kesra',
                'email' => 'asisten1@pringsewu.go.id',
                'password' => Hash::make('password'),
                'opd_id' => $asisten1->id,
                'email_verified_at' => now(),
            ]
        );
        $userAsisten1->syncRoles(['asisten']);

        $userKabagTapem = User::firstOrCreate(
            ['username' => 'kabag_tapem'],
            [
                'name' => 'Kabag Tata Pemerintahan',
                'email' => 'kabag.tapem@pringsewu.go.id',
                'password' => Hash::make('password'),
                'opd_id' => $kabagTapem->id,
                'email_verified_at' => now(),
            ]
        );
        $userKabagTapem->syncRoles(['kabag']);

        $userKabagKesra = User::firstOrCreate(
            ['username' => 'kabag_kesra'],
            [
                'name' => 'Kabag Kesejahteraan Rakyat',
                'email' => 'kabag.kesra@pringsewu.go.id',
                'password' => Hash::make('password'),
                'opd_id' => $kabagKesra->id,
                'email_verified_at' => now(),
            ]
        );
        $userKabagKesra->syncRoles(['kabag']);

        $userKabagHukum = User::firstOrCreate(
            ['username' => 'kabag_hukum'],
            [
                'name' => 'Kabag Hukum',
                'email' => 'kabag.hukum@pringsewu.go.id',
                'password' => Hash::make('password'),
                'opd_id' => $kabagHukum->id,
                'email_verified_at' => now(),
            ]
        );
        $userKabagHukum->syncRoles(['kabag']);

        $dibuatOleh = $admin?->id ?? $userAsisten1->id;

        // ── 3. IKU Utama Kabag Tata Pemerintahan (bobot 10%) ─────────────
        $ikuTapem = Indikator::firstOrCreate(
            [
                'tahun_anggaran_id' => $tahun->id,
                'opd_id' => $kabagTapem->id,
                'nama' => 'Persentase Penyelenggaraan Administrasi Pemerintahan Desa yang Tertib',
            ],
            [
                'sekda_id' => $sekda?->id,
                'asisten_id' => $asisten1->id,
                'kabag_id' => $kabagTapem->id,
                'bidang_id' => null,
                'category' => 'utama',
                'measurement_type' => 'kuantitatif',
                'definisi' => 'Persentase desa/kelurahan di Kabupaten Pringsewu yang menyelenggarakan administrasi pemerintahan sesuai ketentuan peraturan perundang-undangan (laporan tepat waktu, APBDes tersusun, monografi desa terbarui).',
                'satuan' => '%',
                'target' => 90.00,
                'bobot' => 10.00,
                'owner_user_id' => $userKabagTapem->id,
                'status' => 'disetujui',
                'dibuat_oleh' => $dibuatOleh,
            ]
        );

        // ── 4. IKU Utama Kabag Kesejahteraan Rakyat (bobot 10%) ──────────
        $ikuKesra = Indikator::firstOrCreate(
            [
                'tahun_anggaran_id' => $tahun->id,
                'opd_id' => $kabagKesra->id,
                'nama' => 'Persentase Usulan Bantuan Sosial yang Diproses Sesuai SOP',
            ],
            [
                'sekda_id' => $sekda?->id,
                'asisten_id' => $asisten1->id,
                'kabag_id' => $kabagKesra->id,
                'bidang_id' => null,
                'category' => 'utama',
                'measurement_type' => 'kuantitatif',
                'definisi' => 'Persentase usulan bantuan sosial (beasiswa, bansos, PKH, dll.) yang masuk ke Bagian Kesra dan diproses verifikasi-rekomendasi tepat waktu sesuai SOP ≤ 10 hari kerja.',
                'satuan' => '%',
                'target' => 88.00,
                'bobot' => 10.00,
                'owner_user_id' => $userKabagKesra->id,
                'status' => 'disetujui',
                'dibuat_oleh' => $dibuatOleh,
            ]
        );

        // ── 5. IKU Utama Kabag Hukum (bobot 10%) ─────────────────────────
        $ikuHukum = Indikator::firstOrCreate(
            [
                'tahun_anggaran_id' => $tahun->id,
                'opd_id' => $kabagHukum->id,
                'nama' => 'Persentase Produk Hukum Daerah yang Ditetapkan Tepat Waktu',
            ],
            [
                'sekda_id' => $sekda?->id,
                'asisten_id' => $asisten1->id,
                'kabag_id' => $kabagHukum->id,
                'bidang_id' => null,
                'category' => 'utama',
                'measurement_type' => 'kuantitatif',
                'definisi' => 'Persentase rancangan produk hukum daerah (Perda, Perbup, SK Bupati) yang berhasil ditetapkan sesuai jadwal Program Legislasi Daerah tahun berjalan.',
                'satuan' => '%',
                'target' => 85.00,
                'bobot' => 10.00,
                'owner_user_id' => $userKabagHukum->id,
                'status' => 'disetujui',
                'dibuat_oleh' => $dibuatOleh,
            ]
        );

        // ── 6. IKU Koordinasi Lintas OPD (bobot 10%) ─────────────────────
        // IKU ini dimiliki Asisten I sendiri sebagai koordinator
        $ikuKoordinasi = Indikator::firstOrCreate(
            [
                'tahun_anggaran_id' => $tahun->id,
                'opd_id' => $asisten1->id,
                'nama' => 'Indeks Koordinasi Asisten I dengan OPD Binaan',
            ],
            [
                'sekda_id' => $sekda?->id,
                'asisten_id' => $asisten1->id,
                'kabag_id' => null,
                'bidang_id' => null,
                'category' => 'utama',
                'measurement_type' => 'kualitatif',
                'definisi' => 'Indeks koordinasi yang diukur berdasarkan frekuensi rapat koordinasi, tindak lanjut rekomendasi, dan tingkat responsivitas OPD binaan terhadap arahan Asisten I.',
                'satuan' => 'indeks',
                'target' => 80.00,
                'bobot' => 10.00,
                'owner_user_id' => $userAsisten1->id,
                'status' => 'disetujui',
                'dibuat_oleh' => $dibuatOleh,
            ]
        );

        // ── 7. IKU Kontribusi Disdik → Asisten I (bobot 35%) ─────────────
        // Disdik berada di bawah Asisten I, sehingga capaian Disdik
        // berkontribusi 35% terhadap skor Asisten I.
        // Menggunakan IKU proxy (opd_id = asisten1, parent_indikator = null)
        // yang merefleksikan rata-rata capaian Disdik.
        $ikuKontribusiDisdik = Indikator::firstOrCreate(
            [
                'tahun_anggaran_id' => $tahun->id,
                'opd_id' => $asisten1->id,
                'nama' => '[Kontribusi] Capaian Rata-rata IKU Dinas Pendidikan dan Kebudayaan',
            ],
            [
                'sekda_id' => $sekda?->id,
                'asisten_id' => $asisten1->id,
                'kabag_id' => null,
                'bidang_id' => null,
                'category' => 'utama',
                'measurement_type' => 'kuantitatif',
                'definisi' => 'Rata-rata persentase capaian seluruh IKU Dinas Pendidikan dan Kebudayaan yang menjadi tanggung jawab koordinasi Asisten I. Skor ini otomatis diambil dari rata-rata tertimbang IKU Disdik yang telah difinalisasi.',
                'satuan' => '%',
                'target' => 90.00,
                'bobot' => 35.00,
                'owner_user_id' => $userAsisten1->id,
                'status' => 'disetujui',
                'dibuat_oleh' => $dibuatOleh,
            ]
        );

        // ── 8. IKU Kontribusi Diskominfo → Asisten I (bobot 25%) ─────────
        // Diskominfo berada di Asisten II, tapi ada koordinasi lintas Asisten
        // untuk program digitalisasi pemerintahan & kesra.
        $ikuKontribusiDiskominfo = Indikator::firstOrCreate(
            [
                'tahun_anggaran_id' => $tahun->id,
                'opd_id' => $asisten1->id,
                'nama' => '[Kontribusi] Capaian Program Digitalisasi Pemerintahan & Kesra (Diskominfo)',
            ],
            [
                'sekda_id' => $sekda?->id,
                'asisten_id' => $asisten1->id,
                'kabag_id' => null,
                'bidang_id' => null,
                'category' => 'utama',
                'measurement_type' => 'kuantitatif',
                'definisi' => 'Persentase capaian program digitalisasi layanan pemerintahan dan kesejahteraan rakyat yang dikoordinasikan Asisten I bersama Dinas Komunikasi dan Informatika, mencakup e-government desa, sistem data sosial terpadu, dan digitalisasi administrasi kependudukan.',
                'satuan' => '%',
                'target' => 85.00,
                'bobot' => 25.00,
                'owner_user_id' => $userAsisten1->id,
                'status' => 'disetujui',
                'dibuat_oleh' => $dibuatOleh,
            ]
        );

        // ── 9. Target Bulanan ─────────────────────────────────────────────
        $targetsBulanan = [
            // IKU Kabag Tapem (target bertahap, naik dari 75% ke 90%)
            $ikuTapem->id => [
                1 => 75, 2 => 78, 3 => 80, 4 => 82, 5 => 83, 6 => 84,
                7 => 85, 8 => 86, 9 => 87, 10 => 88, 11 => 89, 12 => 90,
            ],
            // IKU Kabag Kesra
            $ikuKesra->id => [
                1 => 70, 2 => 73, 3 => 76, 4 => 78, 5 => 80, 6 => 82,
                7 => 83, 8 => 84, 9 => 85, 10 => 86, 11 => 87, 12 => 88,
            ],
            // IKU Kabag Hukum
            $ikuHukum->id => [
                1 => 60, 2 => 65, 3 => 68, 4 => 70, 5 => 72, 6 => 74,
                7 => 75, 8 => 78, 9 => 80, 10 => 82, 11 => 83, 12 => 85,
            ],
            // IKU Koordinasi (indeks 0-100)
            $ikuKoordinasi->id => [
                1 => 65, 2 => 67, 3 => 69, 4 => 71, 5 => 73, 6 => 75,
                7 => 75, 8 => 76, 9 => 77, 10 => 78, 11 => 79, 12 => 80,
            ],
            // Kontribusi Disdik
            $ikuKontribusiDisdik->id => [
                1 => 75, 2 => 77, 3 => 79, 4 => 81, 5 => 82, 6 => 83,
                7 => 84, 8 => 85, 9 => 86, 10 => 87, 11 => 88, 12 => 90,
            ],
            // Kontribusi Diskominfo
            $ikuKontribusiDiskominfo->id => [
                1 => 55, 2 => 60, 3 => 63, 4 => 66, 5 => 68, 6 => 70,
                7 => 72, 8 => 74, 9 => 76, 10 => 78, 11 => 81, 12 => 85,
            ],
        ];

        foreach ($targetsBulanan as $indikatorId => $bulanTargets) {
            foreach ($bulanTargets as $bulan => $nilai) {
                TargetIndikator::updateOrCreate(
                    ['indikator_id' => $indikatorId, 'bulan' => $bulan],
                    ['target' => $nilai]
                );
            }
        }

        // ── 10. Realisasi Jan–Apr 2026 ────────────────────────────────────
        $realisasiData = [
            $ikuTapem->id => [
                1 => ['nilai' => 76.5,  'ket' => 'Monitoring administrasi 126 desa selesai, 97 desa tertib laporan.'],
                2 => ['nilai' => 79.2,  'ket' => 'APBDes 2026 seluruh desa selesai ditetapkan, 3 desa terlambat.'],
                3 => ['nilai' => 81.0,  'ket' => 'Bimtek administrasi pemerintahan desa diikuti 134 perangkat desa.'],
                4 => ['nilai' => 83.5,  'ket' => 'Persentase naik signifikan pasca evaluasi Q1, 105 dari 126 desa tertib.'],
            ],
            $ikuKesra->id => [
                1 => ['nilai' => 72.0,  'ket' => '144 dari 200 usulan bansos diproses tepat waktu. 56 terlambat karena verifikasi lapangan.'],
                2 => ['nilai' => 75.5,  'ket' => 'Implementasi sistem digitalisasi usulan bansos mempercepat proses verifikasi.'],
                3 => ['nilai' => 78.0,  'ket' => 'Sinkronisasi data dengan Dinsos berhasil mengurangi duplikasi penerima.'],
                4 => ['nilai' => 80.5,  'ket' => 'Pencairan PKH Q1 selesai 100%, usulan beasiswa PPDB 2026 sudah diproses.'],
            ],
            $ikuHukum->id => [
                1 => ['nilai' => 61.0,  'ket' => '11 dari 18 Perda target Q1 sudah masuk tahap pembahasan DPRD.'],
                2 => ['nilai' => 66.0,  'ket' => '3 Perda berhasil ditetapkan, harmonisasi 5 Perbup selesai.'],
                3 => ['nilai' => 69.5,  'ket' => 'Produk hukum meningkat, meski 2 Perda ditunda karena revisi substansi.'],
                4 => ['nilai' => 72.0,  'ket' => 'Target Q2 on-track: 4 Perda lagi dijadwalkan Mei 2026.'],
            ],
            $ikuKoordinasi->id => [
                1 => ['nilai' => 66.0,  'ket' => '8 rapat koordinasi terlaksana, tindak lanjut 72% rekomendasi.'],
                2 => ['nilai' => 68.5,  'ket' => 'Rapat koordinasi rutin 2x/bulan berjalan. Responsivitas OPD meningkat.'],
                3 => ['nilai' => 70.0,  'ket' => 'Evaluasi triwulan: semua OPD binaan hadir, tindak lanjut 78% rekomendasi.'],
                4 => ['nilai' => 72.5,  'ket' => 'Koordinasi persiapan PPDB dan penyaluran bansos berjalan baik.'],
            ],
            $ikuKontribusiDisdik->id => [
                1 => ['nilai' => 76.8,  'ket' => 'Rata-rata capaian IKU Disdik Jan 2026 — APK PAUD dan nilai UN menunjukkan tren positif.'],
                2 => ['nilai' => 78.3,  'ket' => 'Capaian Disdik meningkat, program PAUD berkualitas berjalan baik.'],
                3 => ['nilai' => 79.5,  'ket' => 'Bimtek guru GTK selesai, dampak positif pada sertifikasi.'],
                4 => ['nilai' => 81.1,  'ket' => 'Rata-rata capaian 4 IKU Disdik Apr 2026: APK 74.2%, Kelas Ortu 57.3%, Nilai UN 68.0, Guru Sertif 83.5%.'],
            ],
            $ikuKontribusiDiskominfo->id => [
                1 => ['nilai' => 57.5,  'ket' => 'Digitalisasi layanan desa baru dimulai. e-Monografi desa 62% sudah online.'],
                2 => ['nilai' => 62.0,  'ket' => 'Sistem aduan digital desa aktif di 45 desa (36%). Integrasi data sosial berjalan.'],
                3 => ['nilai' => 65.0,  'ket' => 'Pelatihan PPID desa selesai, 65% desa sudah punya web desa aktif.'],
                4 => ['nilai' => 67.5,  'ket' => 'Target Q2 digitalisasi desa on-track. SPBE mendukung layanan bansos digital.'],
            ],
        ];

        foreach ($realisasiData as $indikatorId => $bulanRealisasi) {
            foreach ($bulanRealisasi as $bulan => $r) {
                $status = match (true) {
                    $bulan <= 2 => 'diverifikasi',
                    $bulan === 3 => 'diajukan',
                    default => 'draft',
                };

                Realisasi::updateOrCreate(
                    ['indikator_id' => $indikatorId, 'bulan' => $bulan],
                    [
                        'nilai' => $r['nilai'],
                        'keterangan' => $r['ket'],
                        'user_id' => $userAsisten1->id,
                        'status' => $status,
                    ]
                );
            }
        }

        // ── 11. Output ────────────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✅  Seeder Asisten I berhasil dijalankan!');
        $this->command->info('');
        $this->command->info('   Struktur IKU Asisten I (Pemerintahan & Kesra):');
        $this->command->info('   ┌─────────────────────────────────────────────────┬───────┐');
        $this->command->info('   │ IKU                                             │ Bobot │');
        $this->command->info('   ├─────────────────────────────────────────────────┼───────┤');
        $this->command->info('   │ Kabag Tata Pemerintahan                         │  10%  │');
        $this->command->info('   │ Kabag Kesejahteraan Rakyat                      │  10%  │');
        $this->command->info('   │ Kabag Hukum                                     │  10%  │');
        $this->command->info('   │ Indeks Koordinasi Asisten I                     │  10%  │');
        $this->command->info('   │ Kontribusi Disdik                               │  35%  │');
        $this->command->info('   │ Kontribusi Diskominfo (lintas koordinasi)        │  25%  │');
        $this->command->info('   ├─────────────────────────────────────────────────┼───────┤');
        $this->command->info('   │ TOTAL                                           │ 100%  │');
        $this->command->info('   └─────────────────────────────────────────────────┴───────┘');
        $this->command->info('');
        $this->command->table(
            ['Username', 'Password', 'Role', 'Unit'],
            [
                ['asisten1',     'password', 'kepala_bidang', 'Asisten I'],
                ['kabag_tapem',  'password', 'kepala_bidang', 'Kabag Tapem'],
                ['kabag_kesra',  'password', 'kepala_bidang', 'Kabag Kesra'],
                ['kabag_hukum',  'password', 'kepala_bidang', 'Kabag Hukum'],
            ]
        );
    }
}
