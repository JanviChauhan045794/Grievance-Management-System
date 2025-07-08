<?php
session_start(); // Start the session

// Check if user is logged in and role is student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get the user_id from session

// Database connection
include("php/config.php"); // Adjust this path as needed

// Fetch the student_id from the database using user_id
$student_query = mysqli_query($con, "SELECT student_id FROM student WHERE user_id = '$user_id'");
if ($student_query && mysqli_num_rows($student_query) > 0) {
    $student_data = mysqli_fetch_assoc($student_query);
    $student_id = $student_data['student_id'];
    $_SESSION['student_id'] = $student_id;
} else {
    header("Location: error.php?error=Student ID not found for user.");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $complaintId = $_POST["complaintId"];
    $registrationDateTime = date('Y-m-d H:i:s'); // Current date and time
    $dueDate = date('Y-m-d H:i:s', strtotime('+15 days')); // Due date (15 days from now)
    $endDate = null; // Initially null, will be set by faculty when resolved
    $complaintCategoryID = $_POST["complaintCategoryID"];
    $complaintDescription = $_POST["complaintDescription"];

    // Validate the selected complaint category
    if (empty($complaintCategoryID)) {
        header("Location: error.php?error=Please select a complaint category.");
        exit;
    }

    // Fetch faculty_complaint_cat ID and faculty_id based on complaint category ID
    $query = "SELECT faculty_complaint_cat, faculty_id FROM faculty_complaints_cat WHERE Complaint_Category_ID = '$complaintCategoryID'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $facultyComplaintCatID = $row['faculty_complaint_cat'];
        $facultyID = $row['faculty_id'];
    } else {
        header("Location: error.php?error=Faculty ID not found for the selected complaint category.");
        exit;
    }

    // File upload logic
    if (isset($_FILES["complaintDocument"]) && $_FILES["complaintDocument"]["error"] == 0) {
        $targetDirectory = "uploads/"; // Directory where uploaded files will be stored
        $fileExtension = strtolower(pathinfo($_FILES["complaintDocument"]["name"], PATHINFO_EXTENSION)); // Get the file extension

        // Validate file size (limit to 500KB)
        $maxFileSize = 500 * 1024; // 500KB in bytes
        if ($_FILES["complaintDocument"]["size"] > $maxFileSize) {
            header("Location: error.php?error_message=" . urlencode("File size exceeds the limit."));
            exit;
        }

        // Validate file extension (only allow JPG, JPEG, PNG, and PDF)
        $allowedExtensions = ["jpg", "jpeg", "png", "pdf"];
        if (!in_array($fileExtension, $allowedExtensions)) {
            header("Location: error.php?error_message=" . urlencode("Only JPG, JPEG, PNG, and PDF files are allowed."));
            exit;
        }

        $targetFile = $targetDirectory . $complaintId . '.' . $fileExtension; // Path to the uploaded file

        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES["complaintDocument"]["tmp_name"], $targetFile)) {
            // Insert data into database
            $insertQuery = "INSERT INTO complaints 
                            (Comp_ID, complaint_id, complaint_datetime, due_date, end_date, Complaint_Cat_ID, complaint_description, Complaint_Document, Student_ID, F_Comp_ID, faculty_id)
                            VALUES 
                            ('$complaintId', '$complaintId', '$registrationDateTime', '$dueDate', '$endDate', '$complaintCategoryID', '$complaintDescription', '$targetFile', '$student_id', '$facultyComplaintCatID', '$facultyID')";

            if (mysqli_query($con, $insertQuery)) {
                header("Location: after_complain.php?CompId=$complaintId&status=success");
                exit;
            } else {
                header("Location: error.php?error=" . mysqli_error($con));
                exit;
            }
        } else {
            header("Location: error.php?error=Error uploading file.");
            exit;
        }
    } else {
        header("Location: error.php?error=No file uploaded or upload error occurred.");
        exit;
    }
}
?>
