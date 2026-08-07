<?php

/**
 * Class untuk manage PAP Import dari Excel
 * Menyimpan data Excel ke database untuk kemudahan generate gambar & text
 */
class PapImportRepository {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->createTableIfNotExists();
    }
    
    /**
     * Create tables if not exists
     */
    private function createTableIfNotExists() {
        // Tabel pap_import
        $sql1 = "
        CREATE TABLE IF NOT EXISTS pap_import (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nomor_virtual_account VARCHAR(50) NOT NULL,
            nomor_berkas VARCHAR(50) NOT NULL,
            nama_wajib_pajak VARCHAR(200) NOT NULL,
            alamat_wajib_pajak VARCHAR(500),
            nama_perusahaan VARCHAR(200),
            alamat_perusahaan VARCHAR(500),
            peruntukan_pap VARCHAR(200),
            no_kohir VARCHAR(50),
            bagian_bulan VARCHAR(50),
            tahun INT,
            ditetapkan_tanggal DATE,
            jatuh_tempo_pembayaran DATE,
            jenis_pungutan VARCHAR(100),
            volume_areal_per_daya DECIMAL(15,2),
            harga_dasar_air DECIMAL(15,2),
            tarif_pajak DECIMAL(10,4),
            pajak_terutang DECIMAL(15,2),
            jumlah_pap DECIMAL(15,2),
            no_hp VARCHAR(20) NOT NULL,
            nama_pap VARCHAR(100) DEFAULT 'PRABASINI',
            keterangan_pap TEXT,
            status_pap ENUM('1', '0') DEFAULT '1',
            custom_field_1 VARCHAR(255),
            custom_field_2 VARCHAR(255),
            custom_field_3 VARCHAR(255),
            custom_field_4 VARCHAR(255),
            custom_field_5 VARCHAR(255),
            user_pap INT DEFAULT 1,
            date_pap TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_nomor_hp (no_hp),
            INDEX idx_nomor_virtual (nomor_virtual_account),
            INDEX idx_nomor_berkas (nomor_berkas),
            INDEX idx_status (status_pap),
            INDEX idx_date (date_pap),
            UNIQUE KEY unique_berkas (nomor_berkas, date_pap)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        try {
            $this->pdo->exec($sql1);
        } catch (PDOException $e) {
            // Table might already exist
        }
        
        // Tabel pap_kirim_log
        $sql2 = "
        CREATE TABLE IF NOT EXISTS pap_kirim_log (
            id INT PRIMARY KEY AUTO_INCREMENT,
            id_pap_import INT,
            nomor_pengirim_kirim VARCHAR(20) NOT NULL,
            nomor_tujuan_kirim VARCHAR(20) NOT NULL,
            nama_gambar VARCHAR(255),
            panjang_pesan INT,
            status_kirim ENUM('1', '0', '2', '3') DEFAULT '1',
            response_kirim TEXT,
            error_message VARCHAR(500),
            waktu_kirim DATETIME,
            waktu_terima DATETIME,
            user_kirim INT DEFAULT 1,
            date_kirim TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_pap_import (id_pap_import),
            INDEX idx_nomor_tujuan (nomor_tujuan_kirim),
            INDEX idx_status (status_kirim),
            INDEX idx_date (date_kirim)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        try {
            $this->pdo->exec($sql2);
        } catch (PDOException $e) {
            // Table might already exist
        }
    }
    
    /**
     * Insert PAP data dari Excel array
     */
    public function insertFromExcelArray($excelData) {
        $inserted = 0;
        $errors = [];
        
        // Skip header row jika ada
        $startIndex = 0;
        if (!empty($excelData) && is_array($excelData[0])) {
            // Check if first row looks like headers
            if (isset($excelData[0][0]) && 
                (stripos($excelData[0][0], 'virtual') !== false || 
                 stripos($excelData[0][0], 'berkas') !== false ||
                 stripos($excelData[0][0], 'nomor') !== false)) {
                $startIndex = 1;
            }
        }
        
        for ($i = $startIndex; $i < count($excelData); $i++) {
            $row = $excelData[$i];
            
            if (empty($row) || !isset($row[0])) {
                continue;
            }
            
            try {
                // Map Excel columns to database fields
                // Kolom urutan dari Excel harus sesuai dengan file yang di-upload
                $rawNoHpPrimary = $row[21] ?? null;   // kolom V / no_wa pada pap.xls
                $rawNoHpFallback = $row[22] ?? null;   // fallback format lama
                $rawNoHpLegacy = $row[18] ?? null;     // fallback format paling lama

                $data = [
                    'nomor_virtual_account' => trim($row[0] ?? ''),      // Col 0
                    'nomor_berkas' => trim($row[1] ?? ''),               // Col 1
                    'nama_wajib_pajak' => trim($row[2] ?? ''),           // Col 2
                    'alamat_wajib_pajak' => trim($row[3] ?? ''),         // Col 3
                    'nama_perusahaan' => trim($row[4] ?? ''),            // Col 4
                    'alamat_perusahaan' => trim($row[5] ?? ''),          // Col 5
                    'peruntukan_pap' => trim($row[6] ?? ''),             // Col 6
                    'no_kohir' => trim($row[7] ?? ''),                   // Col 7
                    'bagian_bulan' => trim($row[8] ?? ''),               // Col 8
                    'tahun' => intval($row[9] ?? 0),                     // Col 9
                    'ditetapkan_tanggal' => $this->parseDate($row[10] ?? ''), // Col 10
                    'jatuh_tempo_pembayaran' => $this->parseDate($row[11] ?? ''), // Col 11
                    'jenis_pungutan' => trim($row[12] ?? ''),            // Col 12
                    'volume_areal_per_daya' => floatval($row[13] ?? 0),  // Col 13
                    'harga_dasar_air' => floatval($row[14] ?? 0),        // Col 14
                    'tarif_pajak' => floatval($row[15] ?? 0),            // Col 15
                    'pajak_terutang' => floatval($row[16] ?? 0),         // Col 16
                    'jumlah_pap' => floatval($row[17] ?? 0),             // Col 17
                    'no_hp' => $this->sanitizePhone($rawNoHpPrimary ?? $rawNoHpFallback ?? $rawNoHpLegacy),
                ];
                
                // Custom fields jika ada
                if (isset($row[19])) $data['custom_field_1'] = trim($row[19]);
                if (isset($row[20])) $data['custom_field_2'] = trim($row[20]);
                if (isset($row[21])) $data['custom_field_3'] = trim($row[21]);
                if (isset($row[22])) $data['custom_field_4'] = trim($row[22]);
                if (isset($row[23])) $data['custom_field_5'] = trim($row[23]);
                
                // Validate required fields
                if (empty($data['nomor_virtual_account']) || empty($data['no_hp'])) {
                    throw new Exception("Nomor Virtual Account dan No HP harus diisi");
                }
                
                if ($this->insert($data)) {
                    $inserted++;
                }
            } catch (Throwable $e) {
                $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
            }
        }
        
        return [
            'inserted' => $inserted,
            'errors' => $errors
        ];
    }
    
    /**
     * Insert satu record PAP
     */
    public function insert($data) {
        $sql = "INSERT INTO pap_import (
            nomor_virtual_account, nomor_berkas, nama_wajib_pajak, alamat_wajib_pajak,
            nama_perusahaan, alamat_perusahaan, peruntukan_pap, no_kohir, bagian_bulan,
            tahun, ditetapkan_tanggal, jatuh_tempo_pembayaran, jenis_pungutan,
            volume_areal_per_daya, harga_dasar_air, tarif_pajak, pajak_terutang,
            jumlah_pap, no_hp, custom_field_1, custom_field_2, custom_field_3,
            custom_field_4, custom_field_5, nama_pap
        ) VALUES (
            :nomor_virtual_account, :nomor_berkas, :nama_wajib_pajak, :alamat_wajib_pajak,
            :nama_perusahaan, :alamat_perusahaan, :peruntukan_pap, :no_kohir, :bagian_bulan,
            :tahun, :ditetapkan_tanggal, :jatuh_tempo_pembayaran, :jenis_pungutan,
            :volume_areal_per_daya, :harga_dasar_air, :tarif_pajak, :pajak_terutang,
            :jumlah_pap, :no_hp, :custom_field_1, :custom_field_2, :custom_field_3,
            :custom_field_4, :custom_field_5, 'PRABASINI'
        )";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':nomor_virtual_account' => $data['nomor_virtual_account'] ?? '',
            ':nomor_berkas' => $data['nomor_berkas'] ?? '',
            ':nama_wajib_pajak' => $data['nama_wajib_pajak'] ?? '',
            ':alamat_wajib_pajak' => $data['alamat_wajib_pajak'] ?? '',
            ':nama_perusahaan' => $data['nama_perusahaan'] ?? '',
            ':alamat_perusahaan' => $data['alamat_perusahaan'] ?? '',
            ':peruntukan_pap' => $data['peruntukan_pap'] ?? '',
            ':no_kohir' => $data['no_kohir'] ?? '',
            ':bagian_bulan' => $data['bagian_bulan'] ?? '',
            ':tahun' => $data['tahun'] ?? 0,
            ':ditetapkan_tanggal' => $data['ditetapkan_tanggal'] ?? null,
            ':jatuh_tempo_pembayaran' => $data['jatuh_tempo_pembayaran'] ?? null,
            ':jenis_pungutan' => $data['jenis_pungutan'] ?? '',
            ':volume_areal_per_daya' => $data['volume_areal_per_daya'] ?? 0,
            ':harga_dasar_air' => $data['harga_dasar_air'] ?? 0,
            ':tarif_pajak' => $data['tarif_pajak'] ?? 0,
            ':pajak_terutang' => $data['pajak_terutang'] ?? 0,
            ':jumlah_pap' => $data['jumlah_pap'] ?? 0,
            ':no_hp' => $data['no_hp'] ?? '',
            ':custom_field_1' => $data['custom_field_1'] ?? '',
            ':custom_field_2' => $data['custom_field_2'] ?? '',
            ':custom_field_3' => $data['custom_field_3'] ?? '',
            ':custom_field_4' => $data['custom_field_4'] ?? '',
            ':custom_field_5' => $data['custom_field_5'] ?? '',
        ]);
    }
    
    /**
     * Get all PAP data
     */
    public function getAll($limit = 100, $offset = 0) {
        $sql = "SELECT * FROM pap_import WHERE status_pap='1' ORDER BY date_pap DESC LIMIT :offset, :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get PAP by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM pap_import WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Get PAP by nomor HP
     */
    public function getByNoHp($no_hp) {
        $sql = "SELECT * FROM pap_import WHERE no_hp = :no_hp ORDER BY date_pap DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':no_hp' => $no_hp]);
        return $stmt->fetch();
    }
    
    /**
     * Update PAP
     */
    public function update($id, $data) {
        $allowedFields = [
            'nomor_virtual_account', 'nomor_berkas', 'nama_wajib_pajak', 'alamat_wajib_pajak',
            'nama_perusahaan', 'alamat_perusahaan', 'peruntukan_pap', 'no_kohir', 'bagian_bulan',
            'tahun', 'ditetapkan_tanggal', 'jatuh_tempo_pembayaran', 'jenis_pungutan',
            'volume_areal_per_daya', 'harga_dasar_air', 'tarif_pajak', 'pajak_terutang',
            'jumlah_pap', 'no_hp', 'keterangan_pap', 'status_pap'
        ];
        
        $updates = [];
        $values = [':id' => $id];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updates[] = "$key = :$key";
                $values[":$key"] = $value;
            }
        }
        
        if (empty($updates)) return false;
        
        $sql = "UPDATE pap_import SET " . implode(', ', $updates) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Delete PAP
     */
    public function delete($id) {
        $sql = "DELETE FROM pap_import WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Delete all PAP data
     */
    public function deleteAll() {
        $sql = "DELETE FROM pap_import";
        return $this->pdo->exec($sql);
    }

    /**
     * Update status PAP
     */
    public function updateStatus($id, $status) {
        $sql = "UPDATE pap_import SET status_pap = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':status' => $status,
        ]);
    }
    
    /**
     * Log kirim gambar & pesan
     */
    public function logKirim($papId, $nomorPengirim, $nomorTujuan, $namaGambar, $pesanKirim) {
        $sql = "INSERT INTO pap_kirim_log 
                (id_pap_import, nomor_pengirim_kirim, nomor_tujuan_kirim, nama_gambar, panjang_pesan, waktu_kirim)
                VALUES (:id_pap_import, :nomor_pengirim, :nomor_tujuan, :nama_gambar, :panjang_pesan, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_pap_import' => $papId,
            ':nomor_pengirim' => $nomorPengirim,
            ':nomor_tujuan' => $nomorTujuan,
            ':nama_gambar' => $namaGambar,
            ':panjang_pesan' => strlen($pesanKirim)
        ]);
    }
    
    /**
     * Update kirim status
     */
    public function updateKirimStatus($kirimId, $status, $response = '') {
        $sql = "UPDATE pap_kirim_log 
                SET status_kirim = :status, response_kirim = :response, waktu_terima = NOW()
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $kirimId,
            ':status' => $status,
            ':response' => $response
        ]);
    }
    
    /**
     * Parse date string dalam multiple formats
     */
    private function parseDate($dateString) {
        if ($dateString === null || $dateString === '') {
            return null;
        }

        if (!is_scalar($dateString)) {
            return null;
        }

        $dateString = trim(str_replace("\0", '', (string)$dateString));
        if ($dateString === '') {
            return null;
        }
        
        // Check if it's Excel serial number
        if (is_numeric($dateString) && $dateString > 0) {
            $excelDate = intval($dateString);
            if ($excelDate > 21000) { // Excel serial dates start around 1900
                $unixDate = ($excelDate - 25569) * 86400;
                return gmdate("Y-m-d", $unixDate);
            }
        }
        
        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d',
            'd/m/Y H:i',
            'd/m/Y',
            'd-m-Y',
            'm/d/Y'
        ];
        
        foreach ($formats as $format) {
            try {
                $date = DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            } catch (Throwable $e) {
                // Ignore invalid date payload and continue to next format.
            }
        }
        
        return null;
    }

    private function sanitizePhone($value) {
        if ($value === null) {
            return '';
        }

        $value = trim(str_replace("\0", '', (string)$value));
        if ($value === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $value);
        return $digits ?? '';
    }
    
    /**
     * Get total records
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM pap_import WHERE status_pap='1'";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}

?>
