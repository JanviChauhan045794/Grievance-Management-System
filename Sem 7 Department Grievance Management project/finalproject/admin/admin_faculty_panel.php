<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin_panel.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh; /* Full height of the viewport */
        }

        /* Sidebar with fixed width */
        .sidebar {
            width: 220px; /* Sidebar width */
            background-color: #343a40;
            color: white;
            height: 100vh; /* Full height of the sidebar */
            position: fixed; /* Fixed positioning */
            top: 0;
            left: 0;
        }

        /* Main content with padding */
        .main-content {
            margin-left: 220px; /* Margin to allow space for the sidebar */
            padding: 40px; /* Increased padding */
            flex-grow: 1; /* Fill the remaining space */
            background-color: #f8f9fa; /* Light background for main content */
        }
    </style>
</head>
<body>

    <!-- Include the side navbar -->
    <?php include 'admindashboard.php'; ?>

    <div class="main-content">
        <h1 class="mb-4">Faculty</h1>
        <div class="user-list mb-4">
            <!-- Include get_user.php to display user data -->
            <?php include 'get_faculty_users.php'; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                // Check if total pages is greater than 1
                if ($totalPages > 1) {
                    if ($page > 1) {
                        echo "<li class='page-item'><a class='page-link' href='admin_faculty_panel.php?page=".($page - 1)."'>&lt; Prev</a></li>";
                    }
                    for ($i = 1; $i <= $totalPages; $i++) {
                        echo "<li class='page-item".($page == $i ? " active" : "")."'><a class='page-link' href='admin_faculty_panel.php?page=".$i."'>$i</a></li>";
                    }
                    if ($page < $totalPages) {
                        echo "<li class='page-item'><a class='page-link' href='admin_faculty_panel.php?page=".($page + 1)."'>Next </a></li>";
                    }
                }
                ?>
            </ul>
        </nav>
    </div>

    <!-- Bootstrap JS and dependencies (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
