<?php
// Sample data (you should replace this with your actual data retrieval logic)
$data = [
    ['id' => 1, 'text' => 'Result 1'],
    ['id' => 2, 'text' => 'Result 2'],
    ['id' => 3, 'text' => 'Result 3'],
    // Add more results as needed
];

// Retrieve the search term from the 'q' parameter
if(isset($_GET['search']) && $_GET['search'] != null){
    $searchTerm = $_GET['search'];

    // Filter results based on the search term
    $results = [];
    foreach ($data as $item) {
        if (stripos($item['text'], $searchTerm) !== false) {
            $results[] = $item;
        }
    }
    
    // Prepare and send JSON response
    header('Content-Type: application/json');
    echo json_encode(['results' => $results]);
}
else{
    // Filter results based on the search term
    $results = [];
    foreach ($data as $item) {
        $results[] = $item;
    }

    // Prepare and send JSON response
    header('Content-Type: application/json');
    echo json_encode(['results' => $results]);
}
?>
