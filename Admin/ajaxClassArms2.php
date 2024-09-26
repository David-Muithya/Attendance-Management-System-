<?php
include '../Includes/dbcon.php';

if(isset($_GET['cid'])) {
    $classId = $_GET['cid'];
    
    // Fetch class arms for the selected class
    $query = "SELECT * FROM tblclassarms WHERE classId = '$classId'";
    $result = $conn->query($query);
    
    if($result->num_rows > 0) {
        echo '<select required name="classArmId" class="form-control mb-3">';
        echo '<option value="">--Select Class Arm--</option>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="'.$row['Id'].'">'.$row['classArmName'].'</option>';
        }
        echo '</select>';
    } else {
        // If no class arms found, show a disabled select with a message
        echo '<select  name="classArmId" class="form-control mb-3" >';
        echo '<option value="">--No Class Arms Available--</option>';
        echo '</select>';
    }
}
?>

       
    
