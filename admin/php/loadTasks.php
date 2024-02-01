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
$sel = mysqli_query($db, "select count(*) as allcount FROM tasks WHERE deleted = '0'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db, "select count(*) as allcount FROM tasks WHERE tasks.deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT * FROM tasks WHERE deleted = '0'".$searchQuery." 
order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $customerName = '';
  $hypermarketName = '';
  $statesName = '';
  $zonesName = '';
  $outletName = '';

  if($row['customer'] != null && $row['customer'] != ''){
    $id = $row['customer'];

    if ($update_stmt = $db->prepare("SELECT customer_name FROM customers WHERE id=?")) {
      $update_stmt->bind_param('s', $id);
      
      // Execute the prepared query.
      if ($update_stmt->execute()) {
        $result1 = $update_stmt->get_result();
        
        if ($row1 = $result1->fetch_assoc()) {
          $customerName = $row1['customer_name'];
        }
      }

      $update_stmt->close();
    }
  }

  if($row['hypermarket'] != null && $row['hypermarket'] != ''){
    $id = $row['hypermarket'];

    if ($update_stmt = $db->prepare("SELECT name FROM hypermarket WHERE id=?")) {
      $update_stmt->bind_param('s', $id);
      
      // Execute the prepared query.
      if ($update_stmt->execute()) {
        $result1 = $update_stmt->get_result();
        
        if ($row1 = $result1->fetch_assoc()) {
          $hypermarketName = $row1['name'];
        }
      }

      $update_stmt->close();
    }
  }

  if($row['states'] != null && $row['states'] != ''){
    $id = $row['states'];

    if ($update_stmt = $db->prepare("SELECT states FROM states WHERE id=?")) {
      $update_stmt->bind_param('s', $id);
      
      // Execute the prepared query.
      if ($update_stmt->execute()) {
        $result1 = $update_stmt->get_result();
        
        if ($row1 = $result1->fetch_assoc()) {
          $statesName = $row1['states'];
        }
      }

      $update_stmt->close();
    }
  }

  if($row['zones'] != null && $row['zones'] != ''){
    $id = $row['zones'];

    if ($update_stmt = $db->prepare("SELECT zones FROM zones WHERE id=?")) {
      $update_stmt->bind_param('s', $id);
      
      // Execute the prepared query.
      if ($update_stmt->execute()) {
        $result1 = $update_stmt->get_result();
        
        if ($row1 = $result1->fetch_assoc()) {
          $zonesName = $row1['zones'];
        }
      }

      $update_stmt->close();
    }
  }

  if($row['outlet'] != null && $row['outlet'] != ''){
    $id = $row['outlet'];

    if ($update_stmt = $db->prepare("SELECT name FROM outlet WHERE id=?")) {
      $update_stmt->bind_param('s', $id);
      
      // Execute the prepared query.
      if ($update_stmt->execute()) {
        $result1 = $update_stmt->get_result();
        
        if ($row1 = $result1->fetch_assoc()) {
          $outletName = $row1['name'];
        }
      }

      $update_stmt->close();
    }
  }

  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "type"=>$row['type'],
    "customer"=>$row['customer'],
    "vehicle_no"=>$row['vehicle_no'],
    "driver_name"=>$row['driver_name'],
    "states"=>$row['states'],
    "hypermarket"=>$row['hypermarket'],
    "zones"=>$row['zones'],
    "outlet"=>$row['outlet'],
    "booking_date"=>$row['booking_date'],
    "code"=>$row['code'],
    "remark"=>$row['remark'],
    "status"=>$row['status'],
    "source"=>$row['source'],
    "customerName"=>$customerName,
    "hypermarketName"=>$hypermarketName,
    "statesName"=>$statesName,
    "zonesName"=>$zonesName,
    "outletName"=>$outletName
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