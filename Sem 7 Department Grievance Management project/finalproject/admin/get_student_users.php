<?php
// Include the database configuration file
include '../php/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];
    $deleteSql = "DELETE FROM student WHERE student_id = ?";
    
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

    // Query to count total number of student records
    $countSql = "SELECT COUNT(*) AS total
                 FROM student s
                 JOIN users u ON s.user_id = u.user_id
                 WHERE u.role = 'student'";
    $totalCountResult = $con->query($countSql);
    $totalCountRow = $totalCountResult->fetch_assoc();
    $totalRecords = $totalCountRow['total'];

    // Calculate total pages
    $totalPages = ceil($totalRecords / $limit);

    // Query to fetch student data for the current page
    $sql = "SELECT s.student_id, u.firstname, u.lastname, u.email, s.enrollment_id, s.batch, s.class, s.semester
            FROM student s
            JOIN users u ON s.user_id = u.user_id
            WHERE u.role = 'student'
            ORDER BY s.student_id DESC
            LIMIT $start, $limit";

    $result = $con->query($sql);

    // Check if data exists
    if ($result && $result->num_rows > 0) {
        echo "<div class='list-group mb-4'>"; // Bootstrap list group
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<div class='list-group-item d-flex justify-content-between align-items-center'>";
            echo "<div class='user-details'>";
            echo "<h5 class='mb-1'>{$row['firstname']} {$row['lastname']}</h5>";
            echo "<p class='mb-1'>Email: {$row['email']}</p>";
            echo "<p class='mb-1'>Enrollment ID: {$row['enrollment_id']}</p>";
            echo "<p class='mb-1'>Batch: {$row['batch']}</p>";
            echo "<p class='mb-1'>Class: {$row['class']}</p>";
            echo "<p class='mb-1'>Semester: {$row['semester']}</p>";
            echo "</div>";
            echo "<button class='btn btn-danger' onclick='deleteUser({$row['student_id']})'>Delete</button>"; // Bootstrap delete button
            echo "</div>";
        }
        echo "</div>"; // End of list group

        
    } else {
        echo "<div class='alert alert-warning'>0 results</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Failed to connect to the database.</div>";
}

// Close the database connection
$con->close();
?>

<script>
function deleteUser(student_id) {
    if (confirm("Are you sure you want to delete this record?")) {
        // Send the DELETE request via AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "get_student_users.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText);
                location.reload(); // Reload the page to see the updated list
            }
        };
        xhr.send("delete_id=" + student_id);
    }
}
</script>
