<?php
session_start();
include('../includes/db.php');

// Redirect if not logged in or not a citizen
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'citizen') {
    header("Location: login.php");
    exit();
}

// Greeting from session
$userName = $_SESSION['name'] ?? 'Citizen';

$userId = $_SESSION['user_id'];

// Updated SQL query to use `submitted_at` for ordering
$sql = "SELECT id, type, category, status, submitted_at, details FROM requests WHERE user_id = ? ORDER BY submitted_at DESC";
$stmt = $conn->prepare($sql);

// Check if the statement was prepared successfully
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Fetch notifications for the logged-in user
$notificationsQuery = "SELECT message, sent_at FROM notifications WHERE user_id = ? ORDER BY sent_at DESC LIMIT 5";
$notificationsStmt = $conn->prepare($notificationsQuery);

if (!$notificationsStmt) {
    die("SQL Error: " . $conn->error);
}

$notificationsStmt->bind_param("i", $userId);
$notificationsStmt->execute();
$notificationsResult = $notificationsStmt->get_result();

$notifications = [];
while ($row = $notificationsResult->fetch_assoc()) {
    $notifications[] = $row;
}

$notificationsStmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Citizen Dashboard</title>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-straight/css/uicons-solid-straight.css'>
    <link rel="stylesheet" href="../assets/css/user-dashboard.css">

</head>
<body>
<div class="user-container">
    <!-- Sidebar -->
    <aside class="user-sidebar">
        <h2>Citizen Portal</h2>
        <ul>
            <li><a href="#"><span class="icon"><i class="fi fi-ss-home"></i></span>Dashboard</a></li>
            <li><a href="submit-request.php"><span class="icon"><i class="fi fi-ss-scroll"></i></span>Submit Requests</a></li>
            <li><a href="#"><span class="icon"><i class="fi fi-ss-messages"></i></span>Messages</a></li>
            <li><a href="#"><span class="icon"><i class="fi fi-ss-settings"></i></span>Settings</a></li>
            <li class="logout"><a href="logout.php"><span class="icon"><i class="fi fi-ss-sign-out-alt"></i></span>Log Out</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <h1>Welcome back, <?= htmlspecialchars($userName) ?></h1>
        <div class="quick-actions">
            <button onclick="window.location.href='submit-request.php'">Apply For New Service</button>
            <button onclick="window.location.href='submit-request.php'">View My Requests</button>
            <button>Track Request Status</button>
        </div>

        <h3>My Service Requests</h3>
        <div class="request-cards">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($req = $result->fetch_assoc()): ?>
                    <div class="request-card">
                        <h4>Service: <?= htmlspecialchars($req['type'] . " - " . $req['category']) ?></h4>
                        <div class="status <?= strtolower($req['status']) ?>" data-tooltip="<?php
                            if ($req['status'] === 'Pending') {
                                echo 'Your request has been submitted and is awaiting review.';
                            } elseif ($req['status'] === 'Approved') {
                                echo 'Your request has been approved.';
                            } elseif ($req['status'] === 'Rejected') {
                                echo 'Your request has been rejected.';
                            } else {
                                echo 'Status: ' . htmlspecialchars($req['status']);
                            }
                        ?>">
                            <?= htmlspecialchars($req['status']) ?>
                        </div>
                        <div class="date"><?= date("F j, Y", strtotime($req['submitted_at'])) ?></div>

                        <?php if ($req['status'] === 'Approved'): ?>
                            <div class="actions">
                                <a href="dummy-files/sample.pdf" class="download-btn" download>Download</a>
                                <a href="feedback.php?request_id=<?= $req['id'] ?>" class="feedback-link">Give Feedback</a>
                            </div>
                        <?php else: ?>
                            <button onclick="showDetails('<?= htmlspecialchars($req['details']) ?>')">View Details</button>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No service requests found.</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Notifications Panel -->
    <div class="notifications">
        <h3>Notifications 🔔</h3>
        <div class="mark-all">Mark all as read</div>
        <?php if (empty($notifications)): ?>
            <p>No notifications available.</p>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notif">
                    <p><?= htmlspecialchars($notification['message']) ?></p>
                    <small>Sent at: <?= date("F j, Y, g:i A", strtotime($notification['sent_at'])) ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal for Request Details -->
<div id="detailsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3>Request Details</h3>
        <p id="requestDetails"></p>
    </div>
</div>

<!-- Feedback Modal -->
<div id="feedbackModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-btn" onclick="closeFeedbackModal()">&times;</span>
        <h3>Give Feedback</h3>
        <form id="feedbackForm">
            <input type="hidden" id="feedbackRequestId" name="request_id">
            <label for="rating">Rating:</label>
            <select id="rating" name="rating" required>
                <option value="">-- Select Rating --</option>
                <option value="1">1 - Very Poor</option>
                <option value="2">2 - Poor</option>
                <option value="3">3 - Average</option>
                <option value="4">4 - Good</option>
                <option value="5">5 - Excellent</option>
            </select>
            <label for="comments">Comments:</label>
            <textarea id="comments" name="comments" rows="4" placeholder="Write your feedback here..." required></textarea>
            <button type="submit">Submit Feedback</button>
        </form>
    </div>
</div>

<style>
/* Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 400px;
    text-align: center;
    position: relative;
}

.close-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
    cursor: pointer;
}
</style>

<script>
    function showDetails(details) {
        // Set the details in the modal
        document.getElementById('requestDetails').textContent = details;

        // Show the modal
        document.getElementById('detailsModal').style.display = 'flex';
    }

    function closeModal() {
        // Hide the modal
        document.getElementById('detailsModal').style.display = 'none';
    }
</script>
</body>
</html>
