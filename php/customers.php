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
$_POST['email'], $_POST['payment_term'], $_POST['rate'])){
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $reg_no = filter_input(INPUT_POST, 'reg_no', FILTER_SANITIZE_STRING);
	$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $payment_term = filter_input(INPUT_POST, 'payment_term', FILTER_SANITIZE_STRING);
    $rate = filter_input(INPUT_POST, 'rate', FILTER_SANITIZE_STRING);

    $shortname = null;
    $pic = null;
    $term = null;

    // Branch
    $branch_name = $_POST['branch_name'];
    $branch_Address = $_POST['branch_Address'];

    // Pricing
    $type = $_POST['type'];
    $size = $_POST['size'];
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

    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
            if ($update_stmt = $db->prepare("UPDATE customers SET customer_code=?, customer_name=?, customer_address=?, customer_phone=?, customer_email=? WHERE id=?")) {
                $update_stmt->bind_param('ssssss', $code, $name, $address, $phone, $email, $_POST['id']);
                
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
                    $pricing[] = array( 
                        'type' => $type[$i],
                        'size' => $size[$i],
                        'price' => $price[$i]
                    );
                }
            }

            if ($insert_stmt = $db->prepare("INSERT INTO customers (username, password, salt, customer_code, customer_name, short_name, reg_no, pic, customer_address, customer_phone, customer_email, payment_term, payment_details, rate, pricing) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $data = json_encode($pricing);
                $insert_stmt->bind_param('sssssssssssssss', $username, $password, $random_salt, $code, $name, $shortname, $reg_no, $pic, $address, $phone, $email, $payment_term, $term, $rate, $data);
                
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
                    $id = $insert_stmt->insert_id;;
                    $insert_stmt->close();
                    $success = true;

                    for($j=0; $j<sizeof($branch_name); $j++){
                        if ($insert_stmt2 = $db->prepare("INSERT INTO branch (customer_id, name, address) VALUES (?, ?, ?)")) {
                            $insert_stmt2->bind_param('sss', $id, $branch_name[$j], $branch_Address[$j]);
                            
                            // Execute the prepared query.
                            if (! $insert_stmt2->execute()) {
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
                                "message"=> "Added Successfully!!"
                            )
                        );
                    }
                    else{
                        $insert_stmt2->close();
                        $db->close();

                        echo json_encode(
                            array(
                                "status"=> "failed", 
                                "message"=> "Failed to created branch records due to ".$insert_stmt2->error 
                            )
                        );
                    }
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