<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['id'], $_POST['totalAmount'])){
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $totalAmount = filter_input(INPUT_POST, 'totalAmount', FILTER_SANITIZE_STRING);

    $sent_date = null;
	$back_date = null;
    $grn_receive = null;
    $noting = null;
    $pricing_details = array();
    $pricing = '';
    $status = 'Printed';

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
        $status = 'Invoiced';
    }
    
    if(isset($_POST['notes']) && $_POST['notes'] != null && $_POST['notes'] != ''){
        $noting = $_POST['notes'];
    }

    $ds = DIRECTORY_SEPARATOR;  
    $storeFolder = '../grns';  // Removed '../' from the store folder path
    $storeFolder2 = 'grns';  // Removed '../' from the store folder path
    $filename = $_FILES['grn_files']['name'];
    $jobLog = null;
    
    if (!empty($_FILES['grn_files']['name'])) {
        // File was uploaded successfully
        $tempFile = $_FILES['grn_files']['tmp_name'];
        $temp = explode(".", $_FILES["grn_files"]["name"]);
        $newfilename = $filename . '.' . end($temp);
        $targetPath = dirname(__FILE__) . $ds . $storeFolder . $ds;
        $targetFile = $targetPath . $newfilename;
    
        if (move_uploaded_file($tempFile, $targetFile)) {
            // File moved successfully
            $jobLog = $storeFolder2 . $ds . $newfilename; // Assigning $jobLog with the relative path
        }
    }
    
    if ($update_stmt = $db->prepare("UPDATE do_request SET grn_upload=?, sent_date=?, back_date=?, grn_receive=?, pricing_details=?, total_price=?, status=?, note=? WHERE id=?")){
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