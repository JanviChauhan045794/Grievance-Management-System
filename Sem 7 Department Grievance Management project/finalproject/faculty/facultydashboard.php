<?php 
// Start the session
session_start(); 

// Check if the user is logged in and has the faculty role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit;
}

// Include the configuration file
include("../php/config.php");

// Fetch faculty_id using user_id from the session
$user_id = $_SESSION['user_id']; 
$faculty_query = mysqli_query($con, "SELECT faculty_id FROM faculty WHERE user_id = '$user_id'");
$faculty_data = mysqli_fetch_assoc($faculty_query);
$faculty_id = $faculty_data['faculty_id']; 

if (!$faculty_id) {
    echo "Error: Faculty ID not found for user.";
    exit;
}

$_SESSION['faculty_id'] = $faculty_id;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand {
            color: #fff;
            font-size: 1.5rem;
        }
        .dashboard-stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .stat-card {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex-basis: 23%;
        }
        .filter-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        table {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'header_sidenavbar.php'; ?>
    <div class="container">
        <div class="dashboard-stats">
            <?php
            $totalComplaintsQuery = "
                SELECT COUNT(DISTINCT c.Comp_ID) AS total
    FROM complaints c
    JOIN complaint_category cc ON c.Complaint_Cat_ID = cc.complaint_category_id
    JOIN faculty_complaints_cat fcc ON cc.complaint_category_id = fcc.Complaint_Category_ID
    JOIN faculty f ON fcc.Faculty_ID = f.faculty_id
    JOIN users u ON f.user_id = u.user_id
    WHERE (f.faculty_id = '$faculty_id' OR fcc.Faculty_ID = '$faculty_id')
    AND fcc.assigned_date = (
        SELECT MAX(fcc2.assigned_date)
        FROM faculty_complaints_cat fcc2
        WHERE fcc2.Complaint_Category_ID = fcc.Complaint_Category_ID
    )
            ";
            $totalComplaints = mysqli_fetch_assoc(mysqli_query($con, $totalComplaintsQuery))['total'];

            $totalPendingQuery = "
                    SELECT COUNT(DISTINCT c.Comp_ID) AS total
    FROM complaints c
    JOIN complaint_category cc ON c.Complaint_Cat_ID = cc.complaint_category_id
    JOIN faculty_complaints_cat fcc ON cc.complaint_category_id = fcc.Complaint_Category_ID
    JOIN faculty f ON fcc.Faculty_ID = f.faculty_id
    JOIN users u ON f.user_id = u.user_id
    WHERE c.status = 'Pending' 
    AND (f.faculty_id = '$faculty_id' OR fcc.Faculty_ID = '$faculty_id')
    AND fcc.assigned_date = (
        SELECT MAX(fcc2.assigned_date)
        FROM faculty_complaints_cat fcc2
        WHERE fcc2.Complaint_Category_ID = fcc.Complaint_Category_ID
    )

            ";
            $totalPending = mysqli_fetch_assoc(mysqli_query($con, $totalPendingQuery))['total'];
            $totalInProcessQuery = "
            SELECT COUNT(DISTINCT c.Comp_ID) AS total
    FROM complaints c
    JOIN complaint_category cc ON c.Complaint_Cat_ID = cc.complaint_category_id
    JOIN faculty_complaints_cat fcc ON cc.complaint_category_id = fcc.Complaint_Category_ID
    JOIN faculty f ON fcc.Faculty_ID = f.faculty_id
    JOIN users u ON f.user_id = u.user_id
    WHERE c.status = 'Processing'
    AND (f.faculty_id = '$faculty_id' OR fcc.Faculty_ID = '$faculty_id')
    AND fcc.assigned_date = (
        SELECT MAX(fcc2.assigned_date)
        FROM faculty_complaints_cat fcc2
        WHERE fcc2.Complaint_Category_ID = fcc.Complaint_Category_ID
    )

        ";
        
            $totalInProcess = mysqli_fetch_assoc(mysqli_query($con, $totalInProcessQuery))['total'];

            $totalResolvedQuery = "
                 SELECT COUNT(DISTINCT c.Comp_ID) AS total
    FROM complaints c
    JOIN complaint_category cc ON c.Complaint_Cat_ID = cc.complaint_category_id
    JOIN faculty_complaints_cat fcc ON cc.complaint_category_id = fcc.Complaint_Category_ID
    JOIN faculty f ON fcc.Faculty_ID = f.faculty_id
    JOIN users u ON f.user_id = u.user_id
    WHERE c.status = 'Resolved'
    AND (f.faculty_id = '$faculty_id' OR fcc.Faculty_ID = '$faculty_id')
    AND fcc.assigned_date = (
        SELECT MAX(fcc2.assigned_date)
        FROM faculty_complaints_cat fcc2
        WHERE fcc2.Complaint_Category_ID = fcc.Complaint_Category_ID
    )

            ";
            $totalResolved = mysqli_fetch_assoc(mysqli_query($con, $totalResolvedQuery))['total'];
            ?>
            <div class="stat-card">
                <h4>Total Complaints</h4>
                <h3><?php echo $totalComplaints; ?></h3>
            </div>
            <div class="stat-card">
                <h4>Total Pending</h4>
                <h3><?php echo $totalPending; ?></h3>
            </div>
            <div class="stat-card">
                <h4>Total In Process</h4>
                <h3><?php echo $totalInProcess; ?></h3>
            </div>
            <div class="stat-card">
                <h4>Total Resolved</h4>
                <h3><?php echo $totalResolved; ?></h3>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label for="due_date">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo isset($_GET['due_date']) ? $_GET['due_date'] : ''; ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">Select Status</option>
                            <option value="Pending" <?php echo isset($_GET['status']) && $_GET['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Processing" <?php echo isset($_GET['status']) && $_GET['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="Resolved" <?php echo isset($_GET['status']) && $_GET['status'] == 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
    <label for="category">Category</label>
    <select class="form-control" id="category" name="category">
        <option value="">Select Category</option>
        <?php
        // Fetch assigned categories for the faculty
        $categoryQuery = "
            SELECT c.Complaint_Category_ID, c.category_description
            FROM complaint_category c
            INNER JOIN faculty_complaints_cat fcc 
                ON c.Complaint_Category_ID = fcc.Complaint_Category_ID
            WHERE fcc.Faculty_ID = '$faculty_id'
              AND fcc.assigned_date <= NOW()
        ";

        $categoryResult = mysqli_query($con, $categoryQuery);

        // Check query result and populate categories
        if ($categoryResult && mysqli_num_rows($categoryResult) > 0) {
            while ($category = mysqli_fetch_assoc($categoryResult)) {
                // Check for selected category in GET
                $selected = isset($_GET['category']) && $_GET['category'] == $category['Complaint_Category_ID'] ? 'selected' : '';
                echo "<option value='{$category['Complaint_Category_ID']}' $selected>{$category['category_description']}</option>";
            }
        } else {
            echo "<option value=''>No categories assigned</option>";
        }
        ?>
    </select>
</div>

                    <div class="form-group col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <button type="button" class="btn btn-success ml-2" id="exportBtn">Export to Excel</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Complaints Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Complaint ID</th>
                        <th>Complaint Date</th>
                        <th>Due Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Complaint Category</th>
                        <th>Faculty Name</th>
                    </tr>
                </thead>
                <tbody>
                <?php
            
            $whereClause = [];
            
            // Checking if filter parameters are set in the GET request
            if (!empty($_GET['due_date'])) {
                $whereClause[] = "c.Due_Date = '{$_GET['due_date']}'";
            }
            if (!empty($_GET['end_date'])) {
                $whereClause[] = "c.End_Date = '{$_GET['end_date']}'";
            }
            if (!empty($_GET['status'])) {
                $whereClause[] = "c.Status = '{$_GET['status']}'";
            }
            if (!empty($_GET['category'])) {
                $whereClause[] = "c.Complaint_Cat_ID = '{$_GET['category']}'";
            }
            
            // Query for fetching complaints for the current faculty based on session user_id
            $query = "SELECT c.Comp_ID, c.complaint_datetime, c.Due_Date, c.End_Date, c.Status, 
                             cc.category_description AS Complaint_Category, 
                             CONCAT(u.firstname, ' ', u.lastname) AS Faculty_Name
                      FROM complaints c
                      JOIN complaint_category cc ON c.Complaint_Cat_ID = cc.complaint_category_id
                      JOIN faculty_complaints_cat fcc ON cc.complaint_category_id = fcc.Complaint_Category_ID
                      JOIN faculty f ON fcc.Faculty_ID = f.faculty_id
                      JOIN users u ON f.user_id = u.user_id
                      WHERE f.user_id = '{$_SESSION['user_id']}' AND fcc.assigned_date = (
                          SELECT MAX(fcc2.assigned_date)
                          FROM faculty_complaints_cat fcc2
                          WHERE fcc2.Complaint_Category_ID = fcc.Complaint_Category_ID
                      )";
            
            // Adding additional filter clauses dynamically based on the GET parameters
            if (!empty($whereClause)) {
                $query .= " AND " . implode(" AND ", $whereClause);
            }
            $query .= " ORDER BY c.complaint_datetime DESC";
            
            // Executing the query
            $result = $con->query($query);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['Comp_ID']}</td>
                            <td>{$row['complaint_datetime']}</td>
                            <td>{$row['Due_Date']}</td>
                            <td>{$row['End_Date']}</td>
                            <td>{$row['Status']}</td>
                            <td>{$row['Complaint_Category']}</td>
                            <td>{$row['Faculty_Name']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No complaints found</td></tr>";
            }
            ?>
                    </tbody>
                </tbody>
            </table>
        </div>
    </div>
    

    <script>
        function exportToExcel() {
        let dueDate = document.getElementById('due_date').value || ''; // Correct ID
        let endDate = document.getElementById('end_date').value || ''; // Correct ID
        let status = document.getElementById('status').value || ''; // Correct ID
        let category = document.getElementById('category').value || ''; // Correct ID

        // Redirect to export script with filters as GET parameters
        window.location.href = `export_to_excel.php?due_date=${dueDate}&end_date=${endDate}&status=${status}&category=${category}`;
    }

    document.getElementById('exportBtn').addEventListener('click', exportToExcel);
 


    // When the Export button is clicked, prevent form submission and trigger export
    document.getElementById('exportBtn').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent form submission
        exportToExcel(); // Trigger Excel export
    });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
