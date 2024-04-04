<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
}
else{
	$custDet = array();
	$checking = array();
	$today = date("Y-m-d 00:00:00");
	$uid = $_SESSION['userID'];

	if ($select_stmt2 = $db->prepare("SELECT distinct(customer) FROM booking WHERE status = 'Invoiced'")) {
		if ($select_stmt2->execute()) {
            $customers = $select_stmt2->get_result();
            
            while($customer = $customers->fetch_assoc()) {
				$total = 0.0;
                // For Booking
				if ($booking_stmt = $db->prepare("SELECT form_no FROM booking WHERE status = 'Invoiced' AND customer = ?")) {
					$booking_stmt->bind_param('s', $customer['customer']);
					
					if ($booking_stmt->execute()) {
						$bookings = $booking_stmt->get_result();
						if(!in_array($customer['customer'], $checking)){
							array_push($custDet, array(
								"customer" => $customer['customer'],
								"total" => 0,
								"booking" => array()
							));

							array_push($checking, $customer['customer']);
						}
						
						while($booking = $bookings->fetch_assoc()) {
							$key = array_search($customer['customer'], $checking);
							array_push($custDet[$key]["booking"], array(
								"itemName" => $booking['form_no'],
								"itemPrice" => 30.00
							));
							$custDet[$key]["total"] += 30;
						}

						// Update status to "Completed"
						if ($update_stmt2 = $db->prepare("UPDATE booking SET status = 'Completed' WHERE status = 'Invoiced' AND customer = ?")) {
							$update_stmt2->bind_param('s', $customer['customer']);
							$update_stmt2->execute();
							$update_stmt2->close(); // Close the statement
						}
					}
				}

				// For DO Request
				if ($do_stmt = $db->prepare("SELECT pricing_details FROM do_request WHERE status = 'Invoiced' AND customer = ?")) {
					$do_stmt->bind_param('s', $customer['customer']);
					
					if ($do_stmt->execute()) {
						$dos = $do_stmt->get_result();
						
						while($do = $dos->fetch_assoc()) {
							if($do['pricing_details'] != null && $do['pricing_details'] != ''){
								$pricings = json_decode($do['pricing_details'], true);
								$key = array_search($customer['customer'], $checking);

								for($i=0; $i<count($pricings); $i++){
									array_push($custDet[$key]["booking"], array(
										"itemName" => $pricings[$i]['size']." X ".$pricings[$i]['unit_price'],
										"itemPrice" => (float)$pricings[$i]['price']
									));

									$custDet[$key]["total"] += (float)$pricings[$i]['price'];
								}
							}
						}

						// Update status to "Completed"
						if ($update_stmt3 = $db->prepare("UPDATE do_request SET status = 'Completed' WHERE status = 'Invoiced' AND customer = ?")) {
							$update_stmt3->bind_param('s', $customer['customer']);
							$update_stmt3->execute();
							$update_stmt3->close(); // Close the statement
						}
					}
				}

				// For Return
				if ($return_stmt = $db->prepare("SELECT return_details FROM goods_return WHERE status = 'Invoiced' AND customer = ?")) {
					$return_stmt->bind_param('s', $customer['customer']);
					
					if ($return_stmt->execute()) {
						$returns = $return_stmt->get_result();
						
						while($return = $returns->fetch_assoc()) {
							if($return['return_details'] != null && $return['return_details'] != ''){
								$pricings = json_decode($return['return_details'], true);
								$key = array_search($customer['customer'], $checking);

								for($i=0; $i<count($pricings); $i++){
									array_push($custDet[$key]["booking"], array(
										"itemName" => $pricings[$i]['grn_no'],
										"itemPrice" => (float)$pricings[$i]['price']
									));

									$custDet[$key]["total"] += (float)$pricings[$i]['price'];
								}
							}
						}

						// Update status to "Completed"
						if ($update_stmt = $db->prepare("UPDATE goods_return SET status = 'Completed' WHERE status = 'Invoiced' AND customer = ?")) {
							$update_stmt->bind_param('s', $customer['customer']);
							$update_stmt->execute();
							$update_stmt->close(); // Close the statement
						}
					}
				}
            }

			for($j=0; $j<count($custDet); $j++){
				if ($select_stmt = $db->prepare("SELECT COUNT(*) FROM invoice WHERE created_datetime >= ?")) {
					$select_stmt->bind_param('s', $today);
					
					// Execute the prepared query.
					if (! $select_stmt->execute()) {
						echo json_encode(
							array(
								"status" => "failed",
								"message" => "Failed to get latest count"
							)); 
					}
					else{
						$result = $select_stmt->get_result();
						$count = 1;
						$firstChar = 'I-'.date("ym")."-";
						
						if ($row = $result->fetch_assoc()) {
							$count = (int)$row['COUNT(*)'] + 1;
							//$select_stmt->close();
						}
		
						$charSize = strlen(strval($count));
		
						for($i=0; $i<(3-(int)$charSize); $i++){
							$firstChar.='0';
						}
				
						$firstChar .= strval($count);
		
						if ($insert_stmt = $db->prepare("INSERT INTO invoice (invoice_no, customer, total_amount, created_by) VALUES (?, ?, ?, ?)")) {
							$insert_stmt->bind_param('ssss', $firstChar, $custDet[$j]['customer'], $custDet[$j]['total'], $uid);
							
							if($insert_stmt->execute()){
								$invid = $insert_stmt->insert_id;;
								//$insert_stmt->close();

								for($k=0; $k<count($custDet[$j]['booking']); $k++){
									if ($insert_stmt2 = $db->prepare("INSERT INTO invoice_cart (invoice_id, items, amount) VALUES (?, ?, ?)")) {
										$insert_stmt2->bind_param('sss', $invid, $custDet[$j]['booking'][$k]['itemName'], $custDet[$j]['booking'][$k]['itemPrice']);
										$insert_stmt2->execute();
									}
								}
							}
						}
					}
				}
			}

			$booking_stmt->close();
			$do_stmt->close();
			$return_stmt->close();
			$select_stmt->close();
			$select_stmt2->close();
			$insert_stmt2->close();
			$db->close();

			echo json_encode(
    	        array(
    	            "status"=> "success", 
    	            "message"=> "Successfully generated"
    	        )
    	    );
        }
		else{
		    echo json_encode(
    	        array(
    	            "status"=> "failed", 
    	            "message"=> $select_stmt->error
    	        )
    	    );
		}
	}
	else{
	    echo json_encode(
	        array(
	            "status"=> "failed", 
	            "message"=> "Cannot pull customer list!!!"
	        )
	    );
	}
} 
?>
