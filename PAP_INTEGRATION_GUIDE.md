# PAP Import System - Complete Integration Guide

## 📋 Overview

Sistem PAP (Pajak Air Permukaan) Import yang terintegrasi dengan:
- ✅ Excel/CSV parser multi-format
- ✅ Database schema optimal untuk image/message generation  
- ✅ Image generator (menggunakan GD library)
- ✅ Message generator dinamis untuk WhatsApp
- ✅ Webhook untuk batch processing
- ✅ API untuk integration dengan sistem lain

## 🗂️ File Structure

```
src/
├── PapImportRepository.php      # CRUD layer untuk pap_import & pap_kirim_log
├── PapGeneratorHelper.php       # Utility untuk generate pesan & gambar
├── api_pap_import.php           # API endpoints untuk import & retrieval
├── webhook_pap.php              # Webhook untuk batch processing
├── ExcelImporter.php            # (Existing) Parser Excel/CSV
└── Helper.php                   # (Existing) Utilities

config/
└── database.php                 # (Existing) PDO connection

public/
├── pap.php                      # (Existing) Web UI
└── uploads/                     # Folder untuk gambar output
```

## 🔄 Data Flow

```
1. Excel File Upload
         ↓
2. ExcelImporter.parseExcel()
         ↓
3. PapImportRepository.insertFromExcelArray()
         ↓
4. Data stored in pap_import table
         ↓
5. Webhook or API trigger
         ↓
6. PapGeneratorHelper.generateGambarPap()
         ↓
7. Image saved to public/uploads/
         ↓
8. PapGeneratorHelper.generatePesanPap()
         ↓
9. Logged to pap_kirim_log table
         ↓
10. Ready for WhatsApp send
```

## 📊 Database Schema

### pap_import Table

Kolom utama yang digunakan untuk image generation:
- `nomor_virtual_account` - Virtual account number (VA) - Parameter **a**
- `nomor_berkas` - File number - Parameter **b**
- `nama_wajib_pajak` - Taxpayer name - Parameter **c**
- `alamat_wajib_pajak` - Taxpayer address - Parameter **d**
- `nama_perusahaan` - Company name - Parameter **e**
- `alamat_perusahaan` - Company address - Parameter **f**
- `peruntukan_pap` - PAP allocation - Parameter **g**
- `no_kohir` - Kohir number - Parameter **h**
- `bagian_bulan` - Month/portion - Parameter **i**
- `tahun` - Year - Parameter **j**
- `ditetapkan_tanggal` - Established date - Parameter **k**
- `jatuh_tempo_pembayaran` - Due date - Parameter **l**
- `jenis_pungutan` - Type of collection - Parameter **m**
- `volume_areal_per_daya` - Volume/area - Parameter **n**
- `harga_dasar_air` - Basic water price - Parameter **o**
- `tarif_pajak` - Tax rate - Parameter **p**
- `pajak_terutang` - Tax owed - Parameter **q**
- `jumlah_pap` - Total PAP - Parameter **r**
- `no_hp` - **CRITICAL** - WhatsApp contact number

### pap_kirim_log Table

Tracking untuk setiap send:
- `id_pap_import` - Foreign key ke pap_import
- `nomor_pengirim_kirim` - Sender number
- `nomor_tujuan_kirim` - Recipient WhatsApp number
- `nama_gambar` - Generated image filename
- `panjang_pesan` - Message length
- `status_kirim` - Status (pending/sent/failed/retrying)
- `waktu_kirim` - Send timestamp
- `waktu_terima` - Receive timestamp

## 🚀 API Usage

### 1. Upload Excel PAP

```bash
curl -X POST \
  -F "file=@pap_2024.xlsx" \
  -F "action=upload" \
  http://localhost/prabasini/src/api_pap_import.php
```

**Response:**
```json
{
  "success": true,
  "message": "Import berhasil",
  "inserted": 145,
  "errors": [],
  "total_records": 250
}
```

### 2. Get All PAP Records

```bash
curl "http://localhost/prabasini/src/api_pap_import.php?action=get_list&limit=50&offset=0"
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nomor_virtual_account": "62234100001",
      "nomor_berkas": "BRK-2024-001",
      "nama_wajib_pajak": "PT. Maju Jaya",
      "no_hp": "08123456789",
      "jumlah_pap": 50000.00,
      "status_pap": "1",
      ...
    }
  ],
  "total": 250,
  "limit": 50,
  "offset": 0
}
```

### 3. Get Specific PAP

```bash
curl "http://localhost/prabasini/src/api_pap_import.php?action=get_by_id&id=1"
```

### 4. Generate Message for PAP

```bash
curl "http://localhost/prabasini/src/api_pap_import.php?action=generate_pesan&id=1"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nomor_berkas": "BRK-2024-001",
    "nama_wajib_pajak": "PT. Maju Jaya",
    "no_hp": "08123456789",
    "pesan": "Selamat Siang, Bpk/Ibu PT. Maju Jaya\nKami dari BADAN PENDAPATAN...",
    "url_wa": "https://wa.me/62812345678",
    "jumlah": 50000.00
  }
}
```

### 5. Delete PAP Record

```bash
curl -X POST \
  -d "action=delete&id=1" \
  http://localhost/prabasini/src/api_pap_import.php
```

## 🔗 Webhook Integration

### Process Single PAP

Generates image, message, dan log to pap_kirim_log:

