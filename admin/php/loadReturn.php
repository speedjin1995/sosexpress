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
$sel = mysqli_query($db,"select count(*) as allcount from goods_return, customers WHERE goods_return.customer = customers.id AND goods_return.deleted = '0'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from goods_return, customers WHERE goods_return.customer = customers.id AND goods_return.deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT goods_return.id, goods_return.GR_No, goods_return.return_date, goods_return.driver, customers.id AS custId, 
customers.customer_name, goods_return.return_details, goods_return.collection_date, goods_return.collection_type, goods_return.status, 
goods_return.total_carton, goods_return.return_type FROM goods_return, customers WHERE goods_return.customer = customers.id AND goods_return.deleted = '0'".$searchQuery." 
order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $details = array();
  $locations = array();

  if($row['return_details'] != null){
    $details = json_decode($row['return_details'], true);

    for($i=0; $i<count($details); $i++){
      $hypermarket = '';
      $outlets = '';
  
      if ($update_stmt = $db->prepare("SELECT name FROM hypermarket WHERE id=?")) {
        $update_stmt->bind_param('s', $details[$i]['hypermarket']);
        
        // Execute the prepared query.
        if ($update_stmt->execute()) {
          $result1 = $update_stmt->get_result();
          
          if ($row1 = $result1->fetch_assoc()) {
            $hypermarket = $row1['name'];
          }
        }
      }
  
      if ($update_stmt2 = $db->prepare("SELECT name FROM outlet WHERE id=?")) {
        $update_stmt2->bind_param('s', $details[$i]['location']);
        
        // Execute the prepared query.
        if ($update_stmt2->execute()) {
          $result2 = $update_stmt2->get_result();
          
          if ($row2 = $result2->fetch_assoc()) {
            $outlets = $row2['name'];
          }
        }
      }
  
      $details[$i]['hypermarket'] = $hypermarket;
      $details[$i]['location'] = $outlets;
      array_push($locations, $outlets);
    }
  }
  
  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "GR_No"=>$row['GR_No'],
    "return_date"=>substr($row['return_date'], 0, 10),
    "driver"=>$row['driver'],
    "customer_id"=>$row['custId'],
    "customer_name"=>$row['customer_name'],
    "return_details"=>$details,
    "locations"=>$locations,
    "collection_date"=>substr($row['collection_date'], 0, 10),
    "collection_type"=>$row['collection_type'],
    "return_type"=>$row['return_type'],
    "total_carton"=>$row['total_carton'],
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