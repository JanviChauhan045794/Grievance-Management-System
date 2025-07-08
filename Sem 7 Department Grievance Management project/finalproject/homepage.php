<?php
// Start the session
session_start();

// Debug session variables
// echo "<pre>";
// var_dump($_SESSION);
// echo "</pre>";

// Include the navigation bar (make sure only one navbar is in the nav.php)
// include("php/nav.php");

// Include your database connection
include 'php/config.php'; 
include 'php/nav.php';

// Check if the session is set for user_id
// if (isset($_SESSION['user_id'])) {
//     echo "User session is set with ID: " . $_SESSION['user_id'];
// } else {
//     echo "No user session is set."; 
// }

// Check if the user is logged in
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'student') {
    // User is logged in as a student, fetch the user_id from the session
    $user_id = $_SESSION['user_id'];

    // Prepare SQL to fetch user details
    $sql = "SELECT u.firstname, u.lastname, u.email 
            FROM users u 
            JOIN student s ON u.user_id = s.user_id 
            WHERE u.user_id = ?";

    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a record was found
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $firstname = $user['firstname'];
            $lastname = $user['lastname'];
            $email = $user['email'];

            // Display the welcome message with the first and last name
            echo "<div class='alert alert-success mt-4'>Welcome, $firstname $lastname!</div>";
        } else {
            // If no user found, handle the case appropriately
            echo "<div class='alert alert-warning mt-4'>User information not found.</div>";
        }
    } else {
        // Output the MySQL error
        echo "Error preparing the SQL statement: " . $con->error;
    }
} else {
    // User is not logged in or is not a student, display a default welcome message or redirect to the login page
    echo "<div class='alert alert-info mt-4'>Welcome to our Grievance Management System!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Grievance Management System</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Header Styling */
        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 20px 0;
        }

        header h1 {
            margin: 0;
            font-size: 32px;
        }

        /* Container Styling */
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .content h2 {
            margin-top: 0;
            font-size: 28px;
            color: #333;
        }

        .content p {
            color: #555;
            font-size: 16px;
            line-height: 1.6;
        }

        /* Button Styling */
        .btn-custom {
            background-color: #444;
            color: #fff;
            padding: 12px 24px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #666;
        }
    </style>
</head>
<body>






<div class="container">
    <div class="content">
        <h2>Welcome to our Grievance Management System!</h2>
        <p>If you have any grievances or complaints, please click the button below to submit them.</p>
        <a href="complaintform.php" class="btn btn-custom">Submit a Grievance</a>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

