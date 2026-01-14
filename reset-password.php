<form action="reset-password.php" method="POST">
    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
    <label>New Password:</label>
    <input type="password" name="new_password" required>
    <button type="submit">Reset Password</button>
</form>

<?php
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    // Update password in database
    $sql = "UPDATE Users SET password='$new_password', reset_token=NULL WHERE reset_token='$token'";
    if ($conn->query($sql) === TRUE) {
        echo "Password reset successfully!";
        header("Location: index.html");
        exit();
    } else {
        echo "Invalid token or error resetting password.";
    }
}
?>
