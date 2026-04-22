# PROMPT SISTEM MONITORING IKU — PEMKAB PRINGSEWU
# Untuk dijalankan di Claude Code

---

## INSTRUKSI UTAMA

Bangun sistem web Laravel 11 lengkap untuk monitoring IKU (Indikator Kinerja Utama)
Pemerintah Kabupaten Pringsewu. Kerjakan dari awal hingga siap dijalankan dengan
`php artisan serve`. Semua teks UI dalam Bahasa Indonesia.

---

## STACK TEKNIS

- Laravel 11
- MySQL 8
- Filament v3 (admin panel)
- Livewire 3
- TailwindCSS
- Queue driver: database (bukan Redis — Niagahoster shared hosting)
- WhatsApp: Fonnte API
- AI: Anthropic Claude API (model: claude-sonnet-4-6)
- Excel: maatwebsite/excel
- PDF: barryvdh/laravel-dompdf

---

## KONTEKS BISNIS

Bupati memantau kinerja OPD tiap bulan via IKU. Alurnya:
1. Kabid/Kabag input progres realisasi IKU
2. AI beri skor otomatis (pertimbangan)
3. Tenaga Ahli/superadmin beri skor manual (pertimbangan)
4. Bupati beri skor final 1-10 — INI SATU-SATUNYA SKOR YANG BERLAKU
5. Dashboard menampilkan rekap berjenjang

IKU ada dua kategori: UTAMA (milik OPD sendiri) dan KERJASAMA
(skornya otomatis = skor Bupati dari IKU pemilik/sumber).

---

## HIERARKI ORGANISASI

Bupati
└── Sekda
    ├── Asisten I (Pemerintahan & Kesra)
    │   Kabag: Pemerintahan, Kesejahteraan Rakyat, Hukum
    │   OPD: Dinas Pendidikan & Kebudayaan, Dinas Kesehatan, Dinas Sosial,
    │        Dinas PPPA & KB, Dinas Perpustakaan & Kearsipan,
    │        Inspektorat, Badan Kesbangpol, BPBD
    ├── Asisten II (Perekonomian & Pembangunan)
    │   Kabag: Perekonomian & Pembangunan, Adm Pembangunan, Pengadaan B&J
    │   OPD: DPUPR, Dinas Lingkungan Hidup, Dinas Ketahanan Pangan,
    │        Dinas Perikanan, Dinas Pertanian, Dinas Koperasi UKM Perindag,
    │        DPMPTSP, Dinas Nakertrans, Bapperida
    └── Asisten III (Administrasi Umum)
        Kabag: Umum, Organisasi, Protokol & Komunikasi Pimpinan
        OPD: Diskominfo, Dinas Disporapar, Dinas PMD & Pekon,
             Dinas Perhubungan, Dinas Dukcapil, Satpol PP,
             BKPSDM, BPKAD, Bapenda, Sekretariat DPRD

Kecamatan (9): Gadingrejo, Pringsewu, Pagelaran, Sukoharjo, Pardasuka,
               Banyumas, Adiluwih, Ambarawa, Pagelaran Utara
Kelurahan (5): Pringsewu Barat, Pringsewu Selatan, Pringsewu Timur,
               Pringsewu Utara, Pajaresuk

Staf Ahli (3): viewer saja, tidak punya IKU
UPTD (Puskesmas, RSUD, dll): masuk sistem tapi Fase 2

---

## STEP 1 — SETUP AWAL

```bash
composer create-project laravel/laravel . --prefer-dist
composer require filament/filament:"^3.0" -W
composer require livewire/livewire:"^3.0"
composer require maatwebsite/excel:"^3.1"
composer require barryvdh/laravel-dompdf:"^2.0"
composer require guzzlehttp/guzzle
php artisan filament:install --panels
php artisan queue:table
php artisan session:table
php artisan cache:table
php artisan notifications:table
```

File .env:
```
APP_NAME="Sistem IKU Pringsewu"
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_DATABASE=iku_pringsewu
DB_USERNAME=root
DB_PASSWORD=
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
```

---

## STEP 2 — MIGRATIONS

Buat dalam urutan ini (urutan penting karena FK):

### 2.1 — opds
```
id, name, short_name(50), type(enum: sekretariat_daerah|dinas|badan|
inspektorat|satpol|sekretariat_dprd|kecamatan|kelurahan),
asisten_opd_id(FK opds nullable), is_active(bool true),
sort_order(int 0), timestamps, softDeletes
```

