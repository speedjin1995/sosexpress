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
customers.customer_name, goods_return.return_details, goods_return.collection_date, goods_return.collection_type, 
goods_return.total_carton, goods_return.return_type FROM goods_return, customers WHERE goods_return.customer = customers.id AND goods_return.deleted = '0'".$searchQuery." 
order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "GR_No"=>$row['GR_No'],
    "return_date"=>$row['return_date'],
    "driver"=>$row['driver'],
    "customer_id"=>$row['custId'],
    "customer_name"=>$row['customer_name'],
    "return_details"=>json_decode($row['return_details'], true),
    "collection_date"=>$row['collection_date'],
    "collection_type"=>$row['collection_type'],
    "return_type"=>$row['return_type'],
    "total_carton"=>$row['total_carton']
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