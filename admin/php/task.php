<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['taskType'], $_POST['bookingDate'])){
	$userId = $_SESSION['userID'];
	$taskType = filter_input(INPUT_POST, 'taskType', FILTER_SANITIZE_STRING);
	$bookingDate = filter_input(INPUT_POST, 'bookingDate', FILTER_SANITIZE_STRING);
	$bookingDate = DateTime::createFromFormat('d/m/Y', $bookingDate)->format('Y-m-d H:i:s');

	$today = date("Y-m-d 00:00:00");
	$driver = null;
	$lorry = null;
	$customerNo = null;
	$hypermarket = null;
	$states = null;
	$zones = null;
	$outlets = null;
	$rtvgrn = null;
	$description = null;
	

	if(isset($_POST['driver']) && $_POST['driver'] != null && $_POST['driver'] != ''){
		$driver = filter_input(INPUT_POST, 'driver', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['lorry']) && $_POST['lorry'] != null && $_POST['lorry'] != ''){
		$lorry = filter_input(INPUT_POST, 'lorry', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['customerNo']) && $_POST['customerNo'] != null && $_POST['customerNo'] != ''){
		$customerNo = filter_input(INPUT_POST, 'customerNo', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['hypermarket']) && $_POST['hypermarket'] != null && $_POST['hypermarket'] != ''){
		$hypermarket = filter_input(INPUT_POST, 'hypermarket', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['states']) && $_POST['states'] != null && $_POST['states'] != ''){
		$states = filter_input(INPUT_POST, 'states', FILTER_SANITIZE_STRING);
	}
	
	if(isset($_POST['zones']) && $_POST['zones'] != null && $_POST['zones'] != ''){
		$zones = filter_input(INPUT_POST, 'zones', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['outlets']) && $_POST['outlets'] != null && $_POST['outlets'] != ''){
		$outlets = filter_input(INPUT_POST, 'outlets', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['rtvgrn']) && $_POST['rtvgrn'] != null && $_POST['rtvgrn'] != ''){
		$rtvgrn = filter_input(INPUT_POST, 'rtvgrn', FILTER_SANITIZE_STRING);
	}
	
	if(isset($_POST['description']) && $_POST['description'] != null && $_POST['description'] != ''){
		$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		if ($update_stmt = $db->prepare("UPDATE tasks SET type=?, customer=?, vehicle_no=?, driver_name=?, states=?, hypermarket=?, 
		zones=?, outlet=?, booking_date=?, code=?, remark=? WHERE id=?")){
			$update_stmt->bind_param('ssssssssssss', $taskType, $customerNo, $lorry, $driver, $states, $hypermarket, $zones, 
			$outlets, $bookingDate, $rtvgrn, $description, $_POST['id']);
		
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
		if ($insert_stmt = $db->prepare("INSERT INTO tasks (type, customer, vehicle_no, driver_name, states, hypermarket, zones, 
		outlet, booking_date, code, remark) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
			$insert_stmt->bind_param('sssssssssss', $taskType, $customerNo, $lorry, $driver, $states, $hypermarket, $zones, 
			$outlets, $bookingDate, $rtvgrn, $description);
			
			if(!$insert_stmt->execute()){
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
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}

?>