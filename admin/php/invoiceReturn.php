<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
}

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$customer = '1';
	$uid = $_SESSION['userID'];
	$totalDiscount = "0.00";

	if ($update_stmt = $db->prepare("SELECT * FROM goods_return WHERE id=?")) {
		$update_stmt->bind_param('s', $id);
        
        // Execute the prepared query.
        if ($update_stmt->execute()) {
            $result2 = $update_stmt->get_result();
            
            if ($row2 = $result2->fetch_assoc()) {
                $customer = $row2['customer'];
				$totalDiscount = $row2['total_amount'];
            }
        }
	}

	$del = "Invoiced";
	$today = date("Y-m-d 00:00:00");

	if ($stmt2 = $db->prepare("UPDATE goods_return SET status=? WHERE id=?")) {
		$stmt2->bind_param('ss', $del, $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			$db->close();
					
			echo json_encode(
				array(
					"status"=> "success", 
					"message"=> "Invoiced"
				)
			);

			/*if ($select_stmt = $db->prepare("SELECT COUNT(*) FROM invoice WHERE created_datetime >= ?")) {
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
						$select_stmt->close();
					}
	
					$charSize = strlen(strval($count));
	
					for($i=0; $i<(3-(int)$charSize); $i++){
						$firstChar.='0';
					}
			
					$firstChar .= strval($count);
	
					if ($insert_stmt = $db->prepare("INSERT INTO invoice (invoice_no, customer, total_amount, created_by) VALUES (?, ?, ?, ?)")) {
						$insert_stmt->bind_param('ssss', $firstChar, $customer, $totalDiscount, $uid);
						
						if(!$insert_stmt->execute()){
							echo json_encode(
								array(
									"status"=> "failed", 
									"message"=> $insert_stmt->error
								)
							);
						}
						else{
							$invid = $insert_stmt->insert_id;;
                        	$insert_stmt->close();
							$item = "Pickup Fees (Return)";

							if ($insert_stmt2 = $db->prepare("INSERT INTO invoice_cart (invoice_id, items, amount) VALUES (?, ?, ?)")) {
								$insert_stmt2->bind_param('sss', $invid, $item, $totalDiscount);
								
								if(!$insert_stmt2->execute()){
									echo json_encode(
										array(
											"status"=> "failed", 
											"message"=> $insert_stmt2->error
										)
									);
								}
								else{
									$insert_stmt2->close();
									$db->close();
					
									echo json_encode(
										array(
											"status"=> "success", 
											"message"=> "Invoiced"
										)
									);
								}
							}
							else{
								echo json_encode(
									array(
										"status"=> "failed", 
										"message"=> "Failed to create invoice cart"
									)
								);
							}
						}
					}
					else{
						echo json_encode(
							array(
								"status"=> "failed", 
								"message"=> "Failed to create invoice"
							)
						);
					}
				}
			}*/
		} 
		else{
		    echo json_encode(
    	        array(
    	            "status"=> "failed", 
    	            "message"=> $stmt2->error
    	        )
    	    );
		}
	} 
	else{
	    echo json_encode(
	        array(
	            "status"=> "failed", 
	            "message"=> "Somthings wrong"
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
