<?php
session_start();
include('../php/config.php');

// Ensure the user is logged in and has the correct session key
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$res_fname = "";
$res_lname = "";
$res_Email = "";

// Handle profile update
if (isset($_POST['update_profile'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];

    $user_id = $_SESSION['user_id'];

    $edit_query = mysqli_query($con, "UPDATE users SET firstname='$firstname', lastname='$lastname', email='$email' WHERE user_id='$user_id' AND role='faculty'");

    if ($edit_query) {
        echo "<div class='message'><p>Profile Updated!</p></div><br>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $user_id = $_SESSION['user_id'];

    $check_query = mysqli_query($con, "SELECT password FROM users WHERE user_id='$user_id'");
    $row = mysqli_fetch_assoc($check_query);

    if (password_verify($current_password, $row['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = mysqli_query($con, "UPDATE users SET password='$hashed_password' WHERE user_id='$user_id'");

            if ($update_query) {
                echo "<div class='message'><p>Password Updated Successfully!</p></div>";
            } else {
                echo "Error: " . mysqli_error($con);
            }
        } else {
            echo "<div class='message'><p>New passwords do not match.</p></div>";
        }
    } else {
        echo "<div class='message'><p>Current password is incorrect.</p></div>";
    }
}

// Retrieve current user details
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = mysqli_query($con, "SELECT firstname, lastname, email FROM users WHERE user_id='$user_id' AND role='faculty'");

    if ($query) {
        $result = mysqli_fetch_assoc($query);
        if ($result) {
            $res_fname = $result['firstname'];
            $res_lname = $result['lastname'];
            $res_Email = $result['email'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile & Password Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #fff;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar a {
            color: white !important;
        }
        .container {
            margin-top: 70px;
            max-width: 600px;
        }
        .form-box {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .tab-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .tab-buttons button {
            width: 48%;
        }
        .form-container {
            display: none;
            animation: fadeIn 0.5s;
        }
        .form-container.active {
            display: block;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        .password-wrapper {
            position: relative;
        }
        .password-wrapper input {
            padding-right: 40px; /* Ensure space for the icon */
        }
        .password-wrapper .toggle-password {
            position: absolute;
            right: 10px;
            top: 70%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888; /* Make sure the icon is visible */
            font-size: 1.2rem; /* Adjust size if needed */
            line-height: 1; /* Prevent spacing issues */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="facultydashboard.php">Department Grievance Management System</a>
        <div class="ml-auto">   
        <a class="navbar-brand" href="facultydashboard.php">FacultyDashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="form-box">
            <div class="tab-buttons">
                <button class="btn btn-primary" onclick="showForm('profile')">Edit Profile</button>
                <button class="btn btn-secondary" onclick="showForm('password')">Change Password</button>
            </div>

            <!-- Profile Form -->
            <div id="profile" class="form-container active">
                <h3>Update Profile</h3>
                <form method="post">
                    <div class="form-group">
                        <label>Firstname</label>
                        <input type="text" class="form-control" name="firstname" value="<?php echo $res_fname; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Lastname</label>
                        <input type="text" class="form-control" name="lastname" value="<?php echo $res_lname; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo $res_Email; ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
                </form>
            </div>

            <!-- Password Change Form -->
            <div id="password" class="form-container">
                <h3>Change Password</h3>
                <form method="post">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="form-group password-wrapper">
                        <label>New Password</label>
                        <input type="password" class="form-control" name="new_password" id="newPassword" required>
                        <i class="fas fa-eye-slash toggle-password" onclick="togglePassword('newPassword')"></i>
                    </div>
                    <div class="form-group password-wrapper">
                        <label>Confirm New Password</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirmPassword" required>
                        <i class="fas fa-eye-slash toggle-password" onclick="togglePassword('confirmPassword')"></i>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Show or hide form based on clicked button
        function showForm(formId) {
            const forms = document.querySelectorAll('.form-container');
            forms.forEach(form => {
                form.classList.remove('active');
            });
            document.getElementById(formId).classList.add('active');
        }

        // Toggle password visibility
        function togglePassword(inputId) {
            const passwordField = document.getElementById(inputId);
            const icon = passwordField.nextElementSibling;
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordField.type = "password";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>