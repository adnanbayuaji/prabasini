<?php

/**
 * Helper class untuk generate gambar dan text PAP
 * Menggunakan data dari tabel pap_import
 */
class PapGeneratorHelper {
    
    /**
     * Generate pesan WhatsApp untuk PAP
     * 
     * @param array $papData Data dari tabel pap_import
     * @return string Pesan yang sudah di-format
     */
    public static function generatePesanPap($papData) {
        $nama_wajib_pajak = $papData['nama_wajib_pajak'] ?? '';
        $nomor_virtual_account = $papData['nomor_virtual_account'] ?? '';
        
        $pesan = "Selamat Siang, Bpk/Ibu *" . $nama_wajib_pajak . "*
Kami dari BADAN PENDAPATAN DAERAH PROVINSI JAWA TIMUR 
UPT PPD MALANG UTARA & BATU KOTA
Jl. Terusan Borobudur no. 28, Malang
Telepon : (0341) 491654

Bapak/Ibu yang terhormat, 
Melalui Surat Digital Elektronik ini kami menyampaikan informasi, bahwa Berikut adalah Surat ketetapan Pajak Daerah ( SKPD ) Pajak Air permukaan ( PAP ) berdasarkan Peraturan Daerah Provinsi Jawa Timur Nomor 9 Tahun 2010 sebagaimana surat terlampir. 

Untuk melakukan Pembayaran Pajak Air permukaan ( PAP ) dapat dilakukan melalui fasilitas yang kami sediakan antara lain :

1. Pembayaran Melalui Virtual Account:
   ```" . $nomor_virtual_account . "```
   (Tekan dan tahan untuk copy nomor)

2. Pembayaran Dapat dilakukan di :
   Kantor UPT PPD MALANG UTARA & BATU KOTA
   Jl. Terusan Borobudur no. 28, Malang 
   Tlpn : (0341) 491654

3. Jika sudah Melakukan Pembayaran, Mohon Bukti Pembayaran Dapat Dikirimkan Melalui Kami pada Nomor Ini.

Surat ini merupakan Pemberitahuan kepada Bapak/Ibu, dan mohon diabaikan jika pembayaran telah dilakukan. 

Demikian kami sampaikan dan terima kasih.

Salam Hangat,
Petugas Pendataan dan penetapan 
BADAN PENDAPATAN DAERAH PROVINSI JAWA TIMUR 
UPT PPD MALANG UTARA & BATU KOTA";
        
        return $pesan;
    }
    
