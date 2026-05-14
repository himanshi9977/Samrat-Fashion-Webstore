<?php
session_start();
$conn = new mysqli("localhost", "root", "", "samrat_fashion");

$user_otp = $_POST['otp'];
$email = $_SESSION['temp_email'] ?? '';

if (!$email) die("session_expired");

$stmt = $conn->prepare("SELECT uid FROM users WHERE email = ? AND otp_code = ?");
$stmt->bind_param("si", $email, $user_otp);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $user = $res->fetch_assoc();
    
    // Set the permanent Login Session
    $_SESSION['user_id'] = $user['uid'];
    $_SESSION['user_email'] = $email;
    
    // Clear OTP for security
    $conn->query("UPDATE users SET otp_code = NULL WHERE email = '$email'");
    unset($_SESSION['temp_email']);
    
    echo "success";
} else {
    echo "invalid_otp";
}
?>