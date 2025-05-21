<?php
session_start();
require_once 'db.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'غير مصرح']);
    exit();
}

$user_id = $_SESSION['user_id'];

// استخدم JOIN لجلب بيانات المنتج مع السلة
$sql = "SELECT 
    cart.id,
    cart.quantity,
    products.name,
    products.price,
    products.product_id
FROM cart
INNER JOIN products ON cart.product_id = products.product_id
WHERE cart.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'price' => $row['price'],
        'quantity' => $row['quantity'],
        'product_id' => $row['product_id']
    ];
}

echo json_encode($cartItems);

$stmt->close();
$conn->close();
?>