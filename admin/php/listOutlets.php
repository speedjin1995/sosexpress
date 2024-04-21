<?php
require_once "db_connect.php";

session_start();

if (isset($_POST['states'], $_POST['zones'], $_POST['hypermarket'])) {
    $hypermarket = filter_input(INPUT_POST, 'hypermarket', FILTER_SANITIZE_STRING);
    $states = filter_input(INPUT_POST, 'states', FILTER_SANITIZE_STRING);
    $zones = filter_input(INPUT_POST, 'zones', FILTER_SANITIZE_STRING);

    // Query the zones table to check the value of the zones column
    $check_zones_stmt = $db->prepare("SELECT * FROM zones WHERE id=? AND zones <> '-'");
    $check_zones_stmt->bind_param('s', $zones);
    $check_zones_stmt->execute();
    $check_zones_result = $check_zones_stmt->get_result();

    // Check if the zones column contains "-"
    if ($check_zones_result->num_rows > 0) {
        // Zone is not "-", so execute the query with the zones condition
        $query = "SELECT * FROM outlet WHERE hypermarket=? AND states=? AND zones=?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('sss', $hypermarket, $states, $zones);
    } 
    else {
        // Zone is "-", so execute the query without the zones condition
        $query = "SELECT * FROM outlet WHERE hypermarket=? AND states=?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('ss', $hypermarket, $states);
    }

    // Execute the prepared query
    if ($stmt->execute()) {
        $result = $stmt->get_result();
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
                "message" => $message,
                "check" => $check_zones_result->num_rows
            )
        );
    } else {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Something went wrong"
            )
        );
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