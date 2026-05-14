<?php
session_start();
include 'mail_helper.php'; // Using the PHPMailer helper we discussed
$conn = new mysqli("localhost", "root", "", "samrat_fashion");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // 1. Check if user exists, if not, create them (Auto-Registration)
    $checkUser = $conn->prepare("SELECT uid FROM users WHERE email = ?");
    $checkUser->bind_param("s", $email);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO users (email) VALUES (?)");
        $stmt->bind_param("s", $email);
        $stmt->execute();
    }

    // 2. Generate and Save OTP
    $otp = rand(100000, 999999);
    $update = $conn->prepare("UPDATE users SET otp_code = ? WHERE email = ?");
    $update->bind_param("is", $otp, $email);
    $update->execute();

    // 3. Send the Email
    if (sendOTP($email, $otp)) {
        $_SESSION['temp_email'] = $email;
        echo "otp_sent";
    } else {
        echo "error_sending_email";
    }
}
