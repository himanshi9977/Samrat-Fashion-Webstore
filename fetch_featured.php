<?php
$conn = new mysqli("localhost", "root", "", "samrat_fashion");

// Query to get products with an average rating of 4 or more
$sql = "SELECT p.*, AVG(r.rating) as avg_rating 
        FROM product p 
        LEFT JOIN reviews r ON p.pid = r.pid 
        GROUP BY p.pid 
        HAVING avg_rating >= 4 OR avg_rating IS NULL 
        LIMIT 4";

$result = $conn->query($sql);
$featured = [];
while($row = $result->fetch_assoc()) {
    $featured[] = $row;
}
header('Content-Type: application/json');
echo json_encode($featured);
?>