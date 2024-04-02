<?php
// Include the connection function file
require_once "getConnection.php";

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the JSON data sent from the client
    $data = json_decode(file_get_contents("php://input"));

    // Extract the event data
    $title = $data->title;
    $description = $data->description;
    $date = $data->date;
    $time = $data->time;
    $location = $data->location;

    // Validate the data if necessary

    try {
        // Establish a database connection using the getConnection() function
        $pdo = getConnection();

        // Prepare the update query
        $updateQuery = $pdo->prepare("UPDATE events SET description = ?, date = ?, time = ?, location = ? WHERE title = ?");

        // Bind parameters and execute the query
        $updateQuery->execute([$description, $date, $time, $location, $title]);

        // Check if the update was successful
        if ($updateQuery->rowCount() > 0) {
            // Update successful
            http_response_code(200);
            echo json_encode(array("message" => "Event updated successfully"));
        } else {
            // No rows affected, likely event not found
            http_response_code(404);
            echo json_encode(array("message" => "Event not found"));
        }
    } catch (PDOException $e) {
        // Error occurred, display error message
        http_response_code(500);
        echo json_encode(array("message" => "Error updating event: " . $e->getMessage()));
    }
} else {
    // If the request method is not POST, return method not allowed
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed"));
}
?>
