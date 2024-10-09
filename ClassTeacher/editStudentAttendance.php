<?php
session_start(); // Start the session

include '../Includes/dbcon.php';

// Handle AJAX request to update attendance status
if (isset($_POST['attendanceId']) && isset($_POST['newStatus'])) {
  $attendanceId = $_POST['attendanceId'];
  $newStatus = $_POST['newStatus'];
  $newStatusText = ($newStatus == '1') ? 'Present' : 'Absent';
  $newStatusColor = ($newStatus == '1') ? '#00FF00' : '#FF0000';

  // Update the attendance status in the database
  $updateQuery = "UPDATE tblattendance SET status='$newStatus' WHERE Id='$attendanceId'";
  if ($conn->query($updateQuery)) {
    echo json_encode(array(
      'success' => true,
      'newStatusText' => $newStatusText,
      'newStatusColor' => $newStatusColor
    ));
  } else {
    echo json_encode(array('success' => false));
  }
  exit; // Exit to ensure no further code is executed
}

// Initialize variables
$statusMsg = "";
$attendanceRecords = [];

// Handle form submission to view attendance records
if (isset($_POST['view'])) {
  $admissionNumber = $_POST['admissionNumber'];
  $type = $_POST['type'];
  $query = "";

  if ($type == "1") { // All Attendance
    $query = "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, tblclass.className,
                  tblclassarms.classArmName, tblsessionterm.sessionName, tblsessionterm.termId, tblterm.termName,
                  tblstudents.firstName, tblstudents.lastName, tblstudents.admissionNumber
                  FROM tblattendance
                  INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
                  INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
                  INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
                  INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
                  INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
                  WHERE tblattendance.admissionNo = '$admissionNumber' AND tblattendance.classId = '$_SESSION[classId]' AND tblattendance.classArmId = '$_SESSION[classArmId]'";
  } elseif ($type == "2") { // Single Date Attendance
    $singleDate = $_POST['singleDate'];
    $query = "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, tblclass.className,
                  tblclassarms.classArmName, tblsessionterm.sessionName, tblsessionterm.termId, tblterm.termName,
                  tblstudents.firstName, tblstudents.lastName, tblstudents.admissionNumber
                  FROM tblattendance
                  INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
                  INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
                  INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
                  INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
                  INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
                  WHERE  DATE(tblattendance.dateTimeTaken) = '$singleDate' AND tblattendance.admissionNo = '$admissionNumber' AND tblattendance.classId = '$_SESSION[classId]' AND tblattendance.classArmId = '$_SESSION[classArmId]'";
  } elseif ($type == "3") { // Date Range Attendance
    $fromDate = $_POST['fromDate'];
    $toDate = $_POST['toDate'];
    $query = "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, tblclass.className,
                  tblclassarms.classArmName, tblsessionterm.sessionName, tblsessionterm.termId, tblterm.termName,
                  tblstudents.firstName, tblstudents.lastName, tblstudents.admissionNumber
                  FROM tblattendance
                  INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
                  INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
                  INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
                  INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
                  INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
                  WHERE DATE(tblattendance.dateTimeTaken) BETWEEN '$fromDate' AND '$toDate' AND tblattendance.admissionNo = '$admissionNumber' AND tblattendance.classId = '$_SESSION[classId]' AND tblattendance.classArmId = '$_SESSION[classArmId]'";
  }

  // Debugging SQL query
  // echo $query; 

  $result = $conn->query($query);

  if ($result) {
    $attendanceRecords = $result->fetch_all(MYSQLI_ASSOC);
  } else {
    $statusMsg = "<div class='alert alert-danger' role='alert'>Error fetching records!</div>";
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
  <title>AMS - Edit Attendance</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">

  <script>
    function toggleStatus(attendanceId, currentStatus) {
      const newStatus = (currentStatus === '1') ? '0' : '1'; // Toggle status
      const xhr = new XMLHttpRequest();
      xhr.open('POST', '', true); // Post to the same file
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          const response = JSON.parse(xhr.responseText);
          if (response.success) {
            document.getElementById('status-' + attendanceId).innerText = response.newStatusText;
            document.getElementById('status-' + attendanceId).style.backgroundColor = response.newStatusColor;
          } else {
            alert('Error updating status');
          }
        }
      };
      xhr.send('attendanceId=' + attendanceId + '&newStatus=' + newStatus);
    }

    function typeDropDown(type) {
      document.getElementById('dateSingle').style.display = (type == '2') ? 'block' : 'none';
      document.getElementById('dateRange').style.display = (type == '3') ? 'block' : 'none';
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
            <h1 class="h3 mb-0 text-gray-800">Edit Student Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Edit Student Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Edit Attendance</h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Select Student<span class="text-danger ml-2">*</span></label>
                        <?php
                        $qry = "SELECT * FROM tblstudents WHERE classId = '$_SESSION[classId]' AND classArmId = '$_SESSION[classArmId]' ORDER BY firstName ASC";
                        $result = $conn->query($qry);
                        if ($result && $result->num_rows > 0) {
                          echo '<select required name="admissionNumber" class="form-control mb-3">';
                          echo '<option value="">--Select Student--</option>';
                          while ($rows = $result->fetch_assoc()) {
                            echo '<option value="' . $rows['admissionNumber'] . '">' . $rows['firstName'] . ' ' . $rows['lastName'] . '</option>';
                          }
                          echo '</select>';
                        }
                        ?>
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Type<span class="text-danger ml-2">*</span></label>
                        <select required name="type" class="form-control mb-3" onchange="typeDropDown(this.value)">
                          <option value="">--Select Type--</option>
                          <option value="1">All Attendance</option>
                          <option value="2">Single Date</option>
                          <option value="3">Date Range</option>
                        </select>
                      </div>
                    </div>
                    <div id="dateSingle" style="display:none;">
                      <div class="form-group row mb-3">
                        <div class="col-xl-6">
                          <label class="form-control-label">Date<span class="text-danger ml-2">*</span></label>
                          <input type="date" name="singleDate" class="form-control mb-3" />
                        </div>
                      </div>
                    </div>
                    <div id="dateRange" style="display:none;">
                      <div class="form-group row mb-3">
                        <div class="col-xl-6">
                          <label class="form-control-label">From Date<span class="text-danger ml-2">*</span></label>
                          <input type="date" name="fromDate" class="form-control mb-3" />
                        </div>
                        <div class="col-xl-6">
                          <label class="form-control-label">To Date<span class="text-danger ml-2">*</span></label>
                          <input type="date" name="toDate" class="form-control mb-3" />
                        </div>
                      </div>
                    </div>
                    <button type="submit" name="view" class="btn btn-primary">Edit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Display attendance records -->
          <?php if (!empty($attendanceRecords)) : ?>
            <div class="row">
              <div class="col-lg-12">
                <div class="card mb-4">
                  <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Attendance Records</h6>
                    <h6 class="m-0 font-weight-bold text-danger">Note: <i>Click on the status beside each student to edit attendance!</i></h6>
                  </div>
                  <div class="table-responsive p-3">
                    <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                      <thead class="thead-light">
                        <tr>
                          <th>Date</th>
                          <th>Status</th>
                          <th>Class</th>
                          <th>Class Arm</th>
                          <th>Session</th>
                          <th>Term</th>
                          <th>Student Name</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($attendanceRecords as $record) : ?>
                          <tr>
                            <td><?php echo htmlspecialchars($record['dateTimeTaken']); ?></td>
                            <td id="status-<?php echo $record['Id']; ?>" style="background-color: <?php echo ($record['status'] == '1') ? '#00FF00' : '#FF0000'; ?>;" onclick="toggleStatus(<?php echo $record['Id']; ?>, '<?php echo $record['status']; ?>')">
                              <?php echo ($record['status'] == '1') ? 'Present' : 'Absent'; ?>
                            </td>
                            <td><?php echo htmlspecialchars($record['className']); ?></td>
                            <td><?php echo htmlspecialchars($record['classArmName']); ?></td>
                            <td><?php echo htmlspecialchars($record['sessionName']); ?></td>
                            <td><?php echo htmlspecialchars($record['termName']); ?></td>
                            <td><?php echo htmlspecialchars($record['firstName'] . ' ' . $record['lastName']); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
      <?php include "Includes/footer.php"; ?>
      <!-- Footer -->
    </div>
  </div>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
</body>

</html>