<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();

if(isset($_POST['branch'], $_POST['address'], $_POST['extimated_ctn'])){
	$userId = $_SESSION['userID'];
	$branch = filter_input(INPUT_POST, 'branch', FILTER_SANITIZE_STRING);
	$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
	$extimated_ctn = filter_input(INPUT_POST, 'extimated_ctn', FILTER_SANITIZE_STRING);
	$description = null;

	if($_POST['description'] != null && $_POST['description'] != ''){
		$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		if ($update_stmt = $db->prepare("UPDATE booking SET customer=?, branch=?, pickup_location=?, description=?, estimated_ctn=? WHERE id=?")){
			$update_stmt->bind_param('ssssss', $userId, $branch, $address, $description, $extimated_ctn, $_POST['id']);
		
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
		if ($insert_stmt = $db->prepare("INSERT INTO booking (customer, branch, pickup_location, description, estimated_ctn) VALUES (?, ?, ?, ?, ?)")){
			$insert_stmt->bind_param('sssss', $userId, $branch, $address, $description, $extimated_ctn);
			
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