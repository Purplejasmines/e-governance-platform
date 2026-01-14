<?php
session_start();
include('../includes/db.php'); // Include database connection

// Check if the user is logged in and has the required role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(["message" => "Unauthorized access."]);
    exit;
}

// Get the JSON payload
$data = json_decode(file_get_contents("php://input"), true);

// Validate the input
if (!isset($data['id']) || !isset($data['status'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Invalid input."]);
    exit;
}

$requestId = $data['id'];
$newStatus = $data['status'];

// Update the request status in the database
$sql = "UPDATE requests SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("si", $newStatus, $requestId);
    if ($stmt->execute()) {
        // Fetch the user_id associated with the request
        $userQuery = "SELECT user_id FROM requests WHERE id = ?";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param("i", $requestId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        $user = $userResult->fetch_assoc();

        if ($user) {
            $userId = $user['user_id'];

            // Insert a notification into the notifications table
            $message = "Your service request #$requestId has been $newStatus.";
            $notificationQuery = "INSERT INTO notifications (user_id, request_id, message) VALUES (?, ?, ?)";
            $notificationStmt = $conn->prepare($notificationQuery);
            $notificationStmt->bind_param("iis", $userId, $requestId, $message);
            $notificationStmt->execute();
        }

        echo json_encode(["message" => "Status updated successfully."]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["message" => "Failed to update status."]);
    }
    $stmt->close();
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "Failed to prepare the statement."]);
}

$conn->close();
?>
