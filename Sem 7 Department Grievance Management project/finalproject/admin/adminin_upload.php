<?php
include '../php/config.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['excel_file'])) {
        // Check for upload errors
        if ($_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            echo "File upload error: " . $_FILES['excel_file']['error'];
            exit;
        }

        // Check if the uploaded file is indeed a CSV
        $file_type = mime_content_type($_FILES['excel_file']['tmp_name']);
        $file_ext = pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION);

        if ($file_type !== 'text/plain' && $file_type !== 'text/csv' && $file_ext !== 'csv') {
            echo "Please upload a valid CSV file.";
            exit;
        }

        // Proceed with processing the CSV file
        $file = $_FILES['excel_file']['tmp_name'];

        // Debugging: Check if file is readable
        if (!is_readable($file)) {
            echo "File is not readable.";
            exit;
        }

        // Open the CSV file
        if (($handle = fopen($file, 'r')) !== FALSE) {
            echo "CSV file opened successfully.<br>"; // Debugging

            // Skip the header row
            fgetcsv($handle);

            // Prepare the insertion for users table
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Assuming the first column is for role, second for first name, etc.
                $role = strtolower(trim($row[0]));
                $firstname = trim($row[1]);
                $lastname = trim($row[2]);
                $email = strtolower(trim($row[3]));
                $password = password_hash(trim($row[4]), PASSWORD_DEFAULT);

                // Check if email already exists
                $emailCheckStmt = $con->prepare("SELECT * FROM users WHERE email = ?");
                $emailCheckStmt->bind_param("s", $email);
                $emailCheckStmt->execute();
                $emailResult = $emailCheckStmt->get_result();

                if ($emailResult->num_rows > 0) {
                    echo "Email {$email} already exists. Skipping this entry.<br>";
                    $emailCheckStmt->close();
                    continue; // Skip this row if the email already exists
                }
                $emailCheckStmt->close(); // Close the statement

                // Insert into users table
                $stmt = $con->prepare("INSERT INTO users (firstname, lastname, email, password, role) VALUES (?, ?, ?, ?, ?)");
                if (!$stmt) {
                    echo "Error preparing statement for users: " . $con->error;
                    continue;
                }

                $stmt->bind_param("sssss", $firstname, $lastname, $email, $password, $role);
                if ($stmt->execute()) {
                    $user_id = $stmt->insert_id;
                    $stmt->close(); // Close the statement for users

                    if ($role === 'student') {
                        // Insert into student table
                        $enrollment_id = trim($row[5]);
                        $phone = trim($row[6]);
                        $class = trim($row[7]);
                        $semester = trim($row[8]);
                        $batch = trim($row[9]);

                        $stmt = $con->prepare("INSERT INTO student (user_id, enrollment_id, phone, class, semester, batch) VALUES (?, ?, ?, ?, ?, ?)");
                        if (!$stmt) {
                            echo "Error preparing statement for student: " . $con->error;
                            continue;
                        }

                        $stmt->bind_param("isssis", $user_id, $enrollment_id, $phone, $class, $semester, $batch);
                        if (!$stmt->execute()) {
                            echo "Error executing statement for student: " . $stmt->error;
                        }
                        $stmt->close(); // Close the student statement

                    } elseif ($role === 'faculty') {
                        // Insert into faculty table
                        $post = trim($row[5]); 

                        $stmt = $con->prepare("INSERT INTO faculty (user_id, post) VALUES (?, ?)");
                        if (!$stmt) {
                            echo "Error preparing statement for faculty: " . $con->error;
                            continue;
                        }

                        $stmt->bind_param("is", $user_id, $post);
                        if (!$stmt->execute()) {
                            echo "Error executing statement for faculty: " . $stmt->error;
                        }
                        $stmt->close(); // Close the faculty statement
                    }
                } else {
                    echo "Error executing statement for users: " . $stmt->error;
                }
            }

            fclose($handle);
            echo "Users imported successfully.";
        } else {
            echo "Unable to open CSV file.";
        }
    } else {
        echo "Please upload a valid CSV file.";
    }
}
?>
