# Prabasini PAP System - API Documentation

## Base URL
```
/src/api_pap.php
```

## Endpoints

### 1. Upload & Import Data
**Endpoint:** `POST /src/api_pap.php?action=upload`

**Request:**
- Method: POST
- Content-Type: multipart/form-data
- Parameters:
  - `file`: File upload (.xls, .xlsx, atau .csv)
  - `action`: "upload"

**Example:**
```javascript
const formData = new FormData();
formData.append('file', fileInput.files[0]);
formData.append('action', 'upload');

fetch('/src/api_pap.php', {
    method: 'POST',
    body: formData
})
.then(res => res.json())
.then(data => console.log(data));
```

**Response:**
```json
{
    "success": true,
    "message": "Import berhasil",
    "inserted": 10,
    "errors": []
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "File terlalu besar"
}
```

---

### 2. Get All PAP Data
**Endpoint:** `GET /src/api_pap.php?action=get_list`

**Request:**
- Method: GET
- Parameters: `action=get_list`

**Example:**
```javascript
fetch('/src/api_pap.php?action=get_list')
    .then(res => res.json())
    .then(data => console.log(data));
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nomor_pengirim": "62812345678",
            "nomor_tujuan": "62898765432",
            "waktu_request": "2024-01-15 10:30:00",
            "waktu_terkirim": null,
            "status": "pending",
            "created_at": "2024-01-15 10:30:00",
            "updated_at": "2024-01-15 10:30:00"
        },
        {
            "id": 2,
            "nomor_pengirim": "62812345679",
            "nomor_tujuan": "62898765433",
            "waktu_request": "2024-01-15 10:35:00",
            "waktu_terkirim": "2024-01-15 10:36:00",
            "status": "sent",
            "created_at": "2024-01-15 10:35:00",
            "updated_at": "2024-01-15 10:36:00"
        }
    ]
}
```

---

### 3. Delete PAP Data
**Endpoint:** `POST /src/api_pap.php?action=delete`

**Request:**
- Method: POST
- Parameters:
  - `action`: "delete"
  - `id`: ID data yang akan dihapus

**Example:**
```javascript
const formData = new FormData();
formData.append('action', 'delete');
formData.append('id', 5);

fetch('/src/api_pap.php', {
    method: 'POST',
    body: formData
})
.then(res => res.json())
.then(data => console.log(data));
```

**Response:**
```json
{
    "success": true,
    "message": "Data berhasil dihapus"
}
```

---

### 4. Update Status
**Endpoint:** `POST /src/api_pap.php?action=update_status`

**Request:**
- Method: POST
- Parameters:
  - `action`: "update_status"
  - `id`: ID data
  - `status`: Status baru (pending, sent, atau failed)

**Example:**
```javascript
const formData = new FormData();
formData.append('action', 'update_status');
formData.append('id', 5);
formData.append('status', 'sent');

fetch('/src/api_pap.php', {
    method: 'POST',
    body: formData
})
.then(res => res.json())
.then(data => console.log(data));
```

**Response:**
```json
{
    "success": true,
    "message": "Status berhasil diperbarui"
}
```

---

## Data Format

### PAP Data Structure
```json
{
    "id": 1,
    "nomor_pengirim": "62812345678",        // Nomor pengirim (format: +62...)
    "nomor_tujuan": "62898765432",          // Nomor tujuan WhatsApp (format: +62...)
    "waktu_request": "2024-01-15 10:30:00", // Waktu request (datetime)
    "waktu_terkirim": null,                 // Waktu terkirim (null jika belum terkirim)
    "status": "pending",                    // Status: pending, sent, failed
    "created_at": "2024-01-15 10:30:00",    // Waktu dibuat
    "updated_at": "2024-01-15 10:30:00"     // Waktu diupdate terakhir
}
```

### Excel/CSV Format

File harus memiliki minimal 2 kolom:

| Kolom 1 | Kolom 2 | Kolom 3 (Optional) | Kolom 4 (Optional) |
|---------|---------|-------------------|-------------------|
| Nomor Pengirim | Nomor Tujuan | Waktu Request | Status |
| 62812345678 | 62898765432 | 2024-01-15 10:30:00 | pending |
| 62812345679 | 62898765433 | 2024-01-15 10:35:00 | pending |

**Format yang didukung:**
- .xls (Excel 97-2003)
- .xlsx (Excel 2007+)
- .csv (Comma Separated Values)

**Contoh CSV:**
```csv
Nomor Pengirim,Nomor Tujuan,Waktu Request
62812345678,62898765432,2024-01-15 10:30:00
62812345679,62898765433,2024-01-15 10:35:00
```

---

## Error Handling

### Possible Error Messages

| Error | Deskripsi |
|-------|-----------|
| File tidak ditemukan | File upload tidak ada |
| Format file tidak didukung | File bukan .xls, .xlsx, atau .csv |
| Ukuran file terlalu besar | File lebih dari 5MB |
| Gagal upload file | Error saat menyimpan file |
| File Excel/CSV kosong | File tidak memiliki data |
| ID tidak ditemukan | ID parameter kosong |
| Gagal menghapus data | Error database saat delete |
| Gagal memperbarui status | Error database saat update |

---

## Status Values

| Status | Deskripsi |
|--------|-----------|
| pending | Data belum dikirim |
| sent | Data sudah dikirim |
| failed | Data gagal dikirim |

---

## Rate Limiting
Tidak ada rate limiting dalam versi ini. Implementasikan jika diperlukan.

## Authentication
Tidak ada authentication dalam versi ini. Tambahkan middleware jika diperlukan.

## CORS
Tidak ada CORS restriction dalam versi ini.

---

## Testing dengan cURL

### Upload File
```bash
curl -X POST -F "file=@pap.xlsx" -F "action=upload" http://localhost/prabasini/src/api_pap.php
```

### Get List
```bash
curl http://localhost/prabasini/src/api_pap.php?action=get_list
```

### Delete Data
```bash
curl -X POST -d "action=delete&id=1" http://localhost/prabasini/src/api_pap.php
```

### Update Status
```bash
curl -X POST -d "action=update_status&id=1&status=sent" http://localhost/prabasini/src/api_pap.php
```

---

## Response Headers
```
Content-Type: application/json
HTTP/1.1 200 OK (atau 400/500 untuk error)
```

---

## Changelog

### Version 1.0
- Initial release
- Upload file Excel/CSV
- CRUD operations
- Status tracking
- WhatsApp integration
