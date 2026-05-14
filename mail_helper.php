<?php
// mail_helper.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Update these paths to match your actual folder structure
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'himanshisavla07@gmail.com'; 
        $mail->Password   = 'cfjqylnntjilmpti';   // 16-character Google App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('your_email@gmail.com', 'Samrat Fashions');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your Login OTP';
        $mail->Body    = "Your OTP code is: <b>$otp</b>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error if needed: error_log($mail->ErrorInfo);
        return false;
    }
}