<?php

require_once 'db_connect.php';
 
if(isset($_POST['id'], $_POST['driver'], $_POST['lorry'])){
    $selectedIds = $_POST['id'];
    $arrayOfId = explode(",", $selectedIds);
    $driver = filter_input(INPUT_POST, 'driver', FILTER_SANITIZE_STRING);
    $lorry = filter_input(INPUT_POST, 'lorry', FILTER_SANITIZE_STRING);
    $todayDate = date('Y-m-d');

    $driverName = '';
    $todayDate = date('d/m/Y');
    $today = date("Y-m-d 00:00:00");

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
    $select_stmt = $db->prepare("SELECT customers.customer_name, outlet.name, do_request.do_number, do_request.do_details, 
    do_request.po_number, do_request.note, do_request.actual_carton FROM customers, outlet, do_request WHERE 
    do_request.outlet = outlet.id AND do_request.customer = customers.id AND do_request.id IN ($placeholders)");

    // Check if the statement is prepared successfully
    if ($select_stmt) {
        // Bind variables to the prepared statement
        $types = str_repeat('i', count($arrayOfId)); // Assuming the IDs are integers
        $select_stmt->bind_param($types, ...$arrayOfId);
        $select_stmt->execute();
        $select_stmt->bind_result($customer_name, $outlet_name, $do_number, $do_details, $po_number, $note, $actual_carton);
        $results = array();
        $index = 1;

        while ($select_stmt->fetch()) {
            if($do_details == null || $do_details == ''){
                $results[]=array(
                    'index' => $index,
                    'customer' => $customer_name,
                    'notes' => $note,
                    'po' => $po_number,
                    'do' => $do_number,
                    'carton' => $actual_carton,
                    'outlet' => $outlet_name
                );

                $index++;
            }
            else{
                $poList = json_decode($do_details, true);

                for($i=0; $i<count($poList); $i++){
                    $results[]=array(
                        'index' => $index,
                        'customer' => $customer_name,
                        'notes' => $note,
                        'po' => $poList[$i]['poNumber'],
                        'do' => $poList[$i]['doNumber'],
                        'carton' => $actual_carton,
                        'outlet' => $outlet_name
                    );

                    $index++;
                }
            }
        }

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
            <br><br>
            <table style="width:100%">
                <tbody>
                    <tr>
                        <td style="width:40%">
                            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$lorry.'<span>
                        </td>
                        <td style="width:20%">
                            <span></span>
                        </td>
                        <td style="width:20%">
                            <span><span>
                        </td>
                        <td style="width:40%">
                            <span>&nbsp;&nbsp;&nbsp;&nbsp;'.$todayDate.'</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:40%">
                            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$driverName.'<span>
                        </td>
                        <td colspan="2">
                            <span>'.$results[0]['outlet'].'<span>
                        </td>
                        <td style="width:40%">
                            <span><span>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:40%">
                            <span><span>
                        </td>
                        <td colspan="2">
                            <span>&nbsp;&nbsp; / <span>
                        </td>
                        <td style="width:40%">
                            <span><span>
                        </td>
                    </tr>
                </tbody>
            </table><br><br><br>
            <table style="width:100%">
                <tbody>';

        for($j=0; $j<count($results); $j++) {
            $message .= '<tr><td>'.$results[$j]['index'].'</td><td></td><td>'.$results[$j]['customer'].'</td><td></td><td>'.$results[$j]['po'].'</td><td>'.$results[$j]['do'].'</td><td>'.$results[$j]['carton'].'</td><td></td><td></td><td>'.$results[$j]['notes'].'</td></tr>';
        }

        $message .= '</tbody></table></body></html>';

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