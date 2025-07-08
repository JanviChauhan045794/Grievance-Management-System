<?php
session_start(); // Start the session

// Check if user is logged in and role is student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get the user_id from session

// Fetch the student_id from the database using user_id
include("php/config.php"); // Adjust this path as needed

$student_query = mysqli_query($con, "SELECT student_id FROM student WHERE user_id = '$user_id'");
$student_data = mysqli_fetch_assoc($student_query);
$student_id = $student_data['student_id']; // Get the student_id

// Store student_id in the session if needed
$_SESSION['student_id'] = $student_id;

if (!$student_id) {
    echo "Error: Student ID not found for user.";
    exit;
}

include("php/config.php");


    include ("php/nav.php"); // Include the navbar on the page


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <title>Complaint Submission Form</title>
</head>
<body>


<!-- Complaint Form -->
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header text-center bg-dark text-white">
            <h2>Submit a Complaint</h2>
        </div>
        <div class="card-body">
            <form id="complaintForm" action="submitcomplaint.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="complaintId" class="form-label">Complaint ID</label>
                    <input type="text" id="complaintId" name="complaintId" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label for="complaintCategoryID" class="form-label">Complaint Category</label>
                    <select name="complaintCategoryID" id="complaintCategoryID" class="form-select" required>
                        <option value="" selected disabled>Select Complaint Category</option>
                        <?php
                        // Fetch categories from the database
                        $category_query = mysqli_query($con, "SELECT * FROM complaint_category");
                        while ($row = mysqli_fetch_assoc($category_query)) {
                            echo "<option value='" . $row['complaint_category_id'] . "'>" . $row['category_description'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="complaintDescription" class="form-label">Complaint Description </label>
                    <textarea id="complaintDescription" name="complaintDescription" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
               
            <label for="complaintDocument" class="form-label">Complaint Document (PDF, PNG, JPEG, JPG)</label>
            <input type="file" id="complaintDocument" name="complaintDocument" class="form-control" accept=".pdf, .png, .jpeg, .jpg" required>


                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-dark">Submit Complaint</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Generate complaint ID using current timestamp
    function generateComplaintId() {
        var date = new Date();
        var day = date.getDate().toString().padStart(2, '0');
        var month = (date.getMonth() + 1).toString().padStart(2, '0');
        var year = date.getFullYear().toString().substr(-2);
        var hours = date.getHours().toString().padStart(2, '0');
        var minutes = date.getMinutes().toString().padStart(2, '0');
        var seconds = date.getSeconds().toString().padStart(2, '0');
        var milliseconds = date.getMilliseconds().toString().padStart(3, '0');
        var complaintId = day + month + year + hours + minutes + seconds + milliseconds;
        return complaintId;
    }

    // Set the generated complaint ID to the input field based on the selected category
    document.getElementById('complaintCategoryID').addEventListener('change', function() {
        var selectedCategory = this.value;
        var prefix = '';

        // Determine prefix based on selected category
        switch (selectedCategory) {
            case '1':
                prefix = 'L';
                break;
            case '2':
                prefix = 'E';
                break;
            case '3':
                prefix = 'Ac';
                break;
            case '4':
                prefix = 'Li';
                break;
            case '5':
                prefix = 'AB';
                break;
            case '6':
                prefix = 'T';
                break;
            case '7':
                prefix = 'C';
                break;
            case '8':
                prefix = 'S';
                break;
            case '9':
                prefix = 'Eq';
                break;
            case '10':
                prefix = 'I';
                break;
            case '11':
                prefix ='O';
                break;
            default:
                prefix = 'NA';
                break;
        }

        // Generate complaint ID and set it to the input field
        document.getElementById('complaintId').value = prefix + '-' + generateComplaintId();
    });
</script>

</body>
</html>
