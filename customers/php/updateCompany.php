<?php
require_once 'db_connect.php';

if(isset($_POST['id'], $_POST['name'], $_POST['reg_no'], $_POST['phone'], $_POST['address'], $_POST['email'])){
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
	$reg_no = filter_input(INPUT_POST, 'reg_no', FILTER_SANITIZE_STRING);
	$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
	$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
	$pic = null;
	$shortname = null;

	if($_POST['pic'] != null && $_POST['pic'] != ""){
		$pic = filter_input(INPUT_POST, 'pic', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['shortname'] != null && $_POST['shortname'] != ""){
		$shortname = filter_input(INPUT_POST, 'shortname', FILTER_SANITIZE_STRING);
	}

	if ($stmt2 = $db->prepare("UPDATE customers SET customer_name=?, short_name=?, reg_no=?, pic=?, customer_address=?, customer_phone=?, customer_email=? WHERE id=?")) {
		$stmt2->bind_param('ssssssss', $name, $shortname, $reg_no, $pic, $address, $phone, $email, $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			$db->close();
			
			echo json_encode(
				array(
					"status"=> "success", 
					"message"=> "Your company profile is updated successfully!" 
				)
			);
		} else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> $stmt->error
				)
			);
		}
	} 
	else{
		echo json_encode(
			array(
				"status"=> "failed", 
				"message"=> "Something went wrong!"
			)
		);
	}
} 
else{
	echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all fields"
        )
    ); 
}
?>
