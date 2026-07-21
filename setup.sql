-- Prabasini PAP Management System
-- Unified Setup Database SQL (base table + PAP import + log)

CREATE DATABASE IF NOT EXISTS prabasini;
USE prabasini;

-- Legacy table (existing workflow compatibility)
CREATE TABLE IF NOT EXISTS pap (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nomor_pengirim VARCHAR(20) NOT NULL,
    nomor_tujuan VARCHAR(20) NOT NULL,
    waktu_request DATETIME DEFAULT CURRENT_TIMESTAMP,
    waktu_terkirim DATETIME NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_nomor_tujuan (nomor_tujuan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Main import table for PAP data from Excel
CREATE TABLE IF NOT EXISTS pap_import (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID unik',
    nomor_virtual_account VARCHAR(50) NOT NULL COMMENT 'VA dari Excel column 1',
    nomor_berkas VARCHAR(50) NOT NULL COMMENT 'Nomor berkas dari Excel column 2',
    nama_wajib_pajak VARCHAR(200) NOT NULL COMMENT 'Nama wajib pajak',
    alamat_wajib_pajak VARCHAR(500) COMMENT 'Alamat wajib pajak',
    nama_perusahaan VARCHAR(200) COMMENT 'Nama perusahaan',
    alamat_perusahaan VARCHAR(500) COMMENT 'Alamat perusahaan',
    peruntukan_pap VARCHAR(200) COMMENT 'Peruntukan PAP (jenis pungutan)',
    no_kohir VARCHAR(50) COMMENT 'No kohir/areal',
    bagian_bulan VARCHAR(50) COMMENT 'Bagian/bulan',
    tahun INT COMMENT 'Tahun',
    ditetapkan_tanggal DATE COMMENT 'Tanggal ditetapkan',
    jatuh_tempo_pembayaran DATE COMMENT 'Jatuh tempo pembayaran',
    jenis_pungutan VARCHAR(100) COMMENT 'Jenis pungutan',
    volume_areal_per_daya DECIMAL(15,2) COMMENT 'Volume/areal per daya',
    harga_dasar_air DECIMAL(15,2) COMMENT 'Harga dasar air',
    tarif_pajak DECIMAL(10,4) COMMENT 'Tarif pajak (%)',
    pajak_terutang DECIMAL(15,2) COMMENT 'Pajak terutang',
    jumlah_pap DECIMAL(15,2) COMMENT 'Jumlah PAP (total)',
    no_hp VARCHAR(20) NOT NULL COMMENT 'Nomor HP (tujuan kirim)',
    nama_pap VARCHAR(100) DEFAULT 'PRABASINI' COMMENT 'Nama organisasi PAP',
    keterangan_pap TEXT COMMENT 'Keterangan tambahan',
    status_pap ENUM('1', '0') DEFAULT '1' COMMENT '1=aktif, 0=non-aktif',
    custom_field_1 VARCHAR(255) COMMENT 'Custom field 1 dari Excel',
    custom_field_2 VARCHAR(255) COMMENT 'Custom field 2 dari Excel',
    custom_field_3 VARCHAR(255) COMMENT 'Custom field 3 dari Excel',
    custom_field_4 VARCHAR(255) COMMENT 'Custom field 4 dari Excel',
    custom_field_5 VARCHAR(255) COMMENT 'Custom field 5 dari Excel',
    user_pap INT DEFAULT 1 COMMENT 'User ID yang import',
    date_pap TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu insert',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Waktu update terakhir',
    INDEX idx_nomor_hp (no_hp),
    INDEX idx_nomor_virtual (nomor_virtual_account),
    INDEX idx_nomor_berkas (nomor_berkas),
    INDEX idx_status (status_pap),
    INDEX idx_date (date_pap),
    UNIQUE KEY unique_berkas (nomor_berkas, date_pap)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabel untuk menyimpan data PAP hasil import Excel';

-- Delivery log table
CREATE TABLE IF NOT EXISTS pap_kirim_log (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID unik',
    id_pap_import INT COMMENT 'Reference ke pap_import',
    nomor_pengirim_kirim VARCHAR(20) NOT NULL COMMENT 'Nomor pengirim (bot)',
    nomor_tujuan_kirim VARCHAR(20) NOT NULL COMMENT 'Nomor tujuan (wajib pajak)',
    nama_gambar VARCHAR(255) COMMENT 'Nama file gambar yang dibuat',
    panjang_pesan INT COMMENT 'Panjang pesan yang dikirim',
    status_kirim ENUM('1', '0', '2', '3') DEFAULT '1' COMMENT '1=pending, 0=failed, 2=sent, 3=retrying',
    response_kirim TEXT COMMENT 'Response dari API kirim',
    error_message VARCHAR(500) COMMENT 'Error message jika ada',
    waktu_kirim DATETIME COMMENT 'Waktu kirim request',
    waktu_terima DATETIME COMMENT 'Waktu response diterima',
    user_kirim INT DEFAULT 1,
    date_kirim TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pap_import (id_pap_import),
    INDEX idx_nomor_tujuan (nomor_tujuan_kirim),
    INDEX idx_status (status_kirim),
    INDEX idx_date (date_kirim),
    FOREIGN KEY (id_pap_import) REFERENCES pap_import(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log tracking kirim gambar dan pesan PAP';
