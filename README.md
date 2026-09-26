# SIPRA-C4.5 (Sistem Informasi Prediksi Risiko Akademik Decision Tree C4.5)

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![D3.js](https://img.shields.io/badge/D3.js-v7-F9A03C?style=for-the-badge&logo=d3.js&logoColor=white)](https://d3js.org)
[![Tests](https://img.shields.io/badge/Tests-33%20Passed%20(131%20assertions)-10b981?style=for-the-badge)](tests)
[![License](https://img.shields.io/badge/License-MIT-blue.svg?style=for-the-badge)](LICENSE)

Aplikasi Web Data Mining dan Early Warning System (EWS) untuk **Klasifikasi & Prediksi Risiko Keterlambatan Kelulusan Mahasiswa** menggunakan implementasi murni algoritma **Decision Tree C4.5**.

Dikembangkan sebagai produk penelitian skripsi:
- **Peneliti**: Pingky Hera Veliyanti
- **NPM / NIM**: 2271020052
- **Program Studi**: Sistem Informasi
- **Fakultas**: Sains dan Teknologi
- **Institusi**: Universitas Islam Negeri (UIN) Raden Intan Lampung

---

## 🎯 Fitur Utama Sistem

### 1. Pure PHP C4.5 Engine (`C45EngineService.php`)
- **Implementasi Murni Algoritma C4.5**: Perhitungan matematis *Entropy*, *Information Gain*, *Split Information*, dan *Gain Ratio* secara transparan.
- **Induksi Pohon & Aturan IF-THEN**: Ekstraksi pohon keputusan rekursif hingga simpul daun (*leaf nodes*) dan pembentukan aturan klasifikasi (*production rules*).
- **Evaluasi Model Komprehensif**: Menghitung Akurasi, Presisi, Recall, F1-Score, dan *Heatmap Confusion Matrix* 3x3 (*Risiko Rendah, Risiko Sedang, Risiko Tinggi*).

### 2. Visualisasi Pohon Interaktif D3.js Vector Tree
- Diagram pohon keputusan hierarki vertikal atas-ke-bawah (*top-down*) berbasis SVG vektor kurva bezier.
- Lencana kondisi cabang (*branch conditions*) mengambang langsung pada garis penghubung.
- Kontrol kanvas interaktif (*Pan & Drag*, *Scroll-to-Zoom*, *Zoom In/Out*, *Pusatkan*, dan *Mode Teks Logika*).

### 3. Early Warning System (EWS) Alert Center
- Deteksi dini mahasiswa dengan indikator kritis (IPK < 2.75, Kehadiran < 75%, atau SKS mengulang).
- Rekomendasi intervensi akademik otomatis untuk Kaprodi dan Dosen Pembimbing Akademik (PA).

### 4. Simulator Prediksi Multi-Mode
- **Prediksi Tunggal**: Dilengkapi fitur *Auto-Fill* data mahasiswa untuk kemudahan sesi konseling perwalian.
- **Prediksi Massal (*Batch Upload*)**: Unggah berkas spreadsheet Excel `.xlsx` untuk mengklasifikasikan seluruh mahasiswa dalam satu angkatan sekaligus.
- **Surat Rekomendasi Resmi**: Hasil prediksi dapat langsung dicetak berstandar kop surat akademik UIN Raden Intan Lampung.

### 5. Multi-Role Authentication & Keamanan
- 4 Level Hak Akses: **Admin**, **Kaprodi**, **Dosen PA**, dan **Mahasiswa**.
- Dilengkapi fitur interaktif *Hide/Unhide Password* (Ikon Mata).
- Pusat Bantuan (*Help Center*) & FAQ terintegrasi.

---

## 📊 6 Variabel Prediktor Akademik

| No | Atribut / Fitur | Kategori Diskretisasi |
| :---: | :--- | :--- |
| 1 | **Indeks Prestasi Kumulatif (IPK)** | • Rendah: `< 2.75`<br>• Sedang: `2.75 - 3.24`<br>• Tinggi: `≥ 3.25` |
| 2 | **Indeks Prestasi Semester (IPS)** | • Rendah: `< 2.75`<br>• Sedang: `2.75 - 3.24`<br>• Tinggi: `≥ 3.25` |
| 3 | **SKS Semester Diambil** | • Rendah: `< 15 SKS`<br>• Sedang: `15 - 19 SKS`<br>• Tinggi: `≥ 20 SKS` |
| 4 | **Persentase Kehadiran Kuliah** | • Rendah: `< 75%`<br>• Sedang: `75% - 84.9%`<br>• Tinggi: `≥ 85%` |
| 5 | **Status Cuti Akademik** | • Ya: Cuti<br>• Tidak: Aktif Perkuliahan |
| 6 | **SKS Tidak Lulus (Nilai D/E)** | • Ada: `> 0 SKS`<br>• Tidak Ada: `0 SKS` |

---

## 💻 Kredensial Masuk Default

| Peran (Role) | Email / NPM / NIP | Password | Akses Utama |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@uinril.ac.id` | `admin123` | Master Mahasiswa, Impor Excel, Training C4.5, Prediksi Massal |
| **Kaprodi** | `kaprodi@uinril.ac.id` | `prodi123` | Monitoring EWS, Evaluasi Model, Pohon Keputusan, Cetak Laporan |
| **Dosen PA** | `dosenpa@uinril.ac.id` | `dosen123` | Monitoring Mahasiswa Bimbingan, Simulasi KRS |
| **Mahasiswa** | `2271020052` | `password` | Portal Deteksi Dini & Rekam Akademik Pribadi |

---

## ⚙️ Panduan Instalasi Lokal

### Prasyarat Sistem
- PHP `>= 8.2` (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `curl`)
- Composer
- Node.js & NPM
- MySQL Server

### Langkah Instalasi
```bash
# 1. Clone repositori
git clone https://github.com/fredli4qooni/sipra-c45.git
cd sipra-c45

# 2. Install dependensi PHP & JavaScript
composer install
npm install

# 3. Konfigurasi berkas lingkungan (.env)
cp .env.example .env
php artisan key:generate

# 4. Sesuaikan konfigurasi database pada .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=db_sipra_c45
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Jalankan migrasi database & seeding data awal
php artisan migrate:fresh --seed

# 6. Build aset frontend & jalankan server lokal
npm run build
php artisan serve
```

Aplikasi dapat diakses melalui peramban di: **`http://localhost:8000`**

---

## 🧪 Pengujian Otomatis (*Automated Testing*)

Proyek ini dilengkapi dengan 33 *feature test suites* (131 *assertions*) yang mencakup pengujian alur autentikasi, pra-pemrosesan data, kalkulasi algoritma C4.5, evaluasi matriks, simulasi prediksi, dan pembuatan laporan:

```bash
php artisan test
```

Hasil uji:
```text
Tests:    33 passed (131 assertions)
Duration: ~2.5s
Status:   100% PASSED (0 FAILURES)
```

---

## 📄 Lisensi

Hak Cipta © 2026 **Pingky Hera Veliyanti** (NPM: 2271020052) & Tim Peneliti Program Studi Sistem Informasi UIN Raden Intan Lampung. Dilisensikan di bawah lisensi [MIT](LICENSE).
