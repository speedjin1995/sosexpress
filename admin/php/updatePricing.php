<?php
require_once "db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['id'];
    
    if(isset($_POST['particular'])){
        $particular = $_POST['particular'];
    }

    if(isset($_POST['quantity_in'])){
        $quantity_in = $_POST['quantity_in'];
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

    $pricing_details = array();
    $update_successful = true;

    //foreach ($ids as $index => $id) {
    if(isset($particular) && $particular != null && count($particular) > 0){
        for($i=0; $i<count($particular); $i++){
            $notes = '';

            if(isset($particular[$i]) && $particular[$i] != null && $particular[$i]!=''){
                $notes = $particular[$i];
            }

            $pricing_details[] = array(
                "particular" => $notes,
                "quantity_in" => $quantity_in[$i],
                "size" => $size[$i] ?? '',
                "unit_price" => $unit_price[$i],
                "price" => $price[$i],
                "unit" => $unit[$i] ?? ''
            );
        }
    }

    $pricing = json_encode($pricing_details);

    // Fetch the existing JSON data
    if ($update_stmt = $db->prepare("UPDATE do_request SET pricing_details=? WHERE id=?")) {
        $update_stmt->bind_param('ss', $pricing, $ids[0]);

        if (!$update_stmt->execute()) {
            $update_successful = false;
        }

        $update_stmt->close();
    } 
    else {
        $update_successful = false;
    }
    //}

    if ($update_successful) {
        echo json_encode(array("status" => "success", "message" => "Updated"));
    } 
    else {
        echo json_encode(array("status" => "failed", "message" => "Error updating prices"));
    }
} 
else {
    echo json_encode(array("status" => "failed", "message" => "Invalid request method"));
}
?>
