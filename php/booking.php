<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['pickup_method'], $_POST['customerNo'], $_POST['extimated_ctn'])){
	$userId = $_SESSION['userID'];
	$pickup_method = filter_input(INPUT_POST, 'pickup_method', FILTER_SANITIZE_STRING);
	$customerNo = filter_input(INPUT_POST, 'customerNo', FILTER_SANITIZE_STRING);
	$extimated_ctn = filter_input(INPUT_POST, 'extimated_ctn', FILTER_SANITIZE_STRING);
	$branch = null;
	$address = null;
	$description = null;
	$actual_ctn = null;
	$gate = null;
	$checker = null;
	$vehicleNoTxt = null;
	$internal_notes = null;
	$form_no = null;
	$col_goods = filter_input(INPUT_POST, 'col_goods', FILTER_SANITIZE_STRING);
	$col_chk = filter_input(INPUT_POST, 'col_chk', FILTER_SANITIZE_STRING);

	if(isset($_POST['branch']) && $_POST['branch'] != null && $_POST['branch'] != ''){
		$branch = filter_input(INPUT_POST, 'branch', FILTER_SANITIZE_STRING);
	}

	if($_POST['address'] != null && $_POST['address'] != ''){
		$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
	}

	if($_POST['description'] != null && $_POST['description'] != ''){
		$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
	}

	if($_POST['internal_notes'] != null && $_POST['internal_notes'] != ''){
		$internal_notes = filter_input(INPUT_POST, 'internal_notes', FILTER_SANITIZE_STRING);
	}

	if($_POST['actual_ctn'] != null && $_POST['actual_ctn'] != ''){
		$actual_ctn = filter_input(INPUT_POST, 'actual_ctn', FILTER_SANITIZE_STRING);
	}

	if($_POST['gate'] != null && $_POST['gate'] != ''){
		$gate = filter_input(INPUT_POST, 'gate', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['checker']) && $_POST['checker'] != null && $_POST['checker'] != ''){
		$checker = filter_input(INPUT_POST, 'checker', FILTER_SANITIZE_STRING);
	}

	if($_POST['vehicleNoTxt'] != null && $_POST['vehicleNoTxt'] != ''){
		$vehicleNoTxt = filter_input(INPUT_POST, 'vehicleNoTxt', FILTER_SANITIZE_STRING);
	}

	if($_POST['form_no'] != null && $_POST['form_no'] != ''){
		$form_no = filter_input(INPUT_POST, 'form_no', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		if ($update_stmt = $db->prepare("UPDATE booking SET pickup_method=?, customer=?, pickup_location=?, description=?, estimated_ctn=?, actual_ctn=?, vehicle_no=?, col_goods=?
		, col_chq=?, form_no=?, gate=?, checker=?, internal_notes=? WHERE id=?")){
			$update_stmt->bind_param('ssssssssssssss', $pickup_method, $customerNo, $address, $description, $extimated_ctn, $actual_ctn, $vehicleNoTxt, $col_goods,
			$col_chk, $form_no, $gate, $checker, $internal_notes, $_POST['id']);
		
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
		if ($insert_stmt = $db->prepare("INSERT INTO booking (pickup_method, customer, branch, pickup_location, description, estimated_ctn, actual_ctn, vehicle_no, 
		col_goods, col_chq, form_no, gate, checker, internal_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
			$insert_stmt->bind_param('ssssssssssssss', $pickup_method, $customerNo, $branch, $address, $description, $extimated_ctn, $actual_ctn,
			$vehicleNoTxt, $col_goods, $col_chk, $form_no, $gate, $checker, $internal_notes);
			
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