<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['customerNo'], $_POST['inputDate'], $_POST['totalAmount'])){
    $customerNo = filter_input(INPUT_POST, 'customerNo', FILTER_SANITIZE_STRING);
    $inputDate = filter_input(INPUT_POST, 'inputDate', FILTER_SANITIZE_STRING);
    $totalAmount = filter_input(INPUT_POST, 'totalAmount', FILTER_SANITIZE_STRING);

    $purchaseId = $_POST['purchaseId'];
    $itemName = $_POST['itemName'];
    $itemPrice = $_POST['itemPrice'];
    $today = date("Y-m-d 00:00:00");
    $uid = $_SESSION['userID'];

    if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE transporters SET transporter_code=?, transporter_name=?, transporter_price=? WHERE id=?")) {
            $update_stmt->bind_param('ssss', $code, $transporter, $price, $_POST['id']);
            
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
        if ($select_stmt = $db->prepare("SELECT COUNT(*) FROM invoice WHERE created_datetime >= ?")) {
            $select_stmt->bind_param('s', $today);
            
            // Execute the prepared query.
            if (! $select_stmt->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Failed to get latest count"
                    )); 
            }
            else{
                $result = $select_stmt->get_result();
                $count = 1;
                $firstChar = 'I'.date("Ymd");
                
                if ($row = $result->fetch_assoc()) {
                    $count = (int)$row['COUNT(*)'] + 1;
                    $select_stmt->close();
                }

                $charSize = strlen(strval($count));

                for($i=0; $i<(4-(int)$charSize); $i++){
                    $firstChar.='0';
                }
        
                $firstChar .= strval($count);

                if ($insert_stmt = $db->prepare("INSERT INTO invoice (invoice_no, customer, total_amount, created_by) VALUES (?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssss', $firstChar, $customerNo, $totalAmount, $uid);
                    
                    if(!$insert_stmt->execute()){
                        echo json_encode(
                            array(
                                "status"=> "failed", 
                                "message"=> $insert_stmt->error
                            )
                        );
                    }
                    else{
                        $invid = $insert_stmt->insert_id;;
                        $insert_stmt->close();
                        $success = true;

                        for($i=0; $i<count($purchaseId); $i++){
                            if ($insert_stmt2 = $db->prepare("INSERT INTO invoice_cart (invoice_id, items, amount) VALUES (?, ?, ?)")) {
                                $insert_stmt2->bind_param('sss', $invid, $itemName[$i], $itemPrice[$i]);
                                
                                if(!$insert_stmt2->execute()){
                                    $success = false;
                                }
                            }
                        }

                        if($success){
                            $insert_stmt2->close();
                            $db->close();
            
                            echo json_encode(
                                array(
                                    "status"=> "success", 
                                    "message"=> "Invoiced"
                                )
                            );
                        }
                        else{
                            echo json_encode(
                                array(
                                    "status"=> "failed", 
                                    "message"=> $insert_stmt2->error
                                )
                            );
                        }
                    }
                }
                else{
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> "Failed to create invoice"
                        )
                    );
                }
            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to new invoice no"
                )
            );
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