<?php
// Start the session
session_start();

// Include database connection
include("../php/config.php");

// Display session data for debugging
// echo "<pre>";
// var_dump($_SESSION);
// echo "</pre>";

// Ensure the user is logged in and has a faculty role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    // Redirect to login if not logged in
    header("Location: ../index.php");
    exit();
}

// Fetch faculty details from session
$faculty_id = $_SESSION['faculty_id'];
$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];

// Default order by registration date in descending order
$order_by = "complaint_datetime DESC";

// Check if any filter/sort option is applied
if (isset($_GET['sort_by'])) {
    $sort_by = $_GET['sort_by'];
    
    // Modify the order_by clause based on the selected option
    if ($sort_by == 'oldest') {
        $order_by = "complaint_datetime ASC"; // Oldest to latest
    } elseif ($sort_by == 'status') {
        $order_by = "status"; // Sort by status
    }
}

// SQL query to fetch complaints assigned to the logged-in faculty
$query = "
    SELECT DISTINCT c.*
    FROM complaints AS c
    LEFT JOIN faculty_complaints_cat AS fcc ON c.Complaint_Cat_ID = fcc.Complaint_Category_ID
    WHERE c.faculty_id = '$faculty_id' OR fcc.faculty_id = '$faculty_id'
    ORDER BY $order_by
";
$result = mysqli_query($con, $query);
$result = mysqli_query($con, $query);

// Check if query executed successfully
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

// Fetch all complaints into an array
$complaints = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Complaints</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-mQ93Cd5C7/U0VV8lR3SNy9+4lbmJ6I5R7V7xaqqQkM8Oeg9ELGpE+nkW9iFA5Bfg" crossorigin="anonymous">

    <style>
        .container-custom {
            max-width: 800px;
            margin-top: 50px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .complaint-box {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-left: 5px solid #007bff;
            border-radius: 5px;
        }
        .complaint h5 {
            color: #333;
            font-weight: bold;
        }
        .complaint p {
            color: #555;
        }
        .btn {
            margin-top: 10px;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<?php include 'header_sidenavbar.php'; ?>
    <div class="container container-custom">
        <div class="text-center mb-4">
            <h2 class="text-primary">Complaints Assigned to You</h2>
            <p class="lead">Welcome, <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?>!</p>
        </div>

        <!-- Filter/Sort Form -->
        <div class="row mb-4">
            <div class="col-md-6 offset-md-3">
                <form method="GET" action="" class="d-flex justify-content-center">
                    <label for="sort_by" class="me-2">Sort by:</label>
                    <select name="sort_by" id="sort_by" class="form-select" onchange="this.form.submit()">
                        <option value="latest" <?php if (!isset($_GET['sort_by']) || $_GET['sort_by'] == 'latest') echo 'selected'; ?>>Latest to Oldest</option>
                        <option value="oldest" <?php if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'oldest') echo 'selected'; ?>>Oldest to Latest</option>
                        <option value="status" <?php if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'status') echo 'selected'; ?>>Status</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Display complaints or message -->
        <?php if (empty($complaints)) : ?>
            <div class="alert alert-warning text-center">
                <p>No complaints assigned to you.</p>
            </div>
        <?php else : ?>
            <?php foreach ($complaints as $complaint) : ?>
                <div class="complaint-box">
                    <h5>Complaint ID: <?php echo htmlspecialchars($complaint['Comp_ID']); ?></h5>
                    <p><strong>Registration Date:</strong> <?php echo htmlspecialchars($complaint['complaint_datetime']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($complaint['status']); ?></p>
                    <div class="d-flex justify-content-between">
                        <a class="btn btn-primary" href="faculty_fetch_complaints.php?complaint_id=<?php echo $complaint['complaint_id']; ?>">Open</a>
                        <button class="btn btn-danger" onclick="deleteComplaint('<?php echo $complaint['complaint_id']; ?>')">Delete</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-J1xrIOpyguGnN7KZ7d+jtcPfiQQCOlqgj8Oeg9ELGpE+nkW9iFA5Bfg" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-qQo8LPdC1WKaM7LUjh+MDDThbfQP+R/j8bmCoWT+BKA5/7Bv4P2FkfBf5+BrWqrP" crossorigin="anonymous"></script>

    <script>
        function deleteComplaint(compId) {
            if (confirm('Are you sure you want to delete this complaint?')) {
                // Perform AJAX request to delete complaint
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_complaint.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                        location.reload(); // Reload the page after deletion
                    }
                };
                xhr.send("complaint_id=" + compId);
            }
        }
    </script>
</body>
</html>
