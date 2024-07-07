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
    $select_stmt = $db->prepare("SELECT customers.short_name, customers.pricing, customers.payment_term, outlet.name, do_request.do_number, do_request.do_details, do_request.hold, 
    do_request.po_number, do_request.note, do_request.actual_carton, do_request.status, do_request.delivery_date, do_request.cancellation_date FROM customers, outlet, do_request WHERE 
    do_request.outlet = outlet.id AND do_request.customer = customers.id AND do_request.id IN ($placeholders)");

    // Check if the statement is prepared successfully
    if ($select_stmt) {
        // Bind variables to the prepared statement
        $types = str_repeat('i', count($arrayOfId)); // Assuming the IDs are integers
        $select_stmt->bind_param($types, ...$arrayOfId);
        $select_stmt->execute();
        $select_stmt->bind_result($customer_name, $pricing, $term, $outlet_name, $do_number, $do_details, $hold, $po_number, $note, $actual_carton, $status, $delivery_date, $cancellation_date);
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

            $dateTime = new DateTime($delivery_date);
            $formattedDate = date_format($dateTime, 'd/m');

            $dateTime2 = new DateTime($cancellation_date);
            $formattedDate2 = date_format($dateTime2, 'd/m');

            array_push($results[$key]['items'], array(
                'index' => $index,
                'customer' => $customer_name,
                'term' => $term,
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

            /*if($do_details == null || $do_details == '' || $do_details == '[]'){
                $dateTime = new DateTime($delivery_date);
                $formattedDate = date_format($dateTime, 'd/m');

                $dateTime2 = new DateTime($cancellation_date);
                $formattedDate2 = date_format($dateTime2, 'd/m');

                array_push($results[$key]['items'], array(
                    'index' => $index,
                    'customer' => $customer_name,
                    'term' => $term,
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
                        'term' => $term,
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
            }*/
        }

        usort($results, 'sortByOutlet');

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
                        @page {
                            size: landscape;
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
            <br><br><br>
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
                <tbody>
                    <tr>
                        <td colspan="12" style="text-align:center;font-size: 10px;">' . $results[$k]['outlet'] . '</td>
                    </tr>
                    <tr>
                        <td colspan="12" style="text-align:center;font-size: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>';

                $results2 = $results[$k]['items'];

                for($j=0; $j<count($results2); $j++) {
                    $pring = '';

                    if($results2[$j]['term'] == 'Cash'){
                        $message .= '<tr>
                            <td width="37.795px" style="font-size: 10px;">' . $results2[$j]['delivery'] . '</td>
                            <td width="41.575px" style="font-size: 10px;">' . $results2[$j]['cancellation'] . '</td>
                            <td width="192.756px" style="font-size: 10px;">' . $results2[$j]['customer'] . '</td>
                            <td width="30.236px" style="font-size: 10px;">' . $results2[$j]['status'] . '</td>
                            <td width="113.386px" style="font-size: 10px;">' . $results2[$j]['po'] . '</td>
                            <td width="132.283px" style="font-size: 10px;">' . $results2[$j]['do'] . '</td>
                            <td width="30.236px" style="font-size: 10px;">' . $results2[$j]['carton'] . '</td>
                            <td width="30.236px" style="font-size: 10px;">' . $results2[$j]['hold'] . '</td>
                            <td colspan="6" style="font-size: 10px;">' . $results2[$j]['notes'] . '</td>
                        </tr>';
                    }
                    else{
                        $pring = ''; // Initialize $pring before the foreach loop
                        $pricingCount = count($results2[$j]['pricing']); // Count the number of pricing items
                    
                        // Loop to create pricing columns
                        for ($i = 0; $i < 5; $i++) {
                            if ($i < $pricingCount) {
                                $pring .= '<td width="37.795px" style="font-size: 10px;">';
                                $pring .= $results2[$j]['pricing'][$i]['size'] . '<br>' . $results2[$j]['pricing'][$i]['price'];
                                $pring .= '</td>';
                            } 
                            else {
                                $pring .= '<td width="37.795px" style="font-size: 10px;"></td>'; // Empty columns if less than 5 pricing items
                            }
                        }
                    
                        // Loop to add extra pricing columns if more than 5
                        if ($pricingCount > 5) {
                            for ($i = 5; $i < $pricingCount && $i < 11; $i++) {
                                $pring .= '<td width="37.795px" style="font-size: 10px;">';
                                $pring .= $results2[$j]['pricing'][$i]['size'] . '<br>' . $results2[$j]['pricing'][$i]['price'];
                                $pring .= '</td>';
                            }
                        }
                    
                        // Calculate colspan for notes column
                        $colspan = 6 - min($pricingCount, 6);
                    
                        // Create the message with adjusted colspan
                        $message .= '<tr>
                                        <td width="37.795px" style="font-size: 10px;">' . $results2[$j]['delivery'] . '</td>
                                        <td width="41.575px" style="font-size: 10px;">' . $results2[$j]['cancellation'] . '</td>
                                        <td width="192.756px" style="font-size: 10px;">' . $results2[$j]['customer'] . '</td>
                                        <td width="30.236px" style="font-size: 10px;">' . $results2[$j]['status'] . '</td>
                                        <td width="113.386px" style="font-size: 10px;">' . $results2[$j]['po'] . '</td>
                                        <td width="132.283px" style="font-size: 10px;">' . $results2[$j]['do'] . '</td>
                                        <td width="30.236px" style="font-size: 10px;">' . $results2[$j]['carton'] . '</td>
                                        <td width="30.236px" style="font-size: 10px;">' . $results2[$j]['hold'] . '</td>'
                                        . $pring .
                                        '<td colspan="' . $colspan . '" style="font-size: 10px;">' . $results2[$j]['notes'] . '</td>
                                    </tr>';
                    }
                    
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


function sortByOutlet($a, $b) {
    return strcmp($a['outlet'], $b['outlet']);
}
?>