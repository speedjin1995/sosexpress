<?php
// Include your database connection or any necessary files
require_once "db_connect.php";

// Assuming $_POST['id'] contains the ID sent from the client
if (isset($_POST['id'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

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
                // New check for other do_request entries with the same customer, outlet, delivery_date, and non-null pricing_details
                $start_of_day = date('Y-m-d 00:00:00', strtotime($row["delivery_date"]));
                $end_of_day = date('Y-m-d 23:59:59', strtotime($row["delivery_date"]));
                    
                if ($check_stmt = $db->prepare("SELECT * FROM do_request WHERE customer=? AND outlet=? AND delivery_date BETWEEN ? AND ? AND pricing_details IS NOT NULL AND pricing_details <> '' AND id != ?")) {
                    $check_stmt->bind_param('sssss', $row["customer"], $row["outlet"], $start_of_day, $end_of_day, $id);

                    // Execute the prepared query
                    if ($check_stmt->execute()) {
                        $check_result = $check_stmt->get_result();
                        
                        while ($check_row = $check_result->fetch_assoc()){
                            $pricing_details = json_decode($check_row['pricing_details'], true);

                            for($i=0; $i<count($pricing_details); $i++){
                                $message[] = array(
                                    "id" => $check_row['id'],
                                    "do_no" => $check_row['do_number'],
                                    "po_no" => $check_row['po_number'],
                                    "size" => $pricing_details[$i]['size'],
                                    "price" => $pricing_details[$i]['price'],
                                    "particular" => $pricing_details[$i]['particular'] ?? ''
                                );
                            }
                        }
                    }

                    $check_stmt->close(); // Close the statement
                }
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                )
            );   
        }
    }
} 
else {
    echo json_encode(array(
        'status' => 'failed',
        'message' => 'Missing ID parameter'
    ));
    exit;
}
?>
