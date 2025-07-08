<?php 
include "admindashboard.php";
include '../php/config.php'; // Ensure this path is correct and the config includes database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Complaints</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin-left: 250px; /* Adjust according to sidebar width */
        }
        .container {
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 95%;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            color: #333;
        }
        .filter-section {
            margin-bottom: 20px;
        }
        .filter-section .row > div {
            margin-bottom: 10px;
        }
        .btn-group {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .table-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>All Complaints</h1>

    <!-- Filter Section -->
    <div class="filter-section">
        <form action="" method="get">
            <div class="row">
                <!-- Filters -->
                <div class="col-md-2">
                    <label for="dueDateFilter">Due Date:</label>
                    <input type="date" class="form-control" id="dueDateFilter" name="due_date">
                </div>
                <div class="col-md-2">
                    <label for="endDateFilter">End Date:</label>
                    <input type="date" class="form-control" id="endDateFilter" name="end_date">
                </div>
                <div class="col-md-2">
                    <label for="statusFilter">Status:</label>
                    <select class="form-control" id="statusFilter" name="status">
                        <option value="">Select Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Processing">Processing</option>
                        <option value="Resolved">Resolved</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="facultyFilter">Faculty:</label>
                    <select class="form-control" id="facultyFilter" name="faculty">
                        <option value="">Select Faculty</option>
                        <?php
                        // Fetch all faculty
                        $sql = "SELECT f.faculty_id, u.firstname, u.lastname 
                                FROM faculty f 
                                JOIN users u ON f.user_id = u.user_id 
                                WHERE u.role = 'faculty'";
                        $facultyResult = $con->query($sql);
                        if ($facultyResult && $facultyResult->num_rows > 0) {
                            while ($row = $facultyResult->fetch_assoc()) {
                                echo "<option value='{$row['faculty_id']}'>{$row['firstname']} {$row['lastname']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="categoryFilter">Category:</label>
                    <select class="form-control" id="categoryFilter" name="category">
                        <option value="">Select Category</option>
                        <?php
                        // Fetch all complaint categories
                        $sql = "SELECT complaint_category_id, category_description FROM complaint_category";
                        $categoryResult = $con->query($sql);
                        if ($categoryResult && $categoryResult->num_rows > 0) {
                            while ($row = $categoryResult->fetch_assoc()) {
                                echo "<option value='{$row['complaint_category_id']}'>{$row['category_description']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="btn-group mt-3">
                <button type="submit" class="btn btn-primary">Filter</button>
                <button type="button" id="exportBtn" class="btn btn-success">Export to Excel</button>
            </div>
        </form>
    </div>

    <!-- Complaints Table -->
    <div class="table-container">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Complaint ID</th>
                    <th>Complaint Datetime</th>
                    <th>Due Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Complaint Category</th>
                    <th>Assigned Faculty</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $whereClause = [];

            // Build dynamic filters
            if (!empty($_GET['due_date'])) $whereClause[] = "c.Due_Date = '" . $con->real_escape_string($_GET['due_date']) . "'";
            if (!empty($_GET['end_date'])) $whereClause[] = "c.End_Date = '" . $con->real_escape_string($_GET['end_date']) . "'";
            if (!empty($_GET['status'])) $whereClause[] = "c.Status = '" . $con->real_escape_string($_GET['status']) . "'";
            if (!empty($_GET['faculty'])) $whereClause[] = "c.Faculty_ID = '" . $con->real_escape_string($_GET['faculty']) . "'";
            if (!empty($_GET['category'])) $whereClause[] = "c.Complaint_Cat_ID = '" . $con->real_escape_string($_GET['category']) . "'";

            // Construct SQL Query with Filters
            $sql = "SELECT 
                        c.Comp_ID, 
                        c.complaint_datetime, 
                        c.Due_Date, 
                        c.End_Date, 
                        c.Status, 
                        cc.category_description AS Complaint_Category, 
                        CONCAT(u.firstname, ' ', u.lastname) AS Faculty_Name 
                    FROM complaints c
                    JOIN complaint_category cc ON c.Complaint_Cat_ID = cc.complaint_category_id
                    JOIN faculty f ON c.Faculty_ID = f.faculty_id
                    JOIN users u ON f.user_id = u.user_id";

            // Apply filters if any
            if (!empty($whereClause)) {
                $sql .= " WHERE " . implode(" AND ", $whereClause);
            }

            $sql .= " ORDER BY c.complaint_datetime DESC"; // Order by latest complaints

            // Execute query
            $result = $con->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['Comp_ID']}</td>";
                    echo "<td>{$row['complaint_datetime']}</td>";
                    echo "<td>{$row['Due_Date']}</td>";
                    echo "<td>{$row['End_Date']}</td>";
                    echo "<td>{$row['Status']}</td>";
                    echo "<td>{$row['Complaint_Category']}</td>";
                    echo "<td>{$row['Faculty_Name']}</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No complaints found</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function exportToExcel() {
        let dueDate = document.getElementById('dueDateFilter').value || '';
        let endDate = document.getElementById('endDateFilter').value || '';
        let status = document.getElementById('statusFilter').value || '';
        let faculty = document.getElementById('facultyFilter').value || '';
        let category = document.getElementById('categoryFilter').value || '';

        // Redirect to export script with filters as GET parameters
        window.location.href = `export_to_excel.php?due_date=${dueDate}&end_date=${endDate}&status=${status}&faculty=${faculty}&category=${category}`;
    }

    // Trigger export on button click
    document.getElementById('exportBtn').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent form submission
        exportToExcel(); // Trigger Excel export
    });
</script>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
