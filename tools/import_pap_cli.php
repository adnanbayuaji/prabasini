<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../src/PapRepository.php';
require __DIR__ . '/../src/ExcelImporter.php';

try {
    $file = __DIR__ . '/../public/pap.xls';
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        exit(1);
    }

    $data = ExcelImporter::parseExcel($file);
    $repo = new PapRepository($pdo);
    $result = $repo->importFromArray($data);

    echo "Inserted: " . $result['inserted'] . "\n";
    if (!empty($result['errors'])) {
        echo "Errors:\n";
        print_r($result['errors']);
    }
} catch (Exception $e) {
    echo 'ERR: ' . $e->getMessage() . PHP_EOL;
}
