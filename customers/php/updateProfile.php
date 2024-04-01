<?php
require_once 'db_connect.php';
ini_set('display_errors', 1);
session_start();

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
} 
else{
	$userId = $_SESSION['userID'];
}

if(isset($_POST['bookingDate'], $_POST['address'], $_POST['extimated_ctn'])){
	// START Booking
	$booking_date = filter_input(INPUT_POST, 'bookingDate', FILTER_SANITIZE_STRING);
	$booking_date = DateTime::createFromFormat('d/m/Y H:i:s A', $booking_date)->format('Y-m-d H:i:s');
	$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
	$extimated_ctn = filter_input(INPUT_POST, 'extimated_ctn', FILTER_SANITIZE_STRING);
	$descriptionB = null;

	if($_POST['description'] != null && $_POST['description'] != ''){
		$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
	}

	if ($insert_stmt2 = $db->prepare("INSERT INTO booking (booking_date, customer, pickup_location, description, estimated_ctn) VALUES (?, ?, ?, ?, ?)")){
		$insert_stmt2->bind_param('sssss', $booking_date, $userId, $address, $descriptionB, $extimated_ctn);		
		$insert_stmt2->execute();
		$insert_stmt2->close();
	}
	// END Booking
	// START DO Request
	$success = true;
	$booking_date2 = $_POST['booking_date'];
	$delivery_date = $_POST['delivery_date'];
	$cancellation_date = $_POST['cancellation_date'];
	$hypermarket = $_POST['hypermarket'];
	$states = $_POST['states'];
	$zone = $_POST['zones'];
	$outlet = $_POST['outlets'];
	$do_type = $_POST['do_type'];
	$do_no = $_POST['do_no'];
	$po_no = $_POST['po_no'];
	$actual_carton = $_POST['actual_ctn'];
    $need_grn = $_POST['need_grn'];
	$description = $_POST['description'];

	for($i=0; $i<sizeof($booking_date2); $i++){
		$innerDO = null;
		$innerPO = null;
		$notes = null;
		$direct_store = null;
		$loading_time = null;

		if(isset($do_no[$i]) && $do_no[$i] != null){
			$innerDO = $do_no[$i];
		}

		if(isset($po_no[$i]) && $po_no[$i] != null){
			$innerPO = $po_no[$i];
		}

		if(isset($description[$i]) && $description[$i] != null){
			$notes = $description[$i];
		}

		if ($insert_stmt = $db->prepare("INSERT INTO do_request (booking_date, delivery_date, cancellation_date, customer
        , hypermarket, states, zone, outlet, do_type, do_number, po_number, note, actual_carton, need_grn, loading_time, direct_store) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
            $insert_stmt->bind_param('ssssssssssssssss', $booking_date2[$i], $delivery_date[$i], $cancellation_date[$i], $userId, $hypermarket[$i]
            , $states[$i], $zone[$i], $outlet[$i], $do_type[$i], $innerDO, $innerPO, $notes, $actual_carton[$i], $need_grn[$i], $loading_time, $direct_store);
            
            // Execute the prepared query.
            if (! $insert_stmt->execute()){
                $success = false;
            }
		} 
		else {
			$success = false;
		}
	}

	$insert_stmt->close();
	$db->close();

	if($success){
		echo json_encode(
			array(
				"status"=> "success", 
				"message"=> "Added Successfully!!" 
			)
		);
	}
	else{
		echo json_encode(
			array(
				"status"=> "failed", 
				"message"=> "Something went worng"
			)
		);
	}
} 
else{
	echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all fields"
        )
    ); 
}
?>
