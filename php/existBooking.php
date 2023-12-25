<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['bookingDate'], $_POST['customerNo'])){
	$customerNo = filter_input(INPUT_POST, 'customerNo', FILTER_SANITIZE_STRING);
    $booking_date = filter_input(INPUT_POST, 'bookingDate', FILTER_SANITIZE_STRING);

    $date_format_1 = DateTime::createFromFormat('d/m/Y', $booking_date);
    $booking_date = $date_format_1->format('Y-m-d 00:00:00');

    if ($update_stmt = $db->prepare("SELECT * FROM booking WHERE customer=? AND booking_date >= ?")) {
        $update_stmt->bind_param('ss', $customerNo, $booking_date);
        
        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                )); 
        }
        else{
            $result = $update_stmt->get_result();
            $message = array();
            
            if ($row = $result->fetch_assoc()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Found dulicated booking. Please edit the existing booking!!"
                    ));  
            }
            else{
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => "Customer Booking Not Found!! Please make a booking first."
                        )); 
            }
        }
    }
    else{
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Failed to retrieve booking info"
                )); 
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>