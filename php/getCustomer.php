<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM customers WHERE id=?")) {
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
            
            while ($row = $result->fetch_assoc()) {
                $message['id'] = $row['id'];
                $message['username'] = $row['username'];
                $message['customer_code'] = $row['customer_code'];
                $message['customer_name'] = $row['customer_name'];
                $message['customer_address'] = $row['customer_address'];
                $message['customer_phone'] = $row['customer_phone'];
                $message['customer_email'] = $row['customer_email'];
                $message['short_name'] = $row['short_name'];
                $message['reg_no'] = $row['reg_no'];
                $message['pic'] = $row['pic'];
                $message['payment_term'] = $row['payment_term'];
                $message['payment_details'] = $row['payment_details'];
                $message['notes'] = $row['notes'];
                $message['pricing'] = json_decode($row['pricing'], true);
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