### 2.2 — users (replace default)
```
id, name, nip(30 nullable unique), email(unique), password,
phone_wa(20 nullable),
role(enum: superadmin|bupati|sekda|asisten|staf_ahli|
kepala_opd|kabid|kabag|viewer, default viewer),
opd_id(FK opds nullable), unit_id(bigint nullable),
jabatan_label(nullable), is_active(bool true),
must_change_password(bool false),
last_login_at(timestamp nullable),
rememberToken, timestamps, softDeletes
```

### 2.3 — units
```
id, opd_id(FK opds), name, short_name(100 nullable),
type(enum: bidang|bagian|subbag|seksi),
head_user_id(bigint nullable), is_active(bool true),
sort_order(int 0), timestamps, softDeletes
```
Setelah buat tabel units, tambahkan:
```php
Schema::table('users', function($t) {
    $t->foreign('unit_id')->references('id')->on('units')->nullOnDelete();
});
```

### 2.4 — iku_definitions
```
id, opd_id(FK opds), unit_id(FK units nullable),
name, description(text nullable),
category(enum: utama|kerjasama),
weight_percentage(decimal 5,2 default 0),
year(smallint),
measurement_type(enum: nilai|persentase|jumlah|predikat, default nilai),
assessment_notes(text nullable),
source_opd_id(bigint nullable FK opds),
source_iku_id(bigint nullable FK iku_definitions self-referencing),
is_active(bool true), created_by(FK users nullable),
timestamps, softDeletes
```

### 2.5 — opd_iku_weight_configs
```
id, opd_id(FK opds), year(smallint),
utama_weight(decimal 5,2 default 70),
kerjasama_weight(decimal 5,2 default 30),
timestamps
UNIQUE(opd_id, year)
```

### 2.6 — iku_cascades
```
id, iku_definition_id(FK iku_definitions), month(tinyint), year(smallint),
target_description(text), target_detail(text nullable),
has_activity(bool true),
created_by(FK users nullable), updated_by(FK users nullable),
timestamps
UNIQUE(iku_definition_id, month, year)
```

### 2.7 — iku_progresses
```
id, iku_definition_id(FK iku_definitions), month(tinyint), year(smallint),
realization_description(text),
evidence_url(string nullable), evidence_file_path(string nullable),
obstacle_notes(text nullable),
status(enum: draft|submitted, default draft),
submitted_by(FK users nullable), submitted_at(timestamp nullable),
timestamps
UNIQUE(iku_definition_id, month, year)
```

### 2.8 — iku_scores
```
id, iku_definition_id(FK iku_definitions), month(tinyint), year(smallint),
score_ai(tinyint nullable), ai_reasoning(text nullable), ai_generated_at(timestamp nullable),
score_ta(tinyint nullable), ta_notes(text nullable),
ta_scored_by(FK users nullable), ta_scored_at(timestamp nullable),
score_bupati(tinyint nullable), bupati_notes(text nullable),
bupati_scored_at(timestamp nullable),
is_finalized(bool false), finalized_by(FK users nullable),
finalized_at(timestamp nullable),
status(enum: pending|ai_done|ta_done|finalized, default pending),
timestamps
UNIQUE(iku_definition_id, month, year)
```

### 2.9 — monthly_summaries
```
id, entity_type(enum: opd|unit|asisten|sekda),
entity_id(bigint), month(tinyint), year(smallint),
score_utama(decimal 5,2 nullable),
score_kerjasama(decimal 5,2 nullable),
score_total(decimal 5,2 nullable),
is_complete(bool false), calculated_at(timestamp nullable),
timestamps
UNIQUE(entity_type, entity_id, month, year)
```

### 2.10 — settings
```
id, key(unique), value(text nullable),
type(enum: boolean|string|integer|json|encrypted, default string),
group(string general), description(nullable), timestamps
```

### 2.11 — wa_logs
```
id, recipient_user_id(bigint nullable), recipient_phone(20),
message_type(enum: report|reminder|blast|notification),
message(text), status(enum: pending|sent|failed, default pending),
provider_response(json nullable), sent_by(bigint nullable),
sent_at(timestamp nullable), timestamps
```

### 2.12 — activity_logs
```
id, user_id(bigint nullable), action, description(text),
model_type(nullable), model_id(bigint nullable),
old_values(json nullable), new_values(json nullable),
ip_address(45 nullable), timestamps
```

---

## STEP 3 — MODELS

Buat model dengan relasi lengkap:

