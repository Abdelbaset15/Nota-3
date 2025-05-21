<?php

require_once 'db.php';

// جلب معرف المنتج من الطلب
$product_id = $_GET['product_id'];

// استعلام لجلب تفاصيل المنتج
$sql = "SELECT * FROM products WHERE product_id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    echo json_encode($product);
} else {
    echo json_encode([]);
}

$conn->close();
?>