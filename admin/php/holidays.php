<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['holiday'], $_POST['startDate'], $_POST['endDate'])){
    $holiday = filter_input(INPUT_POST, 'holiday', FILTER_SANITIZE_STRING);
    $startDate = filter_input(INPUT_POST, 'startDate', FILTER_SANITIZE_STRING);
    $endDate = filter_input(INPUT_POST, 'endDate', FILTER_SANITIZE_STRING);

    $start_date = DateTime::createFromFormat('d/m/Y', $startDate)->format('Y-m-d 00:00:00');
    $end_date = DateTime::createFromFormat('d/m/Y', $endDate)->format('Y-m-d 00:00:00');

    if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE holidays SET holiday=?, start_date=?, end_date=? WHERE id=?")) {
            $update_stmt->bind_param('ssss', $holiday, $start_date, $end_date, $_POST['id']);
            
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
        if ($insert_stmt = $db->prepare("INSERT INTO holidays (holiday, start_date, end_date) VALUES (?, ?, ?)")) {
            $insert_stmt->bind_param('sss', $holiday, $start_date, $end_date);
            
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