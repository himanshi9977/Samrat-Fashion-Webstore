<?php
session_start();
$conn = new mysqli("localhost", "root", "", "samrat_fashion");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$uid = $_SESSION['user_id'];

$sql = "SELECT DISTINCT o.oid, o.order_date, o.payment_id, p.pname, p.pimg, p.price 
        FROM orders o 
        LEFT JOIN product p ON o.pid = p.pid 
        WHERE o.uid = ? 
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

header('Content-Type: application/json');
echo json_encode($orders);
?>