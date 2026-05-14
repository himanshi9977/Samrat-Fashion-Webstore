<?php

$conn = new mysqli("localhost", "root", "", "samrat_fashion");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed"]));
}

$category = isset($_GET['type']) ? $_GET['type'] : '';

$stmt = $conn->prepare("SELECT pid as id, pname as name, price, pimg as image, description, category FROM product WHERE category = ?");
$stmt->bind_param("s", $category);
$stmt->execute();

$result = $stmt->get_result();
$products = [];

while($row = $result->fetch_assoc()){
    $products[] = $row;
}

header('Content-Type: application/json');
echo json_encode($products);
?>