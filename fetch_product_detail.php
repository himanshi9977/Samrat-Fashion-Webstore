<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "samrat_fashion");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed"]));
}

$pid = isset($_GET['id']) ? $_GET['id'] : '';

// Query to get the specific product by its ID
$stmt = $conn->prepare("SELECT pid as id, pname as name, price, pimg as image, description, category FROM product WHERE pid = ?");
$stmt->bind_param("s", $pid);
$stmt->execute();

$result = $stmt->get_result();
$product = $result->fetch_assoc();

// If product found, return it as JSON, else return error
echo json_encode($product ? $product : ["error" => "Product not found"]);
?>