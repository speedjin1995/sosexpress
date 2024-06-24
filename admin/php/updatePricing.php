<?php
require_once "db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['id'];
    $prices = $_POST['price'];

    $update_successful = true;

    foreach ($ids as $index => $id) {
        $new_price = $prices[$index];

        // Fetch the existing JSON data
        if ($select_stmt = $db->prepare("SELECT price FROM do_request WHERE id=?")) {
            $select_stmt->bind_param('i', $id);

            if ($select_stmt->execute()) {
                $result = $select_stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $price_json = $row['price'];
                    $price_data = json_decode($price_json, true);

                    // Assuming you need to update the price for the first item in the JSON array
                    if (is_array($price_data) && count($price_data) > 0) {
                        $price_data[0]['price'] = $new_price;
                        $price_data[0]['unit_price'] = $new_price; // if unit_price needs to be updated as well

                        // Encode the updated data back to JSON
                        $updated_price_json = json_encode($price_data);

                        // Update the database with the new JSON string
                        if ($update_stmt = $db->prepare("UPDATE do_request SET price=? WHERE id=?")) {
                            $update_stmt->bind_param('si', $updated_price_json, $id);

                            if (!$update_stmt->execute()) {
                                $update_successful = false;
                                break;
                            }

                            $update_stmt->close();
                        } else {
                            $update_successful = false;
                            break;
                        }
                    } else {
                        $update_successful = false;
                        break;
                    }
                } else {
                    $update_successful = false;
                    break;
                }
            } else {
                $update_successful = false;
                break;
            }

            $select_stmt->close();
        } else {
            $update_successful = false;
            break;
        }
    }

    if ($update_successful) {
        echo json_encode(array("status" => "success"));
    } else {
        echo json_encode(array("status" => "failed", "message" => "Error updating prices"));
    }
} else {
    echo json_encode(array("status" => "failed", "message" => "Invalid request method"));
}
?>
