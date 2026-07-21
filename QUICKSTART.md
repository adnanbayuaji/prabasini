# Quick Start Guide - PAP Management System

## 30 Detik Setup

### 1️⃣ Database Setup (5 menit)
```bash
# Buka MySQL/phpMyAdmin, jalankan:
CREATE DATABASE prabasini;
```

Atau import file `setup.sql`:
- phpMyAdmin → Import → Pilih `setup.sql` → Go

### 2️⃣ Edit Konfigurasi (2 menit)
Edit file `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'prabasini');
```

### 3️⃣ Akses Aplikasi (1 detik)
Buka di browser:
```
http://localhost/prabasini/public/pap.php
```

**Selesai! ✓**

---

## Penggunaan

### Persiapan Data Excel
Buat file Excel dengan kolom:
- Kolom A: Nomor Pengirim (misal: 62812345678)
- Kolom B: Nomor Tujuan (misal: 62898765432)

**Contoh:**
| Nomor Pengirim | Nomor Tujuan |
|---|---|
| 62812345678 | 62898765432 |
| 62812345679 | 62898765433 |

### Upload File
1. Klik area upload atau drag file ke halaman
2. Pilih file Excel (.xls, .xlsx, atau .csv)
3. Tunggu selesai

### Kelola Data
- **Kirim WA**: Klik tombol "WA.me" untuk buka WhatsApp
- **Copy**: Salin nomor ke clipboard
- **Hapus**: Hapus data (perlu konfirmasi)

---

## File Structure
```
prabasini/
├── config/database.php          ← Edit ini
├── public/pap.php              ← Buka ini di browser
├── src/api_pap.php             ← API backend
└── setup.sql                   ← Import ke database
```

---

## Troubleshooting

### ❌ Database Connection Error
- Pastikan MySQL running
- Cek username/password di `config/database.php`
- Pastikan database `prabasini` sudah ada

### ❌ Upload Gagal
- Pastikan file tidak lebih dari 5MB
- Format harus .xls, .xlsx, atau .csv
- Cek folder `public/uploads/` permission (755/777)

### ❌ Data Tidak Tampil
- Refresh halaman (Ctrl+F5)
- Cek console browser untuk error (F12)
- Pastikan file Excel punya minimal 2 kolom

---

## Help
- Lihat `INSTALL.md` untuk setup detail
- Lihat `API.md` untuk dokumentasi API
- Lihat `README.md` untuk info lengkap

---

**Siap pakai! Mulai upload data Anda sekarang.** 🚀
