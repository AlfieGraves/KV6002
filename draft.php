<?php
// Retrieve the user ID from the session
$user_id = $_SESSION['user_id'];

// SQL query to retrieve events for the logged-in user
$query = "SELECT event_title, event_date, event_time FROM events WHERE user_id = $user_id";

// Execute the query
$result = pg_query($db, $query);

// Check if there are any results
if (!$result) {
    die("Error in SQL query: " . pg_last_error());
}

// Output the results
while ($row = pg_fetch_assoc($result)) {
    echo "Event Title: " . $row['event_title'] . " - Event Date: " . $row['event_date'] . " - Event Time: " . $row['event_time'] . "<br>";
}
?>