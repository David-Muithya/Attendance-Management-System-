<?php
session_start();
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

//------------------------SAVE--------------------------------------------------

if (isset($_POST['save'])) {
  $firstName = $_POST['firstName'];
  $lastName = $_POST['lastName'];
  $email = $_POST['email'];
  $classId = $_POST['classId'];
  $classArmId = $_POST['classArmId'];
  $dateCreated = date("Y-m-d");

  $sampPass = "pwd123"; // Temporary password
  $sampPass_2 = md5($sampPass); // Use stronger encryption if possible

  // Generate next admission number
  $query = mysqli_query($conn, "SELECT MAX(SUBSTRING(admissionNumber, 4)) AS lastNumber FROM tblstudents");
  $row = mysqli_fetch_assoc($query);
  $lastNumber = $row['lastNumber'];
  $nextNumber = str_pad((int)$lastNumber + 1, 2, "0", STR_PAD_LEFT); // Format next number (ASM01, ASM02, ...)
  $admissionNumber = "ASM" . $nextNumber;

  // Check if the email already exists
  $checkEmail = mysqli_query($conn, "SELECT * FROM tblstudents WHERE email = '$email'");

  if (mysqli_num_rows($checkEmail) > 0) {
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This email address already exists!</div>";
  } else {
      // Start transaction
      mysqli_begin_transaction($conn);
      try {
          // Insert the student record
          $query = mysqli_query($conn, "INSERT INTO tblstudents (firstName, lastName, email, password, classId, classArmId, dateCreated, admissionNumber) 
              VALUES ('$firstName', '$lastName', '$email', '$sampPass_2', '$classId', '$classArmId', '$dateCreated', '$admissionNumber')");

          if ($query) {
              // Prepare to send email
              $mail = new PHPMailer(true);

              // Server settings
              $mail->isSMTP();
              $mail->Host = 'smtp.gmail.com'; // Your SMTP server
              $mail->SMTPAuth = true;
              $mail->Username = '*********@gmail.com'; // SMTP username
              $mail->Password = '*******'; // SMTP password
              $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
              $mail->Port = 587;

              // Recipients
              $mail->setFrom('jsurya860@gmail.com', 'AMS System');
              $mail->addAddress($email, $firstName . ' ' . $lastName); // Student's email

              // Content
              $mail->isHTML(true);
              $mail->Subject = 'Account Created in AMS System ';
              $mail->Body    = 'Your student account has been created in AMS System. <br> 
                                 Username: ' . $email . '<br>
                                 Password: ' . $sampPass . '<br>
                                 Please login and change your password immediately.';

              // Send email
              $mail->send();

              // Commit transaction
              mysqli_commit($conn);
              $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Created Successfully! Email Sent.</div>";
          } else {
              throw new Exception("Error inserting student record");
          }
      } catch (Exception $e) {
          // Rollback transaction if there is an error
          mysqli_rollback($conn);
          $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error occurred: " . $e->getMessage() . "</div>";
      }
  }
}

//--------------------EDIT------------------------------------------------------------


if (isset($_GET['Id']) && isset($_GET['action'])) {
  $Id = $_GET['Id'];

  if ($_GET['action'] == "edit") {
    $query = mysqli_query($conn, "SELECT * FROM tblstudents WHERE Id ='$Id'");
    $row = mysqli_fetch_array($query);

    if (isset($_POST['update'])) {
      $firstName = $_POST['firstName'];
      $lastName = $_POST['lastName'];
      $email = $_POST['email'];
      $classId = $_POST['classId'];
      $classArmId = $_POST['classArmId'];
      $dateCreated = date("Y-m-d");

      // Start transaction
      mysqli_begin_transaction($conn);

      try {
        // Check if the email address is already associated with another student
        $checkEmailQuery = mysqli_query($conn, "SELECT * FROM tblstudents WHERE email = '$email' AND Id != '$Id'");
        if (mysqli_num_rows($checkEmailQuery) > 0) {
          throw new Exception("This Email Address is already associated with another student!");
        }

        // Proceed with the update
        $updateQuery = mysqli_query($conn, "UPDATE tblstudents SET 
                  firstName='$firstName',
                  lastName='$lastName',
                  email='$email',
                  classId='$classId',
                  classArmId='$classArmId'
                  WHERE Id='$Id'");

        if (!$updateQuery) {
          throw new Exception("An error occurred while updating student details!");
        }

        // Commit the transaction if all operations were successful
        mysqli_commit($conn);

        $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Student details updated successfully!</div>";
        $_SESSION['statusMsg'] = $statusMsg;
        echo "<script type='text/javascript'>window.location = 'createStudents.php';</script>";
        exit;
      } catch (Exception $e) {
        // Rollback transaction if there is an error
        mysqli_rollback($conn);
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>" . $e->getMessage() . "</div>";
      }
    }
  }


  //--------------------------------DELETE------------------------------------------------------------------

  if ($_GET['action'] == "delete") {
    $classArmId = $_GET['classArmId'];

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
      $query = mysqli_query($conn, "DELETE FROM tblstudents WHERE Id='$Id'");

      if (!$query) {
        throw new Exception("An error occurred while deleting the student!");
      }


      // Commit the transaction if all operations were successful
      mysqli_commit($conn);

      $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Student deleted successfully!</div>";
      $_SESSION['statusMsg'] = $statusMsg;
      echo "<script type='text/javascript'>window.location = 'createStudents.php';</script>";
      exit;
    } catch (Exception $e) {
      // Rollback transaction if there is an error
      mysqli_rollback($conn);
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>" . $e->getMessage() . "</div>";
    }
  }
}


//--------------------IMPORT CSV------------------------------------------------------------

if (isset($_POST['import']) && isset($_FILES['csvFile'])) {
  $file = $_FILES['csvFile']['tmp_name'];

  if (($handle = fopen($file, "r")) !== FALSE) {
    // Skip the header row
    fgetcsv($handle);

    mysqli_begin_transaction($conn);
    try {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $firstName = $data[0];
        $lastName = $data[1];
        $email = $data[2];
        $className = $data[3];
        $classArmName = $data[4];
        $dateCreated = date("Y-m-d");

        $sampPass = "pwd123";
        $sampPass_2 = md5($sampPass);

        // Generate admission number
        $query = mysqli_query($conn, "SELECT MAX(SUBSTRING(admissionNumber, 4)) AS lastNumber FROM tblstudents");
        $row = mysqli_fetch_assoc($query);
        $lastNumber = $row['lastNumber'];
        $nextNumber = str_pad((int)$lastNumber + 1, 2, "0", STR_PAD_LEFT);
        $admissionNumber = "ASM" . $nextNumber;

         // Fetch class ID
         $classQuery = mysqli_query($conn, "SELECT Id FROM tblclass WHERE className = '$className'");
         if (mysqli_num_rows($classQuery) == 0) {
             throw new Exception("Class '$className' does not exist.");
         }
         $classRow = mysqli_fetch_assoc($classQuery);
         $classId = $classRow['Id'];

          // Fetch class arm ID
          $classArmQuery = mysqli_query($conn, "SELECT Id FROM tblclassarms WHERE classArmName = '$classArmName'");
          if (mysqli_num_rows($classArmQuery) == 0) {
              throw new Exception("Class Arm '$classArmName' does not exist.");
          }
          $classArmRow = mysqli_fetch_assoc($classArmQuery);
          $classArmId = $classArmRow['Id'];
          
        // Check if the email already exists
        $checkEmail = mysqli_query($conn, "SELECT * FROM tblstudents WHERE email = '$email'");
        if (mysqli_num_rows($checkEmail) > 0) {
          continue; // Skip to the next record
        }

      {
          // Insert the student record
          $query = mysqli_query($conn, "INSERT INTO tblstudents (firstName, lastName, email, password, classId, classArmId, dateCreated, admissionNumber) 
              VALUES ('$firstName', '$lastName', '$email', '$sampPass_2', '$classId', '$classArmId', '$dateCreated', '$admissionNumber')");

          if ($query) {
              // Prepare to send email
              $mail = new PHPMailer(true);

              // Server settings
              $mail->isSMTP();
              $mail->Host = 'smtp.gmail.com'; // Your SMTP server
              $mail->SMTPAuth = true;
              $mail->Username = 'jsurya860@gmail.com'; // SMTP username
              $mail->Password = 'vgmwvfxetkvysbgv'; // SMTP password
              $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
              $mail->Port = 587;

              // Recipients
              $mail->setFrom('jsurya860@gmail.com', 'AMS System');
              $mail->addAddress($email, $firstName . ' ' . $lastName); // Student's email

              // Content
              $mail->isHTML(true);
              $mail->Subject = 'Account Created in AMS System ';
              $mail->Body    = 'Your student account has been created in AMS System. <br> 
                                 Username: ' . $email . '<br>
                                 Password: ' . $sampPass . '<br>
                                 Please login and change your password immediately.';

              // Send email
              $mail->send();

             
          }
      } 
        if (!$query) {
          throw new Exception("Error inserting student record for $email");
        }
      }
      mysqli_commit($conn);
      $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Students imported successfully Email Sent!</div>";
    } catch (Exception $e) {
      mysqli_rollback($conn);
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Error: " . $e->getMessage() . "</div>";
    }
    fclose($handle);
  } else {
    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Failed to open file.</div>";
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
  <title>AMS - Create Students</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">

  <!-- <style>
        .import-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1000;
        }
        .form-container {
            position: relative;
            padding: 20px;
        }
    </style> -->

  <script>
    function classArmDropdown(str) {
      if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
      } else {
        if (window.XMLHttpRequest) {
          // code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp = new XMLHttpRequest();
        } else {
          // code for IE6, IE5
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
            <h1 class="h3 mb-0 text-gray-800">Create Students</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Students</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Create Students</h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Firstname<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="firstName" value="<?php echo $row['firstName']; ?>" id="exampleInputFirstName">
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Lastname<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="lastName" value="<?php echo $row['lastName']; ?>" id="exampleInputFirstName">
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Email address<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" required name="email" value="<?php echo $row['email']; ?>" id="exampleInputFirstName">
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Select Class<span class="text-danger ml-2">*</span></label>
                        <?php
                        $qry = "SELECT * FROM tblclass ORDER BY className ASC";
                        $result = $conn->query($qry);
                        $num = $result->num_rows;
                        if ($num > 0) {
                          echo ' <select required name="classId" onchange="classArmDropdown(this.value)" class="form-control mb-3">';
                          echo '<option value="">--Select Class--</option>';
                          while ($rows = $result->fetch_assoc()) {
                            echo '<option value="' . $rows['Id'] . '" >' . $rows['className'] . '</option>';
                          }
                          echo '</select>';
                        }
                        ?>
                      </div>
                    </div>
                    <div class="form-group row mb-3">

                      <div class="col-xl-6">
                        <label class="form-control-label">Class Arm<span class="text-danger ml-2">*</span></label>
                        <?php
                        echo "<div id='txtHint'></div>";
                        ?>
                      </div>
                    </div>
                    <?php
                    if (isset($Id)) {
                    ?>
                      <button type="submit" name="update" class="btn btn-success">Update</button>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                    } else {
                    ?>
                      <button type="submit" name="save" class="btn btn-primary">Save</button>
                    <?php
                    }
                    ?>

              
                  </form>
                   <!-- Form for importing CSV -->
  <form action="" method="post" enctype="multipart/form-data">
  <hr>
    <div class="form-group">
      <label for="csvFile">Upload CSV:</label>
      <input type="file" class="form-control" id="csvFile" name="csvFile" accept=".csv" required>
    </div>
    <button type="submit" class="btn btn-primary" name="import">Import CSV</button>
    <div class="alert alert-info mt-2" role="alert">
                        You can import data in bulk using the CSV file. This helps to save time for large datasets.
                      </div>
  </form>
                </div>
              </div>

              <!-- Input Group -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">All Student</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>Admission No</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>email Address</th>
                            <th>Class</th>
                            <th>Class Arm</th>
                            <th>Date Created</th>
                            <th>Edit</th>
                            <th>Delete</th>
                          </tr>
                        </thead>

                        <tbody>

                          <?php
                          $query = "SELECT tblstudents.Id, tblclass.className, tblclassarms.classArmName, tblclassarms.Id AS classArmId,
                          tblstudents.firstName, tblstudents.lastName, tblstudents.admissionNumber, tblstudents.email, tblstudents.dateCreated
                          FROM tblstudents
                          INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
                          INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId";
                
                          $rs = $conn->query($query);
                          $num = $rs->num_rows;
                          $sn = 0;
                          $status = "";
                          if ($num > 0) {
                            while ($rows = $rs->fetch_assoc()) {
                              $sn = $sn + 1;
                              echo "
                              <tr>
                                <td>" . $sn . "</td>
                                <td>" . $rows['admissionNumber'] . "</td>
                                <td>" . $rows['firstName'] . "</td>
                                <td>" . $rows['lastName'] . "</td>
                                <td>" . $rows['email'] . "</td>
                                <td>" . $rows['className'] . "</td>
                                <td>" . $rows['classArmName'] . "</td>
                                 <td>" . $rows['dateCreated'] . "</td>
                                <td><a href='?action=edit&Id=" . $rows['Id'] . "'><i class='fas fa-fw fa-edit'></i></a></td>
                                <td><a href='?action=delete&Id=" . $rows['Id'] . "'><i class='fas fa-fw fa-trash'></i></a></td>
                              </tr>";
                            }
                          } else {
                            echo
                            "<div class='alert alert-danger' role='alert'>
                            No Record Found!
                            </div>";
                          }

                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--Row-->

            <!-- Documentation Link -->
            <!-- <div class="row">
            <div class="col-lg-12 text-center">
              <p>For more documentations you can visit<a href="https://getbootstrap.com/docs/4.3/components/forms/"
                  target="_blank">
                  bootstrap forms documentations.</a> and <a
                  href="https://getbootstrap.com/docs/4.3/components/input-group/" target="_blank">bootstrap input
                  groups documentations</a></p>
            </div>
          </div> -->

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
