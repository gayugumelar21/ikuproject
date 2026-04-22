<?php

namespace Database\Seeders;

use App\Models\Indikator;
use App\Models\IndikatorKerjasama;
use App\Models\Opd;
use App\Models\Realisasi;
use App\Models\TahunAnggaran;
use App\Models\TargetIndikator;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDisdikKerjasamaSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = TahunAnggaran::where('tahun', 2026)->first();
        if (! $tahun) {
            $this->command->warn('Tahun anggaran 2026 tidak ditemukan. Jalankan DummyKominfoSeeder terlebih dahulu.');

            return;
        }

        // ------------------------------------------------------------------
        // 1. OPD Disdik & bidang-bidangnya
        // ------------------------------------------------------------------
        $sekda = Opd::where('type', 'sekda')->first();
        $asisten1 = Opd::firstOrCreate(
            ['code' => 'ASISTEN-II'],
            [
                'name' => 'Asisten II - Perekonomian dan Pembangunan',
                'type' => 'asisten',
                'parent_id' => $sekda?->id,
            ]
        );

        $disdik = Opd::firstOrCreate(
            ['code' => 'DISDIK'],
            [
                'name' => 'Dinas Pendidikan dan Kebudayaan',
                'type' => 'opd',
                'parent_id' => $asisten1->id,
            ]
        );

        $bidangPaud = Opd::firstOrCreate(
            ['code' => 'DISDIK-PAUD'],
            [
                'name' => 'Bidang PAUD dan Dikmas',
                'type' => 'bidang',
                'parent_id' => $disdik->id,
            ]
        );

        $bidangDasmen = Opd::firstOrCreate(
            ['code' => 'DISDIK-DASMEN'],
            [
                'name' => 'Bidang Pembinaan Dikdas & Dikmen',
                'type' => 'bidang',
                'parent_id' => $disdik->id,
            ]
        );

        $bidangGtk = Opd::firstOrCreate(
            ['code' => 'DISDIK-GTK'],
            [
                'name' => 'Bidang Pendidik & Tenaga Kependidikan',
                'type' => 'bidang',
                'parent_id' => $disdik->id,
            ]
        );

        // ------------------------------------------------------------------
        // 2. User Disdik
        // ------------------------------------------------------------------
        $kadis = User::firstOrCreate(
            ['username' => 'kadis_disdik'],
            [
                'name' => 'Kepala Dinas Disdik',
                'email' => 'kadis.disdik@pringsewu.go.id',
                'password' => Hash::make('password'),
                'opd_id' => $disdik->id,
            ]
        );
        $kadis->assignRole('kepala_dinas');

        $kabidPaud = User::firstOrCreate(
            ['username' => 'kabid_paud'],
            [
                'name' => 'Kabid PAUD Disdik',
                'email' => 'kabid.paud@pringsewu.go.id',
                'password' => Hash::make('password'),
                'opd_id' => $bidangPaud->id,
            ]
        );
        $kabidPaud->assignRole('kepala_bidang');

        // ------------------------------------------------------------------
        // 3. IKU Utama Disdik — semua category = utama
        // ------------------------------------------------------------------
        $ikuKelasOrangTua = Indikator::firstOrCreate(
            [
                'tahun_anggaran_id' => $tahun->id,
                'opd_id' => $disdik->id,
                'nama' => 'Persentase Lembaga PAUD yang Melaksanakan Kelas Orang Tua',
            ],
            [
                'sekda_id' => $sekda?->id,
                'asisten_id' => $asisten1->id,
                'bidang_id' => $bidangPaud->id,
                'category' => 'utama',
                'measurement_type' => 'kuantitatif',
                'definisi' => 'Persentase lembaga PAUD (TK/KB/TPA) di Kabupaten Pringsewu yang menyelenggarakan kelas parenting/kelas orang tua minimal 4 kali setahun.',
                'satuan' => '%',
                'target' => 75.00,
                'bobot' => 30.00,
                'owner_user_id' => $kabidPaud->id,
                'status' => 'disetujui',
                'dibuat_oleh' => $kadis->id,
            ]
        );

        $ikuApkPaud = Indikator::firstOrCreate(
            [
                'tahun_anggaran_id' => $tahun->id,
                'opd_id' => $disdik->id,
                'nama' => 'Angka Partisipasi Kasar (APK) PAUD',
            ],
            [
                'sekda_id' => $sekda?->id,
                'asisten_id' => $asisten1->id,
                'bidang_id' => $bidangPaud->id,
                'category' => 'utama',
                'measurement_type' => 'kuantitatif',
                'definisi' => 'Rasio jumlah siswa PAUD terhadap total penduduk usia 3–6 tahun.',
                'satuan' => '%',
                'target' => 82.00,
                'bobot' => 35.00,
                'owner_user_id' => $kabidPaud->id,
                'status' => 'disetujui',
                'dibuat_oleh' => $kadis->id,
            ]
        );

        $ikuNilaiUn = Indikator::firstOrCreate(
            [
                'tahun_anggaran_id' => $tahun->id,
                'opd_id' => $disdik->id,
                'nama' => 'Nilai Rata-rata Hasil Asesmen Nasional SD',
            ],
            [
                'sekda_id' => $sekda?->id,
                'asisten_id' => $asisten1->id,
                'bidang_id' => $bidangDasmen->id,
                'category' => 'utama',
                'measurement_type' => 'kuantitatif',
                'definisi' => 'Rata-rata nilai Asesmen Nasional jenjang SD/MI se-Kabupaten Pringsewu.',
                'satuan' => 'Nilai',
                'target' => 72.00,
                'bobot' => 25.00,
                'owner_user_id' => $kabidPaud->id,
                'status' => 'disetujui',
                'dibuat_oleh' => $kadis->id,
            ]
        );

        $ikuGuruSertif = Indikator::firstOrCreate(
            [
                'tahun_anggaran_id' => $tahun->id,
                'opd_id' => $disdik->id,
                'nama' => 'Persentase Guru Bersertifikat Pendidik',
            ],
            [
                'sekda_id' => $sekda?->id,
                'asisten_id' => $asisten1->id,
                'bidang_id' => $bidangGtk->id,
                'category' => 'utama',
                'measurement_type' => 'kuantitatif',
                'definisi' => 'Persentase guru PNS yang telah memiliki sertifikat pendidik.',
                'satuan' => '%',
                'target' => 88.00,
                'bobot' => 10.00,
                'owner_user_id' => $kabidPaud->id,
                'status' => 'disetujui',
                'dibuat_oleh' => $kadis->id,
            ]
        );

        // ------------------------------------------------------------------
        // 4. Target bulanan Disdik (Jan–Des 2026)
        // ------------------------------------------------------------------
        $targetsBulananDisdik = [
            $ikuKelasOrangTua->id => [1 => 45, 2 => 50, 3 => 55, 4 => 58, 5 => 60, 6 => 62, 7 => 62, 8 => 65, 9 => 68, 10 => 70, 11 => 72, 12 => 75],
            $ikuApkPaud->id => [1 => 70, 2 => 72, 3 => 73, 4 => 74, 5 => 75, 6 => 76, 7 => 76, 8 => 77, 9 => 78, 10 => 79, 11 => 80, 12 => 82],
            $ikuNilaiUn->id => [1 => 65, 2 => 66, 3 => 67, 4 => 68, 5 => 69, 6 => 70, 7 => 70, 8 => 70, 9 => 71, 10 => 71, 11 => 72, 12 => 72],
            $ikuGuruSertif->id => [1 => 80, 2 => 81, 3 => 82, 4 => 83, 5 => 84, 6 => 85, 7 => 85, 8 => 86, 9 => 86, 10 => 87, 11 => 87, 12 => 88],
        ];

        foreach ($targetsBulananDisdik as $indikatorId => $bulanTargets) {
            foreach ($bulanTargets as $bulan => $nilai) {
                TargetIndikator::updateOrCreate(
                    ['indikator_id' => $indikatorId, 'bulan' => $bulan],
                    ['target' => $nilai]
                );
            }
        }

        // ------------------------------------------------------------------
        // 5. Realisasi Disdik Jan–Apr 2026
        // ------------------------------------------------------------------
        $realisasiDisdik = [
            $ikuKelasOrangTua->id => [1 => 47.5, 2 => 51.2, 3 => 54.0, 4 => 57.3],
            $ikuApkPaud->id => [1 => 71.1, 2 => 72.8, 3 => 73.5, 4 => 74.2],
            $ikuNilaiUn->id => [1 => 64.5, 2 => 66.1, 3 => 67.0, 4 => 68.0],
            $ikuGuruSertif->id => [1 => 81.0, 2 => 82.0, 3 => 83.0, 4 => 83.5],
        ];

        foreach ($realisasiDisdik as $indikatorId => $bulanRealisasi) {
            foreach ($bulanRealisasi as $bulan => $nilai) {
                $status = $bulan <= 2 ? 'diverifikasi' : ($bulan === 3 ? 'diajukan' : 'draft');
                Realisasi::updateOrCreate(
                    ['indikator_id' => $indikatorId, 'bulan' => $bulan],
                    ['nilai' => $nilai, 'status' => $status, 'user_id' => $kadis->id]
                );
            }
        }

        // ------------------------------------------------------------------
        // 6. Relasi Kerjasama: IKU Kelas Orang Tua Disdik → Diskominfo
        //    Satu indikator ID, dua OPD — Disdik sebagai pemilik utama,
        //    Diskominfo sebagai mitra kerjasama via linking table.
        // ------------------------------------------------------------------
        $diskominfo = Opd::where('code', 'DISKOMINFO')->first();
        if (! $diskominfo) {
            $this->command->warn('OPD Diskominfo tidak ditemukan. Pastikan DummyKominfoSeeder sudah dijalankan.');

            return;
        }

        $asisten2 = Opd::where('code', 'ASISTEN-II')->first();
        $bidangIkp = Opd::where('code', 'DISKOMINFO-IKP')->first();
        $kabidIkp = User::where('username', 'kabid_ikp')->first();

        IndikatorKerjasama::updateOrCreate(
            [
                'indikator_id' => $ikuKelasOrangTua->id,
                'opd_id' => $diskominfo->id,
                'bidang_id' => $bidangIkp?->id,
            ],
            [
                'sekda_id' => $sekda?->id,
                'asisten_id' => $asisten2?->id,
                'owner_user_id' => $kabidIkp?->id,
                'peran' => 'Kominfo berperan dalam sosialisasi dan promosi digital pelaksanaan Kelas Orang Tua di lembaga PAUD. Skor mengikuti capaian Disdik (Bidang PAUD) secara otomatis.',
                'bobot' => 10.00,
                'status' => 'disetujui',
                'dibuat_oleh' => $kabidIkp?->id,
            ]
        );

        $this->command->info('✓ Seeder Disdik + IKU Kerjasama berhasil dijalankan.');
        $this->command->info("  IKU Sumber   : {$ikuKelasOrangTua->nama} (Disdik, ID #{$ikuKelasOrangTua->id})");
        $this->command->info("  OPD Mitra    : {$diskominfo->name} (Diskominfo, bobot 10%)");
        $this->command->info('  ID Indikator SAMA — relasi kerjasama via tabel indikator_kerjasamas.');
        $this->command->info('  Skor kerjasama otomatis mengikuti skor final Disdik.');
    }
}
