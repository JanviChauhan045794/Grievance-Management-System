<?php
// Database connection function
include '../php/config.php';
// Function to get total users count
function getTotalUsers() {
    $conn = connectDB();

    $sql = "SELECT COUNT(*) AS userid FROM users"; // Replace 'users' with your actual table name
    $result = $con->query($sql);
    $row = $result->fetch_assoc();

    $con->close();

    return $row["userid"];
}

//  get faculty users count
function getFacultyUsers() {
    $conn = connectDB();

    $sql = "SELECT COUNT(*) AS userid FROM users WHERE usercat_id = '1'"; 
    $result = $con->query($sql);
    $row = $result->fetch_assoc();

    $con->close();

    return $row["userid"];
}

//  get student users count
function getStudentUsers() {
    $con = connectDB();

    $sql = "SELECT COUNT(*) AS userid FROM users WHERE usercat_id='2'"; 
    $result = $con->query($sql);
    $row = $result->fetch_assoc();

    $con->close();

    return $row["userid"];
}

//  get total complaints count
function getTotalComplaints() {
    $con = connectDB();

    $sql = "SELECT COUNT(*) AS complaint_id FROM complaints"; 
    $result = $con->query($sql);
    $row = $result->fetch_assoc();

    $con->close();

    return $row["total_complaints"];
}
?>
