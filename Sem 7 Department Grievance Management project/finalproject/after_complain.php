<?php
session_start(); // Start the session

// Check if user is logged in and role is student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get the user_id from session

// Fetch the student_id from the database using user_id
include("php/config.php"); // Adjust this path as needed
include 'php/nav.php';
$student_query = mysqli_query($con, "SELECT student_id FROM student WHERE user_id = '$user_id'");
$student_data = mysqli_fetch_assoc($student_query);
$student_id = $student_data['student_id']; // Get the student_id

// Store student_id in the session if needed
$_SESSION['student_id'] = $student_id;

// Set the timezone to IST
date_default_timezone_set('Asia/Kolkata');

// Modify the query to order by registration date in descending order
$query = "SELECT * FROM complaints WHERE Student_ID = $student_id ORDER BY complaint_datetime DESC";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Details</title>
    
    <!-- Local Bootstrap CSS -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
     
        .card {
            border: none; /* No border for the card */
        }
        .card-header {
            background-color: #000; /* Black background for card header */
            color: #ffffff; /* White text color for header */
        }
        .card-body {
            background-color: #ffffff; /* White background for card body */
            color: #000; /* Black text color for body */
        }
        .complaint-item {
            border: 1px solid #444; /* Dark border for complaint items */
            border-radius: 0.5rem; /* Rounded corners */
            padding: 1.5rem; /* Inner padding */
            margin-bottom: 1rem; /* Space between complaint items */
            background-color: #f8f9fa; /* Light gray background for complaint items */
        }
        .badge {
            font-size: 1rem; /* Badge font size */
        }
        .container {
            padding-top: 2rem; /* Top padding for the container */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Complaint Details</h1>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3>My Complaints</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($complaint = mysqli_fetch_assoc($result)) {
                                // Convert MySQL date to PHP date
                                $registration_datetime = date('Y-m-d H:i:s', strtotime($complaint['complaint_datetime'])); 
                        ?>
                                <div class="complaint-item">
                                    <p><strong>Complaint ID:</strong> <?php echo htmlspecialchars($complaint['Comp_ID']); ?></p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge 
                                        <?php
                                            if ($complaint['status'] == 'pending') echo 'bg-warning text-dark';
                                            elseif ($complaint['status'] == 'processing') echo 'bg-info text-white';
                                            elseif ($complaint['status'] == 'resolved') echo 'bg-success text-white';
                                        ?>">
                                        <?php echo htmlspecialchars($complaint['status']); ?>
                                        </span>
                                    </p>
                                    <p><strong>Complaint Description:</strong> <?php echo htmlspecialchars($complaint['complaint_description']); ?></p>
                                    <p><strong>Registration Date:</strong> <?php echo htmlspecialchars($registration_datetime); ?></p>
                                </div>
                        <?php
                            }
                        } else {
                            echo "<p class='text-center'>No complaints found</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="complaintform.php" class="btn btn-primary">Submit New Complaint</a>
        </div>
    </div>

    <!-- Local Bootstrap JS -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
