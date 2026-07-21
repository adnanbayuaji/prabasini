# Instalasi & Setup PAP Management System

## Prasyarat
- PHP 7.4 atau lebih baru
- MySQL 5.7 atau MariaDB 10.2+
- Web Server (Apache/Nginx)
- Browser modern

## Langkah-Langkah Setup

### 1. Download & Extract
```bash
git clone <repository>
cd prabasini
```

### 2. Setup Database

#### Opsi A: Via phpMyAdmin
1. Buka phpMyAdmin (biasanya http://localhost/phpmyadmin)
2. Klik "Import"
3. Upload file `setup.sql`
4. Klik "Go"

#### Opsi B: Via Command Line
```bash
mysql -u root -p < setup.sql
```

#### Opsi C: Manual
1. Buat database:
```sql
CREATE DATABASE prabasini;
```

2. Import tabel (buka file `setup.sql` dan jalankan query-nya)

### 3. Konfigurasi Database
Edit file `config/database.php`:

```php
define('DB_HOST', 'localhost');      // Host MySQL
define('DB_USER', 'root');           // Username MySQL
define('DB_PASS', '');               // Password MySQL
define('DB_NAME', 'prabasini');      // Nama database
```

**Contoh konfigurasi untuk berbagai setup:**

**Localhost (XAMPP/WAMP/MAMP):**
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'prabasini');
```

**Production Server:**
```php
define('DB_HOST', 'db.example.com');
define('DB_USER', 'dbuser');
define('DB_PASS', 'dbpassword123');
define('DB_NAME', 'prabasini');
```

### 4. Setup Folder Permissions
Pastikan folder `public/uploads/` dapat ditulis:

**Linux/Mac:**
```bash
chmod -R 755 public/uploads/
chmod -R 777 public/uploads/  # Jika masih error
```

**Windows:**
1. Right-click folder `public/uploads/`
2. Properties > Security > Edit
3. Berikan Full Control untuk user

### 5. Akses Aplikasi
Buka di browser:
```
http://localhost/prabasini/public/pap.php
```

## Verifikasi Setup

### Testing Database Connection
Buat file `test_db.php` di folder `public/`:

```php
<?php
require_once '../config/database.php';

try {
    $result = $pdo->query("SELECT 1");
    echo "✓ Database connection OK";
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage();
}
?>
```

Akses: `http://localhost/prabasini/public/test_db.php`

### Check PHP Version
Pastikan PHP version >= 7.4:
```bash
php -v
```

## Troubleshooting

### Error: "Database Connection Error"
**Solusi:**
- Pastikan MySQL running
- Cek username/password di `config/database.php`
- Pastikan database `prabasini` sudah dibuat

### Error: "Permission Denied" pada uploads
**Solusi:**
```bash
# Linux/Mac
sudo chown -R www-data:www-data public/uploads/
chmod -R 755 public/uploads/

# Atau
chmod -R 777 public/uploads/
```

### Error: "File upload failed"
**Solusi:**
- Cek ukuran file (max 5MB)
- Format file harus .xls, .xlsx, atau .csv
- Cek disk space
- Cek file permissions

### Data tidak muncul setelah import
**Solusi:**
- Refresh halaman (Ctrl+F5 atau Cmd+Shift+R)
- Buka console browser (F12) cek ada error?
- Cek file Excel/CSV format (minimal 2 kolom)
- Cek database sudah ada datanya:

```php
<?php
require_once '../config/database.php';
$result = $pdo->query("SELECT COUNT(*) as count FROM pap");
$data = $result->fetch();
echo "Total records: " . $data['count'];
?>
```

### Whatsapp tidak membuka
**Solusi:**
- Pastikan nomor dalam format internasional (misal: 62812345678)
- Whatsapp Web harus sudah login
- Gunakan browser terbaru

## File Structure Final

```
prabasini/
│
├── config/
│   └── database.php              ← Edit sesuaikan database Anda
│
├── public/
│   ├── pap.php                   ← Halaman utama (buka ini di browser)
│   ├── uploads/                  ← Folder upload (harus writable)
│   ├── css/
│   └── js/
│
├── src/
│   ├── api_pap.php              ← API endpoints
│   ├── ExcelImporter.php        ← Parser Excel/CSV
│   └── PapRepository.php        ← Database operations
│
├── README.md                     ← Dokumentasi lengkap
├── INSTALL.md                   ← File ini
├── setup.sql                    ← SQL setup database
└── SAMPLE_PAP_DATA.csv         ← Contoh data
```

## Penggunaan Setelah Setup

1. **Buka halaman**: http://localhost/prabasini/public/pap.php
2. **Import data**: Upload file Excel/CSV
3. **Kelola data**: Lihat list, edit status, hapus data
4. **Kirim WA**: Klik tombol "WA.me" untuk buka Whatsapp

## FAQ

**Q: Berapa file size limit?**
A: Maksimal 5MB per file

**Q: Format Excel apa yang didukung?**
A: .xls (legacy), .xlsx (modern), .csv

**Q: Nomor harus format apa?**
A: Format internasional dengan kode negara (misal: 62812345678 untuk Indonesia)

**Q: Bisa import berapa data sekaligus?**
A: Tergantung PHP memory, biasanya ribuan data bisa

**Q: Data backup bagaimana?**
A: Export dari MySQL, atau backup file database

## Support
Jika ada masalah:
1. Cek error di console browser (F12)
2. Cek PHP error log
3. Cek MySQL error log
