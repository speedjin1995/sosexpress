<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}
else{
    $userId = $_SESSION['userID'];
}

if(isset($_POST['username'], $_POST['code'], $_POST['name'], $_POST['account'], $_POST['reg_no'], $_POST['address'], $_POST['phone'], 
$_POST['email'], $_POST['payment_term'], $_POST['pickupaddress'])){
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $account = filter_input(INPUT_POST, 'account', FILTER_SANITIZE_STRING);
    $reg_no = filter_input(INPUT_POST, 'reg_no', FILTER_SANITIZE_STRING);
	$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $pickupaddress = filter_input(INPUT_POST, 'pickupaddress', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $payment_term = filter_input(INPUT_POST, 'payment_term', FILTER_SANITIZE_STRING);

    $phone2 = null;
    $phone3 = null;
    $phone4 = null;
    $email2 = null;
    $email3 = null;
    $email4 = null;
    $workingHours = null;
    $shortname = null;
    $pic = null;
    $term = null;
    $note = null;

    // Pricing
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }

    if(isset($_POST['size'])){
        $size = $_POST['size'];
    }

    if(isset($_POST['description'])){
        $description = $_POST['description'];
    }

    if(isset($_POST['price'])){
        $price = $_POST['price'];
    }

    if(isset($_POST['unit'])){
        $unit = $_POST['unit'];
    }

    if(isset($_POST['phone2']) && $_POST['phone2'] != null && $_POST['phone2'] != ''){
        $phone2 = filter_input(INPUT_POST, 'phone2', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['phone3']) && $_POST['phone3'] != null && $_POST['phone3'] != ''){
        $phone3 = filter_input(INPUT_POST, 'phone3', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['phone4']) && $_POST['phone4'] != null && $_POST['phone4'] != ''){
        $phone4 = filter_input(INPUT_POST, 'phone4', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['email2']) && $_POST['email2'] != null && $_POST['email2'] != ''){
        $email2 = filter_input(INPUT_POST, 'email2', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['email3']) && $_POST['email3'] != null && $_POST['email3'] != ''){
        $email3 = filter_input(INPUT_POST, 'email3', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['email4']) && $_POST['email4'] != null && $_POST['email4'] != ''){
        $email4 = filter_input(INPUT_POST, 'email4', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['workingHours']) && $_POST['workingHours'] != null && $_POST['workingHours'] != ''){
        $workingHours = filter_input(INPUT_POST, 'workingHours', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['shortname']) && $_POST['shortname'] != null && $_POST['shortname'] != ''){
        $shortname = filter_input(INPUT_POST, 'shortname', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['pic']) && $_POST['pic'] != null && $_POST['pic'] != ''){
        $pic = filter_input(INPUT_POST, 'pic', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['term']) && $_POST['term'] != null && $_POST['term'] != ''){
        $term = filter_input(INPUT_POST, 'term', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['note']) && $_POST['note'] != null && $_POST['note'] != ''){
        $note = filter_input(INPUT_POST, 'note', FILTER_SANITIZE_STRING);
    }

    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
            $pricing = array();

            if(isset($type) && $type != null && count($type) > 0){
                for($i=0; $i<count($type); $i++){
                    $notes = null;

                    if(isset($description[$i]) && $description[$i] != null){
                        $notes = $description[$i];
                    }

                    $pricing[] = array( 
                        'type' => $type[$i],
                        'size' => $size[$i],
                        'price' => $price[$i],
                        'notes' => $notes,
                        'unit' => $unit[$i] ?? ''
                    );
                }
            }

            if ($update_stmt = $db->prepare("UPDATE customers SET username=?, customer_code=?, customer_name=?, short_name=?, reg_no=?, pic=?, customer_address=?, pickup_address=?, customer_phone=?, customer_phone2=?, customer_phone3=?, customer_phone4=?, customer_email=?, customer_email2=?, customer_email3=?, customer_email4=?, working_hours=?, payment_term=?, payment_details=?, pricing=?, notes=?, account_name=? WHERE id=?")) {
                $data = json_encode($pricing);
                $update_stmt->bind_param('sssssssssssssssssssssss', $username, $code, $name, $shortname, $reg_no, $pic, $address, $pickupaddress, $phone, $phone2, $phone3, $phone4, $email, $email2, $email3, $email4, $workingHours, $payment_term, $term, $data, $note, $account, $_POST['id']);
                
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
            $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
            $password = $username;
            $password = hash('sha512', $password . $random_salt);
            $pricing = array();

            if(isset($type) && $type != null && count($type) > 0){
                for($i=0; $i<count($type); $i++){
                    $notes = null;

                    if(isset($description[$i]) && $description[$i] != null){
                        $notes = $description[$i];
                    }

                    $pricing[] = array( 
                        'type' => $type[$i],
                        'size' => $size[$i],
                        'price' => $price[$i],
                        'notes' => $notes
                    );
                }
            }

            if ($insert_stmt = $db->prepare("INSERT INTO customers (username, password, salt, customer_code, customer_name, pickup_address, short_name, reg_no, pic, customer_address, customer_phone, customer_phone2, customer_phone3, customer_phone4, customer_email, customer_email2, customer_email3, customer_email4, working_hours, payment_term, payment_details, pricing, notes, account_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $data = json_encode($pricing);
                $insert_stmt->bind_param('ssssssssssssssssssssssss', $username, $password, $random_salt, $code, $name, $pickupaddress, $shortname, $reg_no, $pic, $address, $phone, $phone2, $phone3, $phone4, $email, $email2, $email3, $email4, $workingHours, $payment_term, $term, $data, $note, $account);
                
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
                "message"=> "Please enter a valid email address"
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