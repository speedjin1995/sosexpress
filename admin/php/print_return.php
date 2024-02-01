<?php

require_once 'db_connect.php';
 
if(isset($_POST['userID'])){
    $selectedIds = $_POST['userID'];
    $todayDate = date('d/m/Y');
    $today = date("Y-m-d 00:00:00");

    $select_stmt = $db->prepare("SELECT GR_No, return_date, vehicle, driver, return_details, total_carton FROM goods_return WHERE id = ?");

    // Check if the statement is prepared successfully
    if ($select_stmt) {
        // Bind variables to the prepared statement
        $select_stmt->bind_param("s", $selectedIds);
        $select_stmt->execute();
        $select_stmt->bind_result($GR_No, $return_date, $vehicle, $driver, $return_details, $total_carton);
        $results = array();
        $reasons = array();
        $warehouses = array();
        $index = 1;

        while ($select_stmt->fetch()) {
            $poList = json_decode($return_details, true);

            for($i=0; $i<count($poList); $i++){
                if(!in_array($poList[$i]['reason'], $reasons)){
                    array_push($reasons, $poList[$i]['reason']);
                }

                if(!in_array($poList[$i]['warehouse'], $warehouses)){
                    array_push($warehouses, $poList[$i]['warehouse']);
                }

                $results[]=array(
                    'index' => $index,
                    'location' => $poList[$i]['location'],
                    'grn_no' => $poList[$i]['grn_no'],
                    'carton' => $poList[$i]['carton']
                );

                $index++;
            }
        }

        $select_stmt->close();

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
                        <td style="width:70%"></td>
                        <td style="width:30%">
                            <span>'.$GR_No.'<span>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:70%"></td>
                        <td style="width:30%">
                            <span>'.$return_date.'<span>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:70%"></td>
                        <td style="width:30%">
                            <span>'.$driver.'<span>
                        </td>
                    </tr>
                </tbody>
            </table><br><br><br>
            <table style="width:100%">
                <tbody>';

        $count = 0;
        for($j=0; $j<count($results); $j++) {
            $customerName = '';
            if($results[$j]['location'] != null && $results[$j]['location'] != ''){
                $id = $results[$j]['location'];
            
                if ($update_stmt = $db->prepare("SELECT name FROM outlet WHERE id=?")) {
                  $update_stmt->bind_param('s', $id);
                  
                  // Execute the prepared query.
                  if ($update_stmt->execute()) {
                    $result1 = $update_stmt->get_result();
                    
                    if ($row1 = $result1->fetch_assoc()) {
                      $customerName = $row1['name'];
                    }
                  }
            
                  $update_stmt->close();
                }
            }
            $message .= '<tr><td>'.$results[$j]['index'].'</td><td>'.$customerName.'</td><td>'.$results[$j]['grn_no'].'</td><td>'.$results[$j]['carton'].'</td></tr>';
            $count += (int)$results[$j]['carton'];
        }

        $message .= '</tbody><tfoot>
        <tr>
            <th colspan="3"></th>
            <th>'.$count.'</th>
        </tr>
    </tfoot></table><br><br><br>
    <table style="width:50%">
        <tbody>
            <tr>
                <td style="width:40%">';
                for($j=0; $j<count($reasons); $j++) {
                    $message .= $reasons[$j].'<br>';
                }
                $message .= '</td>
                <td style="width:40%">';
                for($j=0; $j<count($warehouses); $j++) {
                    $message .= $warehouses[$j].'<br>';
                }
                $message .= '</td>
                <td style="width:20%"></td>
            </tr>
        </tbody>
    </table>';
    
    $message .= '</html>';

        // Fetch each row
        //$select_stmt->close();

        // Return the results as JSON
        echo json_encode(array('status' => 'success', 'reasons'=>$reasons, 'warehouses'=>$warehouses, 'message' => $message));
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