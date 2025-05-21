<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    // التأكد من أن الملف تم تحميله بنجاح
    if ($_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];

        // تحديد المجلد الذي سيتم حفظ الصورة فيه
        $uploadDirectory = "uploads/";
        $uploadFilePath = $uploadDirectory . basename($fileName);

        // التحقق من أن الصورة هي صورة فعلية
        if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
            $username = $_SESSION['user'];
            $sql = "UPDATE users SET picture = ? WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $uploadFilePath, $username);
            
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "pictureUrl" => $uploadFilePath]);
            } else {
                echo json_encode(["success" => false, "message" => "Database update failed"]);
            }
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Error uploading file"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No file uploaded"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
