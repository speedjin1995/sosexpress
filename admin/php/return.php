<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['returnDate'], $_POST['customerNo'], $_POST['totalCarton'], $_POST['totalAmount'])){
	$userId = $_SESSION['userID'];
	$returnDate = filter_input(INPUT_POST, 'returnDate', FILTER_SANITIZE_STRING);
	$customerNo = filter_input(INPUT_POST, 'customerNo', FILTER_SANITIZE_STRING);
	$totalCarton = filter_input(INPUT_POST, 'totalCarton', FILTER_SANITIZE_STRING);
	$totalAmount = filter_input(INPUT_POST, 'totalAmount', FILTER_SANITIZE_STRING);

	$driver = null;
	$lorry = null;
	$collectionType = "Self Collect";
	$collectionDate = null;
	$return_type = "return";
	$today = date("Y-m-01 00:00:00");
	$returnDate = $returnDate." 00:00:00";
	$status = 'Created';

	//$grn_no = $_POST['grn_no'];
	//$hypermarket = $_POST['hypermarket'];
	//$location = $_POST['location'];
	//$carton = $_POST['carton'];
	//$reason = $_POST['reason'];
	$other_reason = $_POST['other_reason'];
	//$warehouse = $_POST['warehouse'];
	//$price = $_POST['price'];
	$return_details = array();

	if(isset($_POST['grn_no'])){
        $grn_no = $_POST['grn_no'];
    }

    if(isset($_POST['hypermarket'])){
        $hypermarket = $_POST['hypermarket'];
    }

    if(isset($_POST['location'])){
        $location = $_POST['location'];
    }

    if(isset($_POST['carton'])){
        $carton = $_POST['carton'];
    }

    if(isset($_POST['reason'])){
        $reason = $_POST['reason'];
    }
    
    if(isset($_POST['warehouse'])){
        $warehouse = $_POST['warehouse'];
    }

	if(isset($_POST['price'])){
        $price = $_POST['price'];
    }

	if(isset($grn_no) && $grn_no != null && count($grn_no) > 0){
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
	}

	if(isset($_POST['driver']) && $_POST['driver'] != null && $_POST['driver'] != ''){
		$driver = filter_input(INPUT_POST, 'driver', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['lorry']) && $_POST['lorry'] != null && $_POST['lorry'] != ''){
		$lorry = filter_input(INPUT_POST, 'lorry', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['collectionType']) && $_POST['collectionType'] != null && $_POST['collectionType'] != ''){
		$collectionType = filter_input(INPUT_POST, 'collectionType', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['collectionDate']) && $_POST['collectionDate'] != null && $_POST['collectionDate'] != ''){
		$collectionDate = filter_input(INPUT_POST, 'collectionDate', FILTER_SANITIZE_STRING);
		$status = 'Collected';
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		if ($update_stmt = $db->prepare("UPDATE goods_return SET return_date=?, customer=?, vehicle=?, driver=?, return_details=?, total_carton=?, total_amount=?, collection_date=?, collection_type=?
		, return_type=?, status=? WHERE id=?")){
			$data = json_encode($return_details);
			$update_stmt->bind_param('ssssssssssss', $returnDate, $customerNo, $lorry, $driver, $data, $totalCarton, $totalAmount, $collectionDate, $collectionType, $return_type, $status, $_POST['id']);
		
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

				if ($insert_stmt = $db->prepare("INSERT INTO goods_return (GR_No, return_date, customer, vehicle, driver, return_details, total_carton, total_amount, collection_date, collection_type, return_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
					$data = json_encode($return_details);
					$insert_stmt->bind_param('ssssssssssss', $firstChar, $returnDate, $customerNo, $lorry, $driver, $data, $totalCarton, $totalAmount, $collectionDate, $collectionType, $return_type, $status);
					
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