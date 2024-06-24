<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM do_request WHERE id=?")) {
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
                $message["booking_date"] = $row['booking_date'];
                $message["delivery_date"] = $row['delivery_date'];
                $message["cancellation_date"] = $row['cancellation_date'];
                $message["customer"] = $row['customer'];
                $message["hypermarket"] = $row['hypermarket'];
                $message["states"] = $row['states'];
                $message["zone"] = $row['zone'];
                $message["outlet"] = $row['outlet'];
                $message["do_type"] = $row['do_type'];
                $message["do_number"] = $row['do_number'];
                $message["do_details"] = json_decode($row['do_details'], true);
                $message["po_number"] = $row['po_number'];
                $message["note"] = $row['note'];
                $message["actual_carton"] = $row['actual_carton'];
                $message["need_grn"] = $row['need_grn'];
                $message["loading_time"] = $row['loading_time'];
                $message["direct_store"] = $row['direct_store'];
                $message["status"] = $row['status'];
                $message["reason"] = $row["reason"];
                $message["similar_requests_count"] = 0;
                $message["pricing_details"] = ($row['pricing_details'] != null && $row['pricing_details'] != '') ? json_decode($row['pricing_details'], true) : [];

                if ($pricing_stmt = $db->prepare("SELECT pricing FROM customers WHERE id=?")) {
                    $pricing_stmt->bind_param('s', $message["customer"]);
                    
                    // Execute the prepared query.
                    if ($pricing_stmt->execute()) {
                        $pricing_result = $pricing_stmt->get_result();
                        
                        // Check if there are any rows returned
                        if ($pricing_row = $pricing_result->fetch_assoc()) {
                            $message["pricing"] = $pricing_row['pricing'];
                            
                            // Now $pricing contains the pricing information for the customer obtained from the customers table
                            // You can use this pricing information as needed in your application
                        }
                    }
                    $pricing_stmt->close(); // Close the statement
                }

                // New check for other do_request entries with the same customer, outlet, delivery_date, and non-null pricing_details
                if ($check_stmt = $db->prepare("SELECT COUNT(*) as count FROM do_request WHERE customer=? AND outlet=? AND delivery_date BETWEEN ? AND ? AND pricing_details IS NOT NULL AND pricing_details <> '' AND id != ?")) {
                    $start_of_day = date('Y-m-d 00:00:00', strtotime($message["delivery_date"]));
                    $end_of_day = date('Y-m-d 23:59:59', strtotime($message["delivery_date"]));
                    
                    $check_stmt->bind_param('sssss', $message["customer"], $message["outlet"], $start_of_day, $end_of_day, $id);

                    // Execute the prepared query
                    if ($check_stmt->execute()) {
                        $check_result = $check_stmt->get_result();
                        
                        if ($check_row = $check_result->fetch_assoc()) {
                            $message["similar_requests_count"] = $check_row['count'];
                        }
                    }
                    $check_stmt->close(); // Close the statement
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