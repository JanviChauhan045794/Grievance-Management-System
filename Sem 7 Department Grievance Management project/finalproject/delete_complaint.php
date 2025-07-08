<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['student_id'])){
    header("Location: index.php");
    exit;
}

// Check if the form is submitted
if(isset($_POST['complaint_id'])){
    // Get the complaint ID from the form
    $complaint_id = $_POST['complaint_id'];

    // Connect to your database
    include("php/config.php");

    // Prepare and execute the delete query
    $delete_query = "DELETE FROM complaints WHERE Comp_ID = '$complaint_id'";
    $delete_result = mysqli_query($con, $delete_query);

    // Check if deletion was successful
    if($delete_result){
        // Redirect back to the complaint page or display a success message
        header("Location: after_complain.php");
        exit;
    } else {
        echo "Error deleting complaint: " . mysqli_error($con);
    }
} else {
    header("Location: after_complain.php");
    exit;
}
?>
