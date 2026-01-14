<?php
include('egovernance\includes\db.php');
include('egovernance\includes\send-email.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Validate and sanitize inputs
$requestId = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
$newStatus = htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8');

if (!$requestId || !$newStatus) {
    http_response_code(400); // Bad Request
    echo "Invalid input.";
    exit;
}

// Validate status
$allowedStatuses = ['Approved', 'Rejected', 'Pending'];
if (!in_array($newStatus, $allowedStatuses)) {
    http_response_code(400); // Bad Request
    echo "Invalid status.";
    exit;
}

// Get request info
$query = "SELECT email, service_type, category FROM requests WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    error_log("Database error: " . $conn->error);
    http_response_code(500); // Internal Server Error
    echo "Failed to fetch request info.";
    exit;
}

$stmt->bind_param("i", $requestId);
$stmt->execute();
$stmt->bind_result($email, $type, $category);
if (!$stmt->fetch()) {
    http_response_code(404); // Not Found
    echo "Request not found.";
    $stmt->close();
    exit;
}
$stmt->close();

// Update the status
$update = "UPDATE requests SET status = ? WHERE id = ?";
$stmt = $conn->prepare($update);
if (!$stmt) {
    error_log("Database error: " . $conn->error);
    http_response_code(500); // Internal Server Error
    echo "Failed to prepare update query.";
    exit;
}

$stmt->bind_param("si", $newStatus, $requestId);
if ($stmt->execute()) {
    // Send the email
    $emailResult = sendStatusEmail($email, $type, $category, $newStatus);
    if ($emailResult['success']) {
        http_response_code(200); // Success
        echo "Status updated and email sent.";
    } else {
        error_log("Email error: " . $emailResult['error']);
        http_response_code(500); // Internal Server Error
        echo "Status updated, but email failed.";
    }
} else {
    error_log("Failed to update status for request ID $requestId: " . $stmt->error);
    http_response_code(500); // Internal Server Error
    echo "Status update failed.";
}
$stmt->close();
?>
