<?php
// Include your database configuration
include("../php/config.php");
session_start();

// Ensure that the user is logged in as a faculty
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit;
}

$faculty_id = $_SESSION['faculty_id'];

$whereClause = [];
// Check if filter parameters are set in the GET request
if (!empty($_GET['due_date'])) {
    $whereClause[] = "c.Due_Date = '{$_GET['due_date']}'";
}
if (!empty($_GET['end_date'])) {
    $whereClause[] = "c.End_Date = '{$_GET['end_date']}'";
}
if (!empty($_GET['status'])) {
    $whereClause[] = "c.Status = '{$_GET['status']}'";
}
if (!empty($_GET['category'])) {
    $whereClause[] = "c.Complaint_Cat_ID = '{$_GET['category']}'";
}

$query = "SELECT c.Comp_ID, c.complaint_datetime, c.Due_Date, c.End_Date, c.Status, 
                 cc.category_description AS Complaint_Category, 
                 CONCAT(u.firstname, ' ', u.lastname) AS Faculty_Name
          FROM complaints c
          JOIN complaint_category cc ON c.Complaint_Cat_ID = cc.complaint_category_id
          JOIN faculty_complaints_cat fcc ON cc.complaint_category_id = fcc.Complaint_Category_ID
          JOIN faculty f ON fcc.Faculty_ID = f.faculty_id
          JOIN users u ON f.user_id = u.user_id
          WHERE f.user_id = '{$_SESSION['user_id']}' 
          AND fcc.assigned_date = (
              SELECT MAX(fcc2.assigned_date)
              FROM faculty_complaints_cat fcc2
              WHERE fcc2.Complaint_Category_ID = fcc.Complaint_Category_ID
          )";

if (!empty($whereClause)) {
    $query .= " AND " . implode(" AND ", $whereClause);
}

$query .= " ORDER BY c.complaint_datetime DESC";

// Execute the query
$result = $con->query($query);
$output = '';

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output .= "<tr>
                        <td>{$row['Comp_ID']}</td>
                        <td>{$row['complaint_datetime']}</td>
                        <td>{$row['Due_Date']}</td>
                        <td>{$row['End_Date']}</td>
                        <td>{$row['Status']}</td>
                        <td>{$row['Complaint_Category']}</td>
                        <td>{$row['Faculty_Name']}</td>
                    </tr>";
    }
} else {
    $output .= "<tr><td colspan='7' class='text-center'>No complaints found</td></tr>";
}

echo $output;
?>
