<?php
## Database configuration
require_once 'db_connect.php';

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
  $searchQuery = " and deleted = '".$_POST['status']."'";
}

if($searchValue != ''){
  $searchQuery = " and (customer_name like '%".$searchValue."%' or customer_code like '%".$searchValue."%')";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from customers");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from customers WHERE deleted IN ('0', '1')".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from customers WHERE deleted IN ('0', '1')".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "id"=>$row['id'],
    "customer_code"=>$row['customer_code'],
    "customer_name"=>$row['customer_name'],
    "customer_address"=>$row['customer_address'],
    "customer_phone"=>$row['customer_phone'],
    "customer_email"=>$row['customer_email'],
    "short_name"=>$row['short_name'],
    "reg_no"=>$row['reg_no'],
    "pic"=>$row['pic'],
    "payment_term"=>$row['payment_term'],
    "payment_details"=>$row['payment_details'],
    "notes"=>$row['notes'],
    "deleted"=>$row['deleted'],
    "pricing"=>json_decode($row['pricing'], true),
  );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

## Response
$response = array(
 "draw" => intval($draw),
 "iTotalRecords" => $totalRecords,
 "iTotalDisplayRecords" => $totalRecordwithFilter,
 "aaData" => $data,
 "query" => $empQuery
);


echo json_encode($response);

?>