<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize status message variable
$statusMessage = '';
$reportGenerated = false; // Track if the report has been generated

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET['action']) && $_GET['action'] == 'download') {
    // Parameters
    $admissionNumber = $_POST['admissionNumber'] ?? '';
    $type = $_POST['type'] ?? '';
    $singleDate = $_POST['singleDate'] ?? '';
    $fromDate = $_POST['fromDate'] ?? '';
    $toDate = $_POST['toDate'] ?? '';
    
    // Determine the query based on the type of report
    $query = "";
    if ($type == "1") { // All Attendance
        $query = "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, 
                tblclass.className, tblclassarms.classArmName, tblsessionterm.sessionName, tblsessionterm.termId, 
                tblterm.termName, tblstudents.firstName, tblstudents.lastName, 
                tblstudents.admissionNumber
                FROM tblattendance
                INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
                INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
                INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
                INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
                INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
                WHERE tblattendance.admissionNo = '$admissionNumber'
                AND tblattendance.classId = '$_SESSION[classId]' 
                AND tblattendance.classArmId = '$_SESSION[classArmId]'";
    } elseif ($type == "2") { // Single Date Attendance
        $query = "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, 
                tblclass.className, tblclassarms.classArmName, tblsessionterm.sessionName, tblsessionterm.termId, 
                tblterm.termName, tblstudents.firstName, tblstudents.lastName, tblstudents.admissionNumber
                FROM tblattendance
                INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
                INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
                INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
                INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
                INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
                WHERE tblattendance.dateTimeTaken = '$singleDate' 
                AND tblattendance.admissionNo = '$admissionNumber'
                AND tblattendance.classId = '$_SESSION[classId]' 
                AND tblattendance.classArmId = '$_SESSION[classArmId]'";
    } elseif ($type == "3") { // Date Range Attendance
        $query = "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, 
                tblclass.className, tblclassarms.classArmName, tblsessionterm.sessionName, tblsessionterm.termId, 
                tblterm.termName, tblstudents.firstName, tblstudents.lastName, tblstudents.admissionNumber
                FROM tblattendance
                INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
                INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
                INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
                INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
                INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
                WHERE tblattendance.dateTimeTaken BETWEEN '$fromDate' AND '$toDate' 
                AND tblattendance.admissionNo = '$admissionNumber'
                AND tblattendance.classId = '$_SESSION[classId]' 
                AND tblattendance.classArmId = '$_SESSION[classArmId]'";
    }

    // Fetch the data
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Set headers for file download
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Attendance_list_" . date("Y-m-d") . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Output table data
        echo '<table border="1">
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
    } else {
        echo 'No data available for the selected criteria.';
    }
}
?>
