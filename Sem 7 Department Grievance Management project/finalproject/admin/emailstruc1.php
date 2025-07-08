<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the PHPMailer library
require '../vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();                                      // Use SMTP
    $mail->Host = 'smtp.gmail.com';                       // Set your SMTP server (Gmail in this case)
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'janvi.chauhan4599@gmail.com';           // Your Gmail address
    $mail->Password = 'lbjm gjff vyca rwtx';           // Your Gmail App Password (NOT your regular password)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;      // Enable SSL encryption
    $mail->Port = 465;                                    // SMTP port

    // Email Configuration
    $mail->setFrom('janvi.chauhan4599@gmail.com', 'Janvi');  // Sender's email and name
    $mail->addAddress('recipient_email@example.com', 'Recipient Name'); // Recipient's email and name
    $mail->Subject = 'Test Email Subject';               // Email subject
    $mail->Body = 'This is a test email sent from PHPMailer.'; // Email body

    // Send the email
    $mail->send();
    echo 'Email has been sent successfully.';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}