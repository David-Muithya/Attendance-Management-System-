<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Check if today is Saturday
$isSaturday = (date('N') == 6); // 6 stands for Saturday

// Get class and arm info
$query = "SELECT tblclass.className, tblclassarms.classArmName 
    FROM tblclassteacher
    INNER JOIN tblclass ON tblclass.Id = tblclassteacher.classId
    INNER JOIN tblclassarms ON tblclassarms.Id = tblclassteacher.classArmId
    WHERE tblclassteacher.Id = '$_SESSION[userId]'";
$rs = $conn->query($query);
$num = $rs->num_rows;
$rrw = $rs->fetch_assoc();

// Session and Term
$querey = mysqli_query($conn, "SELECT * FROM tblsessionterm WHERE isActive ='1'");
$rwws = mysqli_fetch_array($querey);
$sessionTermId = $rwws['Id'];

// Include current time in dateTaken
$dateTaken = date("Y-m-d H:i:s A"); // Date and time

// Skip attendance handling if today is Saturday
if (!$isSaturday) {
  // Check if attendance for today exists
  $qurty = mysqli_query($conn, "SELECT * FROM tblattendance WHERE classId = '$_SESSION[classId]' AND classArmId = '$_SESSION[classArmId]' AND DATE(dateTimeTaken) = CURDATE()");
  $count = mysqli_num_rows($qurty);

  if ($count == 0) { // If record does not exist, insert the new record
    // Insert the students record into the attendance table on page load
    $qus = mysqli_query($conn, "SELECT * FROM tblstudents WHERE classId = '$_SESSION[classId]' AND classArmId = '$_SESSION[classArmId]'");
    while ($ros = $qus->fetch_assoc()) {
      $qquery = mysqli_query($conn, "INSERT INTO tblattendance(admissionNo, classId, classArmId, sessionTermId, status, dateTimeTaken) 
                VALUES('$ros[admissionNumber]', '$_SESSION[classId]', '$_SESSION[classArmId]', '$sessionTermId', '0', '$dateTaken')");
    }
  }

  // Handle form submission
  if (isset($_POST['save'])) {
    $admissionNo = $_POST['admissionNo'];
    $check = $_POST['check'];
    $N = count($admissionNo);
    $status = "";

    // Check if the attendance has already been taken
    $qurty = mysqli_query($conn, "SELECT * FROM tblattendance WHERE classId = '$_SESSION[classId]' AND classArmId = '$_SESSION[classArmId]' AND DATE(dateTimeTaken) = CURDATE() AND status = '1'");
    $count = mysqli_num_rows($qurty);

    if ($count > 0) {
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Attendance has been taken for today!</div>";
    } else { // Update the status to 1 for the checkboxes checked
      for ($i = 0; $i < $N; $i++) {
        if (isset($check[$i])) { // The checked checkboxes
          $qquery = mysqli_query($conn, "UPDATE tblattendance SET status='1', dateTimeTaken='$dateTaken' WHERE admissionNo = '$check[$i]'");

          if ($qquery) {
            $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Attendance Taken Successfully!</div>";
          } else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
          }
        }
      }
    }
  }
} else {
  $statusMsg = "<div class='alert alert-warning d-inline-block'>Today is Saturday. Attendance cannot be taken on Saturdays.</div>";
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
  <title>AMS - Take Attendance</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">

  <script>
    function classArmDropdown(str) {
      if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
      } else {
        if (window.XMLHttpRequest) {
          xmlhttp = new XMLHttpRequest();
        } else {
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("txtHint").innerHTML = this.responseText;
          }
        };
        xmlhttp.open("GET", "ajaxClassArms2.php?cid=" + str, true);
        xmlhttp.send();
      }
    }
  </script>
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
            <h1 class="h3 mb-0 text-gray-800">Take Attendance (Today's Date: <?php echo date("m-d-Y"); ?>)</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Take Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <form method="post">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="card mb-4">
                      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Take Attendance of Class (<?php echo $rrw['className'] . ' - ' . $rrw['classArmName']; ?>) Students </h6>
                        <h6 class="m-0 font-weight-bold text-danger">Note: <i>Click on the checkboxes beside each student to take attendance!</i></h6>
                      </div>
                      <div class="table-responsive p-3">
                        <?php echo $statusMsg; ?>
                        <table class="table align-items-center table-flush table-hover">
                          <thead class="thead-light">
                            <tr>
                              <th>#</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Admission No</th>
                              <th>Class</th>
                              <th>Class Arm</th>
                              <th>Time Taken</th> <!-- New column for time -->
                              <th>Check</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $query = "SELECT tblstudents.Id, tblstudents.admissionNumber, tblclass.className, tblclass.Id AS classId, tblclassarms.classArmName, tblclassarms.Id AS classArmId, tblstudents.firstName, tblstudents.lastName, tblstudents.admissionNumber, tblattendance.dateTimeTaken
      FROM tblstudents
      INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
      INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId
      LEFT JOIN tblattendance ON tblattendance.admissionNo = tblstudents.admissionNumber 
        AND DATE(tblattendance.dateTimeTaken) = CURDATE()
      WHERE tblstudents.classId = '$_SESSION[classId]' AND tblstudents.classArmId = '$_SESSION[classArmId]'";
                            $rs = $conn->query($query);
                            $num = $rs->num_rows;
                            $sn = 0;
                            if ($num > 0) {
                              while ($rows = $rs->fetch_assoc()) {
                                $sn++;
                                $timeTaken = $rows['dateTimeTaken'] ? date("h:i A", strtotime($rows['dateTimeTaken'])) : "Not Taken"; // Format time
                                echo "
          <tr>
            <td>" . $sn . "</td>
            <td>" . $rows['firstName'] . "</td>
            <td>" . $rows['lastName'] . "</td>
            <td>" . $rows['admissionNumber'] . "</td>
            <td>" . $rows['className'] . "</td>
            <td>" . $rows['classArmName'] . "</td>
            <td>" . $timeTaken . "</td> <!-- Show formatted time -->
            <td><input name='check[]' type='checkbox' value='" . $rows['admissionNumber'] . "' class='form-control'></td>
          </tr>";
                                echo "<input name='admissionNo[]' value='" . $rows['admissionNumber'] . "' type='hidden' class='form-control'>";
                              }
                            } else {
                              echo "<div class='alert alert-danger' role='alert'>No Record Found!</div>";
                            }
                            ?>
                          </tbody>
                        </table>
                        <br>
                        <button type="submit" name="save" class="btn btn-primary">Take Attendance</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- Row -->
        </div>
        <!-- Container Fluid -->
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