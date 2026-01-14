<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer dependencies
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

/**
 * Send an email to the user when their request status is updated.
 *
 * @param string $to Recipient's email address.
 * @param string $userName Name of the user.
 * @param string $requestType Type of the request (e.g., Birth Certificate, Business Permit).
 * @param string $newStatus The new status (Approved, Rejected, etc.).
 * @return array An array with 'success' (bool) and 'error' (string) keys.
 */
function sendStatusEmail($to, $userName, $requestType, $newStatus) {
    // Validate email address
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email address: $to");
        return ['success' => false, 'error' => 'Invalid email address'];
    }

    // Sanitize input
    $userName = htmlspecialchars($userName, ENT_QUOTES, 'UTF-8');
    $requestType = htmlspecialchars($requestType, ENT_QUOTES, 'UTF-8');
    $newStatus = htmlspecialchars($newStatus, ENT_QUOTES, 'UTF-8');

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.gmail.com'; // Use environment variable or default to Gmail
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USERNAME'); // Use environment variable
        $mail->Password   = getenv('SMTP_PASSWORD'); // Use environment variable
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom(getenv('SMTP_FROM_EMAIL') ?: 'your_email@gmail.com', 'E-Governance');
        $mail->addAddress($to, $userName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Update: Your $requestType Request Status";
        $mail->Body    = "
            <p>Hi <strong>$userName</strong>,</p>
            <p>Your <strong>$requestType</strong> request has been <strong>$newStatus</strong>.</p>
            <p>If you have any questions, feel free to contact support.</p>
            <br>
            <p>Thank you,<br>The E-Governance Team</p>
        ";

        $mail->send();
        return ['success' => true, 'error' => ''];

    } catch (Exception $e) {
        error_log("Failed to send email to $to for $requestType request. Error: " . $mail->ErrorInfo);
        return ['success' => false, 'error' => $mail->ErrorInfo];
    }
}
?>