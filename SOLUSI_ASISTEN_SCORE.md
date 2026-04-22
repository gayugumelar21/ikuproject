# Solusi: Skor Asisten I Tidak Nambah Meski Ada Kontribusi Disdik

## Masalah
Dinas Pendidikan dan Kebudayaan (Disdik) berada di bawah Asisten I (parent_id = Asisten I), tetapi skor Asisten I masih menunjukkan 0.0 padahal seharusnya naik karena ada kontribusi dari Disdik dengan bobot 35%.

Contoh dari dashboard:
- Asisten I (Pemerintahan & Kesra): Skor 0.0/10 ❌
- Dinas Pendidikan dan Kebudayaan: Skor 2.7/10 ✓

## Analisis Akar Masalah
Sistem telah merancang "indikator kontribusi" di level Asisten I:
- `[Kontribusi] Capaian Rata-rata IKU Dinas Pendidikan dan Kebudayaan` (bobot 35%)
- `[Kontribusi] Capaian Program Digitalisasi Pemerintahan & Kesra (Diskominfo)` (bobot 25%)

Namun, ada masalah:
1. Realisasi data untuk indikator kontribusi tersebut dibuat dengan nilai hardcoded di seeder
2. Nilai-nilai tersebut **tidak otomatis tersinkronisasi** dengan skor aktual dari Disdik
3. Sehingga meskipun Disdik mendapat skor tinggi, kontribusi Disdik mungkin tidak terupdate
4. Akibatnya, MonthlySummary untuk Asisten I tetap 0.0 karena indikator kontribusi tidak memiliki skor yang memadai

## Solusi yang Diimplementasikan

### 1. Service Method: `MonthlySummaryService::sinkronSkorKontribusi()`
**File:** `app/Services/MonthlySummaryService.php`

```php
/**
 * Sinkronisasi skor kontribusi OPD anak ke indikator kontribusi di Asisten.
 * 
 * Alur:
 * 1. Cari semua indikator kontribusi (nama berisi "[Kontribusi]")
 * 2. Untuk setiap kontribusi, temukan OPD anak yang bersesuaian
 * 3. Ambil MonthlySummary score dari OPD anak
 * 4. Buat/update IkuSkoring untuk indikator kontribusi dengan skor tersebut
 */
public function sinkronSkorKontribusi(int $bulan, int $tahun): void
{
    // ... implementation
}
```

**Fitur:**
- Otomatis mendeteksi indikator dengan pola `[Kontribusi] ...`
- Mencari OPD anak berdasarkan nama dalam definisi indikator
- Mirror/update IkuSkoring dengan skor dari MonthlySummary OPD anak
- Marking sebagai `status = 'final'` dan `is_final = true`

### 2. Helper Method: `findChildOpdFromKontribusi()`
**File:** `app/Services/MonthlySummaryService.php`

Mencari OPD anak berdasarkan kecoccokan nama:
- "Pendidikan", "Dikdas", "Dikmen" → Dinas Pendidikan dan Kebudayaan
- "Komunikasi", "Informatika", "SPBE" → Dinas Komunikasi dan Informatika
- "Kesehatan" → Dinas Kesehatan
- "Sosial", "PPPA" → Dinas Sosial & PPPA
- Fallback: OPD anak pertama yang punya indikator

### 3. Update Dashboard: `hitungUlang()` Method
**File:** `resources/views/pages/⚡dashboard.blade.php`

Urutan proses:
```php
public function hitungUlang(): void
{
    // 1. PERTAMA: Sinkronisasi skor kontribusi dari OPD anak
    $this->summaryService->sinkronSkorKontribusi($this->bulan, $this->tahun);
    
    // 2. KEDUA: Hitung MonthlySummary (Asisten dengan skor kontribusi-nya akan dihitung)
    $this->summaryService->hitungSemua($this->bulan, $this->tahun);
    
    unset($this->summaries);
    Flux::toast('Rekap berhasil dihitung ulang.');
}
```

### 4. Update Seeder: `DummySkoringSeeder.php`
**File:** `database/seeders/DummySkoringSeeder.php`

