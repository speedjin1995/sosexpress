<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
}

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$del = "Cancelled";
	$del2 = "1";
	
	if ($stmt2 = $db->prepare("UPDATE do_request SET status=?, deleted=? WHERE id=?")) {
		$stmt2->bind_param('sss', $del, $del2, $id);
	
		if ($stmt2->execute()) {
			$stmt2->close();
	
			// Retrieve details from do_request
			if ($select_stmt = $db->prepare("SELECT booking_date, customer, actual_carton FROM do_request WHERE id=?")) {
				$select_stmt->bind_param('s', $id);
				if ($select_stmt->execute()) {
					$result2 = $select_stmt->get_result();
					if ($row2 = $result2->fetch_assoc()) {
						$select_stmt->close();
	
						// Construct booking date for query
						$booking_date2 = DateTime::createFromFormat('Y-m-d H:i:s', $row2['booking_date']);
						$booking_date2 = $booking_date2->format('Y-m-d 00:00:00');
	
						// Query booking table
						if ($update_stmt3 = $db->prepare("SELECT id, actual_ctn FROM booking WHERE customer=? AND booking_date >= ?")) {
							$update_stmt3->bind_param('ss', $row2['customer'], $booking_date2);
							if ($update_stmt3->execute()) {
								$result3 = $update_stmt3->get_result();
								if ($row3 = $result3->fetch_assoc()) {
									$id2 = $row3['id'];
									$existing_actual_ctn = $row3['actual_ctn'];
									$update_stmt3->close();
	
									// Calculate updated actual_ctn
									$actual_carton_diff = $row2['actual_carton'];
									if (!empty($existing_actual_ctn)) {
										$actual_carton_diff = $existing_actual_ctn - $actual_carton_diff;
									}
	
									// Update actual_ctn in booking table
									if ($update_stmt4 = $db->prepare("UPDATE booking SET actual_ctn=actual_ctn-? WHERE id=?")) {
										$update_stmt4->bind_param('ss', $actual_carton_diff, $id2);
										if ($update_stmt4->execute()) {
											$update_stmt4->close();
	
											// Final response
											echo json_encode(array("status" => "success", "message" => "Deleted"));
										} 
										else {
											echo json_encode(array("status" => "failed", "message" => $update_stmt4->error));
										}
									} 
									else {
										echo json_encode(array("status" => "failed", "message" => "Failed to prepare update statement"));
									}
								} 
								else {
									echo json_encode(array("status" => "failed", "message" => "No records found in booking table"));
								}
							} 
							else {
								echo json_encode(array("status" => "failed", "message" => $update_stmt3->error));
							}
						} 
						else {
							echo json_encode(array("status" => "failed", "message" => "Failed to prepare select statement for booking table"));
						}
					} 
					else {
						echo json_encode(array("status" => "failed", "message" => "No records found in do_request table"));
					}
				} 
				else {
					echo json_encode(array("status" => "failed", "message" => $select_stmt->error));
				}
			} 
			else {
				echo json_encode(array("status" => "failed", "message" => "Failed to prepare select statement for do_request table"));
			}
		} 
		else {
			echo json_encode(array("status" => "failed", "message" => $stmt2->error));
		}
	} 
	else {
		echo json_encode(array("status" => "failed", "message" => "Failed to prepare update statement for do_request table"));
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
