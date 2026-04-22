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

class DummyKominfoSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Tahun Anggaran ─────────────────────────────────────────────
        $tahun = TahunAnggaran::firstOrCreate(
            ['tahun' => 2026],
            ['is_active' => true]
        );

        // ── 2. OPD Hierarki ───────────────────────────────────────────────
        $sekda = Opd::firstOrCreate(
            ['code' => 'SEKDA'],
            ['name' => 'Sekretariat Daerah', 'type' => 'sekda', 'parent_id' => null]
        );

        $asisten3 = Opd::firstOrCreate(
            ['code' => 'ASISTEN-III'],
            ['name' => 'Asisten III - Administrasi Umum', 'type' => 'asisten', 'parent_id' => $sekda->id]
        );

        $kominfo = Opd::firstOrCreate(
            ['code' => 'DISKOMINFO'],
            ['name' => 'Dinas Komunikasi dan Informatika', 'type' => 'opd', 'parent_id' => $asisten3->id]
        );

        $bidangIkp = Opd::firstOrCreate(
            ['code' => 'DISKOMINFO-IKP'],
            ['name' => 'Bidang IKP dan Statistik Sektoral', 'type' => 'bidang', 'parent_id' => $kominfo->id]
        );

        $bidangSpbe = Opd::firstOrCreate(
            ['code' => 'DISKOMINFO-SPBE'],
            ['name' => 'Bidang Tata Kelola SPBE dan Persandian', 'type' => 'bidang', 'parent_id' => $kominfo->id]
        );

        // ── 3. Users ──────────────────────────────────────────────────────
        $admin = User::where('username', 'admin')->first();

        $kepalaDinas = User::firstOrCreate(
            ['username' => 'kadis_kominfo'],
            [
                'name' => 'Kepala Dinas Kominfo',
                'email' => 'kadis@diskominfo.test',
                'password' => Hash::make('password'),
                'opd_id' => $kominfo->id,
                'email_verified_at' => now(),
            ]
        );
        $kepalaDinas->assignRole('kepala_dinas');

        $kabidIkp = User::firstOrCreate(
            ['username' => 'kabid_ikp'],
            [
                'name' => 'Kabid IKP dan Statistik',
                'email' => 'kabid.ikp@diskominfo.test',
                'password' => Hash::make('password'),
                'opd_id' => $kominfo->id,
                'email_verified_at' => now(),
            ]
        );
        $kabidIkp->assignRole('kepala_bidang');

        $kabidSpbe = User::firstOrCreate(
            ['username' => 'kabid_spbe'],
            [
                'name' => 'Kabid SPBE dan Persandian',
                'email' => 'kabid.spbe@diskominfo.test',
                'password' => Hash::make('password'),
                'opd_id' => $kominfo->id,
                'email_verified_at' => now(),
            ]
        );
        $kabidSpbe->assignRole('kepala_bidang');

        $dibuatOleh = $admin?->id ?? $kepalaDinas->id;

        // ── 4. Indikator IKU Kominfo ──────────────────────────────────────
        $indikators = [
            [
                'nama' => 'Indeks SPBE (Sistem Pemerintahan Berbasis Elektronik)',
                'definisi' => 'Nilai indeks SPBE yang diperoleh dari evaluasi Kemenpan-RB terhadap implementasi sistem pemerintahan berbasis elektronik di lingkungan Pemda.',
                'satuan' => 'indeks',
                'target' => 2.60,
                'bobot' => 30.00,
                'bidang' => $bidangSpbe->id,
                'target_bulanan' => [
                    1 => 1.80, 2 => 1.90, 3 => 2.00, 4 => 2.10, 5 => 2.20,
                    6 => 2.30, 7 => 2.35, 8 => 2.40, 9 => 2.45, 10 => 2.50,
                    11 => 2.55, 12 => 2.60,
                ],
                'realisasi' => [
                    1 => ['nilai' => 1.82, 'ket' => 'Evaluasi internal SPBE Q1 selesai, skor meningkat dari baseline 1.75.'],
                    2 => ['nilai' => 1.95, 'ket' => 'Implementasi SSO untuk 3 aplikasi daerah berhasil.'],
                    3 => ['nilai' => 2.05, 'ket' => 'Integrasi data kependudukan dengan aplikasi layanan publik rampung.'],
                    4 => ['nilai' => 2.08, 'ket' => 'Pembangunan data center tahap I selesai, integrasi layanan masih berjalan.'],
                ],
            ],
            [
                'nama' => 'Persentase Layanan Publik Berbasis Digital yang Aktif',
                'definisi' => 'Persentase jumlah layanan publik yang sudah berbasis digital dan aktif digunakan masyarakat dari total layanan publik yang ada.',
                'satuan' => 'persen',
                'target' => 85.00,
                'bobot' => 25.00,
                'bidang' => $bidangSpbe->id,
                'target_bulanan' => [
                    1 => 55.00, 2 => 58.00, 3 => 62.00, 4 => 65.00, 5 => 68.00,
                    6 => 70.00, 7 => 73.00, 8 => 75.00, 9 => 78.00, 10 => 80.00,
                    11 => 83.00, 12 => 85.00,
                ],
                'realisasi' => [
                    1 => ['nilai' => 57.00, 'ket' => 'Digitalisasi 12 layanan baru selesai, total 57% dari 89 layanan aktif digital.'],
                    2 => ['nilai' => 61.00, 'ket' => 'Penambahan 4 layanan digital baru termasuk izin usaha dan akta kelahiran online.'],
                    3 => ['nilai' => 64.00, 'ket' => 'Layanan pengaduan SP4N-LAPOR! terintegrasi, 3 layanan lama masih migrasi.'],
                    4 => ['nilai' => 63.50, 'ket' => 'Terdapat 1 layanan digital yang mengalami gangguan teknis sehingga realisasi turun tipis.'],
                ],
            ],
            [
                'nama' => 'Cakupan Desa/Kelurahan dengan Akses Internet Memadai',
                'definisi' => 'Persentase desa/kelurahan yang memiliki infrastruktur jaringan internet dengan kecepatan minimal 25 Mbps untuk mendukung layanan publik digital.',
                'satuan' => 'persen',
                'target' => 90.00,
                'bobot' => 20.00,
                'bidang' => $bidangIkp->id,
                'target_bulanan' => [
                    1 => 65.00, 2 => 68.00, 3 => 70.00, 4 => 73.00, 5 => 75.00,
                    6 => 78.00, 7 => 80.00, 8 => 82.00, 9 => 84.00, 10 => 86.00,
                    11 => 88.00, 12 => 90.00,
                ],
                'realisasi' => [
                    1 => ['nilai' => 66.00, 'ket' => 'Pemasangan tower BTS baru di 5 desa terpencil selesai.'],
                    2 => ['nilai' => 70.00, 'ket' => 'Program BAKTI Kominfo berhasil menjangkau 8 desa tambahan.'],
                    3 => ['nilai' => 69.00, 'ket' => 'Kerusakan kabel optik di 2 kecamatan menyebabkan sedikit penurunan cakupan.'],
                    4 => ['nilai' => 74.00, 'ket' => 'Perbaikan dan perluasan jaringan serat optik Q2 selesai lebih awal dari jadwal.'],
                ],
            ],
            [
                'nama' => 'Persentase Aduan Masyarakat yang Ditindaklanjuti Tepat Waktu',
                'definisi' => 'Persentase pengaduan masyarakat melalui semua kanal (SP4N-LAPOR!, media sosial, website) yang mendapat respons dan tindak lanjut dalam SLA ≤ 5 hari kerja.',
                'satuan' => 'persen',
                'target' => 90.00,
                'bobot' => 15.00,
                'bidang' => $bidangIkp->id,
                'target_bulanan' => [
                    1 => 75.00, 2 => 78.00, 3 => 80.00, 4 => 82.00, 5 => 83.00,
                    6 => 85.00, 7 => 86.00, 8 => 87.00, 9 => 88.00, 10 => 89.00,
                    11 => 90.00, 12 => 90.00,
                ],
                'realisasi' => [
                    1 => ['nilai' => 77.00, 'ket' => '154 dari 200 aduan ditindaklanjuti tepat waktu, 46 tertunda karena koordinasi lintas OPD.'],
                    2 => ['nilai' => 82.00, 'ket' => 'Implementasi sistem tiket otomatis berhasil mempercepat distribusi aduan.'],
                    3 => ['nilai' => 88.00, 'ket' => 'Pencapaian terbaik — koordinasi lintas OPD semakin efektif pasca rapat evaluasi.'],
                    4 => ['nilai' => 85.00, 'ket' => 'Lonjakan aduan terkait layanan kependudukan pasca lebaran, respons sedikit melambat.'],
                ],
            ],
            [
                'nama' => 'Persentase Data Statistik Sektoral yang Tersedia dan Terpublikasi',
                'definisi' => 'Persentase jenis data statistik sektoral OPD yang telah dikumpulkan, divalidasi, dan dipublikasikan melalui portal data daerah dari total data yang wajib tersedia.',
                'satuan' => 'persen',
                'target' => 80.00,
                'bobot' => 10.00,
                'bidang' => $bidangIkp->id,
                'target_bulanan' => [
                    1 => 40.00, 2 => 45.00, 3 => 50.00, 4 => 55.00, 5 => 60.00,
                    6 => 63.00, 7 => 65.00, 8 => 68.00, 9 => 70.00, 10 => 73.00,
                    11 => 77.00, 12 => 80.00,
                ],
                'realisasi' => [
                    1 => ['nilai' => 38.00, 'ket' => 'Pengumpulan data sektoral Q1 masih berjalan, beberapa OPD belum melengkapi formulir.'],
                    2 => ['nilai' => 46.00, 'ket' => 'Rapat koordinasi data sektoral dengan 12 OPD berhasil mendorong penyerahan data.'],
                    3 => ['nilai' => 52.00, 'ket' => 'Portal open data diluncurkan, 52% data sudah terpublikasi.'],
                    4 => ['nilai' => 58.00, 'ket' => 'Pelatihan pengelola data sektoral di 8 OPD meningkatkan kualitas dan kelengkapan data.'],
                ],
            ],
        ];

        foreach ($indikators as $data) {
            $targetBulanan = $data['target_bulanan'];
            $realisasiData = $data['realisasi'];
            $bidangId = $data['bidang'];

            $indikator = Indikator::firstOrCreate(
                ['nama' => $data['nama'], 'tahun_anggaran_id' => $tahun->id],
                [
                    'tahun_anggaran_id' => $tahun->id,
                    'sekda_id' => $sekda->id,
                    'asisten_id' => $asisten3->id,
                    'opd_id' => $kominfo->id,
                    'bidang_id' => $bidangId,
                    'kabag_id' => null,
                    'parent_indikator_id' => null,
                    'definisi' => $data['definisi'],
                    'satuan' => $data['satuan'],
                    'target' => $data['target'],
                    'bobot' => $data['bobot'],
                    'status' => 'disetujui',
                    'dibuat_oleh' => $dibuatOleh,
                ]
            );

            // Target bulanan (semua 12 bulan)
            foreach ($targetBulanan as $bulan => $target) {
                TargetIndikator::updateOrCreate(
                    ['indikator_id' => $indikator->id, 'bulan' => $bulan],
                    ['target' => $target]
                );
            }

            // Realisasi Jan–Apr dengan status bervariasi
            foreach ($realisasiData as $bulan => $r) {
                $status = match (true) {
                    $bulan <= 2 => 'diverifikasi',
                    $bulan === 3 => 'diajukan',
                    default => 'draft',
                };

                Realisasi::updateOrCreate(
                    ['indikator_id' => $indikator->id, 'bulan' => $bulan],
                    [
                        'nilai' => $r['nilai'],
                        'keterangan' => $r['ket'],
                        'user_id' => $kabidIkp->id,
                        'status' => $status,
                    ]
                );
            }
        }

        $this->command->info('');
        $this->command->info('✅  Dummy data Kominfo berhasil dimuat!');
        $this->command->table(
            ['Username', 'Password', 'Role'],
            [
                ['admin', '(existing)', 'admin_super'],
                ['kadis_kominfo', 'password', 'kepala_dinas'],
                ['kabid_ikp', 'password', 'kepala_bidang'],
                ['kabid_spbe', 'password', 'kepala_bidang'],
            ]
        );
    }
}
