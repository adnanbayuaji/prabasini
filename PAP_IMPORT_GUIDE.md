# PAP Import - Excel Column Mapping Guide

## Struktur Kolom Excel yang Diharapkan

File Excel untuk import PAP harus memiliki kolom dalam urutan ini:

### Format Excel PAP Standard

| No | Kolom Excel | Database Field | Tipe | Contoh |
|----|------------|----------------|------|---------|
| 1 | Nomor Virtual Account | nomor_virtual_account | VARCHAR(50) | 62234100001 |
| 2 | Nomor Berkas | nomor_berkas | VARCHAR(50) | BRK-2024-001 |
| 3 | Nama Wajib Pajak | nama_wajib_pajak | VARCHAR(200) | PT. Maju Jaya |
| 4 | Alamat Wajib Pajak | alamat_wajib_pajak | VARCHAR(500) | Jl. Gatot Subroto No 5 |
| 5 | Nama Perusahaan | nama_perusahaan | VARCHAR(200) | PT. Indonesia Maju |
| 6 | Alamat Perusahaan | alamat_perusahaan | VARCHAR(500) | Jl. Sudirman No 10 |
| 7 | Peruntukan PAP | peruntukan_pap | VARCHAR(200) | Pajak Air Permukaan |
| 8 | No Kohir | no_kohir | VARCHAR(50) | KOH-001 |
| 9 | Bagian/Bulan | bagian_bulan | VARCHAR(50) | Bulan 1 |
| 10 | Tahun | tahun | INT | 2024 |
| 11 | Tanggal Ditetapkan | ditetapkan_tanggal | DATE | 15/01/2024 atau 45325 (Excel serial) |
| 12 | Jatuh Tempo | jatuh_tempo_pembayaran | DATE | 31/03/2024 atau 45411 (Excel serial) |
| 13 | Jenis Pungutan | jenis_pungutan | VARCHAR(100) | PAP - Air Permukaan |
| 14 | Volume/Areal | volume_areal_per_daya | DECIMAL(15,2) | 5000.50 |
| 15 | Harga Dasar Air | harga_dasar_air | DECIMAL(15,2) | 500.00 |
| 16 | Tarif Pajak | tarif_pajak | DECIMAL(10,4) | 0.0100 |
| 17 | Pajak Terutang | pajak_terutang | DECIMAL(15,2) | 50000.00 |
| 18 | Jumlah PAP | jumlah_pap | DECIMAL(15,2) | 50000.00 |
| 19 | No HP Wajib Pajak | no_hp | VARCHAR(20) | 08123456789 atau 62812345678 |
| 20+ | Custom Fields (Optional) | custom_field_1-5 | VARCHAR(255) | Kolom tambahan jika perlu |

## Format File yang Didukung

✅ **.xls** - Excel 97-2003 (menggunakan PHPSpreadsheet)
✅ **.xlsx** - Excel 2007+ (menggunakan PHPSpreadsheet)
✅ **.csv** - Comma Separated Values (auto-delimiter detection)

## Catatan Penting

### 1. Header Row (Baris Pertama)
- **Opsional** - Jika ada header, akan otomatis terdeteksi dan di-skip
- Header harus mengandung kata kunci seperti "virtual", "berkas", atau "nomor"
- Contoh: Nomor Virtual Account, Nomor Berkas, Nama Wajib Pajak, dst

### 2. Nomor HP
- **WAJIB DIISI** untuk setiap record
- Format: Bisa dengan 0 di awal (081...) atau dengan kode negara (62812...)
- Sistem akan otomatis konversi ke format internasional (62812...)

### 3. Nomor Virtual Account
- **WAJIB DIISI** untuk setiap record
- Format: Angka saja (misal: 62234100001)

### 4. Tanggal
- **Format Excel**: Excel Serial Number (misal: 45325)
- **Format Text**: Bisa 15/01/2024, 2024-01-15, 01/15/2024
- Sistem akan otomatis parse multiple format

### 5. Nominal (Angka)
- Format: Angka desimal dengan titik (misal: 50000.00)
- Tanda: Jangan gunakan simbol Rp atau koma
- Contoh: 50000.50 (bukan Rp 50.000,50)

## Contoh Isi File Excel

