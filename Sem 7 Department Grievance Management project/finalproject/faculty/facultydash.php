<?php
// Connect to the database
include '../php/config.php';
session_start();

// Check if user is logged in and has faculty role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    die("Unauthorized access");
}

// Fetch faculty ID from session or database
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

// Define filters
$filterConditions = [];
$params = [];
$types = "i";  // Initial type for faculty ID

// Applying filters based on input
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

$filterSQL = $filterConditions ? " AND " . implode(" AND ", $filterConditions) : "";

// Fetch complaints with dynamic query
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

// Merge the parameters for binding
$params = array_merge([$faculty_id], $params);

$stmt = $con->prepare($complaints_query);
$stmt->bind_param($types, ...$params);  // Bind the parameters based on the dynamic $types and $params
$stmt->execute();
$complaints = $stmt->get_result();
$stmt->close();

// Fetch categories for dropdown (same as before)
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Complaints Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Complaints Dashboard</h2>
    <form method="GET" class="row mb-4">
        <div class="col-md-3">
            <label for="category" class="form-label">Category</label>
            <select name="category" id="category" class="form-select">
                <option value="">All Categories</option>
                <?php while ($row = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $row['complaint_category_id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $row['complaint_category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['category_description']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="due_date" class="form-label">Due Date</label>
            <input type="date" name="due_date" id="due_date" class="form-control" value="<?php echo htmlspecialchars($_GET['due_date'] ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="Pending" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="Resolved" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                <option value="In Progress" <?php echo (isset($_GET['status']) && $_GET['status'] === 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
            </select>
        </div>
        <div class="col-md-12 mt-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="?" class="btn btn-secondary">Reset</a>
        </div>
    </form>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Complaint ID</th>
            <th>Complaint Date</th>
            <th>Due Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Category</th>
            <th>Faculty</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($complaints->num_rows > 0): ?>
            <?php while ($row = $complaints->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Comp_ID']); ?></td>
                    <td><?php echo htmlspecialchars($row['complaint_datetime']); ?></td>
                    <td><?php echo htmlspecialchars($row['Due_Date']); ?></td>
                    <td><?php echo htmlspecialchars($row['End_Date']); ?></td>
                    <td><?php echo htmlspecialchars($row['Status']); ?></td>
                    <td><?php echo htmlspecialchars($row['Complaint_Category']); ?></td>
                    <td><?php echo htmlspecialchars($row['Faculty_Name']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">No complaints found</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).on('change', '.faculty-assign', function() {
    var complaint_id = $(this).data('complaint-id');
    var new_faculty_id = $(this).val();

    // Send AJAX request to assign new faculty to the complaint
    $.ajax({
        url: '..admin/admin_assign_complaint.php', // PHP file that updates the assignment
        type: 'POST',
        data: {
            complaint_id: complaint_id,
            new_faculty_id: new_faculty_id
        },
        success: function(response) {
            var data = JSON.parse(response);
            if (data.status === 'success') {
                alert(data.message);
            } else {
                alert('Error: ' + data.message);
            }
        },
        error: function() {
            alert('Failed to assign the faculty.');
        }
    });
});
</script>

</body>
</html>
