<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['purchasesID'])){
    $purchasesID = filter_input(INPUT_POST, 'purchasesID', FILTER_SANITIZE_STRING);
    //$cancelled_datetime = date("Y-m-d H:i:s");

    if ($update_stmt = $db->prepare("DELETE FROM invoice WHERE id = ?")) {
        $update_stmt->bind_param('i',  $purchasesID);
        
        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $update_stmt->error
                )
            );
        }
        else{
            $update_stmt->close();

            //$name = $_SESSION['name'];
            //$userId = $_SESSION['userID'];
            //$today = date("Y-m-d H:i:s");

            /*$action = "User : ".$name." Cancel Purchase: ".$purchasesID." in purchases table!";

            if ($log_insert_stmt = $db->prepare("INSERT INTO log (userId, userName, created_dateTime, action) VALUES (?,?,?,?)")) {
                $log_insert_stmt->bind_param('ssss', $userId, $name, $today, $action);
            
                if (! $log_insert_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> $log_insert_stmt->error 
                        )
                    );
                }
                else{
                    $log_insert_stmt->close();
                }
            }*/


            $db->close();
            
            echo json_encode(
                array(
                    "status"=> "success", 
                    "message"=> "Cancel Successfully!!" 
                )
            );
        }
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
        )
    ); 
}
?>