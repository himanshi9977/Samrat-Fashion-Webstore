<?php
session_start();
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "samrat_fashion");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$uid = $_SESSION['user_id'];

// We join 'cart' with 'product' to get images and prices
// Inside fetch_cart.php, update your SQL:
$sql = "SELECT c.cid, c.pid, p.pname, p.price, p.pimg, c.quantity, c.size 
        FROM cart c 
        JOIN product p ON c.pid = p.pid 
        WHERE c.uid = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}

echo json_encode($cartItems);
?>