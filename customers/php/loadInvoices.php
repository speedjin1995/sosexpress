<?php
## Database configuration
require_once 'db_connect.php';
session_start();
$user = $_SESSION['userID'];

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
//$columnIndex = $_POST['order'][0]['column']; // Column index
//$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
//$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";
if($searchValue != ''){
   $searchQuery = " AND (invoice.invoice_no like '%".$searchValue."%' OR customers.customer_name like '%".$searchValue."%' OR customers.short_name like '%".$searchValue."%')";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from invoice WHERE invoice.customer = '".$user."'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from invoice, invoice_cart, customers, users WHERE invoice.customer = '".$user."' AND customers.id = invoice.customer AND invoice.id = invoice_cart.invoice_id AND users.id = invoice.created_by".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select invoice.invoice_no, customers.customer_name, customers.short_name, invoice.total_amount, users.name, invoice.created_datetime, 
invoice_cart.id, invoice_cart.invoice_id, invoice_cart.items, invoice_cart.amount from invoice, invoice_cart, customers, users 
WHERE invoice.customer = '".$user."' AND customers.id = invoice.customer AND invoice.id = invoice_cart.invoice_id AND users.id = invoice.created_by".$searchQuery." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$data2 = array();

while($row = mysqli_fetch_assoc($empRecords)) {
  if(!in_array($row['invoice_id'], $data2)){
    $data[] = array( 
      "invoice_id"=>$row['invoice_id'],
      "invoice_no"=>$row['invoice_no'],
      "customer_name"=>$row['customer_name'],
      "short_name"=>$row['short_name'],
      "total_amount"=>$row['total_amount'],
      "name"=>$row['name'],
      "created_datetime"=>$row['created_datetime'],
      "cart" => array()
    );

    array_push($data2, $row['invoice_id']);
  }

  $key = array_search($row['invoice_id'], $data2);
  array_push($data[$key]['cart'], array(
    "id"=>$row['id'],
    "items"=>$row['items'],
    "amount"=>$row['amount']
  ));
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);

?>