```bash
curl -X POST \
  -d "action=process_by_id&id=1&nomor_pengirim=6200000000" \
  http://localhost/prabasini/src/webhook_pap.php
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id_pap": 1,
    "nama_gambar": "6200000000-BRK-2024-001-1704067200.000000.png",
    "nomor_berkas": "BRK-2024-001",
    "nama_wajib_pajak": "PT. Maju Jaya",
    "no_hp": "08123456789",
    "url_wa": "https://wa.me/62812345678",
    "panjang_pesan": 1250,
    "url_gambar": "../public/uploads/6200000000-BRK-2024-001-1704067200.000000.png"
  }
}
```

### Process All Pending PAP

```bash
curl -X POST http://localhost/prabasini/src/webhook_pap.php?action=process
```

### Generate Only Image

```bash
curl -X POST \
  -d "action=generate_image&id=1&nomor_pengirim=6200000000" \
  http://localhost/prabasini/src/webhook_pap.php
```

### Send to WhatsApp

Generate pesan & format untuk WhatsApp click:

```bash
curl -X POST \
  -d "action=send_to_wa&id=1" \
  http://localhost/prabasini/src/webhook_pap.php
```

**Response:**
```json
{
  "success": true,
  "data": {
    "no_hp_formatted": "62812345678",
    "pesan": "Selamat Siang...",
    "url_wa": "https://wa.me/62812345678",
    "url_wa_with_message": "https://wa.me/62812345678?text=Selamat%20Siang..."
  }
}
```

## 💾 Database Setup

Execute SQL schema:

```sql
-- Import pap_import and pap_kirim_log tables
mysql -u user -p database < setup_pap_import.sql
```

Or via PHP:

```php
$pdo = new PDO('mysql:host=localhost;dbname=prabasini', 'user', 'password');
$repo = new PapImportRepository($pdo);
// Tables are auto-created on first instantiation
```

## 📝 Class Reference

### PapImportRepository

```php
$repo = new PapImportRepository($pdo);

// Insert from Excel array
$result = $repo->insertFromExcelArray($excelData);

// Get all records
$records = $repo->getAll($limit = 100, $offset = 0);

// Get by ID
$pap = $repo->getById($id);

// Update record
$repo->update($id, ['nama_wajib_pajak' => 'New Name']);

// Delete record
$repo->delete($id);

// Log send to pap_kirim_log
$repo->logKirim($papId, $nomorPengirim, $nomorTujuan, $namaGambar, $pesanKirim);

// Update send status
$repo->updateKirimStatus($kirimId, $status, $response);

// Count total
$total = $repo->count();
```

### PapGeneratorHelper

```php
// Generate WhatsApp message
$pesan = PapGeneratorHelper::generatePesanPap($papData);

// Generate image
$success = PapGeneratorHelper::generateGambarPap($filename, $papData, $uploadDir);

// Format WhatsApp number
$formatted = PapGeneratorHelper::formatNomorWa('08123456789'); // '62812345678'

// Generate wa.me URL
$url = PapGeneratorHelper::generateUrlWame('08123456789');

// Generate image filename
$filename = PapGeneratorHelper::generateNamaFile($nomorBerkas, $nomorPengirim);

// Format date
$formatted = PapGeneratorHelper::formatTanggal('2024-01-15'); // '15/01/2024'
```

## 🔐 Security Notes

✅ **SQL Injection Prevention**
- All queries use prepared statements
- Parameter binding via PDO

✅ **File Upload Security**
- File type validation (xls, xlsx, csv only)
- File size limit (5MB)
- Original filename sanitization

✅ **Data Validation**
- Required fields validation
- Email/phone format checking
- Numeric field type casting

✅ **Error Handling**
- Exception-based error handling
- Sensitive data not exposed in error messages
- Proper HTTP status codes

## 📋 Common Scenarios

### Scenario 1: Import Excel & Generate All Images

```bash
# 1. Upload file
curl -X POST -F "file=@data.xlsx" -F "action=upload" \
  http://localhost/prabasini/src/api_pap_import.php

# 2. Process all PAP (generate images)
curl -X POST http://localhost/prabasini/src/webhook_pap.php?action=process
```

### Scenario 2: Manual Image Generation

```bash
# Get PAP data
curl "http://localhost/prabasini/src/api_pap_import.php?action=get_by_id&id=5"

# Process specific ID
curl -X POST -d "action=process_by_id&id=5" \
  http://localhost/prabasini/src/webhook_pap.php
```

### Scenario 3: WhatsApp Integration

```bash
# Generate message & URL
curl -X POST -d "action=send_to_wa&id=5" \
  http://localhost/prabasini/src/webhook_pap.php

# Then use url_wa from response in messaging app
# Or auto-open: window.location.href = response.data.url_wa_with_message
```

## 🐛 Troubleshooting

### "Table pap_import not found"
- Run setup SQL: `mysql -u user -p db < setup_pap_import.sql`
- Or instantiate PapImportRepository once to auto-create

### Import shows 0 records
- Check Excel file has data
- Verify column order matches guide
- Check no_hp column is not empty

### Image generation fails
- Ensure GD library enabled: `php -m | grep gd`
- Check `public/uploads/` folder exists and writable
- Check template image exists: `public/images/kosongan4.png`

### Message generation empty
- Ensure all required fields in pap_import are filled
- Check character encoding is UTF-8

## 🔄 Next Steps

1. **Execute setup SQL** - Create tables
2. **Prepare Excel file** - Follow PAP_IMPORT_GUIDE.md
3. **Upload via API** - Test import
4. **Verify data** - Check pap_import table
5. **Generate images** - Webhook call
6. **Send via WhatsApp** - Use generated URL/message

---

**Version:** 1.0
**Last Updated:** 2024
**Status:** Production Ready ✅
