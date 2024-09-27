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
  $emailAddress = $_POST['emailAddress'];
  $phoneNo = $_POST['phoneNo'];
  $classId = $_POST['classId'];
  $classArmId = $_POST['classArmId'];
  $dateCreated = date("Y-m-d");

  // Check if the email already exists
  $emailQuery = mysqli_query($conn, "SELECT * FROM tblclassteacher WHERE emailAddress = '$emailAddress'");
  if (mysqli_num_rows($emailQuery) > 0) {
    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Email Address Already Exists!</div>";
  } else {
    // Check if the class arm is already assigned to another teacher
    $classArmQuery = mysqli_query($conn, "SELECT * FROM tblclassteacher WHERE classArmId = '$classArmId'");
    if (mysqli_num_rows($classArmQuery) > 0) {
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Class Arm is already assigned to another teacher!</div>";
    } else {
      // Start transaction
      mysqli_begin_transaction($conn);
      try {
        // Proceed to insert the new teacher
        $sampPass = "pass123"; // Temporary password
        $sampPass_2 = md5($sampPass); // Use stronger encryption if possible

        $query = mysqli_query($conn, "INSERT INTO tblclassteacher (firstName, lastName, emailAddress, password, phoneNo, classId, classArmId, dateCreated) 
                  VALUES ('$firstName', '$lastName', '$emailAddress', '$sampPass_2', '$phoneNo', '$classId', '$classArmId', '$dateCreated')");

        if ($query) {
          // Update class arm status
          $updateClassArm = mysqli_query($conn, "UPDATE tblclassarms SET isAssigned='1' WHERE Id ='$classArmId'");
          if ($updateClassArm) {
            // Send email to the teacher
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'example0@gmail.com'; // SMTP username
            $mail->Password = '*******'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('example@gmail.com', 'AMS System');
            $mail->addAddress($emailAddress, $firstName . ' ' . $lastName); // Teacher's email

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Account Created in <b> AMS System </b>';
            $mail->Body    = 'Your teacher account has been created in AMS System. <br> 
                                         Username: ' . $emailAddress . '<br>
                                         Password: ' . $sampPass . '<br>
                                         Please login and change your password immediately.';

            $mail->send();

            // Commit transaction
            mysqli_commit($conn);
            $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Created Successfully! Email Sent.</div>";
          } else {
            throw new Exception("Error updating class arm status");
          }
        } else {
          throw new Exception("Error inserting teacher record");
        }
      } catch (Exception $e) {
        // Rollback transaction if there is an error
        mysqli_rollback($conn);
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error occurred: " . $e->getMessage() . "</div>";
      }
    }
  }
}





//--------------------EDIT------------------------------------------------------------


