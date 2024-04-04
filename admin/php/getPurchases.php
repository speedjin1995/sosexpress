<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['purchasesID'])){
	$id = filter_input(INPUT_POST, 'purchasesID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM invoice WHERE id=?")) {
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
                $message["id"] = $row['id'];
                $message["invoice_no"] = $row['invoice_no'];
                $message["customer"] = $row['customer'];
                $message["total_amount"] = $row['total_amount'];
                $message["carts"] = array();

                if ($pricing_stmt = $db->prepare("SELECT * FROM invoice_cart WHERE invoice_id=?")) {
                    $pricing_stmt->bind_param('s', $message["id"]);
                    
                    // Execute the prepared query.
                    if ($pricing_stmt->execute()) {
                        $pricing_result = $pricing_stmt->get_result();
                        
                        // Check if there are any rows returned
                        while ($pricing_row = $pricing_result->fetch_assoc()) {
                            array_push($message["carts"], array(
                                "items" => $pricing_row['items'],
                                "price" => $pricing_row['amount'],
                                "id" => $pricing_row['id'],
                            ));
                            
                            // Now $pricing contains the pricing information for the customer obtained from the customers table
                            // You can use this pricing information as needed in your application
                        }
                    }

                    $pricing_stmt->close(); // Close the statement
                }
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