<?php
session_start();
$conn = new mysqli("localhost", "root", "", "samrat_fashion");

if (isset($_GET['cid']) && isset($_SESSION['user_id'])) {
    $cid = $_GET['cid'];
    $uid = $_SESSION['user_id'];

    // Ensure the user can only delete their own cart items
    $stmt = $conn->prepare("DELETE FROM cart WHERE cid = ? AND uid = ?");
    $stmt->bind_param("ii", $cid, $uid);
    $stmt->execute();
}

header("Location: cart.html");
exit;