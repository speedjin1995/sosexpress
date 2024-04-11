<?php
## Database configuration
require_once 'db_connect.php';
session_start();
$user = $_SESSION['custID'];

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
if($searchValue != ''){
   $searchQuery = " and (hypermarket.name like '%".$searchValue."%' or 
        outlet.name like '%".$searchValue."%' or
        customers.customer_name like'%".$searchValue."%' ) ";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(DISTINCT do_request.id) as allcount from do_request, hypermarket, outlet, states, customers, zones WHERE do_request.deleted = '0' AND do_request.customer = customers.id AND do_request.hypermarket = hypermarket.id AND do_request.states = states.id AND do_request.zone = zones.id AND do_request.outlet = outlet.id AND do_request.customer = '".$user."'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(DISTINCT do_request.id) as allcount from do_request, hypermarket, outlet, states, customers, zones WHERE do_request.deleted = '0' AND do_request.customer = customers.id AND do_request.hypermarket = hypermarket.id AND do_request.states = states.id AND do_request.zone = zones.id AND do_request.outlet = outlet.id AND do_request.customer = '".$user."'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select do_request.id, do_request.booking_date, do_request.delivery_date, do_request.cancellation_date, customers.customer_name, 
hypermarket.name as hypermarket, do_request.direct_store, states.states, zones.zones, outlet.name as outlet, do_type, do_number, po_number, note, actual_carton, 
need_grn, loading_time, loading_time, status from do_request, hypermarket, outlet, states, customers, zones WHERE do_request.deleted = '0' AND do_request.customer = customers.id AND 
do_request.hypermarket = hypermarket.id AND do_request.states = states.id AND do_request.zone = zones.id AND do_request.outlet = outlet.id AND do_request.customer = '".$user."'".$searchQuery." 
order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "booking_date"=>substr($row['booking_date'], 0, 10),
    "delivery_date"=>substr($row['delivery_date'], 0, 10),
    "cancellation_date"=>substr($row['cancellation_date'], 0, 10),
    "customer_name"=>$row['customer_name'],
    "hypermarket"=>$row['hypermarket'],
    "states"=>$row['states'],
    "zones"=>$row['zones'],
    "outlet"=>$row['outlet'],
    "do_type"=>$row['do_type'],
    "do_number"=>$row['do_number'],
    "po_number"=>$row['po_number'],
    "note"=>$row['note'],
    "actual_carton"=>$row['actual_carton'],
    "need_grn"=>$row['need_grn'],
    "loading_time"=>$row['loading_time'],
    "direct_store"=>$row['direct_store'],
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