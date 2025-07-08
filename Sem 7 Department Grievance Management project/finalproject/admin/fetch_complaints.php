<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Complaint Details</title>
    <link rel="stylesheet" href="../style/complaint_view.css">
    <style>
        /* Styling for the admin complaint view */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e0e5ec;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            width: 90%;
        }
        .user-item {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .user-item span {
            font-size: 18px;
            color: #333;
        }
        .user-item strong {
            color: #555;
        }
        .complaint-details, .actions {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .view-btn, .back-btn, .delete-btn {
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s ease;
            font-size: 16px;
        }
        .view-btn {
            background-color: #007bff;
            color: #fff;
        }
        .view-btn:hover {
            background-color: #0056b3;
        }
        .back-btn {
            background-color: #28a745;
            color: #fff;
        }
        .back-btn:hover {
            background-color: #218838;
        }
        .delete-btn {
            background-color: #dc3545;
            color: #fff;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
    <script>
        function deleteUser(complaintId) {
            if (confirm('Are you sure you want to delete this complaint?')) {
                window.location.href = "delete_complaint.php?complaint_id=" + complaintId;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <?php
        include '../php/config.php';

        if (isset($_GET['complaint_id'])) {
            $complaintId = $_GET['complaint_id'];
            $sql = "SELECT c.*, 
               su.firstname AS student_firstname, su.lastname AS student_lastname, 
               s.class, s.batch, s.semester, s.enrollment_id,
               fu.firstname AS faculty_firstname, fu.lastname AS faculty_lastname, f.post AS faculty_post
               
        FROM complaints AS c
        LEFT JOIN student AS s ON c.Student_ID = s.student_id
        LEFT JOIN users AS su ON s.user_id = su.user_id
        LEFT JOIN faculty AS f ON c.faculty_id = f.faculty_id
        LEFT JOIN users AS fu ON f.user_id = fu.user_id
       
        WHERE c.complaint_id = $complaintId";




            $result = $con->query($sql);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<div class='user-item'>";
                echo "<span><strong>Complaint ID:</strong> {$row['Comp_ID']}</span>";
                echo "<span><strong>Due Date:</strong> {$row['Due_Date']}</span>";
                echo "<span><strong>End Date:</strong> " . ($row['End_Date'] ? $row['End_Date'] : 'Not yet resolved') . "</span>";
                echo "<span><strong>Status:</strong> {$row['status']}</span>";
                echo "<span><strong>Complaint Description:</strong> {$row['complaint_description']}</span>";

                // Student details
                echo "<span><strong>Student Name:</strong> {$row['student_firstname']} {$row['student_lastname']}</span>";
                echo "<span><strong>Class:</strong> {$row['class']}</span>";
                echo "<span><strong>Batch:</strong> {$row['batch']}</span>";
                echo "<span><strong>Semester:</strong> {$row['semester']}</span>";

                // Faculty details
                echo "<span><strong>Assigned Faculty:</strong> {$row['faculty_firstname']} {$row['faculty_lastname']}</span>";

                // Resolved by details if resolved
                if ($row['status'] === 'Resolved') {
                    echo "<span><strong>Resolved By:</strong> {$row['resolved_by_firstname']} {$row['resolved_by_lastname']}</span>";
                }

                // Complaint Document
                echo "<div class='complaint-details'>";
                if (!empty($row['Complaint_Document'])) {
                    $documentName = $row['Complaint_Document'];
                    echo "<a href='../{$documentName}' class='view-btn' target='_blank'>View Document</a>";
                } else {
                    echo "<span>No document available</span>";
                }
                echo "</div>";

                // Actions
                echo "<div class='actions'>";
                echo "<a href='admin_complaint_panel.php?complaint_id={$row['complaint_id']}' class='back-btn'>Back</a>";
                echo "<button class='delete-btn' onclick='deleteUser({$row['complaint_id']})'>Delete</button>";
                echo "</div>";
                echo "</div>";
            } else {
                echo "<p>Complaint not found.</p>";
            }
        } else {
            echo "<p>Complaint ID not provided.</p>";
        }

        $con->close();
        ?>
    </div>
</body>
</html>
