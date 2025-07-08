<?php
// Include the database configuration file
include '../php/config.php';

// Check if the connection is established
if ($con) {
    // Pagination variables
    $limit = 5; // Number of records per page
    $page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number

    $start = ($page - 1) * $limit;

    // Query to count total number of faculty records
    $countSql = "SELECT COUNT(*) AS total 
                 FROM faculty f
                 INNER JOIN users u ON f.user_id = u.user_id
                 WHERE u.role = 'faculty'";
    $totalCountResult = $con->query($countSql);
    $totalCountRow = $totalCountResult->fetch_assoc();
    $totalRecords = $totalCountRow['total'];

    // Calculate total pages
    $totalPages = ceil($totalRecords / $limit);

    // Query to fetch faculty firstname, lastname, email from users and post from faculty for the current page
    $sql = "SELECT u.firstname, u.lastname, u.email, f.post, f.faculty_id
            FROM faculty f
            INNER JOIN users u ON f.user_id = u.user_id
            WHERE u.role = 'faculty'
            ORDER BY f.faculty_id DESC
            LIMIT $start, $limit";

    $result = $con->query($sql);

    // Check if data exists
    if ($result && $result->num_rows > 0) {
        echo "<div class='container mt-5'>";
       
        echo "<div class='list-group'>"; // Bootstrap list group

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<div class='list-group-item d-flex justify-content-between align-items-center'>";
            echo "<div>";
            // Display first name, last name, and post with colored text
            echo "<h5 >{$row['firstname']} {$row['lastname']}</h5>";
            echo "<p class='mb-1'>Post: <span class='text-success'>{$row['post']}</span></p>";
            echo "<p class='mb-1'>Email: <span class='text-danger'>{$row['email']}</span></p>";
            echo "</div>";
            // Delete form for each faculty record
            echo "<form action='deletefaculty.php' method='post'>";
            echo "<input type='hidden' name='faculty_id' value='{$row['faculty_id']}'>";
            echo "<button type='submit' class='btn btn-danger'>Delete</button>";
            echo "</form>";
            echo "</div>";
        }

        echo "</div>"; // End of list group

        // Output pagination links
        echo "<nav aria-label='Page navigation' class='mt-4'>";
        echo "<ul class='pagination justify-content-center'>";
       
        echo "</ul>";
        echo "</nav>";
        echo "</div>"; // End of container
    } else {
        echo "<div class='alert alert-warning'>0 results</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Failed to connect to the database.</div>";
}

// Close the database connection
$con->close();
?>
