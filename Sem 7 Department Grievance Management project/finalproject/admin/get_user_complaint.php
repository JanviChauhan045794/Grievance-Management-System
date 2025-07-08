<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Complaints</title>
    <link rel="stylesheet" href="../style/admin_panel.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Additional CSS specific to this page if needed */
        .actions {
            display: flex;
            justify-content: space-between;
        }

        /* Button styles */
        .edit-btn, .delete-btn {
            padding: 8px 15px;
            margin-left: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s, color 0.3s;
        }

        .edit-btn {
            background-color: #5bc0de;
            color: #fff;
        }

        .delete-btn {
            background-color: #d9534f; /* Red color for delete button */
            color: #fff;
        }

        /* Hover styles */
        .edit-btn:hover, .delete-btn:hover {
            transform: scale(1.1);
        }

        /* Remove default link underline */
        .edit-btn a, .delete-btn a {
            text-decoration: none;
            color: #fff;
        }
    </style>
</head>
<body>


    <h1 class="mb-4">User Complaints</h1>
    <div class="user-list">
        <?php
        // Include the database configuration file
        include '../php/config.php';

        // Check if the connection is established
        if ($con) {
            // Pagination variables
            $limit = 5; // Number of records per page
            $page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number

            $start = ($page - 1) * $limit;

            // Query to count total number of records
            $countSql = "SELECT COUNT(*) AS total FROM complaints";
            $totalCountResult = $con->query($countSql);
            $totalCountRow = $totalCountResult->fetch_assoc();
            $totalRecords = $totalCountRow['total'];

            // Calculate total pages
            $totalPages = ceil($totalRecords / $limit);

            // Query to fetch user data for the current page
            $sql = "SELECT c.complaint_id, c.Comp_ID, c.Due_Date, c.End_Date, c.Status
                    FROM complaints c
                    ORDER BY c.complaint_id DESC
                    LIMIT $start, $limit";

            $result = $con->query($sql);

            // Check if data exists
            if ($result && $result->num_rows > 0) {
                
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card mb-3'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>Complaint ID: {$row['Comp_ID']}</h5>";
                    echo "<p class='card-text'>Due Date: {$row['Due_Date']}</p>";
                    echo "<p class='card-text'>End Date: {$row['End_Date']}</p>";
                    echo "<p class='card-text'>Status: {$row['Status']}</p>";
                    echo "<div class='actions'>";
                    echo "<button class='edit-btn'><a href='fetch_complaints.php?complaint_id={$row['complaint_id']}' class='open-btn'>Open</a></button>";
                    echo "<button class='delete-btn' onclick='deleteUser({$row['complaint_id']})'>Delete</button>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            }
        }
        // Close the database connection
        $con->close();
        ?>
    </div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
