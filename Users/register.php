<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $role = htmlspecialchars(trim($_POST['role']), ENT_QUOTES, 'UTF-8');

    // Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        echo "All fields are required.";
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare SQL query to insert user
    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

        if ($stmt->execute()) {
            echo "User registered successfully!";
            header("Location: login.php"); 
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Database error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/register.css">

</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Sign Up</h1>
            <form method="POST" action="register.php">
                <div class="input-group">
                    <span class="icon"><img src="http://localhost/egovernance/assets/user.png" alt="Name" width="20" height="20"></span>
                    <input type="text" name="name" placeholder="Full Name" required>
                </div>
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
                <div class="input-group">
                    <span class="icon"><img src="http://localhost/egovernance/assets/lock.png" alt="Confirm Password" width="20" height="20"></span>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <img src="http://localhost/egovernance/assets/eye.png" alt="Toggle Password" width="20" height="20">
                    </span>
                </div>

                <div class="input-group">
                    <label>Select Role:</label><br>
                    <input type="radio" id="citizen" name="role" value="citizen" checked>
                    <label for="citizen">Citizen</label>
                
                    <input type="radio" id="admin" name="role" value="admin">
                    <label for="admin">Admin</label>
                </div>


                <button type="submit">Register</button>
                <p>Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>


    
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                confirmPasswordField.type = 'text';
            } else {
                passwordField.type = 'password';
                confirmPasswordField.type = 'password';
            }
        }
    </script>
</body>