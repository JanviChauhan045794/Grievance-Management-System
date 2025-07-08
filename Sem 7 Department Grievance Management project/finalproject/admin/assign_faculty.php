<?php


include("../php/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $complaint_id = $_POST['complaint_id'];
    $faculty_id = $_POST['faculty_id'];

    // Update the complaints table with the new faculty ID
    $updateQuery = "UPDATE complaints SET faculty_id = ? WHERE Comp_ID = ?";
    $stmt = $con->prepare($updateQuery);
    $stmt->bind_param("ii", $faculty_id, $complaint_id);

    if ($stmt->execute()) {
        echo "Faculty assigned successfully.";
    } else {
        echo "Error: " . $con->error;
    }

    $stmt->close();
    header("Location: admin_assign_complaints.php"); // Redirect back to the complaints page
    exit;
}
