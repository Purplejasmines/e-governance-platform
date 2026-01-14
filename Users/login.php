<?php
session_start();
include('../includes/db.php'); // Include the database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$alertMessage = ""; // Initialize an empty alert message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate inputs
    if (empty($email) || empty($password)) {
        $alertMessage = "Please fill in all fields.";
    } else {
        // Prepare SQL query
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        if (!$stmt) {
            die("SQL error: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Store user information in the session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin-dashboard.php");
                    exit();
                } elseif ($user['role'] === 'citizen') {
                    header("Location: user-dashboard.php");
                    exit();
                } else {
                    $alertMessage = "Invalid role.";
                }
            } else {
                $alertMessage = "Invalid password!";
            }
        } else {
            $alertMessage = "User not found!";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/register.css">

</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Login</h1>
            <!-- Alert Placeholder -->
            <div id="alert" class="alert <?php echo !empty($alertMessage) ? 'error' : ''; ?>" style="<?php echo !empty($alertMessage) ? 'display: block;' : 'display: none;'; ?>">
                <?php echo htmlspecialchars($alertMessage); ?>
            </div>
            <form method="POST" action="login.php">
                <div class="input-group">
                    <span class="icon"><img src="http://localhost/egovernance/assets/envelope.png" alt="Email" width="20" height="20"></span>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <span class="icon"><img src="http://localhost/egovernance/assets/lock.png" alt="Password" width="20" height="20"></span>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <img src="http://localhost/egovernance/assets/eye.png" alt="Toggle Password" width="20" height="20">
                    </span>
                </div>
                <button type="submit">Login</button>
                <a href="forgot-password.html" class="forgot-password">Forgot Password?</a>
                <p>Don't have an account? <a href="register.php">Sign Up</a></p>
            </form>
        </div>
    </div>

    
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }
    </script>
</body>
</html>