<?php

require_once 'db_connect.php';
require_once '../../vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\NamedRange;
 
// Function to fetch options from database
function fetchOptions($mysqli, $table, $valueColumn) {
    $options = [];
    $result = $mysqli->query("SELECT $valueColumn FROM $table");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $options[] = $row[$valueColumn];
        }
    }
    return $options;
}

// Fetch options from database
$hypermarkets = fetchOptions($db, 'hypermarket', 'name');
$states = fetchOptions($db, 'states', 'states');
$zones = fetchOptions($db, 'zones', 'zones');
$outlets = fetchOptions($db, 'outlet', 'name');

// Default options for DO Type
$doTypes = ['DO', 'Consignment', 'Non-trade'];

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set the active sheet index to the first sheet
$sheet = $spreadsheet->setActiveSheetIndex(0);

// Set column headers
$headers = ['Delivery Date', 'Cancellation Date', 'Hypermarket', 'States', 'Zones', 'Outlets', 'DO Type', 'DO Number', 'PO Number', 'CTN', 'Notes'];
$sheet->fromArray([$headers], NULL, 'A1');

// Save Excel file to a temporary location
$writer = new Xlsx($spreadsheet);
$fileName = "DO_Template.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');

// Save the spreadsheet
$writer->save('php://output');
exit;
?>
