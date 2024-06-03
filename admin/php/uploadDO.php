<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['bookingDate'], $_POST['deliveryDate'], $_POST['cancellationDate'], $_POST['customerNo']
, $_POST['hypermarket'], $_POST['states'], $_POST['zones'], $_POST['do_type']
, $_POST['actual_ctn'], $_POST['on_hold'])){
	$booking_date = filter_input(INPUT_POST, 'bookingDate', FILTER_SANITIZE_STRING);
    $booking_date2 = $booking_date;
	$delivery_date = filter_input(INPUT_POST, 'deliveryDate', FILTER_SANITIZE_STRING);
	$cancellation_date = filter_input(INPUT_POST, 'cancellationDate', FILTER_SANITIZE_STRING);
	$customer = filter_input(INPUT_POST, 'customerNo', FILTER_SANITIZE_STRING);
	$hypermarket = filter_input(INPUT_POST, 'hypermarket', FILTER_SANITIZE_STRING);
	$states = filter_input(INPUT_POST, 'states', FILTER_SANITIZE_STRING);
	$zone = filter_input(INPUT_POST, 'zones', FILTER_SANITIZE_STRING);
	$outlet = '0';
	$do_type = filter_input(INPUT_POST, 'do_type', FILTER_SANITIZE_STRING);
	$actual_carton = filter_input(INPUT_POST, 'actual_ctn', FILTER_SANITIZE_STRING);
    $on_hold = filter_input(INPUT_POST, 'on_hold', FILTER_SANITIZE_STRING);

    $need_grn = null;
	$do_number = null;
    $do_details = null;
    $direct_store = null;
	$po_number = null;
	$note = null;
	$loading_time = null;

    if(isset($_POST['jsonDataField']) && $_POST['jsonDataField'] != null && $_POST['jsonDataField'] != ''){
        $do_details = filter_input(INPUT_POST, 'jsonDataField', FILTER_SANITIZE_STRING);
        $do_details = html_entity_decode($do_details);
    }

    if(isset($_POST['do_no']) && $_POST['do_no'] != null && $_POST['do_no'] != ''){
        $do_number = filter_input(INPUT_POST, 'do_no', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['po_no']) && $_POST['po_no'] != null && $_POST['po_no'] != ''){
        $po_number = filter_input(INPUT_POST, 'po_no', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['description']) && $_POST['description'] != null && $_POST['description'] != ''){
        $note = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['need_grn']) && $_POST['need_grn'] != null && $_POST['need_grn'] != ''){
        $need_grn = filter_input(INPUT_POST, 'need_grn', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['loadingTime']) && $_POST['loadingTime'] != null && $_POST['loadingTime'] != ''){
        $loading_time = filter_input(INPUT_POST, 'loadingTime', FILTER_SANITIZE_STRING);
    }
	
    if($hypermarket == '0' && isset($_POST['direct_store']) && $_POST['direct_store'] != null && $_POST['direct_store'] != ''){
        $direct_store = filter_input(INPUT_POST, 'direct_store', FILTER_SANITIZE_STRING);
        $temp_direct_store = strtoupper($direct_store);
        $outlet = '0';

        if ($update_stmt = $db->prepare("SELECT * FROM outlet WHERE UPPER(name)=?")) {
            $update_stmt->bind_param('s', $temp_direct_store);
            
            // Execute the prepared query.
            if ($update_stmt->execute()) {
                $result = $update_stmt->get_result();
                
                if (($row = $result->fetch_assoc()) !== null) {
                    $outlet = $row['id'];
                }
                else{
                    if ($insert_stmt2 = $db->prepare("INSERT INTO outlet (name, hypermarket, states, zones) VALUES (?, ?, ?, ?)")) {
                        $insert_stmt2->bind_param('ssss', $direct_store, $hypermarket, $states, $zone);
                        
                        // Execute the prepared query.
                        if ($insert_stmt2->execute()) {
                            $outlet = $insert_stmt2->insert_id;;
                            $insert_stmt2->close();
                        }
                    }
                }
            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to prepare statements"
                )
            );
        }
    }
    else{
        $outlet = filter_input(INPUT_POST, 'outlets', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
        $id = $_POST['id'];
        $booking_date = DateTime::createFromFormat('d/m/Y', str_replace(',', '', explode(" ", $booking_date)[0]))->format('Y-m-d H:i:s');
        $delivery_date = DateTime::createFromFormat('d/m/Y', str_replace(',', '', explode(" ", $delivery_date)[0]))->format('Y-m-d H:i:s');
	    $cancellation_date = DateTime::createFromFormat('d/m/Y', str_replace(',', '', explode(" ", $cancellation_date)[0]))->format('Y-m-d H:i:s');

        if ($select_stmt = $db->prepare("SELECT actual_carton FROM do_request WHERE id=?")) {
            $select_stmt->bind_param('s', $id);
    
            // Execute the prepared query to select existing actual_carton
            if ($select_stmt->execute()) {
                $select_stmt->bind_result($existing_actual_carton);
    
                // Fetch the result
                if ($select_stmt->fetch()) {
                    $select_stmt->close();

                    if ($update_stmt = $db->prepare("UPDATE do_request SET booking_date=?, delivery_date=?, cancellation_date=?, customer=?, hypermarket=?, states=?, zone=?
                    , outlet=?, do_type=?, do_number=?, po_number=?, note=?, actual_carton=?, need_grn=?, loading_time=?, direct_store=?, hold=?, do_details=? WHERE id=?")){
                        $id = $_POST['id'];
                        $update_stmt->bind_param('sssssssssssssssssss', $booking_date, $delivery_date, $cancellation_date, $customer, $hypermarket
                        , $states, $zone, $outlet, $do_type, $do_number, $po_number, $note, $actual_carton, $need_grn, $loading_time, $direct_store
                        , $on_hold, $do_details, $id);
                        
                        // Execute the prepared query.
                        if (! $update_stmt->execute()){

                            echo json_encode(
                                array(
                                    "status"=> "failed", 
                                    "message"=> $update_stmt->error
                                )
                            );

                        } 
                        else{
                            $update_stmt->close();

                            echo json_encode(
                                array(
                                    "status"=> "success", 
                                    "message"=> "Added Successfully!!"
                                )
                            );

                            if ($update_stmt3 = $db->prepare("SELECT * FROM booking WHERE customer=? AND booking_date >= ?")) {
                                $booking_date2 = DateTime::createFromFormat('d/m/Y', str_replace(',', '', explode(" ", $booking_date2)[0]));
                                $booking_date2 = $booking_date2->format('Y-m-d 00:00:00');
                                $update_stmt3->bind_param('ss', $customer, $booking_date2);
                                
                                // Execute the prepared query.
                                if ($update_stmt3->execute()) {
                                    $result = $update_stmt3->get_result();
                                    
                                    if ($row = $result->fetch_assoc()) {
                                        $id = $row['id'];
                                        $existing_actual_ctn = $row['actual_ctn'];
                                        $update_stmt3->close();
                            
                                        // Check if $actual_carton is not empty
                                        if (!empty($actual_carton)) {
                                            // If existing_actual_ctn is not empty, sum it with $actual_carton
                                            if (!empty($existing_actual_ctn)) {
                                                $actual_carton = $existing_actual_ctn - $existing_actual_carton + $actual_carton;
                                            }
                            
                                            // Update actual_ctn
                                            if ($update_stmt2 = $db->prepare("UPDATE booking SET actual_ctn=? WHERE id=?")) {
                                                $update_stmt2->bind_param('ss', $actual_carton, $id);
                                                $update_stmt2->execute();
                                                $update_stmt2->close();
                                            }
                                        }
                                    }
                                }
                            }
                            

                            $db->close();
                        }
                    } 
                    else{
                        echo json_encode(
                            array(
                                "status"=> "failed", 
                                "message"=> $update_stmt->error
                            )
                        );
                    }
                }
            }
        }
    }
    else{
        $booking_date = DateTime::createFromFormat('d/m/Y', str_replace(',', '', explode(" ", $booking_date)[0]))->format('Y-m-d H:i:s');
        $delivery_date = DateTime::createFromFormat('d/m/Y', str_replace(',', '', explode(" ", $delivery_date)[0]))->format('Y-m-d H:i:s');
	    $cancellation_date = DateTime::createFromFormat('d/m/Y', str_replace(',', '', explode(" ", $cancellation_date)[0]))->format('Y-m-d H:i:s');

        if ($insert_stmt = $db->prepare("INSERT INTO do_request (booking_date, delivery_date, cancellation_date, customer
        , hypermarket, states, zone, outlet, do_type, do_number, po_number, note, actual_carton, need_grn, loading_time, direct_store, hold, do_details) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
            $insert_stmt->bind_param('ssssssssssssssssss', $booking_date, $delivery_date, $cancellation_date, $customer, $hypermarket
            , $states, $zone, $outlet, $do_type, $do_number, $po_number, $note, $actual_carton, $need_grn, $loading_time, $direct_store
            , $on_hold, $do_details);
            
            // Execute the prepared query.
            if (! $insert_stmt->execute()){
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $insert_stmt->error
                    )
                );
            } 
            else{
                $insert_stmt->close();

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!"
                    )
                );
                
                if ($update_stmt = $db->prepare("SELECT * FROM booking WHERE customer=? AND booking_date >= ?")) {
                    $booking_date2 = DateTime::createFromFormat('d/m/Y', str_replace(',', '', explode(" ", $booking_date2)[0]));
                    $booking_date2 = $booking_date2->format('Y-m-d 00:00:00');
                    $update_stmt->bind_param('ss', $customer, $booking_date2);
                    
                    // Execute the prepared query.
                    if ($update_stmt->execute()) {
                        $result = $update_stmt->get_result();
                        
                        if ($row = $result->fetch_assoc()) {
                            $id = $row['id'];
                            $existing_actual_ctn = $row['actual_ctn'];
                            $update_stmt->close();
                
                            // Check if $actual_carton is not empty
                            if (!empty($actual_carton)) {
                                // If existing_actual_ctn is not empty, sum it with $actual_carton
                                if (!empty($existing_actual_ctn)) {
                                    $actual_carton = $existing_actual_ctn + $actual_carton;
                                }
                
                                // Update actual_ctn
                                if ($update_stmt2 = $db->prepare("UPDATE booking SET actual_ctn=? WHERE id=?")) {
                                    $update_stmt2->bind_param('ss', $actual_carton, $id);
                                    $update_stmt2->execute();
                                    $update_stmt2->close();
                                }
                            }
                        }
                    }
                }                
                
                $db->close();
            }
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