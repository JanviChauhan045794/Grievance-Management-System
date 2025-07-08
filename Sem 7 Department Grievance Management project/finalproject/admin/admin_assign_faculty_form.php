<?php
include '../php/config.php'; // Include the database configuration file
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $faculty_id = $_POST['faculty_id'];
    $complaint_category_id = $_POST['complaint_category_id'];

    // Check if an entry already exists in the faculty_complaints_cat table
    $checkQuery = "SELECT * FROM faculty_complaints_cat WHERE faculty_id = ? AND complaint_category_id = ?";
    $stmt = $con->prepare($checkQuery);
    $stmt->bind_param('ii', $faculty_id, $complaint_category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Entry exists, update the assigned_date
        $updateQuery = "UPDATE faculty_complaints_cat SET assigned_date = NOW() WHERE faculty_id = ? AND complaint_category_id = ?";
        $stmt = $con->prepare($updateQuery);
        $stmt->bind_param('ii', $faculty_id, $complaint_category_id);
        if ($stmt->execute()) {
            $success_message = "Category reassigned successfully.";
        } else {
            $error_message = "Error updating category: " . $con->error;
        }
    } else {
        // No entry exists, insert a new record
        $insertQuery = "INSERT INTO faculty_complaints_cat (faculty_id, complaint_category_id, assigned_date) VALUES (?, ?, NOW())";
        $stmt = $con->prepare($insertQuery);
        $stmt->bind_param('ii', $faculty_id, $complaint_category_id);
        if ($stmt->execute()) {
            $success_message = "Category assigned successfully.";
        } else {
            $error_message = "Error assigning category: " . $con->error;
        }
    }

    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Category</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5dc;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .main-container {
            display: flex;
        }
        /* Sidebar adjustments */
        .sidebar {
            flex: 0 0 250px;
        }
        /* Content adjustments */
        .content {
            flex-grow: 1;
            padding: 20px;
            background-color: #f5f5dc;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #333;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            color: #f5f5dc;
        }
        h1 {
            text-align: center;
            color: #f5f5dc;
            margin-bottom: 30px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            margin-top: 20px;
        }
        select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #f5f5dc;
            color: #333;
            font-size: 16px;
        }
        button {
            width: 100%;
            background-color: #000;
            color: #f5f5dc;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #444;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        .message {
            margin-top: 20px;
            text-align: center;
            font-size: 18px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>

<div class="main-container">
    <div class="sidebar">
        <?php include "admindashboard.php"; ?>
    </div>
    <div class="content">
        <div class="container">
            <h1>Assign Complaint Category</h1>
            
            <!-- Display success or error message -->
            <?php if ($success_message) : ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php elseif ($error_message) : ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <label for="faculty_id">Select Faculty:</label>
                <select name="faculty_id" id="faculty_id">
                <?php
                $facultyQuery = "
                    SELECT f.faculty_id, u.firstname, u.lastname 
                    FROM faculty f 
                    JOIN users u ON f.user_id = u.user_id 
                    WHERE u.role = 'faculty'";
                $facultyResult = $con->query($facultyQuery);

                if ($facultyResult->num_rows > 0) {
                    while ($row = $facultyResult->fetch_assoc()) {
                        echo "<option value='{$row['faculty_id']}'>{$row['firstname']} {$row['lastname']}</option>";
                    }
                } else {
                    echo "<option value=''>No faculty found</option>";
                }
                ?>
                </select>
                
                <label for="complaint_category_id">Select Complaint Category:</label>
                <select name="complaint_category_id" id="complaint_category_id">
                    <?php
                    $categoryQuery = "SELECT complaint_category_id, category_description FROM complaint_category";
                    $categoryResult = $con->query($categoryQuery);
                    if ($categoryResult->num_rows > 0) {
                        while ($row = $categoryResult->fetch_assoc()) {
                            echo "<option value='{$row['complaint_category_id']}'>{$row['category_description']}</option>";
                        }
                    } else {
                        echo "<option value=''>No categories found</option>";
                    }
                    ?>
                </select>
                <button type="submit">Assign Category</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
