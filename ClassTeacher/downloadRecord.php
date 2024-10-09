<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Default to today's date if no date is provided
$dateTaken = isset($_POST['date']) ? $_POST['date'] : date("Y-m-d");

// Initialize status message variable
$statusMessage = '';
$reportGenerated = false; // Track if the report has been generated

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Fetch attendance data
  $query = "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, 
            tblclass.className, tblclassarms.classArmName, tblsessionterm.sessionName, tblsessionterm.termId, 
            tblterm.termName, tblstudents.firstName, tblstudents.lastName, tblstudents.admissionNumber
            FROM tblattendance
            INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
            INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
            INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
            INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
            INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
            WHERE DATE(tblattendance.dateTimeTaken) = '$dateTaken' 
            AND tblattendance.classId = '$_SESSION[classId]' 
            AND tblattendance.classArmId = '$_SESSION[classArmId]'";

  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) == 0) {
    // Handle case with no data
    $statusMessage = '<div id="statusMessage" class="alert alert-warning text-center mt-3" role="alert">No data available for the selected date.</div>';
  } else {
    // Set a flag to indicate that the report was generated
    $reportGenerated = true;

    // Set headers for file download
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Attendance_list_" . $dateTaken . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Output table data
    echo '<table class="table table-bordered">
        <thead>
            <tr>
            <th>#</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Admission No</th>
            <th>Class</th>
            <th>Class Arm</th>
            <th>Session</th>
            <th>Term</th>
            <th>Status</th>
            <th>Date</th>
            </tr>
        </thead>
        <tbody>';

    $cnt = 1;
    while ($row = mysqli_fetch_array($result)) {
      $status = ($row['status'] == '1') ? "Present" : "Absent";

      echo '<tr>  
            <td>' . $cnt . '</td> 
            <td>' . htmlspecialchars($row['firstName']) . '</td> 
            <td>' . htmlspecialchars($row['lastName']) . '</td> 
            <td>' . htmlspecialchars($row['admissionNumber']) . '</td> 
            <td>' . htmlspecialchars($row['className']) . '</td> 
            <td>' . htmlspecialchars($row['classArmName']) . '</td>    
            <td>' . htmlspecialchars($row['sessionName']) . '</td>     
            <td>' . htmlspecialchars($row['termName']) . '</td>    
            <td>' . $status . '</td>      
            <td>' . htmlspecialchars($row['dateTimeTaken']) . '</td>                    
            </tr>';
      $cnt++;
    }
    echo '</tbody></table>';
    exit; // Ensure no additional content is sent
  }
}

// Store the flag in the session to track report generation status
$_SESSION['report_generated'] = $reportGenerated;
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
  <title> AMS - Download Attendance Report</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">


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
            <h1 class="h3 mb-0 text-gray-800">Download Attendance Report</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page"> Attendance Report</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="container">
                <div class="card shadow-sm">
                  <div class="card-body">
                    <h1 class="text-center mb-4">Download Attendance Report</h1>
                    <form action="" method="post">
                      <div class="form-group">
                        <label for="date">Select Date:</label>
                        <input type="date" id="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                      </div>
                      <button type="submit" class="btn btn-primary btn-block mb-3">Generate Report</button>
                    </form>
                    <?php
                    // Display the status message only if no report has been generated and there is a message
                    if (!$_SESSION['report_generated'] && !empty($statusMessage)) {
                      echo $statusMessage;
                    }
                    // Reset the session variable after displaying the message
                    $_SESSION['report_generated'] = false;
                    ?>
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

      $(document).ready(function() {
        // Automatically hide the status message after 5 seconds
        setTimeout(function() {
          $("#statusMessage").fadeOut("fast");
        }, 2000); // 2000 milliseconds = 2 second
      });
    </script>
</body>

</html>