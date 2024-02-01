<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['states'], $_POST['zones'], $_POST['hypermarket'])){
	$hypermarket = filter_input(INPUT_POST, 'hypermarket', FILTER_SANITIZE_STRING);
    $states = filter_input(INPUT_POST, 'states', FILTER_SANITIZE_STRING);
    $zones = filter_input(INPUT_POST, 'zones', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM outlet WHERE hypermarket=? AND states=? AND zones=?")) {
        $update_stmt->bind_param('sss', $hypermarket, $states, $zones);
        
        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                )); 
        }
        else{
            $result = $update_stmt->get_result();
            $message = array();
            
            while ($row = $result->fetch_assoc()) {
                $message[] = array(
                    'id' => $row['id'],
                    'name' => $row['name']
                );
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
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
            )); 
}
?>