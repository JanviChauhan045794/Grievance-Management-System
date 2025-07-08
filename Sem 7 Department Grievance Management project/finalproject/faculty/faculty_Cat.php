<?php
// Establish a database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "grievance";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch data from both tables
$sql = "SELECT fc.faculty_id, f.firstname, c.category_description
        FROM faculty_complaints_cat fc
        INNER JOIN complaint_category c ON fc.Complaint_Category_ID = c.complaint_category_id
        INNER JOIN faculty f ON fc.Faculty_ID = f.faculty_id";

$result = $conn->query($sql);

if (!$result) {
    // Query failed
    die("Query failed: " . $conn->error);
}

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "Faculty Name: " . $row["firstname"]. " - Category: " . $row["category_description"]. "<br>";
    }
} else {
    echo "0 results";
}

$conn->close();
?>
