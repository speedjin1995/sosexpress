<?php

require_once 'db_connect.php';
 
if(isset($_POST['id'])){
    $selectedIds = $_POST['id'];
    $arrayOfId = explode(",", $selectedIds);
    $driver = "";
    $lorry = "";
    $todayDate = date('Y-m-d');

    $driverName = '';
    $todayDate = date('d/m/Y');
    $today = date("Y-m-d 00:00:00");

    if(isset($_POST['driver']) && $_POST['driver'] != null && $_POST['driver'] != ""){
        $driver = filter_input(INPUT_POST, 'driver', FILTER_SANITIZE_STRING);
        
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
    }

    if(isset($_POST['lorry']) && $_POST['lorry'] != null && $_POST['lorry'] != ""){
        $lorry = filter_input(INPUT_POST, 'lorry', FILTER_SANITIZE_STRING);
    }

    $placeholders = implode(',', array_fill(0, count($arrayOfId), '?'));
    $select_stmt = $db->prepare("SELECT customers.short_name, customers.pricing, outlet.name, do_request.do_number, do_request.do_details, do_request.hold, 
    do_request.po_number, do_request.note, do_request.actual_carton, do_request.status, do_request.delivery_date, do_request.cancellation_date FROM customers, outlet, do_request WHERE 
    do_request.outlet = outlet.id AND do_request.customer = customers.id AND do_request.id IN ($placeholders)");

    // Check if the statement is prepared successfully
    if ($select_stmt) {
        // Bind variables to the prepared statement
        $types = str_repeat('i', count($arrayOfId)); // Assuming the IDs are integers
        $select_stmt->bind_param($types, ...$arrayOfId);
        $select_stmt->execute();
        $select_stmt->bind_result($customer_name, $pricing, $outlet_name, $do_number, $do_details, $hold, $po_number, $note, $actual_carton, $status, $delivery_date, $cancellation_date);
        $results = array();
        $outletList = array();
        $index = 1;

        while ($select_stmt->fetch()) {
            if(!in_array($outlet_name, $outletList)){
                $results[]=array(
                    'outlet' => $outlet_name,
                    'items' => array()
                );

                array_push($outletList, $outlet_name);
            }

            $key = array_search($outlet_name, $outletList);

            if($do_details == null || $do_details == '' || $do_details == '[]'){
                $dateTime = new DateTime($delivery_date);
                $formattedDate = date_format($dateTime, 'd/m');

                $dateTime2 = new DateTime($cancellation_date);
                $formattedDate2 = date_format($dateTime2, 'd/m');

                array_push($results[$key]['items'], array(
                    'index' => $index,
                    'customer' => $customer_name,
                    'notes' => $note,
                    'po' => $po_number,
                    'do' => $do_number,
                    'carton' => $actual_carton,
                    'outlet' => $outlet_name,
                    'status' => ($status == 'confirmed') ? '/' : '',
                    'hold' => ($hold == 'No') ? '/' : '',
                    'pricing' => json_decode($pricing, true),
                    'delivery' => $formattedDate,
                    'cancellation' => $formattedDate2 
                ));

                $index++;
            }
            else{
                $poList = json_decode($do_details, true);

                for($i=0; $i<count($poList); $i++){
                    $dateTime = new DateTime($delivery_date);
                    $formattedDate = date_format($dateTime, 'd/m');

                    $dateTime2 = new DateTime($cancellation_date);
                    $formattedDate2 = date_format($dateTime2, 'd/m');

                    array_push($results[$key]['items'], array(
                        'index' => $index,
                        'customer' => $customer_name,
                        'notes' => $note,
                        'po' => $poList[$i]['poNumber'],
                        'do' => $poList[$i]['doNumber'],
                        'carton' => $actual_carton,
                        'outlet' => $outlet_name,
                        'status' => ($status == 'confirmed') ? '/' : '',
                        'hold' => ($hold == 'No') ? '/' : '',
                        'pricing' => json_decode($pricing, true),
                        'delivery' => $formattedDate,
                        'cancellation' => $formattedDate2 
                    ));

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
                            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>
                        </td>
                        <td style="width:40%">
                            <span><span>
                        </td>
                    </tr>
                </tbody>
            </table><br><br><br><br><br>';

            for($k=0; $k<count($results); $k++) {
                $message .= '<table style="width:100%">
                <tbody><tr><td colspan="3"><td><td>'.$results[$k]['outlet'].'<td><td></td></tr><tr><td><td>&nbsp;&nbsp;&nbsp;&nbsp;<td><td><td></td></tr>';

                $results2 = $results[$k]['items'];

                for($j=0; $j<count($results2); $j++) {
                    $pring = '';
                    foreach ($results2[$j]['pricing'] as $item) {
                        $pring.= '<td>';
                        $pring.= $item['size'] . '<br>' . $item['price'];
                        $pring.= '</td>';
                    }

                    $message .= '<tr height=""><td>'.$results2[$j]['delivery'].'</td><td>'.$results2[$j]['cancellation'].'</td><td>'.$results2[$j]['customer'].'</td><td>'.$results2[$j]['status'].'</td><td>'.$results2[$j]['po'].'</td><td>'.$results2[$j]['do'].'</td><td>'.$results2[$j]['carton'].'</td><td>'.$results2[$j]['hold'].'</td>'.$pring.'<td>'.$results2[$j]['notes'].'</td></tr>';
                }

                $message .= '</tbody></table><br><br><br>';
            }

        

        $message .= '</html>';

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