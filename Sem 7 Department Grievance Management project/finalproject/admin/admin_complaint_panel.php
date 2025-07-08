<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin_panel.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        /* Ensure the content doesn't overlap with the sidebar */
        .content {
            margin-left: 250px; /* Adjust this according to your sidebar width */
            padding: 20px;
        }
        .user-list {
            margin-top: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            padding: 10px 20px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
        }
        .pagination a.active {
            background-color: #0056b3;
        }
        .pagination a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

     <!-- Include the side navbar -->
     <?php include 'admindashboard.php'; ?>

    <!-- Main content area with margin to avoid overlap with sidebar -->
    <div class="content">
       
            <!-- Include get_user.php to display user data -->
            <?php include 'get_user_complaint.php'; ?>
        </div>
        
        <!-- Pagination -->
        <div class="pagination">
            <?php
               // Check if total pages is greater than 1
                if ($totalPages > 1) {
                    echo "<a href='admin_complaint_panel.php?page=".($page - 1)."' class='prev-btn'>&lt; Prev</a>";
                    for ($i = 1; $i <= $totalPages; $i++) {
                        echo "<a href='admin_complaint_panel.php?page=".$i."'".($page == $i ? " class='active'" : "").">$i</a>";
                    }
                    echo "<a href='admin_complaint_panel.php?page=".($page + 1)."' class='next-btn'>Next &gt;</a>";
                }
            ?>
       
    </div>
  
</body>
</html>
