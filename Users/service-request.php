<?php
session_start();
include('../includes/db.php'); // Database connection

// Check if the user is logged in and has the required role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle JSON response for AJAX requests
if (isset($_GET['fetch']) && $_GET['fetch'] === 'requests') {
    header('Content-Type: application/json');

    // Fetch service requests from the database
    $sql = "SELECT * FROM requests";
    $result = $conn->query($sql);

    if ($result) {
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        echo json_encode($requests);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to fetch requests."]);
    }

    $conn->close();
    exit; // Stop further execution
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Service Request Management</title>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-straight/css/uicons-solid-straight.css'>
    <link rel="stylesheet" href= '../assets/css/service-requests.css'>
</head>
<body>
    <div class="admin-container">
       
         <!-- Sidebar -->
         <aside class="admin-sidebar">
            <h2>Citizen Portal</h2>
            <ul>
                <li><a href="admin-dashboard.php"><span class="icon"><i class="fi fi-ss-home"></i></span>Dashboard</a></li>
                <li><a href="#"><span class="icon"><i class="fi fi-ss-users"></i></span>User Management</a></li>
                <li><a href="service-request.php"><span class="icon"><i class="fi fi-ss-scroll"></i></span>Service Requests</a></li>
                <li><a href="#"><span class="icon"><i class="fi fi-ss-messages"></i></span>Messages</a></li>
                <li><a href="#"><span class="icon"><i class="fi fi-ss-settings"></i></span>Settings</a></li>
                <li class="logout"><a href="logout.html"><span class="icon"><i class="fi fi-ss-sign-out-alt"></i></span>Log Out</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="admin-main">
            <h1>Service Request Management</h1>

            <table class="service-requests">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="request-body">
                    <!-- Data will load here dynamically -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="http://localhost/egovernance/assets/script/script.js"></script>
</body>
</html>