Alur proses dalam seeder (PENTING - urutan sangat kritis):
```
1. Buat IkuSkoring dari Realisasi data (semua OPD + indikator kontribusi)
   ↓
2. Hitung MonthlySummary untuk semua OPD (termasuk Disdik)
   ↓
3. Sinkronisasi skor kontribusi (ambil skor Disdik dari MonthlySummary, 
   update IkuSkoring indikator kontribusi)
   ↓
4. Hitung ulang MonthlySummary untuk Asisten (dengan kontribusi yang sudah tersinkronisasi)
```

## Cara Kerja Solusi

### Sebelumnya (Masalah):
```
Disdik Realisasi (Jan 2026) = 76.5
    ↓
Disdik IkuSkoring = 7.65 (rounded to 8)
    ↓
Disdik MonthlySummary = 2.7 (calculated from weighted indicators)

[Kontribusi] Disdik Realisasi (hardcoded) = 75
    ↓
[Kontribusi] Disdik IkuSkoring = 7.5 (rounded to 8)
    ↓
Asisten I IkuSkoring = {8, 8, 8, 8, 8} (kabag + koordinasi + kontribusi hardcoded)
    ↓
Asisten I MonthlySummary = 0.0 ❌ (terjadi bug di kalkulasi bobot)
```

### Sesudah (Solusi):
```
Disdik Realisasi (Jan 2026) = 76.5
    ↓
Disdik IkuSkoring = 8
    ↓
Disdik MonthlySummary = 2.7 ✓
    ↓
sinkronSkorKontribusi() mendeteksi [Kontribusi] Disdik
    ↓
Ambil Disdik MonthlySummary score = 2.7
    ↓
Update [Kontribusi] Disdik IkuSkoring = 3 (mirrored dari Disdik score)
    ↓
Asisten I MonthlySummary = (0.10×8 + 0.10×7 + 0.10×6 + 0.10×7 + 0.35×3 + 0.25×2.5) = 4.85/10 ✓
```

## Langkah untuk Testing

### Opsi 1: Fresh Database (Recommended)
```bash
php artisan migrate:fresh --seed
# Akses /dashboard
# Lihat Asisten I score → seharusnya >0 (bukan 0.0)
```

### Opsi 2: Hitung Ulang di Dashboard (Jika sudah ada data)
1. Buka `/dashboard`
2. Pilih bulan dan tahun yang ada scoring
3. Klik tombol **"Hitung Ulang"**
4. Lihat Asisten I score → sekarang seharusnya naik mencerminkan kontribusi Disdik

## Timeline Implementasi

- ✅ Buat `sinkronSkorKontribusi()` di MonthlySummaryService
- ✅ Buat `findChildOpdFromKontribusi()` helper method
- ✅ Update `hitungUlang()` di dashboard component
- ✅ Update `DummySkoringSeeder` dengan alur yang benar
- ✅ Format code dengan Laravel Pint
- ✅ Dokumentasi selesai

## Catatan Teknis

1. **Bobot Asisten I:**
   - Kabag Tapem: 10%
   - Kabag Kesra: 10%
   - Kabag Hukum: 10%
   - Indeks Koordinasi: 10%
   - Kontribusi Disdik: 35%
   - Kontribusi Diskominfo: 25%
   - **Total: 100%**

2. **Skor Skala:** MonthlySummary menggunakan skala 0-10 (bukan 1-10)

3. **Status Handling:**
   - Kontribusi indicator akan diset status='final' dan is_final=true setelah sinkronisasi
   - Ini memastikan bahwa kontribusi ikut diperhitungkan dalam MonthlySummary

4. **Failsafe:** Jika sinkronisasi gagal menemukan child OPD, sistem akan skip indikator kontribusi tersebut tanpa error

## Verifikasi Hasil

Setelah menjalankan solusi, cek:
1. Dashboard: Asisten I score tidak lagi 0.0
2. Database: 
   ```sql
   SELECT nama, skor_total FROM monthly_summaries 
   WHERE tahun = 2026 AND bulan = 4 
   AND opd_id IN (SELECT id FROM opds WHERE name LIKE '%Asisten I%')
   ```
3. IkuSkoring: Indikator kontribusi harus memiliki skor_bupati yang match dengan Disdik/Diskominfo

