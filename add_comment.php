<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $article_id = $_POST['article_id'];
    $user_id = $_SESSION['id'] ?? null; // Lấy ID người dùng nếu đăng nhập
    $comment = $_POST['comment'];

    // Chuẩn bị câu lệnh SQL để thêm bình luận
    $stmt = $conn->prepare("INSERT INTO comments (article_id, user_id, comment, status) VALUES (?, ?, ?, 'pending')");
    
    // Kiểm tra nếu câu lệnh chuẩn bị không thành công
    if (!$stmt) {
        echo "Lỗi câu lệnh: " . $conn->error;
        exit();
    }
    
    $stmt->bind_param("iis", $article_id, $user_id, $comment);
    
    if ($stmt->execute()) {
        header("Location: ./view/view_articles.php?id=" . $article_id);
    } else {
        echo "Lỗi: " . $stmt->error;
    }
}
?>
