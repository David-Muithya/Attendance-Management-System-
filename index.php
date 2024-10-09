<?php
include 'Includes/dbcon.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <link  href="img/logo/T-logo.png" rel="icon"  >
  <title>AMS - Login</title>
  <style>
    @media (max-width: 768px) {
       .img-fluid {
        display: none; /* Hide the image on screens less than 600px wide */
      }
    }
  </style>
</head>

<body class="bg-gradient-login d-flex align-items-center">
  <div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh;">
      <div class="col-md-6 d-flex justify-content-center align-items-center d-none d-md-flex">
        <img src="img/login asset.svg" alt="Attendance" class="img-fluid mt-3 mb-5" style="max-width: 80%;">
      </div>
      <div class="col-md-6 d-flex justify-content-center align-items-center">
        <div class="bg-white p-4 shadow mb-4" style="max-width: 400px; width: 100%; border-radius:12px;">
          <h5 class="text-center text-primary font-weight-bold">STUDENT ATTENDANCE SYSTEM</h5>
          <img src="img/attnlg.jpg" class="d-block mx-auto my-9" style="width: 100px; height: 100px;">
          <h1 class="h4 text-gray-900 mb-4 text-center"><b>Login Panel</b></h1>
          
          <form class="user" method="Post" action="">
            <div class="form-group rounded">
              <select required name="userType" class="form-control mb-3">
                <option value="">--Select User Roles--</option>
                <option value="Administrator">Administrator</option>
                <option value="ClassTeacher">ClassTeacher</option>
                <option value="Student">Student</option>
              </select>
            </div>
            <div class="form-group">
              <input type="text" class="form-control " required name="username" id="exampleInputEmail" placeholder="Enter Email Address">
            </div>
            <div class="form-group">
              <input type="password" name="password" required class="form-control" id="exampleInputPassword" placeholder="Enter Password">
            </div>
            <div class="form-group">
              <input type="submit" class="btn btn-primary btn-block  " value="Login" name="login" />
            </div>
          </form>

          <?php

          $statusMessage = '';
          if (isset($_POST['login'])) {

            $userType = $_POST['userType'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $password = md5($password);

            if ($userType == "Administrator") {
              $query = "SELECT * FROM tbladmin WHERE emailAddress = '$username' AND password = '$password'";
              $rs = $conn->query($query);
              $num = $rs->num_rows;
              $rows = $rs->fetch_assoc();

              if ($num > 0) {
                $_SESSION['userId'] = $rows['Id'];
                $_SESSION['firstName'] = $rows['firstName'];
                $_SESSION['lastName'] = $rows['lastName'];
                $_SESSION['emailAddress'] = $rows['emailAddress'];

                echo "<script type = \"text/javascript\">
                                window.location = (\"Admin/index.php\")
                                </script>";
              } else {
                $statusMessage = '<div id="statusMessage" class="alert alert-warning text-center mt-3" role="alert">Invalid Username/Password! blabla.</div>';
              }
            } else if ($userType == "ClassTeacher") {
              $query = "SELECT * FROM tblclassteacher WHERE emailAddress = '$username' AND password = '$password'";
              $rs = $conn->query($query);
              $num = $rs->num_rows;
              $rows = $rs->fetch_assoc();

              if ($num > 0) {
                $_SESSION['userId'] = $rows['Id'];
                $_SESSION['firstName'] = $rows['firstName'];
                $_SESSION['lastName'] = $rows['lastName'];
                $_SESSION['emailAddress'] = $rows['emailAddress'];
                $_SESSION['classId'] = $rows['classId'];
                $_SESSION['classArmId'] = $rows['classArmId'];

                echo "<script type = \"text/javascript\">
                                window.location = (\"ClassTeacher/index.php\")
                                </script>";
              } else {
                 $statusMessage = '<div id="statusMessage" class="alert alert-warning text-center mt-3" role="alert">Invalid Username/Password!</div>';
              }
            } else if ($userType == "Student") {
              $query = "SELECT * FROM tblstudents WHERE email = '$username' AND password = '$password'";
              $rs = $conn->query($query);
              $num = $rs->num_rows;
              $rows = $rs->fetch_assoc();

              if ($num > 0) {
                $_SESSION['userId'] = $rows['Id'];
                $_SESSION['firstName'] = $rows['firstName'];
                $_SESSION['lastName'] = $rows['lastName'];
                $_SESSION['email'] = $rows['email'];
                $_SESSION['classId'] = $rows['classId'];
                $_SESSION['classArmId'] = $rows['classArmId'];
                $_SESSION['admissionNumber'] = $rows['admissionNumber']; 

                echo "<script type = \"text/javascript\">
                                window.location = (\"Student/index.php\")
                                </script>";
              } else {
                $statusMessage = '<div id="statusMessage" class="alert alert-warning text-center mt-3" role="alert">Invalid Username/Password!</div>';
              }
            } else {
              $statusMessage = '<div id="statusMessage" class="alert alert-warning text-center mt-3" role="alert">Invalid Username/Password!</div>';
            }
          }
          if ($statusMessage !== '') {
            echo $statusMessage;
        }
          ?>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  <script>
    $(document).ready(function() {
      // Automatically hide the status message after 5 seconds
      setTimeout(function() {
        $("#statusMessage").fadeOut("slow");
      }, 3000); // 3000 milliseconds = 3 second
    });
  </script>
</body>

</html>