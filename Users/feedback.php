<?php
session_start();
include('../includes/db.php');

// Check if the user is logged in and has the required role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'citizen') {
    header("Location: login.php");
    exit();
}

$request_id = $_GET['request_id'] ?? null;
$user_id = $_SESSION['user_id'];

// Validate the request ID
if (!$request_id) {
    echo "<script>alert('Invalid request.'); window.location.href='user-dashboard.php';</script>";
    exit();
}

// Check if the request belongs to the logged-in user
$requestQuery = "SELECT id FROM requests WHERE id = ? AND user_id = ?";
$requestStmt = $conn->prepare($requestQuery);
$requestStmt->bind_param("ii", $request_id, $user_id);
$requestStmt->execute();
$requestResult = $requestStmt->get_result();

if ($requestResult->num_rows === 0) {
    echo "<script>alert('You are not authorized to give feedback for this request.'); window.location.href='user-dashboard.php';</script>";
    exit();
}

// Check if feedback already exists for this request
$feedbackQuery = "SELECT id FROM feedback WHERE request_id = ?";
$feedbackStmt = $conn->prepare($feedbackQuery);
$feedbackStmt->bind_param("i", $request_id);
$feedbackStmt->execute();
$feedbackResult = $feedbackStmt->get_result();

if ($feedbackResult->num_rows > 0) {
    echo "<script>alert('Feedback has already been submitted for this request.'); window.location.href='user-dashboard.php';</script>";
    exit();
}

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Insert feedback into the database
    $sql = "INSERT INTO feedback (user_id, request_id, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $user_id, $request_id, $rating, $comment);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Feedback submitted successfully.'); window.location.href='user-dashboard.php';</script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Submit Feedback</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f1f1;
            padding: 2rem;
        }
        .feedback-form {
            max-width: 500px;
            margin: 0 auto;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
        }
        .feedback-form h2 {
            margin-bottom: 1rem;
            color: #151918;
        }
        label {
            display: block;
            margin: 1rem 0 0.5rem;
            font-weight: 600;
        }
        select, textarea, button {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            background: #151918;
            color: #f1f1f1;
            margin-top: 1rem;
            cursor: pointer;
        }
        button:hover {
            background: #2a2f2f;
        }
    </style>
</head>
<body>

<div class="feedback-form">
    <h2>Rate This Service</h2>
    <form method="POST">
        <label for="rating">Rating (1 - 5):</label>
        <select name="rating" id="rating" required>
            <option value="">-- Select Rating --</option>
            <option value="1">1 - Very Poor</option>
            <option value="2">2 - Poor</option>
            <option value="3">3 - Okay</option>
            <option value="4">4 - Good</option>
            <option value="5">5 - Excellent</option>
        </select>

        <label for="comment">Comment (optional):</label>
        <textarea name="comment" id="comment" rows="4" placeholder="Write your feedback here..."></textarea>

        <button type="submit">Submit Feedback</button>
    </form>
</div>

</body>
</html>