<?php
session_start();
$conn = new mysqli("localhost", "root", "", "samrat_fashion");

$uid = $_SESSION['user_id'];
$res = $conn->query("SELECT default_name, default_address, default_city, default_pincode FROM users WHERE id = $uid");
$user = $res->fetch_assoc();

echo json_encode([
    'success' => true,
    'name' => $user['default_name'],
    'address' => $user['default_address'],
    'city' => $user['default_city'],
    'pincode' => $user['default_pincode']
]);
?>