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
$searchQuery = "";
if($searchValue != ''){
   $searchQuery = " and (customers.customer_name like '%".$searchValue."%')";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(DISTINCT booking.id) as allcount from booking, customers, users WHERE (booking.checker = users.id OR booking.checker IS NULL) AND booking.customer = customers.id");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(DISTINCT booking.id) as allcount from booking, customers, users WHERE (booking.checker = users.id OR booking.checker IS NULL) AND booking.customer = customers.id".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT DISTINCT booking.id, booking.pickup_method, customers.customer_name, booking.pickup_location, booking.description, 
booking.estimated_ctn, booking.actual_ctn, booking.vehicle_no, booking.col_goods, booking.col_chq, booking.form_no, 
booking.gate, users.name, booking.status FROM booking, customers, users WHERE (booking.checker = users.id OR booking.checker IS NULL) AND 
booking.customer = customers.id".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "pickup_method"=>$row['pickup_method'],
    "customer_name"=>$row['customer_name'],
    "pickup_location"=>$row['pickup_location'],
    "description"=>$row['description'],
    "estimated_ctn"=>$row['estimated_ctn'],
    "actual_ctn"=>$row['actual_ctn'],
    "vehicle_no"=>$row['vehicle_no'],
    "col_goods"=>$row['col_goods'],
    "col_chq"=>$row['col_chq'],
    "form_no"=>$row['form_no'],
    "gate"=>$row['gate'],
    "name"=>$row['name'],
    "status"=>$row['status']
  );

  $counter++;
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