**Opd:** hasMany units, users, ikuDefinitions; belongsTo asisten(self FK);
hasMany subordinateOpds('asisten_opd_id'); hasMany weightConfigs

**User:** belongsTo opd, unit; helper methods: isSuperadmin(), isBupati(),
isSekda(), isAsisten(), isKepalaOpd(), isKabid(), canScore(), canSeeAllOpd()

**Unit:** belongsTo opd; belongsTo head(User); hasMany ikuDefinitions, users

**IkuDefinition:** belongsTo opd, unit, sourceOpd(Opd), sourceIku(self);
hasMany cascades, progresses, scores;
helper: getCascadeForMonth(m,y), getScoreForMonth(m,y), isKerjasama()

**IkuCascade:** belongsTo ikuDefinition

**IkuProgress:** belongsTo ikuDefinition, submittedBy(User);
helper: isSubmitted()

**IkuScore:** belongsTo ikuDefinition, taScoredBy(User), finalizedBy(User);
helper: getFinalScore() → return score_bupati

**Setting:** static get(key, default), static set(key, value) dengan
enkripsi/dekripsi otomatis sesuai type

**OpdIkuWeightConfig, MonthlySummary, WaLog, ActivityLog:**
fillable + relasi sederhana sesuai kolom masing-masing

---

## STEP 4 — SEEDERS

### OpdSeeder
Seed semua OPD dari hierarki di atas. Buat Asisten sebagai record OPD
dengan type=sekretariat_daerah agar bisa di-assign ke asisten_opd_id.
Format short_name Asisten: ASISTEN-I, ASISTEN-II, ASISTEN-III.
Assign opd_asisten1, opd_asisten2, opd_asisten3 ke FK yang benar.

### UnitSeeder
Minimal 2 unit per OPD. OPD prioritas (buat sesuai nama nyata):

DISKOMINFO: Bidang IKP & Statistik Sektoral | Bidang Tata Kelola SPBE & Persandian
DINKES: Bidang Kesmas | Bidang Yankes | Bidang P2P | Bidang SDK
BKPSDM: Bidang Pengadaan & Info ASN | Bidang Mutasi & Pengembangan Kompetensi
BAPPERIDA: Bid Perencanaan & Evaluasi | Bid Perekonomian & SDA | Bid Infrastruktur | Bid Pemerintahan | Bid Riset
DISTAN: Bid Tanaman Pangan | Bid Perkebunan | Bid PKH | Bid Penyuluhan | Bid PSP
BPKAD: Bid Anggaran | Bid Perbendaharaan | Bid Akuntansi | Bid Aset
BAPENDA: Bid Pendapatan | Bid Pengendalian & Pelaporan
Sisanya: buat 2 Bidang placeholder (Bidang A, Bidang B)

### UserSeeder
Buat user sistem default (tampilkan semua password di console):
superadmin@pringsewu.go.id | role: superadmin | password: random aman
bupati@pringsewu.go.id | role: bupati
sekda@pringsewu.go.id | role: sekda
asisten1@pringsewu.go.id | role: asisten | assign ke OPD Asisten I
asisten2@pringsewu.go.id | role: asisten | assign ke OPD Asisten II
asisten3@pringsewu.go.id | role: asisten | assign ke OPD Asisten III

Import dari Data_Master_PNS.csv (file ada di base_path()):
- Baca header baris pertama untuk tahu nama kolom
- Tentukan role dari jabatan_label:
  contains "Kepala Dinas"|"Kepala Badan"|"Camat"|"Lurah" → kepala_opd
  contains "Kepala Bidang" → kabid
  contains "Kepala Bagian"|"Kepala Sub Bagian"|"Kepala Subbagian" → kabag
  Lainnya → skip
- Email: {nip}@pringsewu.go.id
- Password: NIP (set must_change_password = true)
- Tampilkan jumlah imported dan jumlah skipped

