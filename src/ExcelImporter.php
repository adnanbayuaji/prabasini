<?php

require_once 'ExcelHelpers.php';

class ExcelImporter {
    
    /**
     * Parse Excel file (.xls or .xlsx) and extract data
     */
    public static function parseExcel($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("File tidak ditemukan: $filePath");
        }
        
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'xlsx':
                return self::parseXlsx($filePath);
                
            case 'xls':
                return self::parseXls($filePath);
                
            case 'csv':
                return self::parseCsv($filePath);
                
            default:
                throw new Exception("Format file tidak didukung. Gunakan .xls, .xlsx, atau .csv");
        }
    }
    
    /**
     * Parse .xls file (legacy Excel format)
     * Try CSV fallback if binary format detected
     */
    private static function parseXls($filePath) {
        // First attempt: if PhpSpreadsheet is available, use it to read .xls
        if (class_exists('\\PhpOffice\\PhpSpreadsheet\\IOFactory')) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $sheet = $spreadsheet->getActiveSheet();
                $data = [];

                foreach ($sheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $rowData = [];

                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell ? $cell->getValue() : '';
                    }

                    if (!empty(array_filter(array_map('\strval', $rowData)))) {
                        $data[] = $rowData;
                    }
                }

                if (empty($data)) {
                    throw new Exception('File .xls kosong atau tidak berisi data');
                }

                return $data;
            } catch (Exception $e) {
                // Fall through to legacy handling below
            }
        }

        // Check if it's actually CSV or can be read as text
        $content = file_get_contents($filePath, false, null, 0, 100);

        // If it looks like CSV (no binary markers), try CSV parsing
        if (strpos($content, chr(0)) === false && (strpos($content, ',') !== false || strpos($content, ';') !== false)) {
            return self::parseCsv($filePath);
        }

        // For proper XLS binary format, require PhpSpreadsheet or ask user to convert
        throw new Exception("Format .xls biner memerlukan library tambahan (PhpSpreadsheet). Simpan sebagai .xlsx atau .csv.");
    }
    
    /**
     * Parse .xlsx file (modern Excel format)
     */
    private static function parseXlsx($filePath) {
        try {
            // Try using the new helper class
            if (class_exists('AdvancedExcelReader')) {
                return AdvancedExcelReader::readXlsx($filePath);
            }
            
            // Fallback to basic method
            $data = [];
            $zip = new ZipArchive();
            
            if ($zip->open($filePath) !== true) {
                throw new Exception("Tidak bisa membuka file XLSX");
            }
            
            $xml = $zip->getFromName('xl/worksheets/sheet1.xml');
            $zip->close();
            
            if (!$xml) {
                throw new Exception("Tidak ada sheet data dalam file XLSX");
            }
            
            return self::parseXmlSheet($xml);
        } catch (Exception $e) {
            throw new Exception("Error membaca XLSX: " . $e->getMessage());
        }
    }
    
    /**
     * Parse XML sheet from XLSX
     */
    private static function parseXmlSheet($xml) {
        $data = [];
        
        try {
            libxml_use_internal_errors(true);
            
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            
            libxml_clear_errors();
            
            $rows = $dom->getElementsByTagName('row');
            
            foreach ($rows as $row) {
                $rowData = [];
                $cells = $row->getElementsByTagName('c');
                
                foreach ($cells as $cell) {
                    $cellData = '';
                    $value = $cell->getElementsByTagName('v');
                    
                    if ($value->length > 0) {
                        $cellData = trim($value->item(0)->nodeValue);
                    }
                    
                    $rowData[] = $cellData;
                }
                
                if (!empty(array_filter($rowData))) {
                    $data[] = $rowData;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Error parsing XML: " . $e->getMessage());
        }
        
        return $data;
    }
    
    /**
     * Parse CSV file with auto delimiter detection
     */
    public static function parseCsv($filePath) {
        $data = [];
        
        if (!file_exists($filePath)) {
            throw new Exception("File CSV tidak ditemukan");
        }
        
        // Auto-detect delimiter
        $delimiters = [',', ';', "\t", '|'];
        $detectedDelimiter = ',';
        $maxCount = 0;
        
        // Check first few lines to detect delimiter
        $handle = fopen($filePath, 'r');
        for ($i = 0; $i < 3 && ($line = fgets($handle)); $i++) {
            foreach ($delimiters as $delimiter) {
                $count = substr_count($line, $delimiter);
                if ($count > $maxCount) {
                    $maxCount = $count;
                    $detectedDelimiter = $delimiter;
                }
            }
        }
        fclose($handle);
        
        // Read CSV with detected delimiter
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 10000, $detectedDelimiter)) !== false) {
                // Trim whitespace from each cell
                $row = array_map('trim', $row);
                
                if (!empty(array_filter($row))) {
                    $data[] = $row;
                }
            }
            fclose($handle);
        }
        
        if (empty($data)) {
            throw new Exception("File CSV kosong atau tidak bisa dibaca");
        }
        
        return $data;
    }
}
?>
