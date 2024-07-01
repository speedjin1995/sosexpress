<?php

require_once 'db_connect.php';
 
if(isset($_POST['id'], $_POST['driver'], $_POST['lorry'], $_POST['checker'], $_POST['printDate2'])){
    $selectedIds = $_POST['id'];
    $arrayOfId = explode(",", $selectedIds);
    $driver = filter_input(INPUT_POST, 'driver', FILTER_SANITIZE_STRING);
    $printDate2 = filter_input(INPUT_POST, 'printDate2', FILTER_SANITIZE_STRING);
    $lorry = filter_input(INPUT_POST, 'lorry', FILTER_SANITIZE_STRING);
    $checker = filter_input(INPUT_POST, 'checker', FILTER_SANITIZE_STRING);
    $driverName = '';
    $driverIc = '';
    $driverPhone = '';
    $do = '';
    $todayDate = date('d/m/Y');
    $today = date("Y-m-d 00:00:00");

    $dateTime = DateTime::createFromFormat('d/m/Y', $printDate2);
    $printDateTime = $dateTime->format('Y-m-d 00:00:00');

    if ($update_stmt = $db->prepare("SELECT * FROM drivers WHERE id=?")) {
        $update_stmt->bind_param('s', $driver);
        
        if ($update_stmt->execute()) {
            $result = $update_stmt->get_result();
            $message = array();
            
            if ($row = $result->fetch_assoc()) {
                $driverName = $row['name'];
                $driverIc = $row['ic_number'];
                $driverPhone = $row['contact_no'];
            }  
        }
    }
    
    
    if ($select_stmt2 = $db->prepare("SELECT COUNT(*) FROM do_request WHERE delivery_date >= ?")) {
        $select_stmt2->bind_param('s', $today);
        
        // Execute the prepared query.
        if ($select_stmt2->execute()) {
            $result = $select_stmt2->get_result();
            $count = 1;
            $do = 'DO-'.date("ym")."-";
            
            if ($row = $result->fetch_assoc()) {
                $count = (int)$row['COUNT(*)'] + 1;
                $select_stmt2->close();
            }

            $charSize = strlen(strval($count));

            for($i=0; $i<(3-(int)$charSize); $i++){
                $do.='0';
            }
    
            $do .= strval($count);
        }
    }

    $placeholders = implode(',', array_fill(0, count($arrayOfId), '?'));
    $select_stmt = $db->prepare("SELECT do_request.id, outlet.name, do_request.do_number, do_request.do_details, do_request.po_number, customers.short_name, 
    do_request.note, do_request.actual_carton, do_request.do_type FROM outlet, do_request, customers WHERE do_request.outlet = outlet.id AND do_request.customer = customers.id AND
    do_request.id IN ($placeholders)");

    // Check if the statement is prepared successfully
    if ($select_stmt) {
        // Bind variables to the prepared statement
        $types = str_repeat('i', count($arrayOfId)); // Assuming the IDs are integers
        $select_stmt->bind_param($types, ...$arrayOfId);
        $select_stmt->execute();
        $select_stmt->bind_result($id, $outlet_name, $do_number, $do_details, $po_number, $customer, $note, $actual_carton, $do_type);
        $results = array();
        $doTypes = array();
        $index = 1;

        while ($select_stmt->fetch()) {
            if(!in_array($do_type , $doTypes)){
                $results[]=array(
                    'doType' => $do_type,
                    'values' => array(),
                );

                array_push($doTypes, $do_type);
            }

            $key = array_search($do_type, $doTypes);

            if($do_details == null || $do_details == ''){
                array_push($results[$key]['values'], array(
                    'index' => $index,
                    'notes' => $note,
                    'po' => $po_number,
                    'do' => $do_number,
                    'carton' => $actual_carton,
                    'outlet' => $outlet_name,
                    'id' => $id,
                    'customer' => $customer
                ));

                $index++;
            }
            else{
                $poList = json_decode($do_details, true);

                for($i=0; $i<count($poList); $i++){
                    if($i == 0){
                        array_push($results[$key]['values'], array(
                            'index' => $index,
                            'notes' => $note,
                            'po' => $poList[$i]['poNumber'],
                            'do' => $poList[$i]['doNumber'],
                            'carton' => $actual_carton,
                            'outlet' => $outlet_name,
                            'id' => $id,
                            'customer' => $customer
                        ));
                    }
                    else{
                        array_push($results[$key]['values'], array(
                            'index' => $index,
                            'notes' => $note,
                            'po' => $poList[$i]['poNumber'],
                            'do' => $poList[$i]['doNumber'],
                            'carton' => '',
                            'outlet' => $outlet_name,
                            'id' => $id,
                            'customer' => $customer
                        ));
                    }
                    

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

                    .bottom-table {
                        position: fixed;
                        bottom: 0;
                        width: 100%;
                    }
                </style>
            </head>
            <body>';

            for($t=0; $t<count($results); $t++){
                $result = $results[$t]['values'];

                $message .= '<br><br><br><br><br><br><br><br>
                        <table style="width:100%">
                            <tbody>
                                <tr>
                                    <td style="width:70%">
                                        <span>TO: '.$result[0]['outlet'].'<span>
                                    </td>
                                    <td style="width:30%">
                                        <span>NO. : '.$do.'<span>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="width:70%"></td>
                                    <td style="width:30%">
                                        <span>Date: '.$printDate2.'<span>
                                    </td>
                                </tr>
                            </tbody>
                        </table><br><br><br>
                        <table style="width:100%">
                            <tbody>';

                    $count = 0;
                    for($j=0; $j<count($result); $j++) {
                        $message .= '<tr><td>'.$result[$j]['index'].'</td><td>'.$result[$j]['customer'].'</td><td>'.$result[$j]['notes'].'</td><td>'.$result[$j]['po'].'</td><td>'.$result[$j]['do'].'</td><td style="text-align: center;">'.$result[$j]['carton'].'</td></tr>';
                        $count += (int)$result[$j]['carton'];

                        $update_stmt = $db->prepare("UPDATE do_request SET checker = ?, veh_no = ?, printed_date = ?, status = 'Printed' WHERE id = ?");
                        $update_stmt->bind_param("sssi", $checker, $lorry, $printDateTime, $result[$j]['id']);
                        $update_stmt->execute();
                        $update_stmt->close();
                    }

                    $message .= '</tbody><tfoot>
                    <tr>
                        <th colspan="5" style="text-align: right;">Total: </th>
                        <th style="border-top: 1px solid #ccc;border-bottom: 1px solid #ccc;">'.$count.'</th>
                    </tr>
                </tfoot></table><br><br><br>
                <table class="bottom-table" style="width:100%">
                    <tbody>
                        <tr>
                            <td style="width:60%"></td>
                            <td style="width:40%">
                                <span>Phone: '.$driverPhone.'<span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:60%"></td>
                            <td style="width:40%">
                                <span>Driver Name: '.$driverName.'<span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:60%"></td>
                            <td style="width:40%">
                                <span>Driver IC: '.$driverIc.'<span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:60%"></td>
                            <td style="width:40%">
                                <span>Lorry: '.$lorry.'<span>
                            </td>
                        </tr>
                    </tbody>
                </table>';

                $message .= '<p style="page-break-after: always;">&nbsp;</p>';
            }
            
    
    $message .= '</body></html>';

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