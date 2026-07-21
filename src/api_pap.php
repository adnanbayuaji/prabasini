<?php
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../src/PapRepository.php';
require_once '../src/ExcelImporter.php';

$papRepo = new PapRepository($pdo);
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'upload':
            handleUpload($papRepo);
            break;
            
        case 'delete':
            handleDelete($papRepo);
            break;
            
        case 'update_status':
            handleUpdateStatus($papRepo);
            break;
            
        case 'get_list':
            handleGetList($papRepo);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action tidak ditemukan']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function handleUpload($papRepo) {
    if (!isset($_FILES['file'])) {
        throw new Exception('File tidak ditemukan');
    }
    
    $file = $_FILES['file'];
    $allowedTypes = ['xls', 'xlsx', 'csv'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowedTypes)) {
        throw new Exception('Format file tidak didukung. Gunakan .xls, .xlsx, atau .csv');
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        throw new Exception('Ukuran file terlalu besar (maksimal 5MB)');
    }
    
    $uploadDir = '../public/uploads/';
    $fileName = 'pap_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $filePath = $uploadDir . $fileName;
    
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
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
        // If parsing fails, try CSV parser as fallback but catch its errors as well
        try {
            $data = ExcelImporter::parseCsv($filePath);
        } catch (Exception $e2) {
            // Preserve original exception message if CSV fallback also fails
            throw new Exception($e->getMessage() . ' | Fallback CSV parse error: ' . $e2->getMessage());
        }
    }
    
    if (empty($data)) {
        throw new Exception('File Excel/CSV kosong atau format tidak didukung');
    }
    
    // Import data
    $result = $papRepo->importFromArray($data);
    
    // Clean up temp file
    unlink($filePath);
    
    echo json_encode([
        'success' => true,
        'message' => 'Import berhasil',
        'inserted' => $result['inserted'],
        'errors' => $result['errors']
    ]);
}

function handleDelete($papRepo) {
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        throw new Exception('ID tidak ditemukan');
    }
    
    if ($papRepo->delete($id)) {
        echo json_encode(['success' => true, 'message' => 'Data berhasil dihapus']);
    } else {
        throw new Exception('Gagal menghapus data');
    }
}

function handleUpdateStatus($papRepo) {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? 'sent';
    
    if (!$id) {
        throw new Exception('ID tidak ditemukan');
    }
    
    if ($papRepo->updateStatus($id, $status)) {
        echo json_encode(['success' => true, 'message' => 'Status berhasil diperbarui']);
    } else {
        throw new Exception('Gagal memperbarui status');
    }
}

function handleGetList($papRepo) {
    $data = $papRepo->getAll();
    echo json_encode(['success' => true, 'data' => $data]);
}
?>
