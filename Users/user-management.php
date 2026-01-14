<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Management</title>
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
                <li><a href="#"><span class="icon"><i class="fi fi-ss-settings"></i></span>Settings</a></li>
                <li class="logout"><a href="logout.php"><span class="icon"><i class="fi fi-ss-sign-out-alt"></i></span>Log Out</a></li>
            </ul>
        </aside>

    <!-- Main Content -->
    <main class="admin-main">
      <h1>User Management</h1>
      <p>This is a placeholder page for managing users. Actual functionality will be implemented later.</p>

      <div class="placeholder-box">
        <p>Here you might see a list of users, actions like "Suspend", "Reset Password", etc.</p>
      </div>
    </main>
  </div>
</body>
</html>
