<?php
include("../php/config.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer via Composer
require '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $complaint_id = $_POST['complaint_id'];
    $faculty_id = $_POST['faculty_id'];

    // Update the faculty_id for the specific complaint
    $updateQuery = "UPDATE complaints SET faculty_id = ? WHERE complaint_id = ?";
    if ($stmt = $con->prepare($updateQuery)) {
        $stmt->bind_param("ii", $faculty_id, $complaint_id);
        if ($stmt->execute()) {
            // Fetch faculty details
            $facultyQuery = "
                SELECT CONCAT(u.firstname, ' ', u.lastname) AS faculty_name, u.email AS faculty_email 
                FROM faculty f 
                LEFT JOIN users u ON f.user_id = u.user_id 
                WHERE f.faculty_id = ?";
            if ($facultyStmt = $con->prepare($facultyQuery)) {
                $facultyStmt->bind_param("i", $faculty_id);
                $facultyStmt->execute();
                $facultyResult = $facultyStmt->get_result();
                $faculty = $facultyResult->fetch_assoc();
                $faculty_name = $faculty['faculty_name'];
                $faculty_email = $faculty['faculty_email'];

                // Fetch complaint details
                $complaintQuery = "SELECT Comp_ID, complaint_description FROM complaints WHERE complaint_id = ?";
                if ($complaintStmt = $con->prepare($complaintQuery)) {
                    $complaintStmt->bind_param("i", $complaint_id);
                    $complaintStmt->execute();
                    $complaintResult = $complaintStmt->get_result();
                    $complaint = $complaintResult->fetch_assoc();
                    $comp_id = $complaint['Comp_ID'];
                    $comp_desc = $complaint['complaint_description'];

                    // Send notification email
                    $mail = new PHPMailer(true);
                    try {
                        // SMTP configuration
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'janvi.chauhan4599@gmail.com'; // Your Gmail address
                        $mail->Password = 'lbjm gjff vyca rwtx';   // Your app-specific password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        // Email settings
                        $mail->setFrom('janvi.chauhan4599@gmail.com', 'Admin - DGMS');
                        $mail->addAddress($faculty_email, $faculty_name);

                        // Email content
                        $mail->isHTML(true);
                        $mail->Subject = "New Complaint Assigned: $comp_id";
                        $mail->Body = "
                            <h1>New Complaint Assigned</h1>
                            <p>Dear $faculty_name,</p>
                            <p>You have been assigned a new complaint:</p>
                            <ul>
                                <li><strong>Complaint ID:</strong> $comp_id</li>
                                <li><strong>Description:</strong> $comp_desc</li>
                            </ul>
                            <p>Please address this complaint promptly.</p>
                            <p>Best regards,<br>Admin - DGMS</p>
                        ";
                        $mail->AltBody = "Dear $faculty_name,\n\nYou have been assigned a new complaint:\nComplaint ID: $comp_id\nDescription: $comp_desc\n\nPlease address this complaint promptly.\n\nBest regards,\nAdmin - DGMS";

                        $mail->send();
                        $success_message = "Faculty assigned successfully and notification email sent to $faculty_name!";
                    } catch (Exception $e) {
                        $error_message = "Faculty assigned successfully, but notification email could not be sent. Error: " . $mail->ErrorInfo;
                    }
                }
            }
        } else {
            $error_message = "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Error preparing statement: " . $con->error;
    }
}

// Pagination variables
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch total number of complaints
$totalQuery = "SELECT COUNT(*) AS total FROM complaints";
$totalResult = mysqli_query($con, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalComplaints = $totalRow['total'];
$totalPages = ceil($totalComplaints / $limit);

$query = "
    SELECT c.complaint_id, c.Complaint_Cat_ID, c.complaint_datetime, c.complaint_description, c.Comp_ID, 
           CONCAT(u.firstname, ' ', u.lastname) AS faculty_name, 
           f.post AS faculty_designation 
    FROM complaints c 
    LEFT JOIN faculty f ON c.faculty_id = f.faculty_id
    LEFT JOIN users u ON f.user_id = u.user_id 
    ORDER BY c.complaint_datetime DESC
    LIMIT $start, $limit
";

$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Assign Complaints</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { margin-top: 50px; }
        .complaint { background-color: #f9f9f9; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .assign-btn { margin-top: 10px; }
        .pagination { margin-top: 20px; }
        .pagination a.active { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Assign Complaints to Faculty</h1>
        <?php
        if (isset($success_message)) {
            echo "<div class='alert alert-success'>$success_message</div>";
        }
        if (isset($error_message)) {
            echo "<div class='alert alert-danger'>$error_message</div>";
        }

        if ($result && mysqli_num_rows($result) > 0) {
            while ($complaint = mysqli_fetch_assoc($result)) {
        ?>
                <div class="complaint">
                    <p><strong>Complaint ID:</strong> <?php echo $complaint['Comp_ID']; ?></p>
                    <p><strong>Current Faculty:</strong> 
                        <?php 
                            echo $complaint['faculty_name'] ? 
                            $complaint['faculty_name'] . " - " . $complaint['faculty_designation'] : 
                            'Not assigned'; 
                        ?>
                    </p>
                    <p><strong>Complaint Description:</strong> <?php echo $complaint['complaint_description']; ?></p>
                    <p><strong>Registration Date:</strong> <?php echo $complaint['complaint_datetime']; ?></p>

                    <form action="" method="post">
                        <input type="hidden" name="complaint_id" value="<?php echo $complaint['complaint_id']; ?>">
                        <div class="mb-3">
                            <label for="faculty_id" class="form-label">Assign to Faculty:</label>
                            <select class="form-select" name="faculty_id" id="faculty_id">
                                <?php
                                $facultyQuery = "
                                    SELECT f.faculty_id, CONCAT(u.firstname, ' ', u.lastname) AS faculty_name, f.post
                                    FROM faculty f
                                    LEFT JOIN users u ON f.user_id = u.user_id
                                ";
                                $facultyResult = $con->query($facultyQuery);
                                while ($row = $facultyResult->fetch_assoc()) {
                                    $selected = ($row['faculty_id'] == $complaint['faculty_id']) ? 'selected' : '';
                                    echo "<option value='{$row['faculty_id']}' {$selected}>{$row['faculty_name']} - {$row['post']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary assign-btn">Assign</button>
                    </form>
                </div>
        <?php
            }
        } else {
            echo "<div class='alert alert-info'>No complaints found.</div>";
        }
        ?>
        
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a href="admin_assign_complaints.php?page=<?php echo $page - 1; ?>" class="page-link">&lt; Prev</a></li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a href="admin_assign_complaints.php?page=<?php echo $i; ?>" class="page-link">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a href="admin_assign_complaints.php?page=<?php echo $page + 1; ?>" class="page-link">Next &gt;</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
