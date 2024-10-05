<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';


$query = "SELECT tblclass.className,tblclassarms.classArmName 
    FROM tblclassteacher
    INNER JOIN tblclass ON tblclass.Id = tblclassteacher.classId
    INNER JOIN tblclassarms ON tblclassarms.Id = tblclassteacher.classArmId
    Where tblclassteacher.Id = '$_SESSION[userId]'";

$rs = $conn->query($query);
$num = $rs->num_rows;
$rrw = $rs->fetch_assoc();


// Fetch student details along with class name and class arm name
$query = "
    SELECT 
        tblstudents.firstName,
        tblstudents.lastName,
        tblstudents.email,
        tblstudents.admissionNumber,
        tblclass.className,
        tblclassarms.classArmName,
        tblstudents.dateCreated
    FROM tblstudents
    INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
    INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId
    WHERE tblstudents.Id = '$_SESSION[userId]'
";

$rs = $conn->query($query);
$studentDetails = $rs->fetch_assoc();



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
  <title>AMS - Student Profile</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <style>
    /* Custom CSS for responsive profile photo */
    .profile-photo {
      max-width: 100%;
      height: auto;
    }

    @media (max-width: 768px) {

      .profile-photo {
        width: 100%;
        max-width: 110px;
        margin-bottom: 15px;
        /* Adjust size as needed */
      }
    }
  </style>
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
            <h1 class="h3 mb-0 text-gray-900">Student Profile</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
          </div>

          <!-- Student Profile Card -->
          <div class="row mb-3">
            <div class="col-xl-6 col-md-12 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <h5 class="card-title text-gray-900">Student Profile</h5>
                  <div class="row">
                    <div class="col-md-4 profile-photo-container">
                      <!-- Placeholder or student photo, if available -->
                      <img src="img/user-icn.png" class="img-fluid rounded-square profile-photo " alt="Student Photo">
                    </div>
                    <div class="col-md-8">
                      <p><strong>Full Name:</strong> <?php echo htmlspecialchars($studentDetails['firstName']) . ' ' . htmlspecialchars($studentDetails['lastName']);  ?> </p>
                      <p><strong>Email:</strong> <?php echo htmlspecialchars($studentDetails['email']); ?></p>
                      <p><strong>Admission Number:</strong> <?php echo htmlspecialchars($studentDetails['admissionNumber']); ?></p>
                      <p><strong>Class:</strong> <?php echo htmlspecialchars($studentDetails['className']); ?></p>
                      <p><strong>Class Arm:</strong> <?php echo htmlspecialchars($studentDetails['classArmName']); ?></p>
                      <p><strong>Date Created:</strong> <?php echo htmlspecialchars($studentDetails['dateCreated']); ?></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Additional content can go here -->
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