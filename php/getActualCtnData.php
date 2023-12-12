<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['bookingDate'], $_POST['customerId'])){
	$customerId = filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_STRING);
    $booking_date = filter_input(INPUT_POST, 'bookingDate', FILTER_SANITIZE_STRING);
    $bookingDateTime = new DateTime($booking_date);

    // Set the time part to the beginning of the day (00:00:00)
    $startOfDay = clone $bookingDateTime;
    $startOfDay->setTime(0, 0, 0);

    // Set the time part to the end of the day (23:59:59)
    $endOfDay = clone $bookingDateTime;
    $endOfDay->setTime(23, 59, 59);

    // Format the DateTime objects as strings
    $startOfDayFormatted = $startOfDay->format('Y-m-d H:i:s');
    $endOfDayFormatted = $endOfDay->format('Y-m-d H:i:s');

    $searchQuery = " and (do_request.customer = '".$customerId."' AND
            do_request.booking_date >= '".$startOfDayFormatted."' AND
            do_request.booking_date <= '".$endOfDayFormatted."' ) ";

    $empQuery = "select do_request.id, do_request.booking_date, do_request.delivery_date, do_request.cancellation_date, customers.customer_name, 
    hypermarket.name as hypermarket, do_request.direct_store, states.states, zones.zones, outlet.name as outlet, do_type, do_number, po_number, note, actual_carton, 
    need_grn, loading_time, loading_time, status from do_request, hypermarket, outlet, states, customers, zones WHERE do_request.deleted = '0' AND do_request.customer = customers.id AND 
    do_request.hypermarket = hypermarket.id AND do_request.states = states.id AND do_request.zone = zones.id AND do_request.outlet = outlet.id".$searchQuery;
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

    echo json_encode(
        array(
            "status" => "success",
            "message" => $data
        )
    ); 
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
        )
    ); 
}
?>