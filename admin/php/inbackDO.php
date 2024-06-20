<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
}

if (isset($_POST['userID'])) {
    $id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
    $customer = '1';
    $uid = $_SESSION['userID'];
    $totalDiscount = "0.00";
    $payment_term = 'Cash';
    $del = "Invoiced";
    $del2 = "Posted";
    $today = date("Y-m-d 00:00:00");

    // Step 2: Check is the do_request have pricing?
    if ($update_stmt2 = $db->prepare("SELECT * FROM do_request WHERE id=?")) {
        $update_stmt2->bind_param('s', $id);

        if ($update_stmt2->execute()) {
            $results = $update_stmt2->get_result();

            if ($rows = $results->fetch_assoc()) {
                if($rows['pricing_details'] != null && $rows['pricing_details'] != '' && $rows['pricing_details'] != '[]'){
                    if ($stmtU = $db->prepare("UPDATE do_request SET status=? WHERE id=?")) {
                        $stmtU->bind_param('ss', $del2, $id);
                        $stmtU->execute();
                    }
                
                    // Step 1: Copy the original record to a new record
                    $copy_query = "
                        INSERT INTO do_request (
                            booking_date, delivery_date, cancellation_date, customer, hypermarket, direct_store, 
                            states, zone, outlet, do_type, do_number, do_details, po_number, note, actual_carton, 
                            need_grn, loading_time, pricing_details, total_price, hold, checker, sent_date, back_date, 
                            grn_receive, grn_upload, status, deleted
                        )
                        SELECT 
                            booking_date, delivery_date, cancellation_date, customer, hypermarket, direct_store, 
                            states, zone, outlet, do_type, do_number, do_details, po_number, note, actual_carton, 
                            need_grn, loading_time, pricing_details, total_price, hold, checker, sent_date, back_date, 
                            grn_receive, grn_upload, status, deleted 
                        FROM do_request 
                        WHERE id=?";
                    
                    if ($copy_stmt = $db->prepare($copy_query)) {
                        $copy_stmt->bind_param('s', $id);
                        $copy_stmt->execute();
                        $copy_stmt->close();
                    }
                
                    // Step 2: Retrieve customer and payment term information for further processing
                    if ($update_stmt = $db->prepare("SELECT * FROM do_request WHERE id=?")) {
                        $update_stmt->bind_param('s', $id);
                
                        if ($update_stmt->execute()) {
                            $result2 = $update_stmt->get_result();
                
                            if ($row2 = $result2->fetch_assoc()) {
                                $customer = $row2['customer'];
                                $totalDiscount = $row2['total_price'];
                
                                if (!empty($customer)) {
                                    if ($customer_stmt = $db->prepare("SELECT payment_term FROM customers WHERE customer_name=?")) {
                                        $customer_stmt->bind_param('s', $customer);
                
                                        if ($customer_stmt->execute()) {
                                            $customer_result = $customer_stmt->get_result();
                
                                            if ($customer_row = $customer_result->fetch_assoc()) {
                                                $payment_term = $customer_row['payment_term'];
                
                                                if ($payment_term == 'Cash') {
                                                    $del = "Completed";
                                                }
                                            }
                                        }
                                        $customer_stmt->close();
                                    }
                                }
                            }
                        }
                        $update_stmt->close();
                    }
                
                    // Step 3: Update the original record
                    if ($stmt2 = $db->prepare("UPDATE do_request SET status=? WHERE id=?")) {
                        $stmt2->bind_param('ss', $del, $id);
                
                        if ($stmt2->execute()) {
                            $stmt2->close();
                            $db->close();
                
                            echo json_encode(
                                array(
                                    "status" => "success",
                                    "message" => "Invoiced and Ready for Second Delivery"
                                )
                            );
                        } else {
                            echo json_encode(
                                array(
                                    "status" => "failed",
                                    "message" => $stmt2->error
                                )
                            );
                        }
                    } 
                    else {
                        echo json_encode(
                            array(
                                "status" => "failed",
                                "message" => "Something's wrong"
                            )
                        );
                    }
                }
                else{
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Please key in the price"
                        )
                    );
                }
            }

            $update_stmt2->close();
        }
    }

    
}
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}
?>
