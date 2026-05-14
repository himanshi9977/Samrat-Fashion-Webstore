<?php
$conn = new mysqli("localhost", "root", "", "samrat_fashion");
$pid = $_GET['pid'];

// Get individual reviews
$res = $conn->query("SELECT r.*, u.email as username FROM reviews r JOIN users u ON r.uid = u.uid WHERE pid = '$pid' ORDER BY created_at DESC");
$reviews = $res->fetch_all(MYSQLI_ASSOC);

// Get average
$avgRes = $conn->query("SELECT AVG(rating) as avg, COUNT(*) as count FROM reviews WHERE pid = '$pid'");
$stats = $avgRes->fetch_assoc();

echo json_encode([
    "reviews" => $reviews,
    "avg" => round($stats['avg'], 1),
    "count" => $stats['count']
]);
?>