if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
  $Id = $_GET['Id'];

  $query = mysqli_query($conn, "SELECT * FROM tblclassteacher WHERE Id ='$Id'");
  $row = mysqli_fetch_array($query);

  // Handle form submission for update
  if (isset($_POST['update'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $emailAddress = $_POST['emailAddress'];
    $phoneNo = $_POST['phoneNo'];
    $classId = $_POST['classId'];
    $classArmId = $_POST['classArmId'];
    $dateCreated = date("Y-m-d");

    // Check if the email already exists for another teacher
    $checkEmailQuery = mysqli_query($conn, "SELECT * FROM tblclassteacher WHERE emailAddress = '$emailAddress' AND Id != '$Id'");
    if (mysqli_num_rows($checkEmailQuery) > 0) {
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Email Address is already associated with another teacher!</div>";
    } else {
      // Check if the class arm is already assigned to another teacher
      $checkClassArmQuery = mysqli_query($conn, "SELECT * FROM tblclassteacher WHERE classArmId = '$classArmId' AND Id != '$Id'");
      if (mysqli_num_rows($checkClassArmQuery) > 0) {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Class Arm is already assigned to another teacher!</div>";
      } else {
        // Proceed with the update
        $updateQuery = mysqli_query($conn, "UPDATE tblclassteacher SET 
                  firstName='$firstName', 
                  lastName='$lastName',
                  emailAddress='$emailAddress',
                  phoneNo='$phoneNo',
                  classId='$classId',
                  classArmId='$classArmId'
                  WHERE Id='$Id'");

        if ($updateQuery) {
          // Optionally update the class arm status here if needed
          $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Teacher details updated successfully!</div>";
          $_SESSION['statusMsg'] = $statusMsg;
          header("Location: createClassTeacher.php");
          exit;
        } else {
          $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error occurred while updating teacher details!</div>";
        }
      }
    }
  }
}
//--------------------------------DELETE------------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['classArmId']) && isset($_GET['action']) && $_GET['action'] == "delete") {
  $Id = $_GET['Id'];
  $classArmId = $_GET['classArmId'];

  $query = mysqli_query($conn, "DELETE FROM tblclassteacher WHERE Id='$Id'");
  $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Account Deleted Successfully</div>";

  if ($query) {
    $qu = mysqli_query($conn, "UPDATE tblclassarms SET isAssigned='0' WHERE Id ='$classArmId'");
    if ($qu) {
    } else {
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Error updating class arm status</div>";
    }
  } else {
    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Error deleting account</div>";
  }



  // Redirect after processing
  header("Location: createClassTeacher.php");
  exit; // Ensure script terminates after redirect
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
  <title>AMS - Create Class Teacher</title>
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
            <h1 class="h3 mb-0 text-gray-800">Create Class Teachers</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Class Teachers</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Create Class Teachers</h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">

                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Firstname<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" required name="firstName" value="<?php echo $row['firstName']; ?>" id="exampleInputFirstName">
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Lastname<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" required name="lastName" value="<?php echo $row['lastName']; ?>" id="exampleInputFirstName">
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Email Address<span class="text-danger ml-2">*</span></label>
                        <input type="email" class="form-control" required name="emailAddress" value="<?php echo $row['emailAddress']; ?>" id="exampleInputFirstName">
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Phone No<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="phoneNo" value="<?php echo $row['phoneNo']; ?>" id="exampleInputFirstName">
                      </div>
                    </div>
                    <div class="form-group row mb-3">
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
                      <button type="submit" name="update" class="btn btn-warning">Update</button>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                    } else {
                    ?>
                      <button type="submit" name="save" class="btn btn-primary">Save</button>
                    
                    <?php
                    }
                    ?>
                  </form>
                </div>
              </div>

              <!-- Input Group -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">All Class Teachers</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email Address</th>
                            <th>Phone No</th>
                            <th>Class</th>
                            <th>Class Arm</th>
                            <th>Date Created</th>
                            <th>Edit</th>
                            <th>Delete</th>
                          </tr>
                        </thead>

                        <tbody>

                          <?php



                          $query = "SELECT 
            tc.Id, 
            tc.firstName, 
            tc.lastName, 
            tc.emailAddress, 
            tc.phoneNo, 
            IFNULL(tcl.className, 'No Class Assigned') AS className, 
            IFNULL(tca.classArmName, 'No Class Arm Assigned') AS classArmName, 
            tc.dateCreated
          FROM 
            tblclassteacher tc
          LEFT JOIN 
            tblclass tcl ON tc.classId = tcl.Id
            
          LEFT JOIN 
            tblclassarms tca ON tc.classArmId = tca.Id";


                          $rs = $conn->query($query);
                          $num = $rs->num_rows;
                          $sn = 1;
                          $status = "";
                          if ($num > 0) {
                            while ($rows = $rs->fetch_assoc()) {
                              echo "
                                <tr>
                                  <td>" . $sn++ . "</td>
                                  <td>" . $rows['firstName'] . "</td>
                                  <td>" . $rows['lastName'] . "</td>
                                  <td>" . $rows['emailAddress'] . "</td>
                                  <td>" . $rows['phoneNo'] . "</td>
                                  <td>" . $rows['className'] . "</td>
                                  <td>" . $rows['classArmName'] . "</td>
                                  <td>" . $rows['dateCreated'] . "</td>
                                  <td><a href='?action=edit&Id=" . $rows['Id'] . "'><i class='fas fa-fw fa-edit'></i></a></td>
                                  <td><a href='?action=delete&Id=" . $rows['Id'] . "&classArmId=" . $rows['classArmId'] . "'><i class='fas fa-fw fa-trash'></i></a></td>
                                </tr>";
                            }
                          } else {
                            echo '<tr><td colspan="9" class="text-center">No records found</td></tr>';
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
