<?php
include '../php/config.php';

if (isset($_GET['complaint_id']) && isset($_GET['status'])) {
    $complaintId = $_GET['complaint_id'];
    $status = $_GET['status'];
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

    if ($endDate) {
        $sql = "UPDATE complaints SET status = ?, End_Date = ? WHERE complaint_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssi", $status, $endDate, $complaintId);
    } else {
        $sql = "UPDATE complaints SET status = ? WHERE complaint_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("si", $status, $complaintId);
    }

    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
}
$con->close();
?>