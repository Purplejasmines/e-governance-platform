<?php
session_start();
include('../includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the admin's name and role from the database
$adminId = $_SESSION['user_id'];
$sql = "SELECT name, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();
$adminName = "Admin"; // Default name if not found
$adminRole = "Admin"; // Default role if not found

if ($row = $result->fetch_assoc()) {
    $adminName = $row['name'];
    $adminRole = ucfirst($row['role']); // Capitalize the first letter of the role
}

// Fetch statistics
// Total Pending Requests
$totalPendingQuery = "SELECT COUNT(*) AS total_pending FROM requests WHERE status = 'Pending'";
$totalPendingResult = $conn->query($totalPendingQuery);
$totalPending = $totalPendingResult->fetch_assoc()['total_pending'] ?? 0;

// Total Approved Requests
$totalApprovedQuery = "SELECT COUNT(*) AS total_approved FROM requests WHERE status = 'Approved'";
$totalApprovedResult = $conn->query($totalApprovedQuery);
$totalApproved = $totalApprovedResult->fetch_assoc()['total_approved'] ?? 0;

// Total Rejected Requests
$totalRejectedQuery = "SELECT COUNT(*) AS total_rejected FROM requests WHERE status = 'Rejected'";
$totalRejectedResult = $conn->query($totalRejectedQuery);
$totalRejected = $totalRejectedResult->fetch_assoc()['total_rejected'] ?? 0;

// Total Users
$totalUsersQuery = "SELECT COUNT(*) AS total_users FROM users";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult->fetch_assoc()['total_users'] ?? 0;

// Total Admins
$totalAdminsQuery = "SELECT COUNT(*) AS total_admins FROM users WHERE role = 'admin'";
$totalAdminsResult = $conn->query($totalAdminsQuery);
$totalAdmins = $totalAdminsResult->fetch_assoc()['total_admins'] ?? 0;

// Total Citizens
$totalCitizensQuery = "SELECT COUNT(*) AS total_citizens FROM users WHERE role = 'citizen'";
$totalCitizensResult = $conn->query($totalCitizensQuery);
$totalCitizens = $totalCitizensResult->fetch_assoc()['total_citizens'] ?? 0;

// New Users This Week
$newUsersQuery = "SELECT COUNT(*) AS new_users FROM users WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
$newUsersResult = $conn->query($newUsersQuery);
$newUsers = $newUsersResult->fetch_assoc()['new_users'] ?? 0;

// Recent Activity
$recentActivityQuery = "SELECT id, type, status, submitted_at FROM requests ORDER BY submitted_at DESC LIMIT 5";
$recentActivityResult = $conn->query($recentActivityQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-straight/css/uicons-solid-straight.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">

</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <h2>Citizen Portal</h2>
            <ul>
                <li><a href="admin-dashboard.php"><span class="icon"><i class="fi fi-ss-home"></i></span>Dashboard</a></li>
                <li><a href="user-management.php"><span class="icon"><i class="fi fi-ss-users"></i></span>User Management</a></li>
                <li><a href="service-request.php"><span class="icon"><i class="fi fi-ss-scroll"></i></span>Service Requests</a></li>
                <li><a href="#"><span class="icon"><i class="fi fi-ss-messages"></i></span>Messages</a></li>
                <li><a href="settings.php"><span class="icon"><i class="fi fi-ss-settings"></i></span>Settings</a></li>
                <li class="logout"><a href="logout.php"><span class="icon"><i class="fi fi-ss-sign-out-alt"></i></span>Log Out</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Greeting Section -->
            <h1>Welcome back, <?= htmlspecialchars($adminRole) ?> <?= htmlspecialchars($adminName) ?>!</h1>

            <!-- Quick Stats Cards -->
            <div class="stats-cards">
                <div class="card">
                    <h3>Total Pending</h3>
                    <p><?= htmlspecialchars($totalPending) ?></p>
                </div>
                <div class="card">
                    <h3>Total Approved</h3>
                    <p><?= htmlspecialchars($totalApproved) ?></p>
                </div>
                <div class="card">
                    <h3>Total Rejected</h3>
                    <p><?= htmlspecialchars($totalRejected) ?></p>
                </div>
                <div class="card">
                    <h3>Total Users</h3>
                    <p><?= htmlspecialchars($totalUsers) ?></p>
                </div>
            </div>

            <!-- User Statistics Section -->
            <h2>User Statistics</h2>
            <div class="user-stats">
                <div class="stat">
                    <h3>Admins</h3>
                    <p><?= htmlspecialchars($totalAdmins) ?></p>
                </div>
                <div class="stat">
                    <h3>Citizens</h3>
                    <p><?= htmlspecialchars($totalCitizens) ?></p>
                </div>
                <div class="stat">
                    <h3>New Users This Week</h3>
                    <p><?= htmlspecialchars($newUsers) ?></p>
                </div>
            </div>


            <!-- Recent Activity Section -->
            <h2>Recent Activity</h2>
            <div class="recent-activity">
                <ul>
                    <?php while ($activity = $recentActivityResult->fetch_assoc()): ?>
                        <li>
                            Request #<?= htmlspecialchars($activity['id']) ?> 
                            (<?= htmlspecialchars($activity['type']) ?>) 
                            was <?= htmlspecialchars($activity['status']) ?> 
                            on <?= date("F j, Y", strtotime($activity['submitted_at'])) ?>.
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </main>

        <!-- Right Panel -->
        <aside class="admin-notifications">
            <h2>Notifications</h2>
            <p>No Notifications</p>
        </aside>
    </div>

    <script src="http://localhost/egovernance/assets/script/script.js"></script>
</body>
</html>
