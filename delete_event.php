<?php
// Include the connection function file
include 'getConnection.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Decode the JSON data received from the client
    $data = json_decode(file_get_contents("php://input"));

    // Check if the event ID is provided
    if (isset($data->eventId)) {
        try {
            // Establish a database connection using the getConnection() function
            $pdo = getConnection();

            // Prepare a delete statement
            $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = :event_id");

            // Bind parameters
            $stmt->bindParam(':event_id', $data->eventId);

            // Execute the delete statement
            if ($stmt->execute()) {
                // Return a success message
                http_response_code(200);
                echo json_encode(array("message" => "Event deleted successfully."));
            } else {
                // Return an error message if deletion fails
                http_response_code(500);
                echo json_encode(array("message" => "Unable to delete event."));
            }
        } catch (PDOException $e) {
            // Return an error message if connection or query fails
            http_response_code(500);
            echo json_encode(array("message" => "Connection failed: " . $e->getMessage()));
        }
    } else {
        // Return an error message if event ID is not provided
        http_response_code(400);
        echo json_encode(array("message" => "Event ID is required."));
    }
} else {
    // Return an error message if request method is not POST
    http_response_code(405);
    echo json_encode(array("message" => "Method Not Allowed"));
}
?>
