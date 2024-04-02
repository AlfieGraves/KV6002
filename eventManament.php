<?php
// Include the session file
require_once "session.php";

// Check if the user is logged in
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    // User is logged in, display the event management page

    // Include the connection function file
    include 'getConnection.php';

    try {
        // Establish a database connection using the getConnection() function
        $pdo = getConnection();

        // Retrieve events from the database
        $stmt = $pdo->query("SELECT * FROM events ORDER BY date");
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h1>Event Management System</h1>";
        if(isset($_SESSION["organizer"]) && $_SESSION["organizer"] === true) {
            echo "<button onclick='addEventPopup()'>Add Event</button>";
        }
        

        // Display events
        foreach ($events as $event) {
            echo "<div class='event-container'>";
            echo "<h2>{$event['title']}</h2>";
            echo "<p>{$event['description']}</p>";
            echo "<p>Date: {$event['date']} Time: {$event['time']} Location: {$event['location']}</p>";
            echo "<button onclick='viewEventPopup(\"{$event['title']}\", \"{$event['description']}\", \"{$event['date']}\", \"{$event['time']}\", \"{$event['location']}\")'>View</button>";
            if(isset($_SESSION["organizer"]) && $_SESSION["organizer"] === true) {
                echo "<button onclick='editEventPopup(\"{$event['title']}\", \"{$event['description']}\", \"{$event['date']}\", \"{$event['time']}\", \"{$event['location']}\")'>Edit</button>";
                echo "<button onclick='deleteEvent({$event['event_id']})'>Delete</button>";
            }
            echo "</div>";
        }
    } catch (PDOException $e) {
        // Display an error message if the connection fails
        echo "Connection failed: " . $e->getMessage();
    }
} else {
    // User is not logged in, redirect them to the login page
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link rel="stylesheet" href="event.css">
    <style>
        /* CSS styles for event containers */
        .event-container {
            background-color: #f9f9f9;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .event-container h2 {
            margin-top: 0;
        }
        .event-container p {
            margin: 10px 0;
        }
        .event-container button {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<!-- JavaScript functions for event management -->
<script>
    function viewEventPopup(title, description, date, time, location) {
        // Create and display modal popup
        const modalContent = `
            <div class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <h2>${title}</h2>
                    <p><strong>Description:</strong> ${description}</p>
                    <p><strong>Date:</strong> ${date}</p>
                    <p><strong>Time:</strong> ${time}</p>
                    <p><strong>Location:</strong> ${location}</p>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalContent);
    }

    function editEventPopup(title, description, date, time, location) {
        // Create and display modal popup for editing
        const editModalContent = `
            <div class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeEditModal()">&times;</span>
                    <h2>Edit Event</h2>
                    <label for="edit-title">Title:</label>
                    <input type="text" id="edit-title" value="${title}" readonly>
                    <br>
                    <label for="edit-description">Description:</label>
                    <textarea id="edit-description">${description}</textarea>
                    <br>
                    <label for="edit-date">Date:</label>
                    <input type="date" id="edit-date" value="${date}">
                    <br>
                    <label for="edit-time">Time:</label>
                    <input type="time" id="edit-time" value="${time}">
                    <br>
                    <label for="edit-location">Location:</label>
                    <input type="text" id="edit-location" value="${location}">
                    <br>
                    <button onclick="saveEditedEvent()">Save</button>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', editModalContent);
    }

    function addEventPopup() {
        // Create and display modal popup for adding an event

        const addModalContent = `
            <div class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeAddModal()">&times;</span>
                    <h2>Add Event</h2>
                    <br>
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                    <br>
                    <label for="description">Description:</label>
                    <textarea id="description" name="description"></textarea>
                    <br>
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                    <br>
                    <label for="time">Time:</label>
                    <input type="time" id="time" name="time" required>
                    <br>
                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location">
                    <br>
                    <label for="category">Category:</label>
                    <input type="text" id="category" name="category">
                    <br>
                    <button onclick="saveNewEvent()">Add</button>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', addModalContent);
    }

    function closeModal() {
        const modal = document.querySelector('.modal');
        if (modal) {
            modal.remove();
        }
    }

    function closeEditModal() {
        const editModal = document.querySelector('.modal');
        if (editModal) {
            editModal.remove();
        }
    }

    function closeAddModal() {
        const addModal = document.querySelector('.modal');
        if (addModal) {
            addModal.remove();
        }
    }

    function deleteEvent(eventId) {
    // Confirm with the user before deleting the event
    if (confirm("Are you sure you want to delete this event?")) {
        // Send the event ID to the server-side script for deletion
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_event.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Event deleted successfully, do something if needed
                    console.log('Event deleted successfully');
                    location.reload();
                } else {
                    // Error deleting the event
                    console.error('Error deleting event:', xhr.responseText);
                    // Display an error message or handle the error as needed
                }
            }
        };
        xhr.send(JSON.stringify({ eventId: eventId }));
    }
}


    function saveEditedEvent() {
    // Fetch edited values from the form
    const title = document.getElementById('edit-title').value;
    const description = document.getElementById('edit-description').value;
    const date = document.getElementById('edit-date').value;
    const time = document.getElementById('edit-time').value;
    const location = document.getElementById('edit-location').value;

    // Create an object to hold the edited data
    const editedEventData = {
        title: title,
        description: description,
        date: date,
        time: time,
        location: location
    };

    // Send the edited data to the server-side script using AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_event.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // Event updated successfully, do something if needed
                console.log('Event updated successfully');
                // Close the modal
                closeEditModal();
                location.reload();
            } else {
                // Error updating the event
                console.error('Error updating event:', xhr.responseText);
                // Display an error message or handle the error as needed
            }
        }
    };
    xhr.send(JSON.stringify(editedEventData));
}


    function saveNewEvent() {
    // Fetch values from the form
    const title = document.getElementById('title').value;
    const description = document.getElementById('description').value;
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const location = document.getElementById('location').value;
    const category = document.getElementById('category').value;

    // Create an object to hold the data
    const eventData = {
        title: title,
        description: description,
        date: date,
        time: time,
        location: location,
        category: category
    };

    // Send the data to a server-side script using AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_event.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // Event saved successfully, do something if needed
                console.log('Event saved successfully');
                // Close the modal
                closeAddModal();
                location.reload();
            } else {
                // Error saving the event
                console.error('Error saving event:', xhr.responseText);
                // Display an error message or handle the error as needed
            }
        }
    };
    xhr.send(JSON.stringify(eventData));
}

</script>
</body>
</html>