### SettingsSeeder
```
active_year=2026 (integer), current_scoring_month=5 (integer),
submission_deadline_day=5 (integer),
min_score=1, max_score=10,
ai_enabled=true (boolean), ai_api_key='' (encrypted),
ai_model=claude-sonnet-4-6 (string),
ai_auto_trigger=true (boolean),
ai_prompt_template="OPD: {opd_name}\nBidang: {unit_name}\nIKU: {iku_name}\nDeskripsi: {iku_description}\nTarget bulan ini: {cascade_target}\nRealisasi: {realization}\n\nBeri skor 1-10 dan reasoning singkat Bahasa Indonesia. Return HANYA JSON: {\"score\": integer, \"reasoning\": \"string\"}" (string),
opd_can_see_own_score=false (boolean),
opd_can_see_others=false (boolean),
wa_enabled=false (boolean), wa_provider=fonnte (string),
wa_api_key='' (encrypted), wa_sender_number='' (string),
wa_reminder_enabled=false (boolean), wa_reminder_day=25 (integer),
wa_report_template="*Laporan IKU {bulan} {tahun}*\nOPD: {opd_name}\nSkor: {skor}/10\n\n{detail_iku}\n\nCatatan Bupati: {catatan}" (string),
app_name=Sistem IKU Pringsewu (string),
timezone=Asia/Jakarta (string),
session_timeout_minutes=60 (integer)
```

### DemoDataSeeder
Untuk 3 OPD (DISKOMINFO, DINKES, BKPSDM):
- Buat 3 IKU (2 utama + 1 kerjasama) per OPD
- Buat cascade bulan 1–5 tahun 2026 untuk semua IKU
- Buat progress + skor (semua 3 layer) untuk bulan 1–4 dengan nilai bervariasi
- Skor bervariasi agar dashboard terlihat berwarna (ada hijau, kuning, merah)

### DatabaseSeeder.php
```php
$this->call([
    OpdSeeder::class,
    UnitSeeder::class,
    UserSeeder::class,
    SettingsSeeder::class,
    DemoDataSeeder::class,
]);
```

---

## STEP 5 — AUTH & MIDDLEWARE

Buat middleware: app/Http/Middleware/RoleMiddleware.php
Cek $user->role === salah satu role yang diizinkan.
Redirect ke /unauthorized jika gagal.

Daftarkan di bootstrap/app.php dengan alias 'role'.

Route groups:
- management: bupati, sekda, asisten, staf_ahli, superadmin
- opd_staff: kepala_opd, kabid, kabag, superadmin
- scorer: bupati, superadmin

Redirect setelah login:
- superadmin → /admin
- bupati, sekda, asisten, staf_ahli → /dashboard
- kepala_opd, kabid, kabag → /opd

Update LoginController:
- Catat last_login_at
- Redirect ke /ganti-password jika must_change_password = true
- Tolak login jika is_active = false

Halaman /ganti-password:
- Form password baru + konfirmasi, min 8 char, tidak boleh = NIP
- Set must_change_password = false setelah berhasil

---

## STEP 6 — FILAMENT ADMIN PANEL (/admin)

Accessible hanya role superadmin.

Buat Filament Resources:

**UserResource:** CRUD user, assign role+opd+unit, reset password action,
filter by role/opd/status

**OpdResource:** CRUD opd, assign asisten, toggle aktif/nonaktif

**UnitResource:** CRUD unit, nested per OPD, assign kepala unit

**IkuDefinitionResource:**
- Form: opd, unit, name, description, category, weight_percentage, year,
  measurement_type, assessment_notes
- Jika category=kerjasama: tampilkan field source_opd + source_iku
- Validasi: tampilkan warning jika total bobot OPD > 100%

**OpdIkuWeightConfigResource:** CRUD bobot utama vs kerjasama per OPD per tahun.
Validasi: utama_weight + kerjasama_weight = 100

**SettingsPage (Filament Page):** di /admin/settings
Tampilkan semua settings dikelompokkan per group.
boolean → Toggle, integer → numeric input, encrypted → password input,
string panjang → Textarea.

**ActivityLogPage:** read-only table di /admin/activity-logs

**WaLogPage:** read-only table di /admin/wa-logs

**WaBlastPage:** form kirim WA manual di /admin/wa-blast
Pilih penerima, isi pesan, preview, kirim.
Tampilkan pesan jika wa_enabled=false.

Navigasi Filament dikelompokkan: Master Data | Pengguna | Penilaian | WhatsApp | Pengaturan

---

## STEP 7 — CASCADE MANAGEMENT

Route: /cascades (management + opd_staff)

Halaman utama: grid 12 bulan × semua IKU
Filter: Tahun | OPD | Unit
Warna cell: hijau=ada, merah=belum, abu=has_activity false

Form per cell: target_description, target_detail, has_activity toggle
Akses sesuai role: superadmin semua, kepala_opd hanya OPD-nya, kabid hanya unit-nya

Bulk Import Excel:
- GET /cascades/template → download template
- POST /cascades/import → proses, tampilkan summary berhasil/gagal

