<?php
session_start(); // Start the session

// Include your database configuration
include '../php/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$sql = "SELECT firstname, lastname, email FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    // Handle the case where the user is not found in the database
    echo "User not found.";
    exit();
}

// Store user details in variables
$firstname = $user['firstname'];
$lastname = $user['lastname'];
$email = $user['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="header_sidenavbar.css">
    <style>
        
        header {
            background-color: #333;
            padding: 15px;
            display: flex;
            align-items: center;
            width: 100%;
            position: fixed;
            z-index: 1000;
        }
        .sidebar-toggle {
            font-size: 24px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            margin-right: 20px;
        }
        .sidebar {
            background-color: #1e1e1e;
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: -250px; /* Hide sidebar initially */
            transition: left 0.3s ease;
            z-index: 999;
            padding-top: 60px; /* Adjust padding to make room for header */
        }
        .sidebar.active {
            left: 0; /* Show sidebar when active */
        }
        .admin-profile {
            padding: 20px;
            text-align: center;
            background-color: #333;
            color: white;
        }
        .sidebar-nav {
            padding: 0;
            list-style: none;
        }
        .sidebar-nav ul {
            padding: 0;
            margin: 0;
        }
        .sidebar-nav li {
            margin: 10px 0;
        }
        .sidebar-nav a {
            color: #e0e0e0;
            text-decoration: none;
            padding: 10px;
            display: block;
            transition: background 0.3s;
            border-radius: 5px;
        }
        .sidebar-nav a:hover {
            background-color: #007bff;
            color: white;
        }
        .content {
            margin-left: 260px; /* Adjust for sidebar width */
            padding: 20px;
            flex: 1;
            padding-top: 80px; /* Adjust padding for header */
        }
    </style>
</head>
<body>
    <header>
        <button class="sidebar-toggle" id="sidebar-toggle">&#9776;</button>
        <h1 class="dashboard-title">Faculty Dashboard</h1>
    </header>

    <aside class="sidebar" id="sidebar">
        <div class="admin-profile">
            <h3><?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></h3>
            <p><?php echo htmlspecialchars($email); ?></p>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="facultydashboard.php">Updates</a></li>
                <li><a href="faculty_complaintlist.php">Grievances</a></li>
                <li><a href="edit_faculty.php">Edit Profile</a></li>
                <li><a href="../php/logout.php">Log Out</a></li>
            </ul>
        </nav>
    </aside>

  
    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>
