<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize variables to store messages
$success_message = '';
$error_message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data securely
    $email = $_SESSION['email'];
    $oldPassword = mysqli_real_escape_string($conn, $_POST['old_password']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Hash the old and new passwords
    $hashedOldPassword = md5($oldPassword);
    $hashedNewPassword = md5($newPassword);

    // Check if the old password is correct
    $checkPasswordQuery = "SELECT password, password_changed FROM tblstudents WHERE email = '$email'";
    $result = mysqli_query($conn, $checkPasswordQuery);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $currentPassword = $row['password'];
        $passwordChanged = $row['password_changed'];

        // Verify the old password
        if ($currentPassword === $hashedOldPassword) {
            // Check if new password and confirmation match
            if ($newPassword === $confirmPassword) {
                // Update password for student with associated email in tblstudents
                $updateQuery = "UPDATE tblstudents SET password = '$hashedNewPassword', password_changed = 1 WHERE email = '$email'";

                // Execute the update query
                if (mysqli_query($conn, $updateQuery)) {
                    // Password updated successfully
                    $success_message = "Password updated successfully.";
                } else {
                    // Password update query failed
                    $error_message = "Failed to update password. Please try again later.";
                }
            } else {
                // New password and confirmation do not match
                $error_message = "New password and confirmation do not match.";
            }
        } else {
            // Old password is incorrect
            $error_message = "Old password is incorrect.";
        }
    } 
}

// Check if the user has already changed their password
$email = $_SESSION['email'];
$passwordCheckQuery = "SELECT password_changed FROM tblstudents WHERE email = '$email'";
$result = mysqli_query($conn, $passwordCheckQuery);
$passwordChanged = false;

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $passwordChanged = $row['password_changed'] == 1; // true if changed
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
                            <li class="breadcrumb-item active" aria-current="page">Change Password</li>
                        </ol>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <h4 style="color: skyblue; text-align:center;font-weight:bold;">Change Student Password</h4>
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >
                                        <div class="form-group">
                                            <label for="old_password">Old Password</label>
                                            <input type="password" class="form-control" id="old_password" name="old_password" required >
                                        </div>
                                        <div class="form-group">
                                            <label for="new_password">New Password</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm_password">Confirm New Password</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required >
                                        </div>
                                        <button type="submit" class="btn btn-primary" <?php echo $passwordChanged ? 'disabled' : ''; ?>>Change Password</button>
                                    </form>

                                    <?php
                                    // Display success or error messages
                                    if (!empty($success_message)) {
                                        echo '<div class="alert alert-success mt-3">' . $success_message . '</div>';
                                    } elseif (!empty($error_message)) {
                                        echo '<div class="alert alert-danger mt-3">' . $error_message . '</div>';
                                    } elseif ($passwordChanged) {
                                        echo '<div class="alert alert-warning mt-3">You have already changed your password once. To change the password contact the adminsitrator. </div>';
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