---

## STEP 8 — PROGRESS INPUT (Kabid/Kabag)

Route: /opd (opd_staff)

Dashboard Kabid:
- List IKU unit mereka bulan ini
- Status tiap IKU: Belum Isi / Draft / Submitted / Scored
- Warning deadline jika sudah lewat submission_deadline_day

Form input per IKU:
- Tampilkan target cascade bulan ini
- Input: realization_description (required), evidence_url, file upload, obstacle_notes
- Tombol: [Simpan Draft] [Submit]
- Submit → dispatch GenerateAiScore job jika ai_enabled=true

Kepala OPD: lihat semua unit + status + bisa submit untuk unit yang belum submit

---

## STEP 9 — JOBS & SERVICES

### Job: GenerateAiScore (queue: database)
```
Input: IkuProgress model
Proses:
1. Ambil data: IKU, cascade bulan ini, realisasi
2. Load template dari Setting::get('ai_prompt_template')
3. Replace placeholder {opd_name} {unit_name} {iku_name}
   {iku_description} {cascade_target} {realization}
4. Call Anthropic API:
   POST https://api.anthropic.com/v1/messages
   Header: x-api-key: Setting::get('ai_api_key'), anthropic-version: 2023-06-01
   Body: {model: "claude-sonnet-4-6", max_tokens: 500,
         messages: [{role: user, content: prompt}]}
5. Parse JSON dari response.content[0].text
6. Simpan ke IkuScore: score_ai, ai_reasoning, ai_generated_at, status=ai_done
7. Notifikasi in-app ke superadmin
8. Log ke activity_logs
Retry max 3x. Skip jika ai_enabled=false.
```

### Job: SendWhatsAppMessage (queue: database)
```
Input: phone, message, user_id (opsional)
Proses:
1. Cek wa_enabled = true
2. POST https://fontee.io/api/send
   Header: token: Setting::get('wa_api_key')
   Body: {target: phone, message: message}
3. Catat ke wa_logs (sukses atau gagal)
```

### Service: ScoringService
```php
// Hitung skor OPD satu bulan
calculateOpdScore(Opd $opd, int $month, int $year): array
  → ambil semua IKU aktif OPD + score_bupati masing-masing
  → IKU kerjasama: score = score_bupati dari source_iku_id
  → kalikan weight_percentage / 100
  → hitung score_utama dan score_kerjasama terpisah
  → terapkan utama_weight dan kerjasama_weight dari opd_iku_weight_configs
  → simpan ke monthly_summaries
  → return ['utama', 'kerjasama', 'total', 'is_complete']

calculateAsistenScore(int $asisten_opd_id, int $month, int $year): float
  → rata-rata score_total OPD di bawah asisten ini dari monthly_summaries

recalculateAll(int $month, int $year): void
  → loop semua OPD aktif, calculateOpdScore masing-masing
  → loop semua Asisten, calculateAsistenScore masing-masing
```

### Command: iku:recalculate {month?} {year?}
Panggil ScoringService::recalculateAll(). Jadwalkan daily 01:00.

### Command: iku:send-reminders
Kirim WA ke Kabid yang belum submit bulan aktif.
Jadwalkan monthly sesuai wa_reminder_day dari settings.

---

## STEP 10 — TA SCORING PAGE

Route: /scoring/ta (superadmin only)
List semua IKU status ai_done bulan aktif.
Per card: tampilkan OPD | Unit | Nama IKU | Target | Realisasi | Skor AI | Reasoning AI
Form: input score_ta (1-10) + ta_notes
Setelah simpan: status → ta_done, notifikasi ke Bupati

---

## STEP 11 — BUPATI SCORING PAGE

Route: /scoring/bupati (bupati + superadmin)
Header: filter bulan + tahun. Tab: Belum Dinilai | Sudah Dinilai

Per card (layout jelas dan mudah di tablet):
```
[OPD] [UNIT] [NAMA IKU] — Bobot: X%
TARGET: {cascade_target}
REALISASI: {realization} | [Lihat Bukti]
AI: [X/10] "{ai_reasoning}"
TA: [X/10] "{ta_notes}"
SKOR BUPATI: [1][2][3][4][5][6][7][8][9][10]  ← tombol klik
Catatan: [textarea]
[Simpan Draft] [Finalize ✓]
```

Finalize: set is_finalized=true, trigger calculateOpdScore, kirim WA ke Kepala OPD
(jika wa_enabled dan opd_can_see_own_score=true)

