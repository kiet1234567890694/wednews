<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config.php';

// Kiểm tra nếu người dùng đã đăng nhập và có quyền quản trị
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Kiểm tra nếu ID bài viết tồn tại trong URL
if (isset($_GET['id'])) {
    $article_id = $_GET['id'];

    // Chuẩn bị câu lệnh DELETE để xóa bài viết
    $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->bind_param("i", $article_id);

    // Thực hiện câu lệnh
    if ($stmt->execute()) {
        // Xóa thành công, chuyển hướng về trang bảng điều khiển
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Lỗi khi xóa bài viết.";
    }

    // Đóng statement và kết nối
    $stmt->close();
    $conn->close();
} else {
    // Nếu không có ID trong URL, chuyển hướng về bảng điều khiển
    header("Location: dashboard.php");
    exit();
}
?>
