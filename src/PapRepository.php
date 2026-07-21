<?php

class PapRepository {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->createTableIfNotExists();
    }
    
    /**
     * Create PAP table if it doesn't exist
     */
    private function createTableIfNotExists() {
        $sql = "
        CREATE TABLE IF NOT EXISTS pap (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nomor_pengirim VARCHAR(20) NOT NULL,
            nomor_tujuan VARCHAR(20) NOT NULL,
            waktu_request DATETIME DEFAULT CURRENT_TIMESTAMP,
            waktu_terkirim DATETIME NULL,
            status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            // Table might already exist
        }
    }
    
    /**
     * Insert PAP data
     */
    public function insert($data) {
        $sql = "INSERT INTO pap (nomor_pengirim, nomor_tujuan, waktu_request, status) 
                VALUES (:nomor_pengirim, :nomor_tujuan, :waktu_request, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':nomor_pengirim' => $data['nomor_pengirim'] ?? '',
            ':nomor_tujuan' => $data['nomor_tujuan'] ?? '',
            ':waktu_request' => $data['waktu_request'] ?? date('Y-m-d H:i:s'),
            ':status' => 'pending'
        ]);
    }
    
    /**
     * Get all PAP data
     */
    public function getAll() {
        $sql = "SELECT * FROM pap ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get PAP by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM pap WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Update PAP data
     */
    public function update($id, $data) {
        $sql = "UPDATE pap SET 
                nomor_pengirim = :nomor_pengirim,
                nomor_tujuan = :nomor_tujuan,
                status = :status,
                waktu_terkirim = :waktu_terkirim
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':id' => $id,
            ':nomor_pengirim' => $data['nomor_pengirim'] ?? '',
            ':nomor_tujuan' => $data['nomor_tujuan'] ?? '',
            ':status' => $data['status'] ?? 'pending',
            ':waktu_terkirim' => $data['waktu_terkirim'] ?? null
        ]);
    }
    
    /**
     * Update status PAP
     */
    public function updateStatus($id, $status) {
        $sql = "UPDATE pap SET status = :status, waktu_terkirim = :waktu_terkirim WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':id' => $id,
            ':status' => $status,
            ':waktu_terkirim' => ($status === 'sent') ? date('Y-m-d H:i:s') : null
        ]);
    }
    
    /**
     * Delete PAP data
     */
    public function delete($id) {
        $sql = "DELETE FROM pap WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Import PAP data from array
     */
    public function importFromArray($dataArray) {
        $inserted = 0;
        $errors = [];
        
        // Skip header row if present
        $startIndex = 0;
        if (!empty($dataArray) && is_array($dataArray[0])) {
            $first = $dataArray[0];
            // Check if first row looks like headers
            if (isset($first[0]) && (stripos($first[0], 'nomor') !== false || stripos($first[0], 'no.') !== false)) {
                $startIndex = 1;
            }
        }
        
        for ($i = $startIndex; $i < count($dataArray); $i++) {
            $row = $dataArray[$i];
            
            if (empty($row) || !isset($row[0]) || !isset($row[1])) {
                continue;
            }
            
            try {
                $data = [
                    'nomor_pengirim' => trim($row[0] ?? ''),
                    'nomor_tujuan' => trim($row[1] ?? ''),
                    'waktu_request' => isset($row[2]) ? $this->parseDateTime($row[2]) : date('Y-m-d H:i:s'),
                ];
                
                if (!empty($data['nomor_pengirim']) && !empty($data['nomor_tujuan'])) {
                    if ($this->insert($data)) {
                        $inserted++;
                    }
                }
            } catch (Exception $e) {
                $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
            }
        }
        
        return [
            'inserted' => $inserted,
            'errors' => $errors
        ];
    }
    
    /**
     * Parse datetime string in multiple formats
     */
    private function parseDateTime($dateString) {
        $formats = [
            'Y-m-d H:i:s',
            'd/m/Y H:i',
            'd-m-Y H:i',
            'Y-m-d',
            'd/m/Y'
        ];
        
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return $date->format('Y-m-d H:i:s');
            }
        }
        
        return date('Y-m-d H:i:s');
    }
}
?>
