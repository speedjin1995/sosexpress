<?php

require_once 'db_connect.php';
 
if(isset($_POST['id'], $_POST['driver'], $_POST['lorry'])){
    $selectedIds = $_POST['id'];
    $arrayOfId = explode(",", $selectedIds);
    $driver = filter_input(INPUT_POST, 'driver', FILTER_SANITIZE_STRING);
    $lorry = filter_input(INPUT_POST, 'lorry', FILTER_SANITIZE_STRING);
    $driverName = '';
    $driverIc = '';
    $driverPhone = '';
    $do = '';
    $todayDate = date('d/m/Y');
    $today = date("Y-m-d 00:00:00");

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
    $select_stmt = $db->prepare("SELECT outlet.name, do_request.do_number, do_request.do_details, do_request.po_number, 
    do_request.note, do_request.actual_carton FROM outlet, do_request WHERE do_request.outlet = outlet.id AND 
    do_request.id IN ($placeholders)");

    // Check if the statement is prepared successfully
    if ($select_stmt) {
        // Bind variables to the prepared statement
        $types = str_repeat('i', count($arrayOfId)); // Assuming the IDs are integers
        $select_stmt->bind_param($types, ...$arrayOfId);
        $select_stmt->execute();
        $select_stmt->bind_result($outlet_name, $do_number, $do_details, $po_number, $note, $actual_carton);
        $results = array();
        $index = 1;

        while ($select_stmt->fetch()) {
            if($do_details == null || $do_details == ''){
                $results[]=array(
                    'index' => $index,
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
            <br><br><br><br><br><br><br><br>
            <table style="width:100%">
                <tbody>
                    <tr>
                        <td style="width:70%">
                            <span>TO: '.$results[0]['outlet'].'<span>
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
                            <span>Date: '.$todayDate.'<span>
                        </td>
                    </tr>
                </tbody>
            </table><br><br><br>
            <table style="width:100%">
                <tbody>';

        $count = 0;
        for($j=0; $j<count($results); $j++) {
            $message .= '<tr><td>'.$results[$j]['index'].'</td><td>'.$results[$j]['notes'].'</td><td>'.$results[$j]['po'].'</td><td>'.$results[$j]['do'].'</td><td style="text-align: center;">'.$results[$j]['carton'].'</td></tr>';
            $count += (int)$results[$j]['carton'];
        }

        $message .= '</tbody><tfoot>
        <tr>
            <th colspan="4"></th>
            <th>'.$count.'</th>
        </tr>
    </tfoot></table><br><br><br>
    <table style="width:100%">
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