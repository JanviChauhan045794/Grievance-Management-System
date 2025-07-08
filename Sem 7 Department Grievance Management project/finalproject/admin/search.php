<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Complaints</title>
    <style>
        body {
            background-color: white;
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin: 40px 0;
            color: #333;
        }

        .search-container {
            background-color: #333;
            padding: 30px 40px;
            border-radius: 10px;
            width: 500px;
            margin: 40px auto;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        label {
            color: #fff;
            font-weight: bold;
        }

        input[type="text"] {
            width: calc(100% - 24px);
            padding: 12px;
            border: 2px solid #fff;
            border-radius: 6px;
            margin-top: 15px;
            margin-bottom: 25px;
            font-size: 16px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus {
            border-color: #f39c12;
            outline: none;
        }

        button {
            background-color: #f39c12;
            color: #fff;
            padding: 14px 0;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        button:hover {
            background-color: #e67e22;
            transform: translateY(-2px);
        }

        .result-container {
            margin-top: 40px;
            padding: 30px;
            border: 1px solid #333;
            border-radius: 8px;
            background-color: #f9f9f9;
            max-width: 600px;
            margin: 20px auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .result-container h2 {
            margin-top: 0;
            text-align: center;
            color: #333;
        }

        .complaint-document {
            color: #0066cc;
            text-decoration: none;
        }

        .complaint-document:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 15px;
            border: 1px solid #dc3545;
            border-radius: 8px;
            max-width: 500px;
            margin: 20px auto;
            text-align: center;
        }

        @media (max-width: 600px) {
            .search-container, .result-container, .error-message {
                width: 90%;
                padding: 20px;
            }

            input[type="text"], button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <?php include 'admindashboard.php'; ?>
    <h1>Search Complaints by ID</h1>
    <div class="search-container">
        <form method="GET" action="search.php">
            <label for="complaint_id">Complaint ID:</label>
            <input type="text" id="complaint_id" name="complaint_id" required placeholder="Enter Complaint ID">
            <button type="submit">Search</button>
        </form>
    </div>

    <?php
    if (isset($_GET['complaint_id']) && !empty($_GET['complaint_id'])) {
        include '../php/config.php';
        $complaint_id = $con->real_escape_string($_GET['complaint_id']);
        $query = "SELECT 
        c.Comp_ID, 
        c.status, 
        c.complaint_description, 
        c.Due_Date, 
        c.End_Date, 
        c.Complaint_Document,
        su.firstname AS student_firstname, 
        su.lastname AS student_lastname,
        s.class AS student_class,
        s.batch AS student_batch, 
        s.enrollment_id AS student_enroll,
        fu.firstname AS faculty_firstname, 
        fu.lastname AS faculty_lastname,
        f.post AS faculty_post
    FROM complaints c
    INNER JOIN student s ON c.Student_ID = s.student_id
    INNER JOIN users su ON s.user_id = su.user_id
    -- Direct join with the faculty table based on the current faculty_id from the complaints table
    INNER JOIN faculty f ON c.faculty_id = f.faculty_id
    INNER JOIN users fu ON f.user_id = fu.user_id
    WHERE c.Comp_ID = ?";



        $stmt = $con->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("s", $complaint_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo '<div class="result-container">';
                echo "<h2>Complaint Details</h2>";
                while ($row = $result->fetch_assoc()) {
                    echo "Complaint ID: " . $row['Comp_ID'] . "<br>";
                    echo "Faculty Name: " . $row['faculty_firstname'] . " " . $row['faculty_lastname'] . "<br>";
                    echo "Faculty Designation: " . $row['faculty_post'] .  "<br>";
                    echo "Status: " . $row['status'] . "<br>";
                    echo "Due Date: " . $row['Due_Date'] . "<br>";
                    echo "End Date: " . $row['End_Date'] . "<br>";
                    echo "Student Name: " . $row['student_firstname'] . " " . $row['student_lastname'] . "<br>";
                    echo "Student Class :" . $row['student_class'] . "<br>";
                    echo "Student Batch :" . $row['student_batch'] . "<br>";
                    echo "Student Enrollment ID: " . $row['student_enroll'] . "<br>";
                    echo "Description: " . $row['complaint_description'] . "<br>";
                    echo "Complaint Document: <a class='complaint-document' href='http://localhost/grievance/" . $row['Complaint_Document'] . "'>" . $row['Complaint_Document'] . "</a><br>";
                    echo "<br>";
                }
                echo '</div>';
            } else {
                echo "<div class='error-message'>Complaint ID not found. Please check and try again.</div>";
            }
            
            $stmt->close();
        } else {
            echo "<div class='error-message'>Error preparing the statement.</div>";
        }
        
        $con->close();
    }
    ?>
</body>
</html>
