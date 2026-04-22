<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Manajemen OPD & Pengguna
            'kelola-opd',
            'kelola-pengguna',

            // Indikator
            'buat-indikator',
            'edit-indikator',
            'hapus-indikator',
            'lihat-indikator',
            'ajukan-indikator',

            // Approval indikator
            'setujui-indikator-kabag',
            'setujui-indikator-asisten',
            'setujui-indikator-sekda',
            'setujui-indikator-bupati',

            // Realisasi
            'input-realisasi',
            'edit-realisasi',
            'lihat-realisasi',
            'verifikasi-realisasi',

            // Skoring IKU
            'skoring-ai',
            'skoring-ta',
            'skoring-bupati',
            'lihat-skoring',

            // Laporan
            'lihat-laporan-opd',
            'lihat-laporan-asisten',
            'lihat-laporan-sekda',
            'lihat-laporan-semua',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin Super — akses penuh
        $adminSuper = Role::firstOrCreate(['name' => 'admin_super']);
        $adminSuper->givePermissionTo(Permission::all());

        // Bupati — skoring final + lihat semua laporan
        $bupati = Role::firstOrCreate(['name' => 'bupati']);
        $bupati->givePermissionTo([
            'lihat-indikator',
            'setujui-indikator-bupati',
            'lihat-realisasi',
            'lihat-skoring',
            'skoring-bupati',
            'lihat-laporan-semua',
        ]);

        // Sekda — kelola OPD, setujui indikator, lihat semua laporan
        $sekda = Role::firstOrCreate(['name' => 'sekda']);
        $sekda->givePermissionTo([
            'kelola-opd',
            'lihat-indikator',
            'ajukan-indikator',
            'setujui-indikator-sekda',
            'lihat-realisasi',
            'verifikasi-realisasi',
            'lihat-laporan-sekda',
            'lihat-laporan-semua',
        ]);

        // Kabag — buat & ajukan indikator level kabag
        $kabag = Role::firstOrCreate(['name' => 'kabag']);
        $kabag->givePermissionTo([
            'buat-indikator',
            'edit-indikator',
            'lihat-indikator',
            'ajukan-indikator',
            'setujui-indikator-kabag',
            'lihat-realisasi',
            'lihat-laporan-opd',
        ]);

        // Asisten — setujui indikator OPD di bawahnya
        $asisten = Role::firstOrCreate(['name' => 'asisten']);
        $asisten->givePermissionTo([
            'lihat-indikator',
            'ajukan-indikator',
            'setujui-indikator-asisten',
            'lihat-realisasi',
            'verifikasi-realisasi',
            'lihat-laporan-asisten',
        ]);

        // Kepala Dinas — buat, edit, ajukan indikator OPD-nya
        $kepalaDinas = Role::firstOrCreate(['name' => 'kepala_dinas']);
        $kepalaDinas->givePermissionTo([
            'buat-indikator',
            'edit-indikator',
            'lihat-indikator',
            'ajukan-indikator',
            'input-realisasi',
            'edit-realisasi',
            'lihat-realisasi',
            'lihat-laporan-opd',
        ]);

        // Kepala Bidang — input realisasi bidangnya
        $kepalaBidang = Role::firstOrCreate(['name' => 'kepala_bidang']);
        $kepalaBidang->givePermissionTo([
            'lihat-indikator',
            'input-realisasi',
            'edit-realisasi',
            'lihat-realisasi',
            'lihat-skoring',
            'lihat-laporan-opd',
        ]);

        // Tenaga Ahli (via admin_super) — skoring TA + generate AI
        // admin_super sudah dapat semua permission via Permission::all() di atas
        // Tidak perlu role terpisah — admin_super memegang peran Tenaga Ahli
    }
}
