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


// If a request is made to fetch class arms based on classId
if (isset($_GET['classId']) && !isset($_GET['classArmId'])) {
  $classId = $_GET['classId'];
  if (!empty($classId)) {
    // Query to fetch class arms
    $query = "SELECT Id, classArmName FROM tblclassarms WHERE classId = '$classId'";
    $result = $conn->query($query);

    $classArms = [];
    while ($row = $result->fetch_assoc()) {
      $classArms[] = $row;
    }
    // Return class arms as JSON
    echo json_encode($classArms);
  } else {
    echo json_encode([]);
  }
  exit();
}

// If a request is made to fetch students based on classId and classArmId
if (isset($_GET['classId']) && isset($_GET['classArmId'])) {
  $classId = $_GET['classId'];
  $classArmId = $_GET['classArmId'];
  if (!empty($classId) && !empty($classArmId)) {
    // Query to fetch students
    $query = "SELECT Id, CONCAT(firstName, ' ', lastName) AS name FROM tblstudents WHERE classId = '$classId' AND classArmId = '$classArmId'";
    $result = $conn->query($query);

    $students = [];
    while ($row = $result->fetch_assoc()) {
      $students[] = $row;
    }
    // Return students as JSON
    echo json_encode($students);
  } else {
    echo json_encode([]);
  }
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
  <title>AMS- Admin Dashboard</title>
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
            <h1 class="h3 mb-0 text-gray-800">Administrator Dashboard</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>

          <div class="row mb-3">
            <!-- Students Card -->
            <?php
            $query1 = mysqli_query($conn, "SELECT * from tblstudents");
            $students = mysqli_num_rows($query1);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Students</div>
                      <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $students; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 20.4%</span>
                        <span>Since last month</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Class Card -->
            <?php
            $query1 = mysqli_query($conn, "SELECT * from tblclass");
            $class = mysqli_num_rows($query1);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Classes</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $class; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                        <span>Since last month</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-chalkboard fa-2x text-primary"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Class Arm Card -->
            <?php
            $query1 = mysqli_query($conn, "SELECT * from tblclassarms");
            $classArms = mysqli_num_rows($query1);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Class Arms</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $classArms; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 12%</span>
                        <span>Since last years</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-code-branch fa-2x text-success"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Std Att Card  -->
            <?php
            $query1 = mysqli_query($conn, "SELECT * from tblattendance");
            $totAttendance = mysqli_num_rows($query1);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Total Student Attendance</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totAttendance; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-danger mr-2"><i class="fas fa-arrow-down"></i> 1.10%</span>
                        <span>Since yesterday</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar fa-2x text-secondary"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Teachers Card  -->
            <?php
            $query = mysqli_query($conn, "SELECT * from tblclassteacher");
            $classTeacher = mysqli_num_rows($query);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Class Teachers</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $classTeacher; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 12%</span>
                                    <span>Since last years</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-chalkboard-teacher fa-2x text-danger"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <!-- Session and Terms Card  -->
            <?php
            $query1 = mysqli_query($conn, "SELECT * from tblsessionterm");
            $sessTerm = mysqli_num_rows($query1);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Session & Terms</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $sessTerm; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 12%</span>
                                    <span>Since last years</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar-alt fa-2x text-warning"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <!-- Terms Card  -->
            <?php
            $query1 = mysqli_query($conn, "SELECT * from tblterm");
            $termonly = mysqli_num_rows($query1);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Terms</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $termonly; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                       
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-th fa-2x text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
       

          </div>



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

    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      $(document).ready(function() {
        // When class is selected, fetch class arms
        $('#classSelect').change(function() {
          let classId = $(this).val();

          if (classId) {
            $.ajax({
              url: '?classId=' + classId,
              method: 'GET',
              success: function(data) {
                let classArms = JSON.parse(data);
                let classArmSelect = $('#classArmSelect');
                classArmSelect.html('<option value="">Select Class Section</option>'); // Reset options

                // Populate class arms
                classArms.forEach(arm => {
                  classArmSelect.append('<option value="' + arm.Id + '">' + arm.classArmName + '</option>');
                });
              },
              error: function() {
                console.log('Error fetching class arms.');
              }
            });
          } else {
            // Reset both dropdowns if no class is selected
            $('#classArmSelect').html('<option value="">Select Class Section</option>');
            $('#studentSelect').html('<option value="">Select Student</option>');
          }
        });

        // When class arm is selected, fetch students
        $('#classArmSelect').change(function() {
          let classId = $('#classSelect').val();
          let classArmId = $(this).val();

          if (classArmId) {
            $.ajax({
              url: '?classId=' + classId + '&classArmId=' + classArmId,
              method: 'GET',
              success: function(data) {
                let students = JSON.parse(data);
                let studentSelect = $('#studentSelect');
                studentSelect.html('<option value="">Select Student</option>'); // Reset options

                // Populate students
                students.forEach(student => {
                  studentSelect.append('<option value="' + student.Id + '">' + student.name + '</option>');
                });
              },
              error: function() {
                console.log('Error fetching students.');
              }
            });
          } else {
            $('#studentSelect').html('<option value="">Select Student</option>'); // Reset if no arm is selected
          }
        });
      });


      // Chart.js initialization for Class Attendance Chart
      var ctxClass = document.getElementById('classAttendanceChart').getContext('2d');
      var classAttendanceChart = new Chart(ctxClass, {
        type: 'bar', // Or 'pie' for pie chart
        data: {
          labels: ['Present', 'Absent'],
          datasets: [{
            label: 'Class Attendance',
            data: [0, 0], // Placeholder, will be updated dynamically
            backgroundColor: ['#4CAF50', '#F44336']
          }]
        },
        options: {
          responsive: true,
        }
      });

      // Chart.js initialization for Student Attendance Chart
      var ctxStudent = document.getElementById('studentAttendanceChart').getContext('2d');
      var studentAttendanceChart = new Chart(ctxStudent, {
        type: 'bar',
        data: {
          labels: ['Present', 'Absent'],
          datasets: [{
            label: 'Student Attendance',
            data: [0, 0], // Placeholder
            backgroundColor: ['#2196F3', '#FFEB3B']
          }]
        },
        options: {
          responsive: true,
        }
      });
 -->

   </script> 

</body>

</html>