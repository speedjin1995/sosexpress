<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['names'], $_POST['hypermarket'], $_POST['states'], $_POST['zones'])){
    $names = filter_input(INPUT_POST, 'names', FILTER_SANITIZE_STRING);
    $hypermarket = filter_input(INPUT_POST, 'hypermarket', FILTER_SANITIZE_STRING);
    $states = filter_input(INPUT_POST, 'states', FILTER_SANITIZE_STRING);
    $zones = filter_input(INPUT_POST, 'zones', FILTER_SANITIZE_STRING);

    if($_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE outlet SET name=?, hypermarket=?, states=?, zones=? WHERE id=?")) {
            $update_stmt->bind_param('sssss', $names, $hypermarket, $states, $zones, $_POST['id']);
            
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
                $db->close();
                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }
        }
    }
    else{
        if ($insert_stmt = $db->prepare("INSERT INTO outlet (name, hypermarket, states, zones) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssss', $names, $hypermarket, $states, $zones);
            
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
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