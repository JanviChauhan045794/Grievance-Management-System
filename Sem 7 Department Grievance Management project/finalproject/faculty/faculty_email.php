<?php
include '../php/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if (isset($_GET['status'], $_GET['complaint_id'], $_GET['student_id'], $_GET['end_date'], $_GET['comp_id'])) {
    $status = $_GET['status'];
    $complaintId = $_GET['complaint_id'];
    $studentId = $_GET['student_id'];
    $endDate = $_GET['end_date'];
    $compId = $_GET['comp_id'];

    // Update status in database
    $sql = "UPDATE complaints SET status = '$status', End_Date = '$endDate' WHERE complaint_id = $complaintId";
    $con->query($sql);

    // Send email if status is resolved
    if ($status == 'Resolved') {
        $sql = "SELECT u.email FROM users u INNER JOIN student s ON u.user_id = s.user_id WHERE s.student_id = $studentId";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = $row['email'];

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.example.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'janvi.chauhan4599@gmail.com';
                $mail->Password = 'lbjm gjff vyca rwtx';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('janvi.chauhan', 'Complaint System');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Complaint Status Updated';
                $mail->Body = "Dear Student,<br>Your complaint with ID $compId has been resolved. Please check the details.<br>Regards,<br>Complaint System";

                $mail->send();
            } catch (Exception $e) {
                // Handle email error
            }
        }
    }
}
?>
