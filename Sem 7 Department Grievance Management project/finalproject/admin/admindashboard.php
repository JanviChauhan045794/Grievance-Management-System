<?php 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Start the session and include the configuration file
include '../php/config.php';
// Start the session

// Check if user is logged in and role is student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get the user_id from session


// Debug session variables
// echo "<pre>";
// var_dump($_SESSION);
// echo "</pre>";

?>

<!-- 
Notice: session_start(): Ignoring session_start() 
because a session is already active in E:\xampp\htdocs\grievance\admin\admindashboard.php on line 2 -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Prevent layout and styling conflicts by making styles specific to this page */
       

        /* Sidebar specific styling */
.admin-dashboard .sidebar {
    background-color: #343a40;
    color: white;
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 1rem;
    z-index: 1000; /* Ensure sidebar stays above other content */
    overflow-y: auto; /* Allow scrolling if content exceeds viewport */
    scrollbar-width: none;
}

/* Content area styling */
.admin-dashboard .content {
    margin-left: 250px; /* Offset content to account for fixed sidebar */
    padding: 2rem;
    background-color: #f8f9fa;
    min-height: 100vh;
}

/* Ensure that body fills the entire page */
body, html {
    height: 100%;
    margin: 0;
    padding: 0;
}


        /* Remove excessive space between list items */
        .admin-dashboard .sidebar ul {
            padding-left: 0;
            list-style: none;
        }

        .admin-dashboard .sidebar ul li {
            margin-bottom: 0.5rem; /* Reduced margin between items */
        }

        .admin-dashboard .sidebar ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: flex;
            align-items: center;
        }

        .admin-dashboard .sidebar ul li a .icon {
            margin-right: 15px;
        }

        .admin-dashboard .sidebar ul li a:hover {
            background-color: #495057;
            border-radius: 5px;
        }

        /* Content area styling */
        
        /* Header inside content */
        .admin-dashboard header {
            background-color: #007bff;
            color: white;
            padding: 1rem;
            text-align: center;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }

        /* Styling for dropdown menus */
        .admin-dashboard .dropdown-menu {
            background-color: #495057; /* Darker background for dropdown */
            color: white;
        }

        .admin-dashboard .dropdown-menu a {
            color: white;
        }

        .admin-dashboard .dropdown-menu a:hover {
            background-color: #343a40; /* Darker hover */
        }
    </style>
</head>
<body class="admin-dashboard">

    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="admin-profile text-center mb-4">
            <h3>Admin Profile</h3>
        </div>
        <ul>
            <li><a href="adminmainpage.php"><i class="fas fa-home icon"></i><span>Main Page</span></a></li>
            <li><a href="admin_user_panel.php"><i class="fas fa-users icon"></i><span>User Data</span></a></li>
            <li class="dropdown">
                <a href="admin_user_panel.php" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-user icon"></i><span>Users</span></a>
                <ul class="dropdown-menu">
                    <li><a href="admin_faculty_panel.php">Faculty</a></li>
                    <li><a href="admin_student_panel.php">Students</a></li>
                </ul>
            </li>
            <li><a href="admin_complaint_panel.php"><i class="fas fa-exclamation-triangle icon"></i><span>Complaints Data</span></a></li>
            <li><a href="search.php"><i class="fas fa-search icon"></i><span>Search Complaints</span></a></li>
            <li><a href="display.php"><i class="fas fa-list icon"></i><span>All Complaints</span></a></li>
            <li><a href="admin_assign_faculty_form.php"><i class="fas fa-tasks icon"></i><span>Assign Category</span></a></li>
            <li><a href="admin_assign_complaints.php"><i class="fas fa-clipboard icon"></i><span>Assign Complaint</span></a></li>
            <li><a href="adminin.php"><i class="fas fa-users icon"></i><span>User Management</span></a></li>
            <li><a href="edit.php"><i class="fas fa-pen-to-square icon"></i><span>Edit Page</span></a></li>
            <li><a href="../index.php"><i class="fas fa-right-from-bracket icon"></i><span>Log Out</span></a></li>


        </ul>
    </nav>

  
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
