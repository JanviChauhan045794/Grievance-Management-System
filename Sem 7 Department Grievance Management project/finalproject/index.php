<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #f3f4f6, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 450px;
            padding: 30px;
        }

        .login-card h2 {
            text-align: center;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 20px;
        }

        .login-card p {
            text-align: center;
            color: #6c757d;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-black {
            background-color: #000;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            padding: 12px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-black:hover {
            background-color: #333;
        }

        .form-check-label {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .password-field {
            position: relative;
        }

        .toggle-password {
            cursor: pointer;
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: bold;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .register-text {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
        }

        .register-text a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .register-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Login</h2>
        <p>Welcome back! Please enter your credentials to continue.</p>

        <?php
        session_start();
        include("php/config.php");

        if (isset($_POST['submit'])) {
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $password = mysqli_real_escape_string($con, $_POST['password']);

            $user_result = mysqli_query($con, "SELECT * FROM users WHERE email='$email'") or die("User Select Error: " . mysqli_error($con));
            $user_row = mysqli_fetch_assoc($user_result);

            if ($user_row) {
                if (password_verify($password, $user_row['password'])) {
                    $_SESSION['user_id'] = $user_row['user_id'];
                    $_SESSION['role'] = $user_row['role'];
                    $_SESSION['firstname'] = $user_row['firstname'];
                    $_SESSION['lastname'] = $user_row['lastname'];

                    if ($user_row['role'] === 'admin') {
                        header("Location: admin/adminmainpage.php");
                    } elseif ($user_row['role'] === 'student') {
                        header("Location: homepage.php");
                    } elseif ($user_row['role'] === 'faculty') {
                        header("Location: faculty/facultydashboard.php");
                    }
                    exit();
                } else {
                    echo "<div class='alert alert-danger text-center'>Invalid Password!</div>";
                }
            } else {
                echo "<div class='alert alert-danger text-center'>User not found!</div>";
            }
        }
        ?>

        <form action="" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="mb-3 password-field">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                <br>
            
                <span class="toggle-password" onclick="togglePassword()">Show</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                
            </div>
            <div class="d-grid">
                <button type="submit" name="submit" class="btn btn-black">Login</button>
            </div>
        </form>

    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleButton = document.querySelector('.toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleButton.textContent = 'Hide';
            } else {
                passwordField.type = 'password';
                toggleButton.textContent = 'Show';
            }
        }
    </script>
</body>
</html>
