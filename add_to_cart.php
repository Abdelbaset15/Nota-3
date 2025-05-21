<?php
session_start();
require_once 'db.php';

// تفعيل عرض الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', 1);

// التحقق من الجلسة
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'يجب تسجيل الدخول أولاً']);
    exit();
}

// قراءة البيانات المرسلة
$data = json_decode(file_get_contents('php://input'), true);

// التحقق من وجود product_id
if (!isset($data['product_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'بيانات غير صحيحة']);
    exit();
}

$product_id = $data['product_id'];
$user_id = $_SESSION['user_id'];

try {
    // التحقق من وجود المنتج في السلة
    $check_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // تحديث الكمية
        $update_sql = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ii", $user_id, $product_id);
    } else {
        // إضافة جديدة
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ii", $user_id, $product_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>