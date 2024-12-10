<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); // Dừng thực thi mã sau khi chuyển hướng
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Kiểm tra quyền admin
if ($role !== 'admin') {
    die("Bạn không có quyền truy cập vào trang này.");
}

// Hiển thị bài viết
$stmt = $conn->prepare("SELECT * FROM articles");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bảng Điều Khiển</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <h2>Bảng Điều Khiển</h2>
    <a href="../view/view_add_article.php">Thêm Bài Viết</a>
    <a href="/admin/manage_comments.php">Quản Lý Bình Luận</a> <!-- Thêm nút để quản lý bình luận -->
    <h3>Các Bài Viết</h3>
    <table>
        <tr>
            <th>Tiêu đề</th>
            <th>Nội dung</th>
            <th>Thao tác</th>
        </tr>
        <?php while ($article = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($article['title']); ?></td>
            <td><?php echo htmlspecialchars($article['content']); ?></td>
            <td>
                <a href="../view/view_edit_article.php?id=<?php echo $article['id']; ?>">Sửa</a>
                <a href="delete_article.php?id=<?php echo $article['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">Xóa</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
