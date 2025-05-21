<?php
include 'db.php';

if (!isset($_GET['product_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Product ID not provided']);
    exit;
}

$product_id = $_GET['product_id'];

$stmt = $conn->prepare("
    SELECT pr.comment, pr.created_at, u.username, u.user_picture 
    FROM product_reviews pr 
    JOIN users u ON pr.user_id = u.user_id 
    WHERE pr.product_id = ? 
    ORDER BY pr.created_at DESC
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

header('Content-Type: application/json');

echo json_encode(['status' => 'success', 'comments' => $comments]);

$stmt->close();
$conn->close();
?>