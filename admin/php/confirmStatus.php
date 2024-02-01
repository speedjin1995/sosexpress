<?php
require_once 'db_connect.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if 'ids' parameter is set in the POST data
    if (isset($_POST['ids'])) {
        // Get the array of row IDs from the POST data
        $rowIds = $_POST['ids'];
        $validatedRowIds = array_map('intval', $rowIds);
        $status = 'Confirmed';

        $stmt = $db->prepare("UPDATE do_request SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $rowId);

        foreach ($validatedRowIds as $rowId) {
            $stmt->execute();
        }

        // Close the statement and database connection
        $stmt->close();
        $db->close();

        // Send a response (you can customize the response based on your needs)
        echo json_encode(array('status' => 'success', 'message' => 'Status confirmed successfully'));
    } else {
        // 'ids' parameter is not set in the POST data
        echo json_encode(array('status' => 'error', 'message' => 'Missing IDs parameter'));
    }
} else {
    // Not a POST request
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request method'));
}
?>
