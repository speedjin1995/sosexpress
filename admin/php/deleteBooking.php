<?php
require_once 'db_connect.php';

session_start();

if (!isset($_SESSION['userID'])) {
    echo '<script type="text/javascript">location.href = "../login.html";</script>';
}

if (isset($_POST['userID'])) {
    $id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
    $del = "1";

    // Get the booking date from the booking table
    $bookingDateQuery = "SELECT booking_date FROM booking WHERE id=?";
    if ($stmt1 = $db->prepare($bookingDateQuery)) {
        $stmt1->bind_param('s', $id);
        $stmt1->execute();
        $stmt1->bind_result($bookingDate);
        $stmt1->fetch();
        $stmt1->close();

        // Check if the booking date exists in the do_request table within the specified range
        $checkRequestQuery = "SELECT id FROM do_request WHERE booking_date >= ? AND booking_date <= ?";
        
        // Construct the range start and end dates
        $rangeStartDate = date('Y-m-d 00:00:00', strtotime($bookingDate));
        $rangeEndDate = date('Y-m-d 23:59:59', strtotime($bookingDate));

        if ($stmt2 = $db->prepare($checkRequestQuery)) {
            $stmt2->bind_param('ss', $rangeStartDate, $rangeEndDate);
            $stmt2->execute();
            $stmt2->store_result();

            if ($stmt2->num_rows > 0) {
                // If the booking date exists in do_request, return failure
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Booking date already exists in DO Request"
                    )
                );
            } else {
                // Update the booking record as the booking date is unique
                $updateQuery = "UPDATE booking SET deleted=? WHERE id=?";
                if ($stmt3 = $db->prepare($updateQuery)) {
                    $stmt3->bind_param('ss', $del, $id);

                    if ($stmt3->execute()) {
                        $stmt3->close();
                        $db->close();

                        echo json_encode(
                            array(
                                "status" => "success",
                                "message" => "Deleted"
                            )
                        );
                    } else {
                        echo json_encode(
                            array(
                                "status" => "failed",
                                "message" => $stmt3->error
                            )
                        );
                    }
                } else {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something's wrong with the update query"
                        )
                    );
                }
            }
            $stmt2->close();
        } else {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something's wrong with the check request query"
                )
            );
        }
    } else {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Something's wrong with the booking date query"
            )
        );
    }
} else {
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Please fill in all the fields"
        )
    );
}
?>
