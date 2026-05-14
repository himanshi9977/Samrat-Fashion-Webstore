<?php
session_start();
$conn = new mysqli("localhost", "root", "", "samrat_fashion");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Please login to review"]);
    exit;
}

$uid = $_SESSION['user_id'];
$pid = $_POST['pid'];
$rating = $_POST['rating'];
$comment = $_POST['comment'];

$stmt = $conn->prepare("INSERT INTO reviews (pid, uid, rating, comment) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siis", $pid, $uid, $rating, $comment);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "You already reviewed this item"]);
}
?>