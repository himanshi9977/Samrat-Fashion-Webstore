<?php
session_start();
$conn = new mysqli("localhost", "root", "", "samrat_fashion");

// 1. Capture all parameters from the URL
$payment_id = $_GET['payment_id'] ?? null;
$pid = $_GET['pid'] ?? null;
$qty = $_GET['qty'] ?? 1; // Capture quantity, default to 1 if missing
$uid = $_SESSION['user_id'] ?? null;
$name = $_GET['name'] ?? null;
$address = $_GET['address'] ?? null;
$city = $_GET['city'] ?? '';      
$pincode = $_GET['pincode'] ?? ''; 

if (!$payment_id || !$uid) {
    die("Error: Missing critical payment information.");
}

// 2. Prevent Duplicate Order Entries
$check_query = $conn->prepare("SELECT oid FROM orders WHERE payment_id = ?");
$check_query->bind_param("s", $payment_id);
$check_query->execute();
$check_result = $check_query->get_result();

if ($check_result->num_rows > 0) {
    header("Location: cart.html?status=already_processed");
    exit();
} else {
    // 3. Insert the new Order (Includes Quantity Column)
    // Note: Ensure your 'orders' table has a 'quantity' INT column
    $stmt = $conn->prepare("INSERT INTO orders (uid, pid, payment_id, customer_name, shipping_address, quantity) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $uid, $pid, $payment_id, $name, $address, $qty);
    
    if ($stmt->execute()) {
        // 4. Clear the Cart for this user
        $clear_cart = $conn->prepare("DELETE FROM cart WHERE uid = ?");
        $clear_cart->bind_param("i", $uid);
        $clear_cart->execute();

        // 5. Update User Profile with default shipping details
        // Changed 'id' to 'uid' to match your schema
        $update_user = $conn->prepare("UPDATE users SET default_name = ?, default_address = ?, default_city = ?, default_pincode = ? WHERE uid = ?");
        $update_user->bind_param("ssssi", $name, $address, $city, $pincode, $uid);
        $update_user->execute();

        header("Location: cart.html?status=success");
        exit();
    } else {
        echo "Order Placement Error: " . $stmt->error;
    }
}
?>