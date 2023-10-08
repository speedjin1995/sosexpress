<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
}

if(isset($_POST['id'],$_POST['status'])){
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
	$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

	$arrayOfId = explode(",", $id);
	$success = true;

	for($i=0; $i<count($arrayOfId); $i++){
		if ($stmt2 = $db->prepare("UPDATE booking SET status=? WHERE id=?")) {
			$stmt2->bind_param('ss', $status, $arrayOfId[$i]);
			
			if(!$stmt2->execute()){
				$success = false;
			}
		}
		else{
			$success = false;
		}
	}

	$stmt2->close();
	$db->close();

	if($success){
		echo json_encode(
			array(
				"status"=> "success", 
				"message"=> "Update successfull"
			)
		);
	}
	else{
		echo json_encode(
			array(
				"status"=> "failed", 
				"message"=> "Something wrong when update"
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
