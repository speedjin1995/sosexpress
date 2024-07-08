<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['id'], $_POST['totalAmount'])){
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $totalAmount = filter_input(INPUT_POST, 'totalAmount', FILTER_SANITIZE_STRING);
    $payment_term = filter_input(INPUT_POST, 'payment_term', FILTER_SANITIZE_STRING);

    $sent_date = null;
	$back_date = null;
    $grn_receive = null;
    $noting = null;
    $pricing_details = array();
    $pricing = '';

    if(isset($_POST['jsonDataField']) && $_POST['jsonDataField'] != null && $_POST['jsonDataField'] != ''){
        $do_details = filter_input(INPUT_POST, 'jsonDataField', FILTER_SANITIZE_STRING);
        $do_details = html_entity_decode($do_details);
    }

    if(isset($_POST['particular'])){
        $particular = $_POST['particular'];
    }

    if(isset($_POST['quantity_in'])){
        $quantity_in = $_POST['quantity_in'];
    }

    if(isset($_POST['quantity_delivered'])){
        $quantity_delivered = $_POST['quantity_delivered'];
    }

    if(isset($_POST['size'])){
        $size = $_POST['size'];
    }

    if(isset($_POST['unit_price'])){
        $unit_price = $_POST['unit_price'];
    }
    
    if(isset($_POST['price'])){
        $price = $_POST['price'];
    }

    if(isset($_POST['unit'])){
        $unit = $_POST['unit'];
    }

    if(isset($particular) && $particular != null && count($particular) > 0){
        for($i=0; $i<count($particular); $i++){
            $notes = '';

            if(isset($particular[$i]) && $particular[$i] != null && $particular[$i]!=''){
                $notes = $particular[$i];
            }

            $pricing_details[] = array(
                "particular" => $notes,
                "quantity_in" => $quantity_in[$i],
                "size" => $size[$i],
                "unit_price" => $unit_price[$i],
                "price" => $price[$i],
                "unit" => $unit[$i]
            );
        }
    }

    $pricing = json_encode($pricing_details);

    if(isset($_POST['sentOnDate']) && $_POST['sentOnDate'] != null && $_POST['sentOnDate'] != ''){
        $sent_date = filter_input(INPUT_POST, 'sentOnDate', FILTER_SANITIZE_STRING);
        $sent_date = $sent_date.' 00:00:00';
    }

    if(isset($_POST['backOnDate']) && $_POST['backOnDate'] != null && $_POST['backOnDate'] != ''){
        $back_date = filter_input(INPUT_POST, 'backOnDate', FILTER_SANITIZE_STRING);
        $back_date = $back_date.' 00:00:00';
    }

    if(isset($_POST['grn_received']) && $_POST['grn_received'] != null && $_POST['grn_received'] != ''){
        $grn_receive = filter_input(INPUT_POST, 'grn_received', FILTER_SANITIZE_STRING);

        if(isset($_POST['backOnDate']) && $_POST['backOnDate'] != null && $_POST['backOnDate'] != ''){
            $status = 'Invoiced';
        }
    }
    
    if(isset($_POST['notes']) && $_POST['notes'] != null && $_POST['notes'] != ''){
        $noting = $_POST['notes'];
    }

    $ds = DIRECTORY_SEPARATOR;  
    $storeFolder = '../grns';  // Removed '../' from the store folder path
    $storeFolder2 = 'grns';  // Removed '../' from the store folder path
    $filename = $_FILES['grn_files']['name'];
    $jobLog = array();
    
    if (isset($_FILES['grn_files'])) {
        // Loop through each uploaded file
        foreach ($_FILES['grn_files']['name'] as $key => $name) {
            // Check if the file was uploaded successfully
            if ($_FILES['grn_files']['error'][$key] === UPLOAD_ERR_OK) {
                $tempFile = $_FILES['grn_files']['tmp_name'][$key];
                $temp = explode(".", $_FILES["grn_files"]["name"][$key]);
                $extension = end($temp);
                $timestamp = time(); // Get the current timestamp
                $newfilename = $timestamp . '-' . $key . '.' . $extension; // Unique filename using timestamp and key
                $targetPath = dirname(__FILE__) . $ds . $storeFolder . $ds;
                $targetFile = $targetPath . $newfilename;
    
                if (move_uploaded_file($tempFile, $targetFile)) {
                    // File moved successfully
                    $jobLog[] = $storeFolder2 . $ds . $newfilename; // Assigning $jobLog with the relative path
                } 
            } 
        }
    }

    if(isset($_POST['grn_received']) && $_POST['grn_received'] != null && $_POST['grn_received'] != ''
    && isset($_POST['backOnDate']) && $_POST['backOnDate'] != null && $_POST['backOnDate'] != ''){
        if(count($pricing_details) <= 0 && $payment_term != 'Cash'){
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Please enter the price"
                )
            ); 
        }
        else{
            if ($update_stmt = $db->prepare("UPDATE do_request SET grn_upload=?, sent_date=?, back_date=?, grn_receive=?, pricing_details=?, total_price=?, status=?, note=? WHERE id=?")){
                $jobLog = json_encode($jobLog);
                $update_stmt->bind_param('sssssssss', $jobLog, $sent_date, $back_date, $grn_receive, $pricing, $totalAmount, $status, $noting, $id);
                
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
                            "message"=> "Update Successfully!!"
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
    }
    else{
        if ($update_stmt = $db->prepare("UPDATE do_request SET grn_upload=?, sent_date=?, back_date=?, grn_receive=?, pricing_details=?, total_price=?, note=? WHERE id=?")){
            $jobLog = json_encode($jobLog);
            $update_stmt->bind_param('ssssssss', $jobLog, $sent_date, $back_date, $grn_receive, $pricing, $totalAmount, $noting, $id);
            
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
                        "message"=> "Update Successfully!!"
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