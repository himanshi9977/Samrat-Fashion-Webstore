<?php
session_start();
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "samrat_fashion");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Please login first"]);
    exit;
}

$uid = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

$pid = $data['pid'] ?? '';
$size = $data['size'] ?? '';
$qty = $data['quantity'] ?? 1;
if (!$pid || !$size) {
    echo json_encode(["success" => false, "message" => "Invalid product or size"]);
    exit;
}

$sql = "INSERT INTO cart (uid, pid, size, quantity) VALUES (?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issi", $uid, $pid, $size, $qty);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Database error"]);
}
?>