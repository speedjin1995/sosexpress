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

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery = " and booking.booking_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and booking.booking_date <= '".$toDateTime."'";
}

if($_POST['branch'] != null && $_POST['branch'] != '' && $_POST['branch'] != '-'){
	$searchQuery .= " and booking.branch like '%".$_POST['branch']."%'";
}

if($searchValue != ''){
  $searchQuery = " and (customers.customer_name like '%".$searchValue."%')";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from booking, customers WHERE booking.customer = customers.id AND booking.customer = '".$user."' AND booking.deleted = '0'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from booking, customers WHERE booking.customer = customers.id AND booking.customer = '".$user."' AND booking.deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT booking.id, booking.booking_date, booking.pickup_method, customers.customer_name, booking.pickup_location, booking.description, 
booking.estimated_ctn, booking.actual_ctn, booking.vehicle_no, booking.col_goods, booking.col_chq, booking.form_no, 
booking.gate, booking.checker, booking.status FROM booking, customers WHERE booking.customer = customers.id AND 
booking.customer = '".$user."' AND booking.deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $name = '';

  if($row['checker']!=null && $row['checker']!=''){
    $id = $row['checker'];

    if ($update_stmt = $db->prepare("SELECT * FROM users WHERE id=?")) {
      $update_stmt->bind_param('s', $id);
      
      // Execute the prepared query.
      if ($update_stmt->execute()) {
        $result1 = $update_stmt->get_result();
        
        if ($row1 = $result1->fetch_assoc()) {
          $name = $row1['name'];
        }
      }
    }
  }

  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "booking_date"=>$row['booking_date'],
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
    "name"=>$name,
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