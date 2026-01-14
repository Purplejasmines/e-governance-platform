<?php
include('egovernance\includes\db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Check if email exists
    $sql = "SELECT * FROM Users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));
        
        // Store the token in the database
        $sql = "UPDATE Users SET reset_token='$token' WHERE email='$email'";
        $conn->query($sql);

        // Send reset email
        $resetLink = "http://localhost/e-governance/reset-password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: $resetLink";
        $headers = "From: no-reply@e-governance.com\r\n";

        if (mail($email, $subject, $message, $headers)) {
            echo "Password reset link sent!";
        } else {
            echo "Failed to send email.";
        }
    } else {
        echo "No account found with this email!";
    }
}
?>
