<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'غير مصرح']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'بيانات ناقصة']);
    exit();
}

$itemId = $_GET['id'];
$user_id = $_SESSION['user_id'];

$sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $itemId, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'فشل الحذف']);
}

$stmt->close();
$conn->close();
?>