    /**
     * Generate gambar PAP (menggunakan GD Library)
     * Wrapper untuk cetak_gambar2 dari aaa_webhook.php
     * 
     * @param string $namaFile Nama file output
     * @param array $papData Data dari tabel pap_import
     * @param string $uploadDir Path folder uploads
     * @return bool Success/fail
     */
    public static function generateGambarPap($namaFile, $papData, $uploadDir) {
        // Check if GD/PNG support tersedia
        $img = null;
        if(function_exists('imagecreatefrompng')) {
            $img = @imagecreatefrompng("../images/kosongan4.png");
        }
        if(!$img && function_exists('imagecreatetruecolor')) {
            $img = imagecreatetruecolor(1200, 850);
            if(function_exists('imagecolorallocate') && function_exists('imagefill')) {
                $bg = imagecolorallocate($img, 255, 255, 255);
                imagefill($img, 0, 0, $bg);
            }
        }
        if(!$img) {
            error_log('PAP ERROR - GD extension tidak tersedia, generate gambar dilewati: '.$namaFile);
            return false;
        }
        
        // Mapping data agar kompatibel dengan cetak_gambar2 di webhook referensi.
        $a = (string)($papData['nomor_virtual_account'] ?? '');
        $b = (string)($papData['nomor_berkas'] ?? '');
        $c = (string)($papData['nama_wajib_pajak'] ?? '');
        $d = (string)($papData['alamat_wajib_pajak'] ?? '');
        $e = (string)($papData['nama_perusahaan'] ?? '');
        $f = (string)($papData['alamat_perusahaan'] ?? '');
        $g = (string)($papData['peruntukan_pap'] ?? '');
        $h = (string)($papData['no_kohir'] ?? '');
        $i = (string)($papData['bagian_bulan'] ?? '');
        $j = (string)($papData['tahun'] ?? '');
        $k = self::dateToExcelSerial($papData['ditetapkan_tanggal'] ?? '');
        $l = self::dateToExcelSerial($papData['jatuh_tempo_pembayaran'] ?? '');
        $m = (string)($papData['jenis_pungutan'] ?? '');
        $n = (float)($papData['volume_areal_per_daya'] ?? 0);
        $o = (float)($papData['harga_dasar_air'] ?? 0);
        $p = (string)($papData['tarif_pajak'] ?? '');
        $q = (float)($papData['pajak_terutang'] ?? 0);
        $r = (float)($papData['jumlah_pap'] ?? 0);
        $s = $k;
        $t = (string)($papData['custom_field_2'] ?? $papData['nomor_berkas'] ?? '');
        $u = (string)($papData['custom_field_3'] ?? $papData['alamat_perusahaan'] ?? '');
        
        // Set text color dan font
        $text_color = imagecolorallocate($img, 0x00, 0x00, 0x00);
        $font = function_exists('imageloadfont') ? @imageloadfont('../css/arial.gdf') : false;
        if (!$font) {
            $font = 5;
        }
        $fontTTF = '../css/arial.ttf';
        $canUseTtf = function_exists('imagettftext') && file_exists($fontTTF);
        $fontSize = 22;
        
        // Virtual Account (big)
        imagestring($img, $font, 480, 227, $a, $text_color);
        
        // Data fields. Prioritaskan TTF dengan size lebih besar agar terbaca jelas.
        if ($canUseTtf) {
            imagettftext($img, $fontSize, 0, 248, 250, $text_color, $fontTTF, $b);
            imagettftext($img, $fontSize, 0, 248, 278, $text_color, $fontTTF, $c);
            imagettftext($img, $fontSize, 0, 248, 306, $text_color, $fontTTF, $d);
            imagettftext($img, $fontSize, 0, 248, 334, $text_color, $fontTTF, $e);
            imagettftext($img, $fontSize, 0, 248, 362, $text_color, $fontTTF, $f);

            imagettftext($img, $fontSize, 0, 745, 250, $text_color, $fontTTF, $g);
            imagettftext($img, $fontSize, 0, 745, 278, $text_color, $fontTTF, $h);
            imagettftext($img, $fontSize, 0, 745, 306, $text_color, $fontTTF, $i);
            imagettftext($img, $fontSize, 0, 745, 334, $text_color, $fontTTF, $j);
        } else {
            // Fallback gaya cetak_gambar: seluruh teks pakai imagestring + font gdf.
            imagestring($img, $font, 248, 250, $b, $text_color);
            imagestring($img, $font, 248, 278, $c, $text_color);
            imagestring($img, $font, 248, 306, $d, $text_color);
            imagestring($img, $font, 248, 334, $e, $text_color);
            imagestring($img, $font, 248, 362, $f, $text_color);

            imagestring($img, $font, 745, 250, $g, $text_color);
            imagestring($img, $font, 745, 278, $h, $text_color);
            imagestring($img, $font, 745, 306, $i, $text_color);
            imagestring($img, $font, 745, 334, $j, $text_color);
        }
        
        // Tanggal ditetapkan
        $hasil_date = $k > 0 ? gmdate("d/m/Y", ($k - 25569) * 86400) : '';
        imagestring($img, $font, 745, 359, $hasil_date, $text_color);
        
        // Jatuh tempo
        $hasil_date2 = $l > 0 ? gmdate("d/m/Y", ($l - 25569) * 86400) : '';
        imagestring($img, $font, 308, 578, $hasil_date2, $text_color);
        
        // Nominal
        imagestring($img, $font, 235, 522, number_format($n, 2, ',', '.'), $text_color);
        imagestring($img, $font, 472, 506, $m, $text_color);
        imagestring($img, $font, 650, 522, $p, $text_color);
        imagestring($img, $font, 820, 522, number_format($q, 0, ',', '.'), $text_color);
        imagestring($img, $font, 820, 578, number_format($r, 0, ',', '.'), $text_color);
        
        // Tanggal jatuh tempo (lagi)
        $hasil_date3 = $s > 0 ? gmdate("d/m/Y", ($s - 25569) * 86400) : '';
        imagestring($img, $font, 745, 618, $hasil_date3, $text_color);
        
        // Nomor serial & header
        imagestring($img, $font, 817, 15, $t, $text_color);
        imagestring($img, $font, 745, 818, $u, $text_color);
        
        // Save image
        if(function_exists('imagepng')) {
            imagepng($img, $uploadDir . $namaFile);
            error_log('PAP SUCCESS - Gambar berhasil dibuat: ' . $namaFile);
        } else {
            error_log('PAP ERROR - imagepng() tidak tersedia');
            imagedestroy($img);
            return false;
        }
        
        imagedestroy($img);
        return true;
    }
    
    /**
     * Format tanggal untuk display
     */
    public static function formatTanggal($tanggal) {
        if (empty($tanggal)) return '-';
        
        try {
            $date = new DateTime($tanggal);
            return $date->format('d/m/Y');
        } catch (Exception $e) {
            return '-';
        }
    }
    
    /**
     * Convert date string ke Excel serial number
     */
    public static function dateToExcelSerial($dateString) {
        if (empty($dateString)) return 0;
        
        try {
            $date = new DateTime($dateString);
            $timestamp = $date->getTimestamp();
            $excelDate = intval((($timestamp / 86400) + 25569));
            return $excelDate;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Format nomor untuk WhatsApp (add country code)
     */
    public static function formatNomorWa($nomor) {
        $nomor = preg_replace('/[^0-9]/', '', $nomor);

        if ($nomor === '') {
            return '';
        }
        
        if (substr($nomor, 0, 2) === '62') {
            return $nomor;
        }
        
        if ($nomor[0] === '0') {
            $nomor = '62' . substr($nomor, 1);
        }
        
        return $nomor;
    }
    
    /**
     * Generate URL wa.me
     */
    public static function generateUrlWame($nomor) {
        $nomor = self::formatNomorWa($nomor);
        return 'https://wa.me/' . $nomor;
    }
    
    /**
     * Generate nama file untuk gambar
     */
    public static function generateNamaFile($nomorBerkas, $nomorPengirim) {
        return $nomorPengirim . "-" . $nomorBerkas . "-" . microtime(true) . ".png";
    }
}

?>
