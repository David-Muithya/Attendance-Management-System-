<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Initialize variables to store messages
$success_message = '';
$error_message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data securely
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);

    // Hash the new password (recommended for security)
    $hashedPassword = md5($newPassword);

    // Get the current password for rollback
    $getCurrentPasswordQuery = "SELECT password FROM tblclassteacher WHERE emailAddress = '$email'";
    $result = mysqli_query($conn, $getCurrentPasswordQuery);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $currentPassword = $row['password'];

        // Update password for teacher with associated email in tblclassteacher
        $updateQuery = "UPDATE tblclassteacher SET password = '$hashedPassword' WHERE emailAddress = '$email'";

        // Execute the update query
        if (mysqli_query($conn, $updateQuery)) {
            // Password updated successfully

            // Initialize PHPMailer
            $mail = new PHPMailer(true);
            $mail_sent = false;
            $retry_count = 0;

            // Prepare email content
            $subject = "Attendance Management System Password Changed";
            $message = "Your password for Student Account has been changed. Your new password is: <b>$newPassword</b>";

            // Attempt to send email up to 3 times
            while (!$mail_sent && $retry_count < 3) {
                try {
                    // Server settings
                    $mail->isSMTP();                                            // Send using SMTP
                    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                    $mail->Host       = 'smtp.gmail.com';                        // Set the SMTP server to send through
                    $mail->Username   = 'jsurya860@gmail.com';                  // SMTP username
                    $mail->Password   = 'vgmwvfxetkvysbgv';                      // SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                    // Recipients
                    $mail->setFrom('jsurya860@gmail.com', 'Admin');
                    $mail->addAddress($email);                                  // Add a recipient

                    // Content
                    $mail->isHTML(true);                                        // Set email format to HTML
                    $mail->Subject = $subject;
                    $mail->Body    = $message;

                    // Send email
                    if ($mail->send()) {
                        // Email sent successfully
                        $mail_sent = true;
                    } else {
                        // Email sending failed, increment retry count
                        $retry_count++;
                    }
                } catch (Exception $e) {
                    // Email sending failed, increment retry count
                    $retry_count++;
                }
            }

            if ($mail_sent) {
                // Email sent successfully after retries
                $success_message = "Password updated successfully. Email sent to teacher with the new password.";
            } else {
                // Failed to send email after retries, rollback password update
                mysqli_query($conn, "UPDATE tblclassteacher SET password = '$currentPassword' WHERE emailAddress = '$email'");
                $error_message = "Password updated successfully, but failed to send email after $retry_count attempts. Password change reverted.";
            }
        } else {
            // Password update query failed
            $error_message = "Failed to update password. Please try again later.";
        }
    } else {
        // Email not found in database
        $error_message = "Email address not found in database.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="img/logo/T-logo.png" rel="icon">
    <title>AMS - Change Password</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include "Includes/sidebar.php"; ?>
        <!-- Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- TopBar -->
                <?php include "Includes/topbar.php"; ?>
                <!-- Topbar -->
                <!-- Container Fluid-->
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Change Password</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Teacher Password</li>
                        </ol>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <h4 style="color: skyblue; text-align:center;font-weight:bold;">Change Teacher Password</h4>
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="new_password">New Password</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Change Password</button>
                                    </form>

                                    <?php
                                    // Display success or error messages
                                    if (!empty($success_message)) {
                                        echo '<div class="alert alert-success mt-3">' . $success_message . '</div>';
                                    } elseif (!empty($error_message)) {
                                        echo '<div class="alert alert-danger mt-3">' . $error_message . '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!---Container Fluid-->
            </div>
            <!-- Footer -->
            <?php include 'includes/footer.php'; ?>
            <!-- Footer -->
        </div>
    </div>

    <!-- Scroll to top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <script src="../vendor/chart.js/Chart.min.js"></script>
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</body>

</html>