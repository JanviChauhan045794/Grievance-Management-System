<?php
session_start();
include("../php/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complaint_id'])) {
    $complaint_id = mysqli_real_escape_string($con, $_POST['complaint_id']);

    // Query to delete the complaint
    $query = "DELETE FROM complaints WHERE complaint_id = '$complaint_id'";

    if (mysqli_query($con, $query)) {
        echo "Complaint deleted successfully.";
    } else {
        echo "Failed to delete complaint: " . mysqli_error($con);
    }
} else {
    echo "Invalid request.";
}
?>
