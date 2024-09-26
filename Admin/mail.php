<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $recipient_email = $_POST['recipient_email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Host       = 'smtp.gmail.com';                        // Set the SMTP server to send through
        $mail->Username   = 'jsurya860@gmail.com';                  // SMTP username
        $mail->Password   = 'vgmwvfxetkvysbgv';                      // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom('jsurya860@gmail.com', 'Admin');
        $mail->addAddress($recipient_email);                        // Add a recipient

        //Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        // Send email
        if ($mail->send()) {
            $_SESSION['status'] = "Thank you, your message has been sent.";
        } else {
            $_SESSION['status'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        // Redirect to the referring page after processing form submission
        header("Location: {$_SERVER["PHP_SELF"]}");
        exit;
        
    } catch (Exception $e) {
        $_SESSION['status'] = "Message could not be sent. Mailer Error: {$e->getMessage()}";
    }
}

// If not a POST request or after processing, continue to display the form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email Form</title>
</head>
<body>
    <h2>Send Email</h2>
    
    <?php if (isset($_SESSION['status'])): ?>
        <p><?php echo $_SESSION['status']; ?></p>
        <?php unset($_SESSION['status']); ?>
    <?php endif; ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <label for="recipient_email">Recipient Email:</label><br>
        <input type="email" id="recipient_email" name="recipient_email" required><br><br>

        <label for="subject">Subject:</label><br>
        <input type="text" id="subject" name="subject" required><br><br>

        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="4" required></textarea><br><br>

        <input type="submit" name="submit" value="Send Email">
    </form>
</body>
</html>
