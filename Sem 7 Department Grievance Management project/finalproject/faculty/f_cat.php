<?php
// Start session and check login status
session_start();

if (!isset($_SESSION['faculty_id'])) {
    header("Location: ../index.php");
    exit();
}

$faculty_id = $_SESSION['faculty_id'];
$firstname = $_SESSION['firstname'];


include("../php/config.php");

// Query to fetch complaints assigned to this faculty's category
$query = "SELECT complaint_id, Comp_ID, complaint_datetime, Status 
          FROM complaints c
          INNER JOIN faculty_complaints_cat cat ON c.Complaint_Cat_ID = cat.Complaint_Category_ID
          WHERE cat.Faculty_ID = '$faculty_id'";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

$complaints = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Complaints</title>
    <link rel="stylesheet" href="css/facultydashboard.css"> <!-- Adjust path to your stylesheet -->
</head>
<body>

    <div class="main-content">
        <h2>Complaints Assigned to You</h2>
        <?php if (empty($complaints)) : ?>
            <p>No complaints assigned to you.</p>
        <?php else : ?>
            <table>
                <thead>
                    <tr>
                        <th>Comp ID</th>
                        <th>Registration Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $complaint) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($complaint['Comp_ID']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['complaint_datetime']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['Status']); ?></td>
                            <td><a href="faculty_complaint_details.php?complaint_id=<?php echo $complaint['complaint_id']; ?>">View Details</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script src="scripts.js"></script>
</body>
</html>