Tombol "Finalize Semua" dengan konfirmasi dialog.

Setelah finalize: IKU Kerjasama yang source_iku_id = IKU ini → otomatis update score_bupati juga

---

## STEP 12 — DASHBOARD BUPATI

Route: /dashboard (management)
Controller: DashboardController
Data default: bulan sebelumnya, tahun aktif dari settings

Layout 6 panel grid 3×2 (landscape-friendly):

PANEL 1 — Overview Semua OPD:
Rerata semua OPD. Mini horizontal bar chart (Chart.js CDN).
Toggle tampilkan AI/TA/Bupati. Color badge per OPD.

PANEL 2 — Sekda & Kabag Setda:
Skor Sekda. List 3 Kabag + skor masing-masing.

PANEL 3 — Asisten I & OPD:
Skor Asisten I. List OPD + skor + badge warna.

PANEL 4 — Asisten II & OPD (sama)
PANEL 5 — Asisten III & OPD (sama)

PANEL 6 — Aksi:
Jumlah IKU pending Bupati. Tombol: [Nilai Sekarang] [Export PDF] [Export Excel].
Filter bulan/tahun di sini.

Color: ≥8 hijau, 6-7.9 kuning, <6 merah

Klik OPD → halaman /opd/{id} (lihat detail IKU, skor, tren, kabid list)

Halaman /opd/{id}:
Tab 1: IKU Utama (table + inline scoring Bupati)
Tab 2: IKU Kerjasama (table + skor mirror)
Tab 3: Tren 6 bulan (line chart AI vs TA vs Bupati)
Tab 4: Kabid/Kabag + skor unit masing-masing

Dashboard Sekda (/dashboard/sekda): semua OPD read-only
Dashboard Asisten (/dashboard/asisten): hanya OPD di koordinasinya
Dashboard OPD (/opd): untuk kepala_opd, kabid, kabag — lihat IKU + status input

---

## STEP 13 — EXPORT

Export Excel (GET /export/excel):
- Filter: bulan, tahun, opd_id (opsional)
- Sheet 1: Rekap semua OPD
- Sheet 2: Detail IKU per OPD

Export PDF (GET /export/pdf/{opd_id}):
- Template A4, ada header logo Pemkab
- Nama OPD, bulan, tabel IKU + skor
- Gunakan barryvdh/laravel-dompdf

---

## STEP 14 — NOTIFIKASI IN-APP

Tabel notifications (standard Laravel).
Trigger:
- Ke superadmin: AI score selesai
- Ke Bupati: semua TA sudah scoring bulan ini
- Ke Kepala OPD: Bupati finalize skor OPD-nya (jika setting aktif)

Tampil di navbar: badge angka + dropdown 5 notifikasi terbaru.

---

## STEP 15 — AI ASSISTANT SCAFFOLD

Route: /admin/ai-assistant
Tampilkan UI chat dengan placeholder "Fitur AI Assistant segera hadir"
Buat service kosong: App\Services\AiAssistantService dengan method ask()

---

## STEP 16 — VERIFIKASI AKHIR & README

Jalankan:
```bash
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Pastikan zero error. Tampilkan semua credentials default.

Buat README.md berisi:
- Requirements (PHP 8.2+, MySQL 8+, Composer, Node 18+)
- Langkah instalasi
- Cara setup di Niagahoster (PHP version, cron scheduler, SSH artisan)
- Default credentials
- Cara konfigurasi AI dan WhatsApp lewat /admin/settings

Cron untuk Niagahoster (satu baris di cPanel):
```
* * * * * php /home/USER/public_html/iku/artisan schedule:run >> /dev/null 2>&1
```

---

## CATATAN PENTING

1. Semua API key (AI, WA) disimpan ENCRYPTED di tabel settings, bukan .env
2. Setelah Bupati finalize skor, tidak bisa diubah kecuali superadmin + audit log
3. IKU Kerjasama: skor_bupati otomatis mengikuti skor_bupati dari source_iku_id
4. Setiap IKU WAJIB ada cascade tiap bulan (boleh has_activity=false tapi record harus ada)
5. Tahun aktif: 2026. Bulan mulai: Mei 2026 (current_scoring_month=5)
6. Queue driver: database (bukan Redis) karena Niagahoster shared hosting
7. Kerjakan berurutan dari Step 1 sampai 16. Setelah selesai satu step, lanjut berikutnya.
```
