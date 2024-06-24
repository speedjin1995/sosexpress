<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['id'], $_POST['reasons'])){
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $reasons = filter_input(INPUT_POST, 'reasons', FILTER_SANITIZE_STRING);
    
    if ($update_stmt = $db->prepare("UPDATE do_request SET reason=? WHERE id=?")){
        $update_stmt->bind_param('ss', $reasons, $id);
        
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
            echo json_encode(
                array(
                    "status"=> "success", 
                    "message"=> "Added Successfully!!"
                )
            );

            $update_stmt->close();
            $db->close();
        }
    } 
    else{
        echo json_encode(
            array(
                "status"=> "failed", 
                "message"=> $update_stmt->error
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