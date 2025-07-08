<?php
// Establish connection to the database
include '../php/config.php'; // Include your database connection script


// Start the session and include the configuration file


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Check if user is logged in and role is student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get the user_id from session

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin_panel.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f4f4f9;
        }

        .sidenav {
            width: 250px; /* Set the width of the sidebar */
            position: fixed; /* Keep it fixed on the side */
            height: 100%; /* Full height */
            background-color: #f8f9fa; /* Background color */
            z-index: 1000; /* Ensure it stays on top */
            padding-top: 20px; /* Top padding */
        }

        .main-content {
            margin-left: 260px; /* Space for the sidenav (adjust accordingly) */
            padding: 20px;
            width: calc(100% - 260px); /* Adjust the width based on sidebar width */
            min-height: 100vh; /* Ensure it takes full height */
        }

        .user-list {
            background-color: #fff; /* Background color for user list */
            padding: 20px;
            border-radius: 5px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Box shadow for better design */
        }

        .pagination {
            margin-top: 20px;
        }

    </style>
    <script>
        function deleteUser(studentId) {
            if (confirm('Are you sure you want to delete this user?')) {
                // Create an AJAX request
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "get_student_users.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                // Define the callback function
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        alert(xhr.responseText);
                        location.reload(); // Reload the page to see the changes
                    }
                };

                // Send the request with the delete_id parameter
                xhr.send("delete_id=" + studentId);
            }
        }
    </script>
</head>
<body>

    <!-- Side navbar -->
    <div class="sidenav">
        <?php include 'admindashboard.php'; ?> <!-- Include your side navbar here -->
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h1 class="mb-4">Students</h1>
        <div class="row">
            <div class="user-list col-12">
                <!-- Include get_student_users.php to display user data -->
                <?php include 'get_student_users.php'; ?>
            </div>
        </div>
        
        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <?php
                // Check if total pages is greater than 1
                if ($totalPages > 1) {
                    if ($page > 1) {
                        echo "<li class='page-item'><a class='page-link' href='admin_student_panel.php?page=".($page - 1)."'>Previous</a></li>";
                    }
                    for ($i = 1; $i <= $totalPages; $i++) {
                        echo "<li class='page-item".($page == $i ? " active" : "")."'><a class='page-link' href='admin_student_panel.php?page=".$i."'>$i</a></li>";
                    }
                    if ($page < $totalPages) {
                        echo "<li class='page-item'><a class='page-link' href='admin_student_panel.php?page=".($page + 1)."'>Next</a></li>";
                    }
                }
                ?>
            </ul>
        </nav>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> <!-- jQuery for Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> <!-- Popper.js for Bootstrap -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> <!-- Bootstrap JS -->
</body>
</html>
