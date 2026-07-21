<?php

/**
 * Simple CSV Reader untuk backup parser
 * Jika Excel parsing gagal, gunakan ini
 */
class CsvSimpleReader {
    
    /**
     * Read CSV dengan delimiter detection otomatis
     */
    public static function read($filePath) {
        $data = [];
        $delimiters = [',', ';', '\t', '|'];
        $detectedDelimiter = self::detectDelimiter($filePath);
        
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 10000, $detectedDelimiter)) !== false) {
                if (!empty(array_filter($row))) {
                    $data[] = $row;
                }
            }
            fclose($handle);
        }
        
        return $data;
    }
    
    /**
     * Detect delimiter dalam CSV
     */
    private static function detectDelimiter($filePath, $checkLines = 3) {
        $delimiters = [',', ';', "\t", '|'];
        $delimitCount = array_fill_keys($delimiters, 0);

        if (!file_exists($filePath)) {
            return ',';
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return ',';
        }

        $linesRead = 0;
        while ($linesRead < $checkLines && ($line = fgets($handle)) !== false) {
            foreach ($delimiters as $delimiter) {
                $delimitCount[$delimiter] += substr_count($line, $delimiter);
            }
            $linesRead++;
        }

        fclose($handle);

        // Choose delimiter with highest count
        $best = ',';
        $max = -1;
        foreach ($delimitCount as $delim => $count) {
            if ($count > $max) {
                $max = $count;
                $best = $delim;
            }
        }

        return $best;
    }
}

/**
 * Advanced Excel Reader untuk .xlsx
 * Menggunakan built-in ZIP extraction
 */
class AdvancedExcelReader {
    
    /**
     * Read XLSX dengan better error handling
     */
    public static function readXlsx($filePath) {
        try {
            $zip = new ZipArchive();
            
            if ($zip->open($filePath) !== true) {
                throw new Exception("Tidak bisa membuka file XLSX");
            }
            
            // Get sheet list
            $sheets = [];
            for ($i = 1; $i <= 10; $i++) {
                $sheetPath = "xl/worksheets/sheet{$i}.xml";
                if ($zip->locateName($sheetPath) !== false) {
                    $sheets[] = $sheetPath;
                } else {
                    break;
                }
            }
            
            if (empty($sheets)) {
                throw new Exception("Tidak ada sheet dalam file XLSX");
            }
            
            // Read first sheet
            $xmlData = $zip->getFromName($sheets[0]);
            $zip->close();
            
            if (!$xmlData) {
                throw new Exception("Tidak bisa membaca data sheet");
            }
            
            return self::parseXmlData($xmlData);
        } catch (Exception $e) {
            throw new Exception("Error membaca XLSX: " . $e->getMessage());
        }
    }
    
    /**
     * Parse XML sheet data
     */
    private static function parseXmlData($xmlData) {
        $data = [];
        
        try {
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadXML($xmlData, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
            
            $xpath = new DOMXPath($dom);
            $rows = $xpath->query('//worksheet/sheetData/row');
            
            foreach ($rows as $row) {
                $rowData = [];
                $cells = $xpath->query('.//c', $row);
                
                foreach ($cells as $cell) {
                    $cellValue = '';
                    
                    // Try to get cell value
                    $value = $xpath->query('.//v', $cell);
                    if ($value->length > 0) {
                        $cellValue = $value->item(0)->nodeValue;
                    } else {
                        // Try text content
                        $cellValue = $cell->nodeValue;
                    }
                    
                    $rowData[] = trim($cellValue);
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
}

?>
