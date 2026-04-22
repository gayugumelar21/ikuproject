<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @var array<int, array{key: string, value: string, type: string, group: string, label: string}>
     */
    public function run(): void
    {
        $settings = [
            // [ai] group
            [
                'key' => 'ai_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'ai',
                'label' => 'AI Scoring Aktif',
            ],
            [
                'key' => 'ai_api_key',
                'value' => '',
                'type' => 'encrypted',
                'group' => 'ai',
                'label' => 'Anthropic API Key',
            ],
            [
                'key' => 'ai_model',
                'value' => 'claude-sonnet-4-6',
                'type' => 'string',
                'group' => 'ai',
                'label' => 'Model AI',
            ],
            [
                'key' => 'ai_auto_trigger',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'ai',
                'label' => 'AI Auto Trigger',
            ],

            // [whatsapp] group
            [
                'key' => 'wa_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'whatsapp',
                'label' => 'WhatsApp Aktif',
            ],
            [
                'key' => 'wa_api_key',
                'value' => '',
                'type' => 'encrypted',
                'group' => 'whatsapp',
                'label' => 'Fonnte API Key',
            ],
            [
                'key' => 'wa_sender_number',
                'value' => '',
                'type' => 'string',
                'group' => 'whatsapp',
                'label' => 'Nomor Pengirim WA',
            ],
            [
                'key' => 'wa_reminder_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'whatsapp',
                'label' => 'Reminder WA Aktif',
            ],
            [
                'key' => 'wa_reminder_day',
                'value' => '25',
                'type' => 'integer',
                'group' => 'whatsapp',
                'label' => 'Hari Kirim Reminder',
            ],

            // [general] group
            [
                'key' => 'active_year',
                'value' => '2026',
                'type' => 'integer',
                'group' => 'general',
                'label' => 'Tahun Aktif',
            ],
            [
                'key' => 'current_scoring_month',
                'value' => '4',
                'type' => 'integer',
                'group' => 'general',
                'label' => 'Bulan Skoring Aktif',
            ],
            [
                'key' => 'submission_deadline_day',
                'value' => '5',
                'type' => 'integer',
                'group' => 'general',
                'label' => 'Deadline Input (tgl)',
            ],
            [
                'key' => 'app_name',
                'value' => 'Sistem IKU Pringsewu',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Nama Aplikasi',
            ],
            [
                'key' => 'opd_can_see_own_score',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'general',
                'label' => 'OPD Dapat Lihat Skor Sendiri',
            ],
        ];

        foreach ($settings as $data) {
            Setting::updateOrCreate(
                ['key' => $data['key']],
                $data,
            );
        }
    }
}
