<?php
// Include the connection function file
require_once "getConnection.php";

// Get the event data sent from the client-side JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Check if the data is not empty
if (!empty($data)) {
    // Extract event data
    $title = $data['title'];
    $description = $data['description'];
    $date = $data['date'];
    $time = $data['time'];
    $location = $data['location'];
    $category = $data['category'];

    try {
        // Establish a database connection using the getConnection() function
        $pdo = getConnection();

        // Prepare SQL INSERT statement
        $insertQuery = $pdo->prepare("INSERT INTO events (title, description, date, time, location, category) VALUES (?, ?, ?, ?, ?, ?)");

        // Execute the INSERT statement
        $insertQuery->execute([$title, $description, $date, $time, $location, $category]);

        // Return success response
        echo json_encode(["status" => "success"]);
    } catch (PDOException $e) {
        // Return error response
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    // Return error response if data is empty
    echo json_encode(["status" => "error", "message" => "No data received"]);
}
?>
