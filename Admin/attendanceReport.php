<?php
include '../Includes/dbcon.php';

$classId = $_POST['classId'];
$studentId = $_POST['studentId'];

// Fetch Class Attendance Data
$classAttendanceQuery = mysqli_query($conn, "SELECT 
    SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) AS present,
    SUM(CASE WHEN status='Absent' THEN 1 ELSE 0 END) AS absent 
    FROM tblattendance WHERE classId='$classId'");
$classAttendance = mysqli_fetch_assoc($classAttendanceQuery);

// Fetch Individual Student Attendance Data
$studentAttendanceQuery = mysqli_query($conn, "SELECT 
    SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) AS present,
    SUM(CASE WHEN status='Absent' THEN 1 ELSE 0 END) AS absent 
    FROM tblattendance WHERE studentId='$studentId'");
$studentAttendance = mysqli_fetch_assoc($studentAttendanceQuery);

// Return combined data as JSON
echo json_encode([
    'classAttendance' => $classAttendance,
    'studentAttendance' => $studentAttendance
]);
?>
