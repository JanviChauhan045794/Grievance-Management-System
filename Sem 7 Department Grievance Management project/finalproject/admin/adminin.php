<?php
// Establish connection to the database
include '../php/config.php';


//     session_start();

    


// // Check if user is logged in and role is admin
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../index.php");
//     exit;
// }

// $user_id = $_SESSION['user_id']; // Get the user_id from session
?>

<!-- <?php include "admindashboard.php"; ?> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Upload Users</title>
    <!-- Link Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Link to CSS file (adjust path as needed) -->
    <link rel="stylesheet" href="style.css">
    <style>
        /* General reset and layout styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            display: flex;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        
        /* Main content area styling */
        .main-content {
            margin-left: 250px; /* Leave space for the sidebar */
            padding: 40px;
            flex: 1;
            background-color: #f4f4f9;
        }
        .content-wrapper {
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        h2 {
            color: #34495e;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.8em;
        }
        label {
            font-size: 1.1em;
            color: #34495e;
            display: block;
            margin-bottom: 8px;
        }
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            background-color: #ecf0f1;
        }
        input[type="submit"] {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 1.2em;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="content-wrapper">
            <h2>Upload CSV File to Add Users</h2>
            <!-- Form to upload CSV file -->
            <form action="adminin_upload.php" method="post" enctype="multipart/form-data">
                <label for="file">Select CSV File:</label>
                <input type="file" name="excel_file" id="file" accept=".csv,.xlsx" required>
                <input type="submit" value="Upload and Process">
            </form>
        </div>
    </div>

</body>
</html>