```
62234100001    BRK-2024-001    PT. Maju Jaya         Jl. Gatot Subroto No 5    PT. Indonesia    Jl. Sudirman 10    Pajak Air    KOH-001    Bln 1    2024    15/01/2024    31/03/2024    PAP Air    5000.50    500    0.01    50000    50000    08123456789
62234100002    BRK-2024-002    PT. Sukses Maju       Jl. Ahmad Yani No 3       PT. Sukses       Jl. Diponegoro 5   Pajak Air    KOH-002    Bln 1    2024    15/01/2024    31/03/2024    PAP Air    7500    500    0.01    75000    75000    08198765432
62234100003    BRK-2024-003    Toko Andi             Jl. Raya No 1             CV Andi           Jl. Kartini 2      Pajak Air    KOH-003    Bln 1    2024    15/01/2024    31/03/2024    PAP Air    10000   500    0.01    100000   100000   08156781234
```

## Template CSV Format

```csv
Nomor Virtual,Nomor Berkas,Nama Wajib Pajak,Alamat Wajib,Nama Perusahaan,Alamat Perusahaan,Peruntukan,No Kohir,Bagian Bulan,Tahun,Tgl Ditetapkan,Jatuh Tempo,Jenis Pungutan,Volume,Harga Dasar,Tarif,Pajak Terutang,Jumlah,No HP
62234100001,BRK-2024-001,PT. Maju Jaya,Jl. Gatot Subroto No 5,PT. Indonesia,Jl. Sudirman 10,Pajak Air,KOH-001,Bln 1,2024,15/01/2024,31/03/2024,PAP Air,5000.50,500.00,0.0100,50000.00,50000.00,08123456789
62234100002,BRK-2024-002,PT. Sukses Maju,Jl. Ahmad Yani No 3,PT. Sukses,Jl. Diponegoro 5,Pajak Air,KOH-002,Bln 1,2024,15/01/2024,31/03/2024,PAP Air,7500.00,500.00,0.0100,75000.00,75000.00,08198765432
```

## API Integration

### Upload PAP File

```bash
curl -X POST \
  -F "file=@pap_data.xlsx" \
  -F "action=upload" \
  http://localhost/prabasini/src/api_pap_import.php
```

**Response Success:**
```json
{
  "success": true,
  "message": "Import berhasil",
  "inserted": 100,
  "errors": [],
  "total_records": 105
}
```

### Get List PAP

```bash
curl "http://localhost/prabasini/src/api_pap_import.php?action=get_list&limit=50&offset=0"
```

### Generate Pesan

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
    "pesan": "Selamat Siang, Bpk/Ibu PT. Maju Jaya...",
    "url_wa": "https://wa.me/62812345678",
    "jumlah": 50000.00
  }
}
```

## Troubleshooting

### Import Gagal - File Kosong
- Pastikan file memiliki data
- Cek format kolom sesuai dengan urutan di atas

### Error "No HP harus diisi"
- Pastikan kolom No HP (kolom 19) terisi semua
- Format harus valid

### Tanggal Tidak Terbaca
- Coba gunakan format: 15/01/2024 atau 2024-01-15
- Jika pakai Excel, cek format sel adalah "Date"

### Nominal tidak terbaca
- Pastikan menggunakan titik (.) bukan koma (,)
- Jangan ada simbol Rp atau format lain

## Script Mapping Manual

Jika file Excel Anda berbeda struktur kolom, edit mapping di `src/PapImportRepository.php` method `insertFromExcelArray()`:

```php
$data = [
    'nomor_virtual_account' => trim($row[0] ?? ''),   // Sesuaikan index
    'nomor_berkas' => trim($row[1] ?? ''),
    'nama_wajib_pajak' => trim($row[2] ?? ''),
    // ... dst
    'no_hp' => trim($row[18] ?? ''),  // PENTING: nomor HP posisi terakhir
];
```

---

## Next Steps

1. Siapkan file Excel sesuai format di atas
2. Upload melalui API atau UI
3. Data akan disimpan di tabel `pap_import`
4. Gunakan `generate_pesan` untuk membuat pesan WhatsApp
5. Data siap untuk di-generate gambar dan dikirim via webhook
