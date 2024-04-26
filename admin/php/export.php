<?php

require_once 'db_connect.php';
// // Load the database configuration file 
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 
 
// Excel file name for download 
$fileName = "Invoice_" . date('Y-m-d') . ".xls";
 
// Column names 
$fields = array('Type', 'No', 'Date', 'Customer', 'Customer Reference No', 'Inventory', 'Description', 'UOM', 'Amount', 'Qty',	
'Unit Price', 'Dis%', 'GST Amt', 'GST', 'MSIC', 'Entity Info', 'Branch', 'Project', 'Bill Reference No', 'Comment', 'Shipment',	
'Journal Memo', 'Sales Person'); 

// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n"; 

## Search 
$searchQuery = " ";

if(isset($_GET['fromDate']) && $_GET['fromDate'] != null && $_GET['fromDate'] != ''){
    $searchQuery = " and invoice.created_datetime >= '".$_GET['fromDate']."'";
}

if(isset($_GET['toDate']) && $_GET['toDate'] != null && $_GET['toDate'] != ''){
    $searchQuery = " and invoice.created_datetime <= '".$_GET['toDate']."'";
}

if(isset($_GET['customer']) && $_GET['customer'] != null && $_GET['customer'] != '' && $_GET['customer'] != '-'){
    $searchQuery = " and customers.id = '".$_GET['customer']."'";
}

if(isset($_GET['invoice']) && $_GET['invoice'] != null && $_GET['invoice'] != ''){
    $searchQuery = " and invoice.invoice_no like '%".$_GET['invoice']."%'";
}

// Fetch records from database
$query = $db->query("select invoice.invoice_no, customers.customer_code, customers.customer_name, customers.short_name, customers.customer_address,
invoice.total_amount, users.name, invoice.created_datetime, invoice_cart.id, invoice_cart.invoice_id, invoice_cart.items, 
invoice_cart.amount from invoice, invoice_cart, customers, users WHERE customers.id = invoice.customer AND 
invoice.id = invoice_cart.invoice_id AND users.id = invoice.created_by".$searchQuery);

if($query->num_rows > 0){ 
    // Output each row of the data 
    while($row = $query->fetch_assoc()){ 
        $entity = $row['customer_name'].$row['customer_address'];

        $lineData = array('Invoice', $row['invoice_no'], substr($row['created_datetime'], 0, 10), $row['customer_name'], $row['customer_code'], 'New Product Code'
        , $row['items'], 'Jobs', $row['amount'], '1', $row['amount'], '', '', '', '01112', $entity, '', '', '', '', '', '', '');

        array_walk($lineData, 'filterData'); 
        $excelData .= implode("\t", array_values($lineData)) . "\n"; 
    } 
}else{ 
    $excelData .= 'No records found...'. "\n"; 
} 
 
// Headers for download 
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData; 
 
exit;
?>
