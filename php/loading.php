<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['id'], $_POST['totalAmount'])){
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $totalAmount = filter_input(INPUT_POST, 'totalAmount', FILTER_SANITIZE_STRING);

    $sent_date = null;
	$back_date = null;
    $grn_receive = null;
    $pricing_details = array();
    $pricing = '';

    for($i=0; $i<count($particular); $i++){
		$pricing_details[] = array(
			"particular" => $particular[$i],
			"quantity_in" => $quantity_in[$i],
			"quantity_delivered" => $quantity_delivered[$i],
			"size" => $size[$i],
			"unit_price" => $unit_price[$i],
			"price" => $price[$i],
		);
	}

    $pricing = json_encode($pricing_details);

    if(isset($_POST['sentOnDate']) && $_POST['sentOnDate'] != null && $_POST['sentOnDate'] != ''){
        $sent_date = filter_input(INPUT_POST, 'sentOnDate', FILTER_SANITIZE_STRING);
        $sent_date = $sent_date.' 00:00:00';
    }

    if(isset($_POST['backOnDate']) && $_POST['backOnDate'] != null && $_POST['backOnDate'] != ''){
        $back_date = filter_input(INPUT_POST, 'backOnDate', FILTER_SANITIZE_STRING);
        $back_date = $back_date.' 00:00:00';
    }

    if(isset($_POST['grn_received']) && $_POST['grn_received'] != null && $_POST['grn_received'] != ''){
        $grn_receive = filter_input(INPUT_POST, 'grn_received', FILTER_SANITIZE_STRING);
    }
    
    if ($update_stmt = $db->prepare("UPDATE do_request SET sent_date=?, back_date=?, grn_receive=?, pricing_details=?, total_price=? WHERE id=?")){
        $update_stmt->bind_param('ssssss', $sent_date, $back_date, $grn_receive, $pricing, $totalAmount, $id);
        
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
            echo json_encode(
                array(
                    "status"=> "success", 
                    "message"=> "Added Successfully!!"
                )
            );

            $update_stmt->close();
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
else{

    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
    
}

?>