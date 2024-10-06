<?php
include '../Includes/dbcon.php';

$classId = $_GET['Id'];

// Ensure $classId is properly sanitized before use
if (!empty($classId)) {
    // Fetch students for the selected class
    $query = "SELECT Id, CONCAT(firstName, ' ', lastName) AS name FROM tblstudents WHERE Id='$classId'";
    $result = $conn->query($query);

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    // Return the students as JSON
    echo json_encode($students);
} else {
    echo json_encode([]); // Return an empty array if no class is selected
}
?>
