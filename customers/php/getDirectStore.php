<?php
require_once "db_connect.php";

session_start();

if(isset($_GET['search'])){
	$search = $_GET['search'];
    $states = '0';
    $zones = '0';

    $searchQuery = "name like '%$search%'";

    if(isset($_GET['states']) && $_GET['states'] != null && $_GET['states'] != '' && $_GET['states'] != '-'){
        $states = $_GET['states'];
        $searchQuery .= " AND states = '$states'";
    }

    if(isset($_GET['zones']) && $_GET['zones'] != null && $_GET['zones'] != '' && $_GET['zones'] != '-'){
        $zones = $_GET['zones'];
        $searchQuery .= " AND zones = '$zones'";
    }

    if ($update_stmt = $db->prepare("SELECT * FROM outlet WHERE $searchQuery")) {
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
                    "id" => $row['id'],
                    "name" => $row['name']
                );
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
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