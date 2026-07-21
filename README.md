# Prabasini PAP Management System

## Deskripsi
Sistem manajemen PAP (Program Alokasi Produk) dengan fitur:
- Upload/Import data dari Excel (.xls, .xlsx, atau .csv)
- Tampilan list data PAP yang rapi
- Kolom Aksi untuk:
  - Kirim WhatsApp (wa.me)
  - Copy nomor
  - Hapus data

## Struktur Folder
```
prabasini/
├── config/
│   └── database.php          # Konfigurasi database
├── public/
│   ├── pap.php              # Halaman utama PAP
│   ├── uploads/             # Folder untuk upload file
│   ├── css/
│   └── js/
├── src/
│   ├── api_pap.php          # API handler
│   ├── ExcelImporter.php    # Parser file Excel/CSV
│   └── PapRepository.php    # Database operations
└── README.md
```

## Setup

### 1. Database Setup
Buat database baru (atau gunakan yang sudah ada):
```sql
CREATE DATABASE IF NOT EXISTS prabasini;
```

Edit file `config/database.php` sesuaikan dengan konfigurasi database Anda:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'prabasini');
```

Tabel akan dibuat otomatis saat pertama kali akses halaman.

### 2. Server Setup
Pastikan:
- PHP versi 7.4 atau lebih baru
- MySQL/MariaDB
- Folder `public/uploads/` memiliki permission write (755 atau 777)

### 3. Akses Halaman
- Buka browser: `http://localhost/prabasini/public/pap.php`

## Format File Excel

File Excel yang diimport harus memiliki struktur kolom (minimal 2 kolom):

| Kolom | Keterangan | Format |
|-------|-----------|---------|
| Kolom A | Nomor Pengirim | Angka (misal: 62812345678) |
| Kolom B | Nomor Tujuan | Angka (misal: 62898765432) |
| Kolom C (Opsional) | Waktu Request | DateTime (misal: 2024-01-15 10:30:00) |

**Contoh Data:**
```
62812345678    62898765432    2024-01-15 10:30:00
62812345679    62898765433    2024-01-15 10:35:00
```

**File yang didukung:**
- .xls (Excel 97-2003)
- .xlsx (Excel 2007+)
- .csv (Comma Separated Values)

## Penggunaan

### 1. Import Data
1. Klik area upload atau drag file Excel
2. Pilih file (.xls, .xlsx, atau .csv)
3. Tunggu proses import selesai
4. Data akan muncul di tabel

### 2. Kolom Aksi
- **WA.me**: Buka WhatsApp Web dengan nomor tujuan
- **Copy**: Salin nomor tujuan ke clipboard
- **Hapus**: Hapus data dari database (konfirmasi diperlukan)

### 3. Status Data
- **Pending**: Data belum dikirim (warna kuning)
- **Terkirim**: Data sudah dikirim via WhatsApp (warna hijau)
- **Gagal**: Data gagal dikirim (warna merah)

Status otomatis berubah ke "Terkirim" ketika Anda klik tombol WA.me.

## Fitur
✓ Upload multiple file dalam satu halaman  
✓ Auto-detect format Excel (XLS/XLSX/CSV)  
✓ Data validation sebelum import  
✓ Responsive design (mobile-friendly)  
✓ Drag & drop upload  
✓ Real-time data table  
✓ WhatsApp integration  
✓ Error handling yang baik  

## Teknologi
- **Backend**: PHP 7.4+
- **Frontend**: Bootstrap 5, Tailwind CSS, Vanilla JavaScript
- **Database**: MySQL/MariaDB
- **Parser**: Built-in PHP untuk CSV, simple method untuk XLSX/XLS

## Notes
- Data disimpan dengan timestamp otomatis
- Nomor WhatsApp harus dalam format internasional (dengan kode negara)
- Maksimal ukuran file: 5MB
- Header row di Excel secara otomatis terdeteksi dan skip

## Troubleshooting

### Database Connection Error
- Pastikan MySQL running
- Cek konfigurasi di `config/database.php`
- Pastikan database `prabasini` sudah ada

### Upload Gagal
- Cek permission folder `public/uploads/`
- Pastikan file tidak lebih dari 5MB
- Format file harus .xls, .xlsx, atau .csv

### Data Tidak Tampil
- Refresh halaman (Ctrl+F5)
- Cek console browser untuk error
- Pastikan file Excel memiliki minimal 2 kolom

## License
MIT
