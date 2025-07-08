<?php
// Establish connection to the database
include '../php/config.php'; // Include your database connection script


// Start the session and include the configuration file

session_start(); // Start the session

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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
        }

       

        /* Main content flexes with the rest of the screen */
        .main-content {
            margin-left: 220px; /* Adjust according to sidebar width */
            padding: 70px;
            flex-grow: 1; /* Take up the rest of the available space */
            background-color: #f8f9fa;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .pagination a.active {
            font-weight: bold;
            color: white;
            background-color: #007bff;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
        }
    </style>

    <script>
    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "get_user.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert(xhr.responseText);
                    location.reload(); // Reload the page to see the changes
                }
            };

            xhr.send("delete_id=" + userId);
        }
    }
    </script>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
         <?php include 'admindashboard.php'; ?> 
    </div>

    <!-- Main content area -->
    <div class="main-content">
        <h1 class="text-center">Users</h1>
       
                    <!-- Include get_user.php to populate user data -->
                    <?php include 'get_user.php'; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
