<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM goods_return WHERE id=?")) {
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
                $dateTime = new DateTime($row['return_date']);
                $formattedDate = $dateTime->format('Y-m-d');

                $message["id"]=$row['id'];
                $message["GR_No"]=$row['GR_No'];
                $message["return_date"]=$formattedDate;
                $message["customer"]=$row['customer'];
                $message["driver"]=$row['driver'];
                $message["return_details"]=json_decode($row['return_details'], true);
                $message["total_carton"]=$row['total_carton'];
                $message["total_amount"]=$row['total_amount'];
                $message["collection_date"]=$row['collection_date'];
                $message["collection_type"]=$row['collection_type'];
                $message["return_type"]=$row['return_type'];
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