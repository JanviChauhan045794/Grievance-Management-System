<?php 
// Start the session
session_start();

// Check if the user is logged in and has the faculty role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    die("Unauthorized access");
}

// Include the configuration file
include("../php/config.php");

// Fetch faculty_id using user_id from the session
$user_id = $_SESSION['user_id']; 
$faculty_query = "SELECT faculty_id FROM faculty WHERE user_id = ?";
$stmt = $con->prepare($faculty_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$faculty_data = $stmt->get_result()->fetch_assoc();
if (!$faculty_data || !isset($faculty_data['faculty_id'])) {
    die("Faculty ID not found.");
}
$faculty_id = $faculty_data['faculty_id'];
$stmt->close();

// Set faculty_id in session for future use
$_SESSION['faculty_id'] = $faculty_id;

// Define filters
$filterConditions = [];
$params = [];
$types = "i";  // Initial type for faculty ID

// Check for and apply dynamic filters
if (!empty($_GET['due_date'])) {
    $filterConditions[] = "c.Due_Date = ?";
    $params[] = $_GET['due_date'];
    $types .= "s";  // Adding string type for due_date
}
if (!empty($_GET['end_date'])) {
    $filterConditions[] = "c.End_Date = ?";
    $params[] = $_GET['end_date'];
    $types .= "s";  // Adding string type for end_date
}
if (!empty($_GET['status'])) {
    $filterConditions[] = "c.Status = ?";
    $params[] = $_GET['status'];
    $types .= "s";  // Adding string type for status
}
if (!empty($_GET['category'])) {
    $filterConditions[] = "c.Complaint_Cat_ID = ?";
    $params[] = $_GET['category'];
    $types .= "i";  // Adding integer type for category
}

// Filter SQL part based on conditions
$filterSQL = $filterConditions ? " AND " . implode(" AND ", $filterConditions) : "";

// Fetch complaints based on applied filters
$complaints_query = "
    SELECT c.Comp_ID, c.complaint_datetime, c.Due_Date, c.End_Date, c.Status, 
           cc.category_description AS Complaint_Category, 
           CONCAT(u.firstname, ' ', u.lastname) AS Faculty_Name
    FROM complaints c
    JOIN complaint_category cc ON c.Complaint_Cat_ID = cc.complaint_category_id
    JOIN faculty f ON c.Faculty_ID = f.faculty_id
    JOIN users u ON f.user_id = u.user_id
    WHERE c.Faculty_ID = ? 
    $filterSQL
    ORDER BY c.complaint_datetime DESC";

// Merge parameters to bind dynamically
$params = array_merge([$faculty_id], $params);

$stmt = $con->prepare($complaints_query);
$stmt->bind_param($types, ...$params);  // Dynamically bind parameters
$stmt->execute();
$complaints = $stmt->get_result();
$stmt->close();

// Fetch categories for dropdown (assigned to the faculty)
$category_query = "
    SELECT DISTINCT cc.complaint_category_id, cc.category_description 
    FROM complaint_category cc
    JOIN faculty_complaints_cat fcc ON cc.complaint_category_id = fcc.Complaint_Category_ID
    WHERE fcc.Faculty_ID = ?";
$stmt = $con->prepare($category_query);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$categories = $stmt->get_result();
$stmt->close();

// Fetch summary stats for the boxes
$stats_query = "
    SELECT 
        COUNT(*) AS total_complaints,
        COUNT(CASE WHEN c.Status = 'Pending' THEN 1 END) AS pending,
        COUNT(CASE WHEN c.Status = 'Processing' THEN 1 END) AS processing,
        COUNT(CASE WHEN c.Status = 'Resolved' THEN 1 END) AS resolved
    FROM complaints c
    WHERE c.Faculty_ID = ? $filterSQL";
$stmt = $con->prepare($stats_query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$stats_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>  <!-- Include JQuery for AJAX -->
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
        <!-- Dashboard Stats (Updated Dynamically) -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3><?php echo $stats_result['total_complaints']; ?></h3>
                <p>Total Complaints</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats_result['pending']; ?></h3>
                <p>Pending</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats_result['processing']; ?></h3>
                <p>Processing</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats_result['resolved']; ?></h3>
                <p>Resolved</p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form id="filter-form" method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
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
                            <option value="Processing" <?php echo isset($_GET['status']) && $_GET['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="Resolved" <?php echo isset($_GET['status']) && $_GET['status'] == 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="category">Category</label>
                        <select class="form-control" id="category" name="category">
                            <option value="">Select Category</option>
                            <?php
                            while ($category = $categories->fetch_assoc()) {
                                $selected = isset($_GET['category']) && $_GET['category'] == $category['complaint_category_id'] ? 'selected' : '';
                                echo "<option value='{$category['complaint_category_id']}' $selected>{$category['category_description']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary" onclick="loadDashboardStats()">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Complaints Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Complaint ID</th>
                        <th>Complaint Category</th>
                        <th>Faculty</th>
                        <th>Complaint Date</th>
                        <th>Due Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($complaints->num_rows > 0) {
                        while ($complaint = $complaints->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $complaint['Comp_ID'] . "</td>
                                    <td>" . $complaint['Complaint_Category'] . "</td>
                                    <td>" . $complaint['Faculty_Name'] . "</td>
                                    <td>" . $complaint['complaint_datetime'] . "</td>
                                    <td>" . $complaint['Due_Date'] . "</td>
                                    <td>" . $complaint['End_Date'] . "</td>
                                    <td>" . $complaint['Status'] . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No complaints found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Export to Excel Button -->
<form method="GET" action="export_to_excel.php">
    <input type="hidden" name="export_excel" value="1">
    <button type="submit" id="exportBtn" class="btn btn-success">Export to Excel</button>
</form>

    </div>

    <script>
        // AJAX for dynamically loading dashboard stats
        function loadDashboardStats() {
            $.ajax({
                url: 'get_dashboard_stats.php',  // This is a PHP file that returns the stats
                type: 'GET',
                data: $('#filter-form').serialize(),  // Send filter params
                success: function(response) {
                    // Update stats dynamically (in real-time)
                    let stats = JSON.parse(response);
                    $(".total_complaints").text(stats.total_complaints);
                    $(".pending").text(stats.pending);
                    $(".processing").text(stats.processing);
                    $(".resolved").text(stats.resolved);
                }
            });
        }

        // Initialize dashboard cards with AJAX (on page load and filter change)
        $(document).ready(function() {
            loadDashboardStats();  // Load stats initially
        });


        // Function to handle the export
function exportToExcel() {
    let dueDate = document.getElementById('due_date').value || ''; // Correct ID
    let endDate = document.getElementById('end_date').value || ''; // Correct ID
    let status = document.getElementById('status').value || ''; // Correct ID
    let category = document.getElementById('category').value || ''; // Correct ID

    // Redirect to export script with filters as GET parameters
    window.location.href = `export_to_excel.php?due_date=${dueDate}&end_date=${endDate}&status=${status}&category=${category}`;
}

// Trigger export on button click
document.getElementById('exportBtn').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent form submission
    exportToExcel(); // Trigger Excel export
});

    </script>

</body>
</html>
