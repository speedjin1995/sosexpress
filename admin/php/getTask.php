<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM tasks WHERE id=?")) {
        $update_stmt->bind_param('s', $id);
        
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
                $message["id"]=$row['id'];
                $message["type"]=$row['type'];
                $message["customer"]=$row['customer'];
                $message["vehicle_no"]=$row['vehicle_no'];
                $message["driver_name"]=$row['driver_name'];
                $message["states"]=$row['states'];
                $message["hypermarket"]=$row['hypermarket'];
                $message["zones"]=$row['zones'];
                $message["outlet"]=$row['outlet'];
                $message["booking_date"]=$row['booking_date'];
                $message["code"]=$row['code'];
                $message["remark"]=$row['remark'];
                $message["status"]=$row['status'];
                $message["source"]=$row['source'];
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
        }
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