<?php
include __DIR__ . '/../../setup.php';

use lib\db\Dump;

$pageTitle = 'DB Dump';

// the tables that can be dumped...
$tables = ['users', 'messages'];

try {
    $dump = new Dump($tables);
} catch (Exception $e) {
    echo 'failed to initialize database connection';
    http_response_code(500);
}

if (isset($_GET['table']) && in_array($_GET['table'], $tables, true)) {
    // dump this table...
    $tableToDump = $_GET['table'];

    $filename = "$tableToDump.csv";

    $result = $dump->loadAllRowsFromTable($tableToDump);
    $out = fopen('php://output', 'wb');
    ob_start();
    foreach ($result as $row) {
        fputcsv($out, $row);
    }
    $data = ob_get_clean();

    // write response
    header("Expires: 0");
    header("Cache-Control: no-store");
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    echo $data;

    exit();
}

// if 'table' param is not set (or invalid), show list of links

ob_start();
include TEMPLATE_DIR . '/pages/dbdump/index.php';
$htmlContent = ob_get_clean();

include TEMPLATE_DIR . '/page.php';
