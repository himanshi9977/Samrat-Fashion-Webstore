<?php
session_start();
$conn = new mysqli("localhost", "root", "", "samrat_fashion");

if (!isset($_SESSION['user_id'])) {
    die("Please login to checkout.");
}

$uid = $_SESSION['user_id'];

// 1. Calculate Total Amount from Cart
$sql = "SELECT SUM(p.price * c.quantity) as total FROM cart c 
        JOIN product p ON c.pid = p.pid WHERE c.uid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

$amount_in_rupees = $result['total'];
$amount_in_paise = $amount_in_rupees * 100; 
?>

<script>
var options = {
    "key": "rzp_test_SZkIW2FlLwx11E"
    "amount": "<?php echo $amount_in_paise; ?>", 
    "currency": "INR",
    "name": "Samrat Fashions",
    "description": "Payment for your order",
    "image": "./sam fash (1).png",
    "handler": function (response){
        // This runs after a successful payment
        window.location.href = "verify_payment.php?payment_id=" + response.razorpay_payment_id;
    },
    "prefill": {
        "email": "<?php echo $_SESSION['user_email']; ?>"
    },
    "theme": {
        "color": "#000000" // Matches your black/white theme
    }
};
var rzp1 = new Razorpay(options);

function startPayment() {
    rzp1.open();
}
</script>