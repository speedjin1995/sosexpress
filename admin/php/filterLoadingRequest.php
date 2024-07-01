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

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery .= " and do_request.booking_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and do_request.booking_date <= '".$toDateTime."'";
}

if($_POST['state'] != null && $_POST['state'] != '' && $_POST['state'] != '-'){
  $searchQuery .= " and do_request.states = '".$_POST['state']."'";
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
  $searchQuery .= " and do_request.customer = '".$_POST['customer']."'";
}

if($_POST['zones'] != null && $_POST['zones'] != '' && $_POST['zones'] != '-'){
  /*$check_zones_stmt = $db->prepare("SELECT * FROM zones WHERE id=? AND zones <> '-'");
  $check_zones_stmt->bind_param('s', $zones);
  $check_zones_stmt->execute();
  $check_zones_result = $check_zones_stmt->get_result();

  // Check if the zones column contains "-"
  if ($check_zones_result->num_rows > 0) {*/
    $searchQuery .= " and do_request.zone = '".$_POST['zones']."'";
  /*}

  $check_zones_stmt->close();*/
}

if($_POST['hypermarket'] != null && $_POST['hypermarket'] != '' && $_POST['hypermarket'] != '-'){
  $searchQuery .= " and do_request.hypermarket = '".$_POST['hypermarket']."'";
}

if($_POST['outlets'] != null && $_POST['outlets'] != '' && $_POST['outlets'] != '-'){
  $searchQuery .= " and do_request.outlet = '".$_POST['outlets']."'";
}

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
  if($_POST['status'] == 'Posted'){
    $searchQuery .= " and do_request.status IN ('Posted','Confirmed')";
  }
  else{
    $searchQuery .= " and do_request.status = '".$_POST['status']."'";
  }
}

if($_POST['doNumber'] != null && $_POST['doNumber'] != '' && $_POST['doNumber'] != '-'){
  $searchQuery .= " and do_request.do_number like '%".$_POST['doNumber']."%'";
}

if($_POST['printedDate'] != null && $_POST['printedDate'] != '' && $_POST['printedDate'] != '-'){
  $searchQuery .= " and do_request.do_number like '%".$_POST['doNumber']."%'";
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['printedDate']);
  $printDateTime = $dateTime->format('Y-m-d 00:00:00');
	$searchQuery .= " and do_request.printed_date = '".$printDateTime."'";
}

if($_POST['lorry'] != null && $_POST['lorry'] != '' && $_POST['lorry'] != '-'){
  $searchQuery .= " and do_request.veh_no like '%".$_POST['lorry']."%'";
}

if($searchValue != ''){
  $searchQuery .= " and (hypermarket.name like '%".$searchValue."%' or 
       outlet.name like '%".$searchValue."%' or
       customers.customer_name like'%".$searchValue."%' ) ";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(DISTINCT do_request.id) as allcount from do_request, hypermarket, outlet, states, customers, zones WHERE do_request.customer = customers.id AND do_request.hypermarket = hypermarket.id AND do_request.states = states.id AND do_request.zone = zones.id AND do_request.outlet = outlet.id AND do_request.status  IN ('Posted', 'Delivered', 'Printed', 'Confirmed')");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(DISTINCT do_request.id) as allcount from do_request, hypermarket, outlet, states, customers, zones WHERE do_request.customer = customers.id AND do_request.hypermarket = hypermarket.id AND do_request.states = states.id AND do_request.zone = zones.id AND do_request.outlet = outlet.id AND do_request.status IN ('Posted', 'Delivered', 'Printed', 'Confirmed')".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select do_request.id, do_request.booking_date, do_request.delivery_date, do_request.cancellation_date, customers.customer_name, do_request.pricing_details, do_request.do_details, 
hypermarket.name as hypermarket, do_request.direct_store, states.states, zones.zones, outlet.name as outlet, do_type, do_number, po_number, note, actual_carton, do_request.hold, 
do_request.reason, need_grn, loading_time, loading_time, status from do_request, hypermarket, outlet, states, customers, zones WHERE do_request.customer = customers.id AND 
do_request.hypermarket = hypermarket.id AND do_request.states = states.id AND do_request.zone = zones.id AND do_request.outlet = outlet.id".$searchQuery." 
order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $reason = '';
  
  if($row['reason'] != null && $row['reason'] != ''){
    if ($reason_stmt = $db->prepare("SELECT type FROM reasons WHERE id=?")) {
        $reason_stmt->bind_param('s', $row["reason"]);
        
        // Execute the prepared query.
        if ($reason_stmt->execute()) {
            $reason_result = $reason_stmt->get_result();
            
            // Check if there are any rows returned
            if ($reason_row = $reason_result->fetch_assoc()) {
                $reason = $reason_row['type'];
                
                // Now $pricing contains the pricing information for the customer obtained from the customers table
                // You can use this pricing information as needed in your application
            }
        }
        $reason_stmt->close(); // Close the statement
    }
  }
    
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
    "do_details"=>$row['do_details'] ?? [],
    "actual_carton"=>$row['actual_carton'],
    "hold"=>$row['hold'],
    "need_grn"=>$row['need_grn'],
    "loading_time"=>$row['loading_time'],
    "direct_store"=>$row['direct_store'],
    "status"=>$row['status'],
    "reason"=>$reason,
    "pricing_details" => ($row['pricing_details'] != null && $row['pricing_details'] != '') ? json_decode($row['pricing_details'], true) : []
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