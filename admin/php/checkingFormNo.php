<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
}

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

	if ($stmt2 = $db->prepare("SELECT form_no FROM booking WHERE id=?")) {
		$stmt2->bind_param('s', $id);
		
		if($stmt2->execute()){
			$result = $stmt2->get_result();

			if ($row = $result->fetch_assoc()) {
				if($row['form_no'] != null && $row['form_no'] != ''){
					$stmt2->close();
					$db->close();

					echo json_encode(
						array(
							"status" => "success",
							"message" => "Found booking"
						)
					); 
				}
                else{
					$stmt2->close();
					$db->close();

					echo json_encode(
						array(
							"status" => "failed",
							"message" => "Please make a form no. first."
						)
					); 
				}
            }
			else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something wrong with retrieve form no."
					)
				); 
            }
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
