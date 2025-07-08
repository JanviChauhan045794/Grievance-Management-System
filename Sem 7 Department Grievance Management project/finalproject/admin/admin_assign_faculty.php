<?php
include '../php/config.php'; // Include the database configuration file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $faculty_id = $_POST['faculty_id'];
    $complaint_category_id = $_POST['complaint_category_id'];
    
    // Current date and time for assignment
    $datetime = date('Y-m-d H:i:s');
    
    // Insert or update the assignment into the faculty_complaint_category table
    $sql = "INSERT INTO faculty_complaints_cat (Faculty_ID, faculty_complaint_cat, assigned_date)
            VALUES (?, ?, ?)";
    
    $stmt = $con->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("iis", $faculty_id, $complaint_category_id, $datetime);
        
        if ($stmt->execute()) {
            echo "Category successfully assigned!";
        } else {
            echo "Error assigning category: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing query: " . $con->error;
    }
    
    // Close the database connection
    $con->close();
}
?>
