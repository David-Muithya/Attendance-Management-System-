<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Fetch the teacher's assigned class and class arm from the session
$teacherClassId = $_SESSION['classId'];
$teacherClassArmId = $_SESSION['classArmId'];
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
  <title>AMS - View Students</title>
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
            <h1 class="h3 mb-0 text-gray-800">View Students</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">View Students</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">View Students in Your Class</h6>
                </div>
                <div class="card-body">
                  <form method="post">
                    <button type="submit" name="viewStudents" class="btn btn-primary">View Students</button>
                  </form>
                </div>
              </div>

              <!-- Input Group -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">Student List</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Admission No</th>
                            <th>Email</th>
                            <th>Class</th>
                            <th>Class Arm</th>
                          </tr>
                        </thead>

                        <tbody>
                          <?php
                          if (isset($_POST['viewStudents'])) {
                            // Fetch students based on the teacher's assigned class and class arm
                            $query = "SELECT tblstudents.firstName, tblstudents.lastName, tblstudents.admissionNumber, tblstudents.email, tblclass.className, tblclassarms.classArmName
                                      FROM tblstudents
                                      INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
                                      INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId
                                      WHERE tblstudents.classId = '$teacherClassId' 
                                      AND tblstudents.classArmId = '$teacherClassArmId'";
                            $rs = $conn->query($query);
                            $num = $rs->num_rows;
                            $sn = 0;

                            if ($num > 0) {
                              while ($rows = $rs->fetch_assoc()) {
                                $sn++;
                                echo "
                                  <tr>
                                    <td>" . $sn . "</td>
                                    <td>" . $rows['firstName'] . "</td>
                                    <td>" . $rows['lastName'] . "</td>
                                    <td>" . $rows['admissionNumber'] . "</td>
                                    <td>" . $rows['email'] . "</td>
                                    <td>" . $rows['className'] . "</td>
                                    <td>" . $rows['classArmName'] . "</td>
                                  </tr>";
                              }
                            } else {
                              echo "<div class='alert alert-danger' role='alert'>No students found in your class.</div>";
                            }
                          }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!---Container Fluid-->
        </div>
        <!-- Footer -->
        <?php include "Includes/footer.php"; ?>
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
    <!-- Page level plugins -->
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script>
      $(document).ready(function() {
        $('#dataTable').DataTable(); // ID From dataTable 
        $('#dataTableHover').DataTable(); // ID From dataTable with Hover
      });
    </script>
</body>

</html>