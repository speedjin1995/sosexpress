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
$sel = mysqli_query($db, "select count(*) as allcount FROM tasks, customers, hypermarket, outlet, states, zones WHERE tasks.deleted = '0' AND tasks.customer = customers.id AND 
tasks.hypermarket = hypermarket.id AND tasks.states = states.id AND tasks.zones = zones.id AND tasks.outlet = outlet.id");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db, "select count(*) as allcount FROM tasks, customers, hypermarket, outlet, states, zones WHERE tasks.deleted = '0' AND tasks.customer = customers.id AND 
tasks.hypermarket = hypermarket.id AND tasks.states = states.id AND tasks.zones = zones.id AND tasks.outlet = outlet.id".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT tasks.*, customers.customer_name, hypermarket.name as hypermarket, states.states, zones.zones, outlet.name as outlet 
FROM tasks, customers, hypermarket, outlet, states, zones WHERE tasks.deleted = '0' AND tasks.customer = customers.id AND 
tasks.hypermarket = hypermarket.id AND tasks.states = states.id AND tasks.zones = zones.id AND tasks.outlet = outlet.id".$searchQuery." 
order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "type"=>$row['type'],
    "customer_id"=>$row['customer'],
    "customer_name"=>$row['customer_name'],
    "vehicle_no"=>$row['vehicle_no'],
    "driver_name"=>$row['driver_name'],
    "hypermarket"=>$row['hypermarket'],
    "states"=>$row['states'],
    "zones"=>$row['zones'],
    "outlet"=>$row['outlet'],
    "date"=>$row['date'],
    "code"=>$row['code'],
    "remark"=>$row['remark'],
    "reason"=>$row['reason'],
    "back_on_date"=>$row['back_on_date'],
    "grn_no"=>$row['grn_no']
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