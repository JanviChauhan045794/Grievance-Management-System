<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styling for the error page */
        body, html {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .error-container {
            text-align: center;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            max-width: 500px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }
        .error-cat {
            width: 200px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        /* Cat bounce animation */
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-30px);
            }
            60% {
                transform: translateY(-15px);
            }
        }
        .error-container h1 {
            font-size: 1.8em;
            color: #dc3545;
        }
        .error-container p {
            font-size: 1.2em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <!-- Cat image with bounce animation -->
        <img src="cat.png" alt="Error Cat" class="img-fluid error-cat">
        
        <!-- Error message -->
        <h1>Oops! Something went wrong.</h1>
        <p>
             
       
        
            <?php
            // Display the dynamic error message
            $error_message = isset($_GET['error_message']) ? htmlspecialchars($_GET['error_message']) : "An unexpected error occurred. Please try again.";
            echo $error_message;
            ?>
        </p>
       
        
        <!-- Back to Complaint Form button -->
        <a href="complaintform.php" class="btn btn-danger mt-3">Go Back to Complaint Form</a>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
