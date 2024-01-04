<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['returnDate'], $_POST['customerNo'], $_POST['driver'], $_POST['collectionType'], $_POST['totalCarton'], $_POST['totalAmount'])){
	$userId = $_SESSION['userID'];
	$returnDate = filter_input(INPUT_POST, 'returnDate', FILTER_SANITIZE_STRING);
	$customerNo = filter_input(INPUT_POST, 'customerNo', FILTER_SANITIZE_STRING);
	$driver = filter_input(INPUT_POST, 'driver', FILTER_SANITIZE_STRING);
	$lorry = filter_input(INPUT_POST, 'lorry', FILTER_SANITIZE_STRING);
	$collectionType = filter_input(INPUT_POST, 'collectionType', FILTER_SANITIZE_STRING);
	$totalCarton = filter_input(INPUT_POST, 'totalCarton', FILTER_SANITIZE_STRING);
	$totalAmount = filter_input(INPUT_POST, 'totalAmount', FILTER_SANITIZE_STRING);

	$collectionDate = null;
	$return_type = "return";
	$today = date("Y-m-d 00:00:00");
	$returnDate = $returnDate." 00:00:00";

	$grn_no = $_POST['grn_no'];
	$hypermarket = $_POST['hypermarket'];
	$location = $_POST['location'];
	$carton = $_POST['carton'];
	$reason = $_POST['reason'];
	$other_reason = $_POST['other_reason'];
	$warehouse = $_POST['warehouse'];
	$price = $_POST['price'];
	$return_details = array();

	for($i=0; $i<count($grn_no); $i++){
		$res = '0';
		$others = '';

		if($reason[$i] == 'Others'){
			$others = $other_reason[$i];
		}
		else{
			$res = $reason[$i];
			$others = '';
		}

		$return_details[] = array(
			"grn_no" => $grn_no[$i],
			"hypermarket" => $hypermarket[$i],
			"location" => $location[$i],
			"carton" => $carton[$i],
			"warehouse" => $warehouse[$i],
			"price" => $price[$i],
			"reason" => $res,
			"other_reason" => $others
		);
	}

	if(isset($_POST['collectionDate']) && $_POST['collectionDate'] != null && $_POST['collectionDate'] != ''){
		$collectionDate = filter_input(INPUT_POST, 'collectionDate', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		if ($update_stmt = $db->prepare("UPDATE goods_return SET return_date=?, customer=?, vehicle=?, driver=?, return_details=?, total_carton=?, total_amount=?, collection_date=?, collection_type=?
		, return_type=? WHERE id=?")){
			$update_stmt->bind_param('sssssssssss', $returnDate, $customerNo, $lorry, $driver, $data, $totalCarton, $totalAmount, $collectionDate, $collectionType, $return_type, $_POST['id']);
		
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
				$db->close();
				
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Updated Successfully!!" 
					)
				);
			}
		}
		else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> $insert_stmt->error
				)
			);
		}
	}
	else{
		if ($select_stmt = $db->prepare("SELECT COUNT(*) FROM goods_return WHERE return_date >= ?")) {
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
				$firstChar = 'GR-'.date("ym")."-";
				
				if ($row = $result->fetch_assoc()) {
					$count = (int)$row['COUNT(*)'] + 1;
					$select_stmt->close();
				}

				$charSize = strlen(strval($count));

				for($i=0; $i<(3-(int)$charSize); $i++){
					$firstChar.='0';
				}
		
				$firstChar .= strval($count);

				if ($insert_stmt = $db->prepare("INSERT INTO goods_return (GR_No, return_date, customer, vehicle, driver, return_details, total_carton, total_amount, collection_date, collection_type, return_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
					$data = json_encode($return_details);
					$insert_stmt->bind_param('sssssssssss', $firstChar, $returnDate, $customerNo, $lorry, $driver, $data, $totalCarton, $totalAmount, $collectionDate, $collectionType, $return_type);
					
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
						$db->close();
				
						echo json_encode(
							array(
								"status"=> "success", 
								"message"=> "Added Successfully"
							)
						);
					}
				}
				else{
					echo json_encode(
						array(
							"status"=> "failed", 
							"message"=> "Failed to create GR"
						)
					);
				}
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