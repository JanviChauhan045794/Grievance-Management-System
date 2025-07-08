<?php
// Include necessary files
include '../php/config.php';
require '../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = array('success' => false, 'message' => '');

// Check for database connection
if ($con) {
    // Get complaint_id and new status from the GET request
    if (isset($_GET['status'], $_GET['complaint_id'])) {
        $newStatus = $_GET['status'];
        $complaintId = $_GET['complaint_id'];

        // Fetch student email based on the complaint_id
        $studentEmailSql = "SELECT u.email FROM complaints AS c
                            INNER JOIN student AS s ON c.Student_ID = s.student_id
                            INNER JOIN users AS u ON s.user_id = u.user_id
                            WHERE c.complaint_id = $complaintId";

        $result = $con->query($studentEmailSql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $studentEmail = $row['email'];

            // Prepare PHPMailer instance
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.yourmailserver.com'; // Set your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'janvi.chauhan4599@gmail.com'; // SMTP username
                $mail->Password = 'lbjm gjff vyca rwtx'; // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587; // SMTP port

                // Recipients
                $mail->setFrom('janvi.chauhan4599@gmail.com', 'Grievance Management');
                $mail->addAddress($studentEmail); // Add recipient

                // Email content
                $mail->isHTML(true);
                $mail->Subject = 'Complaint Status Update';
                $mail->Body    = "Dear Student,<br><br>Your complaint with ID #$complaintId has been updated to: <strong>$newStatus</strong>.<br><br>Thank you for your patience.<br><br>Best regards,<br>Grievance Management Team";

                // Send email
                if ($mail->send()) {
                    // Email sent successfully, now update the complaint status
                    $updateSql = "UPDATE complaints SET Status = '$newStatus' WHERE complaint_id = $complaintId";
                    $updateResult = $con->query($updateSql);

                    if ($updateResult) {
                        $response['success'] = true;
                        $response['message'] = "Status updated successfully in the database and email sent.";
                    } else {
                        $response['success'] = false;
                        $response['message'] = "Failed to update status in the database.";
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = "Status updated, but failed to send email.";
                }

            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

        } else {
            $response['success'] = false;
            $response['message'] = "Failed to fetch student details or complaint not found.";
        }

    } else {
        $response['success'] = false;
        $response['message'] = "Missing required parameters.";
    }

} else {
    $response['success'] = false;
    $response['message'] = "Failed to connect to the database.";
}

$con->close();

// Return the response as JSON
echo json_encode($response);
?>
