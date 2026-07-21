<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Content-Type: application/json');

$PAP_DEBUG_ID = uniqid('papdbg_', true);
header('X-Debug-Id: ' . $PAP_DEBUG_ID);

function papDebugLog($message, $context = []) {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0775, true);
    }

    $line = [
        'time' => date('Y-m-d H:i:s'),
        'debug_id' => $GLOBALS['PAP_DEBUG_ID'] ?? '-',
        'message' => $message,
        'context' => $context
    ];

    file_put_contents(
        $logDir . '/pap_import_debug.log',
        json_encode($line, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
        FILE_APPEND
    );
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    papDebugLog('OPTIONS preflight', [
        'origin' => $_SERVER['HTTP_ORIGIN'] ?? '',
        'uri' => $_SERVER['REQUEST_URI'] ?? ''
    ]);
    http_response_code(200);
    exit;
}

require_once '../config/database.php';
if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
}
require_once '../src/PapImportRepository.php';
require_once '../src/PapGeneratorHelper.php';
require_once '../src/ExcelImporter.php';

$papImportRepo = new PapImportRepository($pdo);
$action = $_GET['action'] ?? $_POST['action'] ?? '';

papDebugLog('Request start', [
    'method' => $_SERVER['REQUEST_METHOD'] ?? '',
    'action' => $action,
    'origin' => $_SERVER['HTTP_ORIGIN'] ?? '',
    'referer' => $_SERVER['HTTP_REFERER'] ?? '',
    'run_id' => $_POST['debug_run_id'] ?? ($_GET['debug_run_id'] ?? '')
]);

try {
    switch ($action) {
        case 'upload':
            handleUploadPap($papImportRepo);
            break;
            
        case 'get_list':
            handleGetListPap($papImportRepo);
            break;
            
        case 'get_by_id':
            handleGetByIdPap($papImportRepo);
            break;
            
        case 'delete':
            handleDeletePap($papImportRepo);
            break;
            
        case 'generate_pesan':
            handleGeneratePesanPap();
            break;

        case 'generate_image':
            handleGenerateImagePap();
            break;
            
        default:
            papDebugLog('Invalid action', ['action' => $action]);
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action tidak ditemukan']);
            break;
    }
} catch (Throwable $e) {
    papDebugLog('Unhandled exception', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function handleUploadPap($papImportRepo) {
    papDebugLog('handleUploadPap start', [
        'has_file' => isset($_FILES['file']),
        'post_keys' => array_keys($_POST)
    ]);

    if (!isset($_FILES['file'])) {
        throw new Exception('File tidak ditemukan');
    }
    
    $file = $_FILES['file'];
    papDebugLog('Uploaded file meta', [
        'name' => $file['name'] ?? '',
        'size' => $file['size'] ?? 0,
        'tmp_name' => $file['tmp_name'] ?? '',
        'error' => $file['error'] ?? ''
    ]);

    $allowedTypes = ['xls', 'xlsx', 'csv'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowedTypes)) {
        throw new Exception('Format file tidak didukung. Gunakan .xls, .xlsx, atau .csv');
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        throw new Exception('Ukuran file terlalu besar (maksimal 5MB)');
    }
    
    $uploadDir = '../public/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }
    $fileName = 'pap_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $filePath = $uploadDir . $fileName;
    
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        papDebugLog('move_uploaded_file failed', [
            'tmp' => $file['tmp_name'],
            'target' => $filePath
        ]);
        throw new Exception('Gagal upload file');
    }
    
    // Parse file
    $data = [];
    try {
        if ($ext === 'csv') {
            $data = ExcelImporter::parseCsv($filePath);
        } else {
            $data = ExcelImporter::parseExcel($filePath);
        }
    } catch (Exception $e) {
        papDebugLog('Primary parser failed', [
            'error' => $e->getMessage(),
            'ext' => $ext,
            'file' => $filePath
        ]);

        if ($ext === 'csv') {
            throw new Exception('Gagal membaca file CSV: ' . $e->getMessage());
        }

        throw new Exception('Gagal membaca file ' . strtoupper($ext) . ': ' . $e->getMessage());
    }
    
    if (empty($data)) {
        throw new Exception('File Excel/CSV kosong atau format tidak didukung');
    }
    
    // Import data
    $result = $papImportRepo->insertFromExcelArray($data);
    papDebugLog('Import result', [
        'inserted' => $result['inserted'] ?? 0,
        'errors_count' => count($result['errors'] ?? [])
    ]);
    
    // Clean up temp file
    unlink($filePath);
    
    echo json_encode([
        'success' => true,
        'message' => 'Import berhasil',
        'inserted' => $result['inserted'],
        'errors' => $result['errors'],
        'total_records' => $papImportRepo->count()
    ]);
}

