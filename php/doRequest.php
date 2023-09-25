<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['bookingDate'], $_POST['deliveryDate'], $_POST['cancellationDate'], $_POST['customerNo']
, $_POST['hypermarket'], $_POST['states'], $_POST['zones'], $_POST['outlets'], $_POST['do_type']
, $_POST['actual_ctn'], $_POST['need_grn'])){
	$booking_date = filter_input(INPUT_POST, 'bookingDate', FILTER_SANITIZE_STRING);
	$delivery_date = filter_input(INPUT_POST, 'deliveryDate', FILTER_SANITIZE_STRING);
	$cancellation_date = filter_input(INPUT_POST, 'cancellationDate', FILTER_SANITIZE_STRING);
	$customer = filter_input(INPUT_POST, 'customerNo', FILTER_SANITIZE_STRING);
	$hypermarket = filter_input(INPUT_POST, 'hypermarket', FILTER_SANITIZE_STRING);
	$states = filter_input(INPUT_POST, 'states', FILTER_SANITIZE_STRING);
	$zone = filter_input(INPUT_POST, 'zones', FILTER_SANITIZE_STRING);
	$outlet = filter_input(INPUT_POST, 'outlets', FILTER_SANITIZE_STRING);
	$do_type = filter_input(INPUT_POST, 'do_type', FILTER_SANITIZE_STRING);
	$actual_carton = filter_input(INPUT_POST, 'actual_ctn', FILTER_SANITIZE_STRING);
    $need_grn = filter_input(INPUT_POST, 'need_grn', FILTER_SANITIZE_STRING);

	$do_number = null;
	$po_number = null;
	$note = null;
	$loading_time = null;

    $booking_date = DateTime::createFromFormat('d/m/Y', str_replace(',', '', explode(" ", $booking_date)[0]))->format('Y-m-d 00:00:00');
    $delivery_date = DateTime::createFromFormat('d/m/Y', str_replace(',', '', explode(" ", $delivery_date)[0]))->format('Y-m-d 00:00:00');
	$cancellation_date = DateTime::createFromFormat('d/m/Y', str_replace(',', '', explode(" ", $cancellation_date)[0]))->format('Y-m-d 00:00:00');

    if(isset($_POST['do_number']) && $_POST['do_number'] != null && $_POST['do_number'] != ''){
        $do_number = filter_input(INPUT_POST, 'do_number', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['po_number']) && $_POST['po_number'] != null && $_POST['po_number'] != ''){
        $po_number = filter_input(INPUT_POST, 'po_number', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['note']) && $_POST['note'] != null && $_POST['note'] != ''){
        $note = filter_input(INPUT_POST, 'note', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['loading_time']) && $_POST['loading_time'] != null && $_POST['loading_time'] != ''){
        $loading_time = filter_input(INPUT_POST, 'loading_time', FILTER_SANITIZE_STRING);
    }
	

    if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE count SET vehicleNo=?, lotNo=?, batchNo=?, invoiceNo=?, deliveryNo=?, purchaseNo=?, customer=?, productName=?, package=?
        , unitWeight=?, tare=?, currentWeight=?, actualWeight=?, unit=?, moq=?, unitPrice=?, totalPrice=?, remark=?, status=?, totalPCS=?, dateTime=? WHERE serialNo=?")){
            $update_stmt->bind_param('ssssssssssssssssssssss', $vehicleNo, $lotNo, $batchNo, $invoiceNo, $deliveryNo, $purchaseNo, $customerNo, $product
            , $package, $unitWeight1, $tareWeight, $currentWeight, $actualWeight, $unitWeight, $moq, $unitPrice, $totalPrice, $remark, $status
            , $totalPCS, $dateTime, $_POST['serialNumber']);
            
            // Execute the prepared query.
            if (! $update_stmt->execute()){

                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );

            } else{

                $update_stmt->close();
                $db->close();
                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );

            }
        } else{

            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $update_stmt->error
                )
            );
        }
    
    }else{
        if ($insert_stmt = $db->prepare("INSERT INTO do_request (booking_date, delivery_date, cancellation_date, customer
        , hypermarket, states, zone, outlet, do_type, do_number, po_number, note, actual_carton, need_grn, loading_time) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
            $insert_stmt->bind_param('sssssssssssssss', $booking_date, $delivery_date, $cancellation_date, $customer, $hypermarket
            , $states, $zone, $outlet, $do_type, $do_number, $po_number, $note, $actual_carton, $need_grn, $loading_time);
            
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
                $db->close();
                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );

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