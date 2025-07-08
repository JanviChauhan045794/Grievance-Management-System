<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .complaint-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .complaint-details {
            margin-top: 20px;
        }

        .student-info, .complaint-info {
            margin-bottom: 20px;
        }

        .btn-action {
            margin-right: 10px;
        }

        .status-dropdown {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        h2 {
            color: #343a40;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        session_start();
        include '../php/config.php';

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
            header("Location: ../index.php");
            exit();
        }

        if (isset($_GET['complaint_id'])) {
            $complaintId = $_GET['complaint_id'];
            $sql = "SELECT * FROM complaints WHERE complaint_id = $complaintId";
            $result = $con->query($sql);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<div class='complaint-header'>";
                echo "<h2>Complaint Details</h2>";
                echo "<div>";
                echo "<a href='faculty_complaintlist.php' class='btn btn-outline-success btn-action'>Back</a>";
                echo "<button class='btn btn-outline-danger btn-action' onclick='deleteUser({$row['complaint_id']})'>Delete</button>";
                echo "</div>";
                echo "</div>";
                
                echo "<div class='complaint-details'>";
                echo "<div class='complaint-info'>";
                echo "<strong>Complaint ID:</strong> {$row['Comp_ID']}<br>";
                echo "<strong>Due Date:</strong> {$row['Due_Date']}<br>";
                echo "<strong>End Date:</strong> {$row['End_Date']}<br>";
                echo "<strong>Status:</strong> ";
                echo "<select class='status-dropdown' onchange='updateStatus(this.value, {$row['complaint_id']}, \"{$row['Student_ID']}\", \"{$row['Comp_ID']}\")'>";
                $statusOptions = ['Pending', 'Processing', 'Resolved'];
                
                foreach ($statusOptions as $option) {
                    $selected = ($row['status'] === $option) ? 'selected' : '';
                    echo "<option value='$option' $selected>$option</option>";
                }
                
                echo "</select>";
                echo "</div>";

                $studentId = $row['Student_ID'];
                $studentSql = "
                    SELECT u.firstname, u.lastname, u.email, s.enrollment_id, s.semester, s.batch, s.class 
                    FROM complaints AS c
                    INNER JOIN student AS s ON c.Student_ID = s.student_id
                    INNER JOIN users AS u ON s.user_id = u.user_id
                    WHERE c.Student_ID = $studentId
                ";
                $studentResult = $con->query($studentSql);

                if ($studentResult && $studentResult->num_rows > 0) {
                    $studentRow = $studentResult->fetch_assoc();
                    $firstName = $studentRow['firstname'];
                    $lastName = $studentRow['lastname'];
                    $email = $studentRow['email'];
                    $enrollmentId = $studentRow['enrollment_id'];
                    $semester = $studentRow['semester'];
                    $batch = $studentRow['batch'];
                    $class = $studentRow['class'];

                    echo "<div class='student-info'>";
                    echo "<strong>Student Name:</strong> $firstName $lastName<br>";
                    echo "<strong>Email:</strong> $email<br>";
                    echo "<strong>Enrollment ID:</strong> $enrollmentId<br>";
                    echo "<strong>Semester:</strong> $semester<br>";
                    echo "<strong>Batch:</strong> $batch<br>";
                    echo "<strong>Class:</strong> $class<br>";
                    echo "</div>";
                } else {
                    echo "<div class='student-info'><strong>Student Details:</strong> Not found<br></div>";
                }

                echo "<strong>Complaint Description:</strong> {$row['complaint_description']}<br>";

                echo "<div class='mt-4'>";
                if (!empty($row['Complaint_Document'])) {
                    $documentName = $row['Complaint_Document'];
                    echo "<a href='../{$documentName}' class='btn btn-primary' target='_blank'>View Document</a>";
                } else {
                    echo "<span>No document available</span>";
                }
                echo "</div>";

                echo "</div>";
            } else {
                echo "<p class='text-danger'>Complaint not found.</p>";
            }           
        } else {
            echo "<p class='text-danger'>Complaint ID not provided.</p>";
        }

        $con->close();
        ?>
    </div>
        
    <script>
        function updateStatus(newStatus, complaintId, studentId, compId) {
            if (newStatus === 'Resolved') {
                const currentDate = new Date().toISOString().slice(0, 10);

                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        alert("Status updated and email notification sent.");
                    }
                };
                xmlhttp.open("GET", "update_status.php?status=" + newStatus + "&complaint_id=" + complaintId + "&end_date=" + currentDate + "&student_id=" + studentId + "&comp_id=" + compId, true);
                xmlhttp.send();
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log("Status updated successfully");
                    }
                };
                xmlhttp.open("GET", "update_status.php?status=" + newStatus + "&complaint_id=" + complaintId, true);
                xmlhttp.send();
            }
        }

        function deleteUser(complaintId) {
            if (confirm('Are you sure you want to delete this complaint?')) {
                window.location.href = "delete_complaint.php?complaint_id=" + complaintId;
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
