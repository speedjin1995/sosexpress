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

    $phone = null;
    $address = null;

    if(isset($_POST['phone']) && $_POST['phone'] != null && $_POST['phone'] != ''){
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['address']) && $_POST['address'] != null && $_POST['address'] != ''){
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    }

    if($_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE outlet SET name=?, hypermarket=?, states=?, zones=?, phone=?, address=? WHERE id=?")) {
            $update_stmt->bind_param('sssssss', $names, $hypermarket, $states, $zones, $phone, $address, $_POST['id']);
            
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
        if ($insert_stmt = $db->prepare("INSERT INTO outlet (name, hypermarket, states, zones, phone, address) VALUES (?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssss', $names, $hypermarket, $states, $zones, $phone, $address);
            
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