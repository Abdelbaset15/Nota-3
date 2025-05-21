<?php
session_start();
include 'db.php';

// تفعيل تصحيح الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', 1);

// تحديد نوع المحتوى كـ JSON
header('Content-Type: application/json');

// تحقق من وجود user_id في الجلسة
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID not found in session']);
    exit;
}

// تحقق من طريقة الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// معالجة البيانات
try {
    $product_id = (int)$_POST['product_id'];
    $comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
    $user_id = (int)$_SESSION['user_id'];

    // إدراج التعليق
    $stmt = $conn->prepare("INSERT INTO product_reviews (product_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $product_id, $user_id, $comment);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Comment saved']);
    } else {
        throw new Exception('Failed to execute query: ' . $stmt->error);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>