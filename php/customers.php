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

if(isset($_POST['username'], $_POST['code'], $_POST['name'], $_POST['reg_no'], $_POST['address'], $_POST['phone'], 
$_POST['email'], $_POST['payment_term'])){
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $reg_no = filter_input(INPUT_POST, 'reg_no', FILTER_SANITIZE_STRING);
	$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $payment_term = filter_input(INPUT_POST, 'payment_term', FILTER_SANITIZE_STRING);

    $shortname = null;
    $pic = null;
    $term = null;
    $note = null;

    // Pricing
    $type = $_POST['type'];
    $size = $_POST['size'];
    $description = $_POST['description'];
    $price = $_POST['price'];

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

            if($type != null && count($type) > 0){
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

            if ($update_stmt = $db->prepare("UPDATE customers SET username=?, customer_code=?, customer_name=?, short_name=?, reg_no=?, pic=?, customer_address=?, customer_phone=?, customer_email=?, payment_term=?, payment_details=?, pricing=?, notes=? WHERE id=?")) {
                $data = json_encode($pricing);
                $update_stmt->bind_param('ssssssssssssss', $username, $code, $name, $shortname, $reg_no, $pic, $address, $phone, $email, $payment_term, $term, $data, $note, $_POST['id']);
                
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
            $password = '123456';
            $password = hash('sha512', $password . $random_salt);
            $pricing = array();

            if($type != null && count($type) > 0){
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

            if ($insert_stmt = $db->prepare("INSERT INTO customers (username, password, salt, customer_code, customer_name, short_name, reg_no, pic, customer_address, customer_phone, customer_email, payment_term, payment_details, pricing, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $data = json_encode($pricing);
                $insert_stmt->bind_param('sssssssssssssss', $username, $password, $random_salt, $code, $name, $shortname, $reg_no, $pic, $address, $phone, $email, $payment_term, $term, $data, $note);
                
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