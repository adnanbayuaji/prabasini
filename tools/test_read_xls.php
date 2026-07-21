<?php
require __DIR__ . '/../vendor/autoload.php';

try {
    $file = __DIR__ . '/../public/pap.xls';
    if (!file_exists($file)) {
        echo "MISSING\n";
        exit(0);
    }

    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
    $spreadsheet = $reader->load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = [];
    foreach ($sheet->getRowIterator() as $row) {
        $cells = [];
        foreach ($row->getCellIterator() as $cell) {
            $cells[] = $cell->getValue();
        }
        $rows[] = $cells;
        if (count($rows) >= 5) break;
    }

    echo "OK\n";
    print_r(array_slice($rows,0,5));
} catch (Exception $e) {
    echo 'ERR: ' . $e->getMessage() . PHP_EOL;
}
