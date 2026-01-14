<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add CORS headers
header("Access-Control-Allow-Origin: https://vda7900.is.cc");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Set Content-Type dynamically
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json");
} else {
    header("Content-Type: text/html");
}

include('../includes/db.php'); // Include the database connection

// Temporarily bypass user authentication
$user_id = 1; // Set a default user ID for testing purposes

// Handle POST request (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON input
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    // Check if JSON decoding failed
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid JSON input"]);
        exit;
    }

    // Extract and sanitize fields
    $type = htmlspecialchars($data["type"] ?? '', ENT_QUOTES, 'UTF-8');
    $category = htmlspecialchars($data["category"] ?? '', ENT_QUOTES, 'UTF-8');
    $details = htmlspecialchars($data["details"] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_var($data["email"] ?? '', FILTER_VALIDATE_EMAIL);

    // Debugging: Log input values
    error_log("User ID: " . ($user_id ?? 'Not Set'));

    // Validate required fields
    $missingFields = [];
    if (!$type) $missingFields[] = "type";
    if (!$category) $missingFields[] = "category";
    if (!$details) $missingFields[] = "details";
    if (!$email) $missingFields[] = "email";

    if (!empty($missingFields)) {
        http_response_code(400);
        echo json_encode(["message" => "Missing fields: " . implode(", ", $missingFields)]);
        exit;
    }

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO requests (user_id, type, category, details, email, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("issss", $user_id, $type, $category, $details, $email);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Request submitted successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to submit request"]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Render the form for GET requests
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Citizen - Service Request Management</title>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-straight/css/uicons-solid-straight.css'>
        <link rel="stylesheet" href='../assets/css/user-requests.css'>
    </head>
    <body>
        <div class="user-container">
            <!-- Sidebar -->
            <aside class="user-sidebar">
                <h2>Citizen Portal</h2>
                <ul>
                    <li><a href="user-dashboard.php"><span class="icon"><i class="fi fi-ss-home"></i></span>Dashboard</a></li>
                    <li><a href="user-requests.php"><span class="icon"><i class="fi fi-ss-scroll"></i></span>Submit Requests</a></li>
                    <li><a href="#"><span class="icon"><i class="fi fi-ss-messages"></i></span>Messages</a></li>
                    <li><a href="#"><span class="icon"><i class="fi fi-ss-settings"></i></span>Settings</a></li>
                    <li class="logout"><a href="logout.php"><span class="icon"><i class="fi fi-ss-sign-out-alt"></i></span>Log Out</a></li>
                </ul>
            </aside>

            <!-- Main Content -->
            <div class="form-container">
                <h2>Submit a New Request</h2>
                <form id="requestForm" action="egovernance/Users/submit-request.php" method="POST">
                    <label for="email">Enter your email address:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>

                    <label for="type">Select a service type:</label>
                    <select id="type" name="type" required>
                        <option value="">-- Select Type --</option>
                        <option value="Certificate">Certificate</option>
                        <option value="Permit">Permit</option>
                    </select>

                    <label for="category">Select category:</label>
                    <select id="category" name="category" required>
                        <option value="">-- Select Category --</option>
                    </select>

                    <label for="details">Enter details about your request:</label>
                    <textarea id="details" name="details" rows="4" placeholder="Provide additional details..." required></textarea>

                    <button type="submit" id="submitRequest">Submit Request</button>
                </form>
            </div>
        </div>

        <script src="http://localhost/egovernance/assets/script/script.js"></script>
    </body>
</html>