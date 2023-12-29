<?php

require_once 'db_connect.php';
 
if(isset($_POST['id'], $_POST['driver'], $_POST['lorry'])){
    $selectedIds = $_POST['id'];
    $arrayOfId = explode(",", $selectedIds);
    $driver = filter_input(INPUT_POST, 'driver', FILTER_SANITIZE_STRING);
    $lorry = filter_input(INPUT_POST, 'lorry', FILTER_SANITIZE_STRING);
    $todayDate = date('Y-m-d');
    $driverName = '';

    if ($update_stmt = $db->prepare("SELECT * FROM drivers WHERE id=?")) {
        $update_stmt->bind_param('s', $driver);
        
        if ($update_stmt->execute()) {
            $result = $update_stmt->get_result();
            $message = array();
            
            if ($row = $result->fetch_assoc()) {
                $driverName = $row['name'];
            }  
        }
    }

    $placeholders = implode(',', array_fill(0, count($arrayOfId), '?'));
    $select_stmt = $db->prepare("SELECT customers.customer_name, customers.customer_phone, customers.customer_address, booking.estimated_ctn, booking.internal_notes FROM customers, booking WHERE booking.customer = customers.id AND booking.id IN ($placeholders)");

    // Check if the statement is prepared successfully
    if ($select_stmt) {
        // Bind variables to the prepared statement
        $types = str_repeat('i', count($arrayOfId)); // Assuming the IDs are integers
        $select_stmt->bind_param($types, ...$arrayOfId);
        $select_stmt->execute();
        $select_stmt->bind_result($customer_name, $customer_phone, $customer_address, $estimated_ctn, $internal_notes);
        $results = array();
        $index = 1;
        $count = 0;

        $message = '<html>
            <head>
                <style>
                    @media print {
                        @page {
                            margin-left: 0.5in;
                            margin-right: 0.5in;
                            margin-top: 0.1in;
                            margin-bottom: 0.1in;
                        }
                        
                    } 
                            
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        
                    } 
                    
                    .table th, .table td {
                        padding: 0.70rem;
                        vertical-align: top;
                        border-top: 1px solid #dee2e6;
                        
                    } 
                    
                    .table-bordered {
                        border: 1px solid #000000;
                        
                    } 
                    
                    .table-bordered th, .table-bordered td {
                        border: 1px solid #000000;
                        font-family: sans-serif;
                        font-size: 12px;
                        
                    } 
                    
                    .row {
                        display: flex;
                        flex-wrap: wrap;
                        margin-top: 20px;
                        margin-right: -15px;
                        margin-left: -15px;
                        
                    } 
                    
                    .col-md-4{
                        position: relative;
                        width: 33.333333%;
                    }
                </style>
            </head>
            <body>
                <table style="width:100%" class="table-bordered">
                    <tbody>
                        <tr>
                            <td colspan="4">
                                <span>DAILY PICK UP LIST<span>
                                <span style="float: right;">DATE: '.$todayDate.'<span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <span>DRIVER: '.$driverName.'<span>
                                <span style="float: right;">LORRY NO: '.$lorry.'<span>
                            </td>
                        </tr>
                        <tr>
                            <th>NO.</th>
                            <th>PICKUP CUSTOMER NAME</th>
                            <th>CTN</th>
                            <th>NOTES</th>
                        </tr>';

        while ($select_stmt->fetch()) {
            $message .= '<tr><td>'.$index.'</td><td>'.$customer_name.'<br>'.$customer_phone.'<br>'.$customer_address.'</td><td>'.$estimated_ctn.'</td><td>'.$internal_notes.'</td></tr>';
            $count += (int)$estimated_ctn;
        }

        $message .= '</tbody><tfoot><th colspan="2" style="text-align: right;">TOTAL CTN</th><th>'.$count.'</th><th></th></tfoot></table></html>';

        // Fetch each row
        $select_stmt->close();

        // Return the results as JSON
        echo json_encode(array('status' => 'success', 'message' => $message));
    } 
    else {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Statement preparation failed"
            ));
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