function handleGetListPap($papImportRepo) {
    $limit = intval($_GET['limit'] ?? 50);
    $offset = intval($_GET['offset'] ?? 0);
    
    $data = $papImportRepo->getAll($limit, $offset);
    $total = $papImportRepo->count();
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);
    papDebugLog('handleGetListPap done', ['total' => $total, 'limit' => $limit, 'offset' => $offset]);
}

function handleGetByIdPap($papImportRepo) {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        throw new Exception('ID tidak ditemukan');
    }
    
    $data = $papImportRepo->getById($id);
    
    if (!$data) {
        throw new Exception('Data tidak ditemukan');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
}

function handleDeletePap($papImportRepo) {
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        throw new Exception('ID tidak ditemukan');
    }
    
    if ($papImportRepo->delete($id)) {
        papDebugLog('Delete success', ['id' => $id]);
        echo json_encode(['success' => true, 'message' => 'Data berhasil dihapus']);
    } else {
        papDebugLog('Delete failed', ['id' => $id]);
        throw new Exception('Gagal menghapus data');
    }
}

function handleGeneratePesanPap() {
    $id = $_GET['id'] ?? $_POST['id'] ?? null;
    
    if (!$id) {
        throw new Exception('ID tidak ditemukan');
    }
    
    // Get data from repository
    $papImportRepo = new PapImportRepository($GLOBALS['pdo']);
    $papData = $papImportRepo->getById($id);
    
    if (!$papData) {
        throw new Exception('Data PAP tidak ditemukan');
    }
    
    // Generate pesan
    $pesan = PapGeneratorHelper::generatePesanPap($papData);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $id,
            'nomor_berkas' => $papData['nomor_berkas'],
            'nama_wajib_pajak' => $papData['nama_wajib_pajak'],
            'no_hp' => $papData['no_hp'],
            'pesan' => $pesan,
            'url_wa' => PapGeneratorHelper::generateUrlWame($papData['no_hp']),
            'jumlah' => $papData['jumlah_pap']
        ]
    ]);
}

function handleGenerateImagePap() {
    $id = $_GET['id'] ?? $_POST['id'] ?? null;

    if (!$id) {
        throw new Exception('ID tidak ditemukan');
    }

    $papImportRepo = new PapImportRepository($GLOBALS['pdo']);
    $papData = $papImportRepo->getById($id);

    if (!$papData) {
        throw new Exception('Data PAP tidak ditemukan');
    }

    $uploadDir = '../public/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $namaGambar = PapGeneratorHelper::generateNamaFile(
        $papData['nomor_berkas'] ?? ('pap-' . $id),
        'pap'
    );

    if (!PapGeneratorHelper::generateGambarPap($namaGambar, $papData, $uploadDir)) {
        papDebugLog('Generate image failed', ['id' => $id, 'nama_gambar' => $namaGambar]);
        throw new Exception('Gagal generate gambar PAP');
    }

    papDebugLog('Generate image success', ['id' => $id, 'nama_gambar' => $namaGambar]);

    echo json_encode([
        'success' => true,
        'message' => 'Gambar berhasil dibuat',
        'data' => [
            'id' => $id,
            'nama_gambar' => $namaGambar,
            'path' => 'uploads/' . $namaGambar
        ]
    ]);
}

?>
