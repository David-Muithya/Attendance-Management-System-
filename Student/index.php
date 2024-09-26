<?php

include '../Includes/dbcon.php';
include '../Includes/session.php';

// Check if session contains email
if (isset($_SESSION['email'])) {
  $email = $_SESSION['email'];

  // Fetch student ID using the email address
  $queryStudent = "SELECT admissionNumber FROM tblstudents WHERE email = '$email'";
  $resultStudent = $conn->query($queryStudent);

  if ($resultStudent->num_rows > 0) {
    $rowStudent = $resultStudent->fetch_assoc();
    $admissionNo = $rowStudent['admissionNumber'];

    // Fetch total present days for the logged-in student
    $queryPresent = "
        SELECT COUNT(*) AS totalPresent
        FROM tblattendance
        WHERE admissionNo = '$admissionNo' 
          AND status = '1'
        ";

    $resultPresent = $conn->query($queryPresent);
    $rowPresent = $resultPresent->fetch_assoc();
    $totalPresentDays = $rowPresent['totalPresent'];

    // Fetch total absent days for the logged-in student
    $queryAbsent = "
        SELECT COUNT(*) AS totalAbsent
        FROM tblattendance
        WHERE admissionNo = '$admissionNo' 
          AND status = '0'
        ";

    $resultAbsent = $conn->query($queryAbsent);
    $rowAbsent = $resultAbsent->fetch_assoc();
    $totalAbsentDays = $rowAbsent['totalAbsent'];
  } else {
    echo '<div id="statusMessage" class="alert alert-danger text-center mt-3" role="alert">Student record not found.</div>';
    exit();
  }
} else {
  echo '<div id="statusMessage" class="alert alert-danger text-center mt-3" role="alert">Session expired or invalid. Please log in again.</div>';
  exit();
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
  <title>AMS - Student Dashboard</title>
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
            <h1 class="h3 mb-0 text-gray-800">Student Dashboard </h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>

          <div class="row mb-3">
            <!-- New User Card Example -->

            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Total Present Days</div>
                      <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $totalPresentDays; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 20.4%</span>
                        <span>Since last month</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar-check fa-2x text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>



            <!-- Present Days -->
            <?php
            $query1 = mysqli_query($conn, "SELECT * from tblattendance where classId = '$_SESSION[classId]' and classArmId = '$_SESSION[classArmId]'");
            $totAttendance = mysqli_num_rows($query1);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Total Absent Days</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalAbsentDays; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-danger mr-2"><i class="fas fa-arrow-down"></i> 1.10%</span>
                        <span>Since yesterday</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar-times fa-2x text-warning"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!--Row-->

            <!-- <div class="row">
            <div class="col-lg-12 text-center">
              <p>Do you like this template ? you can download from <a href="https://github.com/indrijunanda/RuangAdmin"
                  class="btn btn-primary btn-sm" target="_blank"><i class="fab fa-fw fa-github"></i>&nbsp;GitHub</a></p>
            </div>
          </div> -->

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
</body>

</html>