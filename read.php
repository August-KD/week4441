<?php 
// Database configuration
$host = 'sql307.infinityfree.com'; // Replace with your database host
$dbname = 'if0_37529205_week4441'; // Replace with your database name
$username = 'if0_37529205'; // Replace with your MySQL username
$password = 'a631187919'; // Replace with your MySQL password

// Create a connection to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    // If the connection fails, output an error message and terminate the script
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Retrieve the raw input data from the HTTP request input stream
$data = file_get_contents('php://input');
// Decode the JSON string into a PHP array
$classes = json_decode($data, true);

// Check if data was sent
if (!empty($classes)) {
    // Prepare the SQL statement to prevent SQL injection attacks
    $stmt = $conn->prepare("INSERT INTO timetable (subject, day, time, teacher) VALUES (?, ?, ?, ?)");
    // Check if the prepared statement was successful
    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    // Loop through all the class data
    foreach ($classes as $class) {
        // Extract the class information from the array
        $subject = $class['subject'];
        $day = $class['day'];
        $time = $class['time'];
        $teacher = $class['teacher'];

        // Bind parameters to the prepared statement
        $stmt->bind_param("ssss", $subject, $day, $time, $teacher);

        // Execute the prepared statement
        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Error inserting data: ' . $stmt->error]);
            exit;
        }
    }

    // Close the prepared statement
    $stmt->close();

    // Output a success message
    echo json_encode(['status' => 'success', 'message' => 'Data saved successfully!']);
} else {
    // If no data was sent, output an error message
    echo json_encode(['status' => 'error', 'message' => 'No data received!']);
}

// Close the database connection
$conn->close();
?>
