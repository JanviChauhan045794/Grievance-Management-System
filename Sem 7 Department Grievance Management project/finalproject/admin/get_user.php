<?php
// Include the database configuration file
include '../php/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];
    // Prepare the DELETE query for the user
    $deleteSql = "DELETE FROM users WHERE user_id = ?";

    if ($stmt = $con->prepare($deleteSql)) {
        $stmt->bind_param("i", $deleteId);
        if ($stmt->execute()) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . $con->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error;
    }
    $con->close();
    exit();
}

// Check if the connection is established
if ($con) {
    // Pagination variables
    $limit = 5; // Number of records per page
    $page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number

    $start = ($page - 1) * $limit;

    // Query to count total number of records
    $countSql = "SELECT COUNT(*) AS total FROM users";
    
    $totalCountResult = $con->query($countSql);
    $totalCountRow = $totalCountResult->fetch_assoc();
    $totalRecords = $totalCountRow['total'];

    // Calculate total pages
    $totalPages = ceil($totalRecords / $limit);

    // Query to fetch user data for the current page
    $sql = "SELECT user_id, firstname, lastname, email, role
            FROM users
            ORDER BY user_id DESC
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
            echo "<h5 class='mb-1'>{$row['firstname']} {$row['lastname']}</h5>";
            echo "<p class='mb-1'>{$row['email']} - Role: {$row['role']}</p>"; // Display role here
            echo "</div>";
            echo "<button class='btn btn-danger' onclick='deleteUser({$row['user_id']})'>Delete</button>"; // Bootstrap delete button
            echo "</div>";
        }
        echo "</div>"; // End of list group

        // Output pagination links
        echo "<nav aria-label='Page navigation' class='mt-4'>";
        echo "<ul class='pagination justify-content-center'>";
        if ($page > 1) {
            echo "<li class='page-item'><a class='page-link' href='?page=".($page - 1)."'>Previous</a></li>";
        }
        for ($i = 1; $i <= $totalPages; $i++) {
            echo "<li class='page-item".($page == $i ? " active" : "")."'><a class='page-link' href='?page=".$i."'>$i</a></li>";
        }
        if ($page < $totalPages) {
            echo "<li class='page-item'><a class='page-link' href='?page=".($page + 1)."'>Next</a></li>";
        }
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script>
function deleteUser(user_id) {
    if (confirm("Are you sure you want to delete this record?")) {
        // Send the DELETE request via AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "submitcomplaint.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText);
                location.reload(); // Reload the page to see the updated list
            }
        };
        xhr.send("delete_id=" + user_id);
    }
}
</script>
