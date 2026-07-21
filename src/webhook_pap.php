<?php

/**
 * Example Webhook Integration untuk PAP
 * Menggunakan data dari tabel pap_import untuk generate gambar & text
 * 
 * Webhook ini dapat dipanggil dari scheduler atau API untuk:
 * 1. Generate gambar dari data pap_import
 * 2. Generate pesan WhatsApp
 * 3. Log hasil kirim ke pap_kirim_log
 * 
 * Usage:
 *   curl -X POST http://localhost/prabasini/src/webhook_pap.php?action=process
 */

header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../src/PapImportRepository.php';
require_once '../src/PapGeneratorHelper.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'process':
            // Process all pending PAP records
            processPendingPap($pdo);
            break;
            
        case 'process_by_id':
            // Process specific PAP by ID
            $id = $_GET['id'] ?? $_POST['id'] ?? null;
            if (!$id) throw new Exception('ID diperlukan');
            processPapById($pdo, $id);
            break;
            
        case 'generate_image':
            // Just generate image
            $id = $_GET['id'] ?? $_POST['id'] ?? null;
            if (!$id) throw new Exception('ID diperlukan');
            generatePapImage($pdo, $id);
            break;
            
        case 'send_to_wa':
            // Generate pesan dan format untuk WhatsApp
            $id = $_GET['id'] ?? $_POST['id'] ?? null;
            if (!$id) throw new Exception('ID diperlukan');
            sendPapToWhatsapp($pdo, $id);
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

/**
 * Process all pending PAP records
 * Akan generate gambar, pesan, dan log ke pap_kirim_log
 */
function processPendingPap($pdo) {
    $papImportRepo = new PapImportRepository($pdo);
    
    // Get all records with status pending (status_pap = '1')
    $papRecords = $papImportRepo->getAll(1000, 0);
    
    $processed = 0;
    $success = 0;
    $failed = 0;
    $errors = [];
    
    foreach ($papRecords as $pap) {
        try {
            // Generate image
            $uploadDir = '../public/uploads/';
            $nomorPengirim = '6200000000'; // Default sender number
            $namaGambar = PapGeneratorHelper::generateNamaFile(
                $pap['nomor_berkas'], 
                $nomorPengirim
            );
            
            if (PapGeneratorHelper::generateGambarPap($namaGambar, $pap, $uploadDir)) {
                // Generate pesan
                $pesan = PapGeneratorHelper::generatePesanPap($pap);
                
                // Log to pap_kirim_log
                $papImportRepo->logKirim(
                    $pap['id'],
                    $nomorPengirim,
                    PapGeneratorHelper::formatNomorWa($pap['no_hp']),
                    $namaGambar,
                    $pesan
                );
                
                $success++;
            } else {
                $failed++;
                $errors[] = "ID {$pap['id']}: Gagal generate gambar";
            }
            $processed++;
        } catch (Exception $e) {
            $failed++;
            $errors[] = "ID {$pap['id']}: " . $e->getMessage();
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Processing selesai',
        'processed' => $processed,
        'success' => $success,
        'failed' => $failed,
        'errors' => $errors
    ]);
}

/**
 * Process specific PAP by ID
 */
function processPapById($pdo, $id) {
    $papImportRepo = new PapImportRepository($pdo);
    $pap = $papImportRepo->getById($id);
    
    if (!$pap) {
        throw new Exception('PAP dengan ID ' . $id . ' tidak ditemukan');
    }
    
    $uploadDir = '../public/uploads/';
    $nomorPengirim = $_POST['nomor_pengirim'] ?? '6200000000';
    
    // Generate image
    $namaGambar = PapGeneratorHelper::generateNamaFile(
        $pap['nomor_berkas'], 
        $nomorPengirim
    );
    
    if (!PapGeneratorHelper::generateGambarPap($namaGambar, $pap, $uploadDir)) {
        throw new Exception('Gagal generate gambar');
    }
    
    // Generate pesan
    $pesan = PapGeneratorHelper::generatePesanPap($pap);
    
    // Log to pap_kirim_log
    $kirimId = $papImportRepo->logKirim(
        $pap['id'],
        $nomorPengirim,
        PapGeneratorHelper::formatNomorWa($pap['no_hp']),
        $namaGambar,
        $pesan
    );
    
    echo json_encode([
        'success' => true,
        'message' => 'Proses berhasil',
        'data' => [
            'id_pap' => $pap['id'],
            'nama_gambar' => $namaGambar,
            'nomor_berkas' => $pap['nomor_berkas'],
            'nama_wajib_pajak' => $pap['nama_wajib_pajak'],
            'no_hp' => $pap['no_hp'],
            'url_wa' => PapGeneratorHelper::generateUrlWame($pap['no_hp']),
            'panjang_pesan' => strlen($pesan),
            'url_gambar' => '../public/uploads/' . $namaGambar
        ]
    ]);
}

/**
 * Generate image untuk PAP tertentu
 */
function generatePapImage($pdo, $id) {
    $papImportRepo = new PapImportRepository($pdo);
    $pap = $papImportRepo->getById($id);
    
    if (!$pap) {
        throw new Exception('PAP dengan ID ' . $id . ' tidak ditemukan');
    }
    
    $uploadDir = '../public/uploads/';
    $nomorPengirim = $_POST['nomor_pengirim'] ?? '6200000000';
    
    $namaGambar = PapGeneratorHelper::generateNamaFile(
        $pap['nomor_berkas'], 
        $nomorPengirim
    );
    
    if (!PapGeneratorHelper::generateGambarPap($namaGambar, $pap, $uploadDir)) {
        throw new Exception('Gagal generate gambar');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Gambar berhasil dibuat',
        'data' => [
            'nama_gambar' => $namaGambar,
            'path' => '../public/uploads/' . $namaGambar,
            'url' => 'https://your-domain.com/prabasini/public/uploads/' . $namaGambar
        ]
    ]);
}

/**
 * Send PAP data to WhatsApp (generate pesan + link)
 */
function sendPapToWhatsapp($pdo, $id) {
    $papImportRepo = new PapImportRepository($pdo);
    $pap = $papImportRepo->getById($id);
    
    if (!$pap) {
        throw new Exception('PAP dengan ID ' . $id . ' tidak ditemukan');
    }
    
    $pesan = PapGeneratorHelper::generatePesanPap($pap);
    $nomorWa = PapGeneratorHelper::formatNomorWa($pap['no_hp']);
    $urlWa = PapGeneratorHelper::generateUrlWame($pap['no_hp']);
    
    // Encode pesan untuk URL
    $pesanEncoded = urlencode($pesan);
    $urlWaWithMessage = $urlWa . '?text=' . $pesanEncoded;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'id_pap' => $pap['id'],
            'nomor_berkas' => $pap['nomor_berkas'],
            'nama_wajib_pajak' => $pap['nama_wajib_pajak'],
            'no_hp_original' => $pap['no_hp'],
            'no_hp_formatted' => $nomorWa,
            'pesan' => $pesan,
            'panjang_pesan' => strlen($pesan),
            'url_wa' => $urlWa,
            'url_wa_with_message' => $urlWaWithMessage,
            'jumlah_pap' => 'Rp ' . number_format($pap['jumlah_pap'], 0, ',', '.')
        ]
    ]);
}

?>
