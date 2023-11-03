<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM booking WHERE id=?")) {
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
                $message["pickup_method"]=$row['pickup_method'];
                $message["customer"]=$row['customer'];
                $message["branch"]=$row['branch'];
                $message["pickup_location"]=$row['pickup_location'];
                $message["description"]=$row['description'];
                $message["internal_notes"]=$row['internal_notes'];
                $message["estimated_ctn"]=$row['estimated_ctn'];
                $message["actual_ctn"]=$row['actual_ctn'];
                $message["vehicle_no"]=$row['vehicle_no'];
                $message["col_goods"]=$row['col_goods'];
                $message["col_chq"]=$row['col_chq'];
                $message["form_no"]=$row['form_no'];
                $message["gate"]=$row['gate'];
                $message["checker"]=$row['checker'];
                $message["status"]=$row['status'];
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