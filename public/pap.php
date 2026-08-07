<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar PAP - Prabasini</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .upload-toolbar {
            display: flex;
            gap: 12px;
            align-items: stretch;
            flex-wrap: wrap;
        }

        .upload-form-wrap {
            flex: 1 1 560px;
            min-width: 280px;
        }

        .upload-side {
            flex: 0 0 220px;
            min-width: 220px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            justify-content: center;
        }

        .btn-bulk-delete {
            width: 100%;
            border: none;
            border-radius: 8px;
            padding: 14px 16px;
            background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
            color: #fff;
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(220, 53, 69, 0.22);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-bulk-delete:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(220, 53, 69, 0.28);
        }

        .btn-bulk-delete:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .container-main {
            max-width: 1300px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
        }

        .header-section h1 {
            margin: 0;
            font-weight: 700;
            font-size: 28px;
        }

        .upload-section {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .table-section {
            padding: 20px;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
            font-size: 14px;
        }

        .table thead {
            background: #f8f9fa;
        }

        .table thead th {
            border: none;
            padding: 15px;
            font-weight: 600;
            color: #333;
            white-space: nowrap;
        }

        .table tbody tr {
            border-bottom: 1px solid #e9ecef;
        }

        .table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-wa {
            background: #25d366;
            color: white;
        }

        .btn-wa:hover {
            background: #1fbb60;
            transform: translateY(-2px);
        }

        .btn-copy {
            background: #0d6efd;
            color: white;
        }

        .btn-copy:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
        }

        .btn-image {
            background: #6f42c1;
            color: white;
        }

        .btn-image:hover {
            background: #5d35a3;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .upload-area {
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f0f4ff;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: #764ba2;
            background: #e8ecff;
        }

        .upload-area.dragover {
            border-color: #764ba2;
            background: #e8ecff;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }

        .loader {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            width: 30px;
            height: 30px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert-custom {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .debug-panel {
            background: #0f172a;
            color: #d1e3ff;
            border-radius: 8px;
            margin: 0 20px 20px 20px;
            padding: 10px 12px;
            font-size: 12px;
        }

        .debug-panel summary {
            cursor: pointer;
            color: #93c5fd;
            font-weight: 600;
        }

        .debug-log {
            margin-top: 8px;
            max-height: 220px;
            overflow: auto;
            white-space: pre-wrap;
            line-height: 1.4;
            font-family: Consolas, monospace;
        }
    </style>
</head>
<body>
    <div class="container-lg py-5">
        <div class="container-main">
            <div class="header-section">
                <h1><i class="fas fa-file-alt"></i> Daftar PAP</h1>
                <p class="mt-2 mb-0">Kelola data Pajak Air Permukaan dengan mudah</p>
            </div>

            <div id="alertContainer"></div>
            <details class="debug-panel">
                <summary>Debug Log Upload PAP</summary>
                <div id="debugLog" class="debug-log">Menunggu aktivitas...</div>
            </details>

            <div class="upload-section">
                <div class="upload-toolbar">
                    <div class="upload-form-wrap">
                        <form id="uploadForm" method="post" action="javascript:void(0)" onsubmit="return false;" enctype="multipart/form-data" novalidate>
                            <div class="upload-area" id="uploadArea">
                                <div>
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 24px; color: #667eea; margin-bottom: 10px; display: block;"></i>
                                    <p class="mb-2"><strong>Klik atau drag file Excel ke sini</strong></p>
                                    <small class="text-muted">Format: .xls, .xlsx, atau .csv (Maksimal 5MB)</small>
                                </div>
                                <input type="file" id="fileInput" name="file" accept=".xls,.xlsx,.csv" style="display: none;">
                            </div>
                        </form>
                    </div>
                    <div class="upload-side">
                        <button type="button" class="btn-bulk-delete" id="deleteAllButton">
                            <i class="fas fa-trash-alt me-2"></i> Hapus Semua Data
                        </button>
                        <div class="loader" id="loader">
                            <div class="spinner"></div>
                            <p class="mt-2 mb-0">Sedang mengupload...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-section">
                <h5 class="mb-4">
                    <i class="fas fa-list"></i> Daftar Data PAP
                    <span class="badge bg-primary float-end" id="totalBadge">0</span>
                </h5>

                <div class="table-responsive" id="tableContainer">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada data. Upload file Excel untuk memulai.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const uploadForm = document.getElementById('uploadForm');
        const loader = document.getElementById('loader');
        const alertContainer = document.getElementById('alertContainer');
        const deleteAllButton = document.getElementById('deleteAllButton');
        const debugLogEl = document.getElementById('debugLog');
        const debugEvents = [];
        const runId = `pap-${Date.now()}`;
        let uploadInProgress = false;

        function pushDebug(message, payload = null) {
            const now = new Date().toLocaleTimeString('id-ID', { hour12: false });
            let line = `[${now}] ${message}`;
            if (payload !== null) {
                try {
                    line += ` | ${JSON.stringify(payload)}`;
                } catch (e) {
                    line += ' | [payload stringify gagal]';
                }
            }
            debugEvents.push(line);
            if (debugEvents.length > 80) {
                debugEvents.shift();
            }
            debugLogEl.textContent = debugEvents.join('\n');
            console.log('[PAP DEBUG]', line);
        }

        function resolveApiUrl() {
            if (window.location.protocol === 'file:') {
                return 'http://localhost/prabasini/src/api_pap_import.php';
            }

            const path = window.location.pathname || '';
            const publicMarker = '/public/';
            const markerIndex = path.indexOf(publicMarker);
            const basePath = markerIndex >= 0 ? path.slice(0, markerIndex) : '/prabasini';
            return `${window.location.origin}${basePath}/src/api_pap_import.php`;
        }

        const apiUrl = resolveApiUrl();
        pushDebug('Init page', { runId, href: window.location.href, apiUrl });

        window.addEventListener('error', (event) => {
            pushDebug('Window error', {
                message: event.message,
                source: event.filename,
                line: event.lineno,
                column: event.colno
            });
        });

        window.addEventListener('unhandledrejection', (event) => {
            pushDebug('Unhandled promise rejection', {
                reason: String(event.reason || '')
            });
        });

        window.addEventListener('beforeunload', () => {
            pushDebug('beforeunload triggered (page akan navigasi/reload)');
        });

        uploadArea.addEventListener('click', () => fileInput.click());
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            fileInput.files = e.dataTransfer.files;
            startUpload();
        });

        fileInput.addEventListener('change', () => {
            startUpload();
        });

        uploadForm.addEventListener('submit', (e) => {
            e.preventDefault();
            e.stopPropagation();
            pushDebug('Upload submit event tertangkap');
            startUpload();
        });

        function startUpload() {
            pushDebug('Upload submit triggered');
            if (uploadInProgress) {
                pushDebug('Upload diabaikan karena proses sebelumnya belum selesai');
                return;
            }
            if (!fileInput.files.length) return;

            uploadInProgress = true;

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('action', 'upload');
            formData.append('debug_run_id', runId);

            pushDebug('Upload request start', {
                name: fileInput.files[0].name,
                size: fileInput.files[0].size,
                type: fileInput.files[0].type,
                apiUrl
            });

            loader.style.display = 'block';
            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
            .then(async (res) => {
                const raw = await res.text();
                pushDebug('Upload response received', {
                    status: res.status,
                    ok: res.ok,
                    debugId: res.headers.get('X-Debug-Id'),
                    bodyPreview: raw.slice(0, 250)
                });

                let data;
                try {
                    data = JSON.parse(raw);
                } catch (jsonError) {
                    throw new Error(`Response bukan JSON valid: ${jsonError.message}`);
                }

                if (!res.ok) {
                    throw new Error(data.message || `HTTP ${res.status}`);
                }

                return data;
            })
            .then(data => {
                uploadInProgress = false;
                loader.style.display = 'none';
                fileInput.value = '';

                if (data.success) {
                    showAlert('success', `Import berhasil. ${data.inserted} data berhasil diimpor.`);
                    loadData();
                } else {
                    showAlert('danger', `Import gagal: ${data.message}`);
                }
            })
            .catch(err => {
                uploadInProgress = false;
                loader.style.display = 'none';
                pushDebug('Upload request failed', { message: err.message, stack: err.stack || '' });
                showAlert('danger', `Terjadi kesalahan saat upload: ${err.message}`);
            });
        }

        function loadData() {
            pushDebug('Load data start');
            fetch(`${apiUrl}?action=get_list&limit=200`)
                .then(async (res) => {
                    const raw = await res.text();
                    pushDebug('Load data response', {
                        status: res.status,
                        debugId: res.headers.get('X-Debug-Id'),
                        bodyPreview: raw.slice(0, 200)
                    });
                    return JSON.parse(raw);
                })
                .then(data => {
                    if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                        renderTable(data.data, data.total || data.data.length);
                    } else {
                        renderEmptyState();
                    }
                })
                .catch(err => {
                    showAlert('danger', `Gagal load data: ${err.message}`);
                    renderEmptyState();
                });
        }

        function renderTable(data, total) {
            const tableContainer = document.getElementById('tableContainer');
            const totalBadge = document.getElementById('totalBadge');
            totalBadge.textContent = String(total);

            let html = `
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nomor VA</th>
                            <th>Nomor Berkas</th>
                            <th>Nama Wajib Pajak</th>
                            <th>No. WA</th>
                            <th>Tanggal Penetapan</th>
                            <th>Jatuh Tempo</th>
                            <th>Jumlah PAP</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach((item, index) => {
                const noHp = formatWaNumber(item.no_hp || '');
                const tanggalPenetapan = formatDate(item.ditetapkan_tanggal);
                const jatuhTempo = formatDate(item.jatuh_tempo_pembayaran);
                const jumlahPap = formatRupiah(item.jumlah_pap);

                html += `
                    <tr>
                        <td><strong>${index + 1}</strong></td>
                        <td>${escapeHtml(item.nomor_virtual_account || '-')}</td>
                        <td>${escapeHtml(item.nomor_berkas || '-')}</td>
                        <td>${escapeHtml(item.nama_wajib_pajak || '-')}</td>
                        <td>${escapeHtml(noHp || '-')}</td>
                        <td><small>${tanggalPenetapan}</small></td>
                        <td><small>${jatuhTempo}</small></td>
                        <td><strong>${jumlahPap}</strong></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action btn-wa" onclick="sendWA('${escapeForInline(noHp)}')">
                                    <i class="fab fa-whatsapp"></i> WA.me
                                </button>
                                <button class="btn-action btn-copy" onclick="copyPesan(${Number(item.id)})">
                                    <i class="fas fa-copy"></i> Copy Text
                                </button>
                                <button class="btn-action btn-image" onclick="copyImage(${Number(item.id)})">
                                    <i class="fas fa-image"></i> Copy Gambar
                                </button>
                                <button class="btn-action btn-delete" onclick="deleteData(${Number(item.id)})">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
            `;

            tableContainer.innerHTML = html;
        }

        function renderEmptyState() {
            const tableContainer = document.getElementById('tableContainer');
            const totalBadge = document.getElementById('totalBadge');
            totalBadge.textContent = '0';
            tableContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Belum ada data. Upload file Excel untuk memulai.</p>
                </div>
            `;
        }

        function sendWA(nomor) {
            const cleanNomor = formatWaNumber(nomor);
            if (!cleanNomor || cleanNomor.length < 9) {
                showAlert('danger', `Nomor tujuan tidak valid: ${nomor}`);
                return;
            }
            const waUrl = `https://wa.me/${cleanNomor}`;
            const win = window.open(waUrl, '_blank', 'noopener');
            if (!win) {
                window.location.href = waUrl;
            }
        }

        function copyPesan(id) {
            fetch(`${apiUrl}?action=generate_pesan&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.success || !data.data || !data.data.pesan) {
                        throw new Error(data.message || 'Pesan tidak tersedia');
                    }
                    return navigator.clipboard.writeText(data.data.pesan);
                })
                .then(() => {
                    showAlert('success', 'Text pesan berhasil disalin');
                })
                .catch(err => {
                    showAlert('danger', `Gagal menyalin text: ${err.message}`);
                });
        }

        async function copyImage(id) {
            try {
                const res = await fetch(`${apiUrl}?action=generate_image&id=${id}`);
                const data = await res.json();
                if (!data.success || !data.data || !data.data.path) {
                    throw new Error(data.message || 'Gambar tidak tersedia');
                }

                const imageRes = await fetch(data.data.path);
                if (!imageRes.ok) {
                    throw new Error('Gagal mengambil file gambar hasil generate');
                }

                const blob = await imageRes.blob();
                if (!window.ClipboardItem || !navigator.clipboard || !navigator.clipboard.write) {
                    throw new Error('Browser tidak mendukung copy gambar ke clipboard');
                }

                await navigator.clipboard.write([
                    new ClipboardItem({ [blob.type || 'image/png']: blob })
                ]);
                showAlert('success', 'Gambar berhasil disalin ke clipboard');
                markAsSent(id);
            } catch (err) {
                showAlert('danger', `Gagal copy gambar: ${err.message}`);
            }
        }

        function markAsSent(id) {
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('id', id);
            formData.append('status', '0');

            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        loadData();
                    } else {
                        showAlert('danger', `Gagal memperbarui status: ${data.message}`);
                    }
                })
                .catch(err => {
                    showAlert('danger', `Gagal memperbarui status: ${err.message}`);
                });
        }

        function deleteAllData() {
            if (!confirm('Yakin ingin menghapus semua data PAP? Tindakan ini tidak dapat dibatalkan.')) return;

            deleteAllButton.disabled = true;
            const formData = new FormData();
            formData.append('action', 'delete_all');

            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message || 'Semua data berhasil dihapus');
                        loadData();
                    } else {
                        showAlert('danger', `Gagal hapus semua data: ${data.message}`);
                    }
                })
                .catch(err => {
                    showAlert('danger', `Terjadi kesalahan: ${err.message}`);
                })
                .finally(() => {
                    deleteAllButton.disabled = false;
                });
        }

        function deleteData(id) {
            if (!confirm('Yakin ingin menghapus data ini?')) return;

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Data berhasil dihapus');
                        loadData();
                    } else {
                        showAlert('danger', `Gagal menghapus data: ${data.message}`);
                    }
                })
                .catch(err => {
                    showAlert('danger', `Terjadi kesalahan: ${err.message}`);
                });
        }

        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-custom alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            alertContainer.innerHTML = alertHtml;
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) alert.remove();
            }, 5000);
        }

        function formatWaNumber(nomor) {
            const digits = String(nomor || '').replace(/\D/g, '');
            if (!digits) return '';
            if (digits.startsWith('62')) return digits;
            if (digits.startsWith('0')) return `62${digits.slice(1)}`;
            return digits;
        }

        function formatDate(value) {
            if (!value) return '-';
            const dt = new Date(value);
            if (Number.isNaN(dt.getTime())) return '-';
            return dt.toLocaleDateString('id-ID');
        }

        function formatRupiah(value) {
            const amount = Number(value || 0);
            return `Rp ${amount.toLocaleString('id-ID')}`;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text == null ? '' : String(text);
            return div.innerHTML;
        }

        function escapeForInline(text) {
            return String(text || '').replace(/'/g, "\\'");
        }

        loadData();

        deleteAllButton.addEventListener('click', deleteAllData);
    </script>
</body>
</html>
