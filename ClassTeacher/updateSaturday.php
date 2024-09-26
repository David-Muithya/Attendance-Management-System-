<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$dateTaken = date("Y-m-d");

// Function to update Saturday attendance
function updateSaturdayAttendance($conn) {
    global $dateTaken;
    $checkAttendance = mysqli_query($conn, "SELECT * FROM tblattendance WHERE dateTimeTaken = '$dateTaken' AND status = '2'");
    if (mysqli_num_rows($checkAttendance) == 0) {
        $query = "SELECT admissionNumber FROM tblstudents WHERE classId = '$_SESSION[classId]' AND classArmId = '$_SESSION[classArmId]'";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            mysqli_query($conn, "INSERT INTO tblattendance (admissionNo, classId, classArmId, sessionTermId, status, dateTimeTaken) 
                VALUES ('".$row['admissionNumber']."', '$_SESSION[classId]', '$_SESSION[classArmId]', '$_SESSION[sessionTermId]', '2', '$dateTaken')
                ON DUPLICATE KEY UPDATE status='2'");
        }
    }
}

if (isset($_POST['updateSaturday'])) {
    updateSaturdayAttendance($conn);
    echo 'Saturday attendance updated!';
}
?>
