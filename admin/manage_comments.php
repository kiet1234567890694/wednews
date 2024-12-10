<?php
session_start();
include '../config.php';

// Kiểm tra người dùng đã đăng nhập và có quyền quản trị chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ./view/view_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Kiểm tra quyền admin
if ($role !== 'admin') {
    die("Bạn không có quyền truy cập vào trang này.");
}

// Lấy tất cả bình luận có trạng thái 'pending'
$stmt = $conn->prepare("SELECT c.id, c.article_id, c.user_id, c.comment, c.created_at, u.username FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.status = 'pending'");
$stmt->execute();
$result = $stmt->get_result();

// Xử lý xóa bình luận
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt_delete = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $stmt_delete->bind_param("i", $delete_id);
    
    if ($stmt_delete->execute()) {
        echo "<script>alert('Bình luận đã được xóa thành công.');</script>";
    } else {
        echo "Lỗi: " . $stmt_delete->error;
    }
}

// Cập nhật trạng thái bình luận (nếu có)
if (isset($_GET['approve_id'])) {
    $approve_id = $_GET['approve_id'];
    $stmt_approve = $conn->prepare("UPDATE comments SET status = 'approved' WHERE id = ?");
    $stmt_approve->bind_param("i", $approve_id);
    
    if ($stmt_approve->execute()) {
        echo "<script>alert('Bình luận đã được duyệt thành công.');</script>";
    } else {
        echo "Lỗi: " . $stmt_approve->error;
    }
}

// Lấy tất cả bình luận đã được duyệt từ cơ sở dữ liệu
$stmt = $conn->prepare("SELECT c.id, c.article_id, c.user_id, c.comment, c.created_at, u.username FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.status = 'approved'");
$stmt->execute();
$result_approved = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/comments_management.css">
    <title>Quản lý bình luận</title>
</head>
<body>
    <h1>Quản lý bình luận</h1>
    <h2>Bình luận đang chờ duyệt</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Bài viết ID</th>
                <th>Người dùng</th>
                <th>Bình luận</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['article_id']); ?></td>
                <td><?php echo htmlspecialchars($row['username'] ?? 'Khách'); ?></td>
                <td><?php echo htmlspecialchars($row['comment']); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td>
                    <a href="?approve_id=<?php echo $row['id']; ?>">Duyệt</a>
                    <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này không?');">Xóa</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Bình luận đã duyệt</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Bài viết ID</th>
                <th>Người dùng</th>
                <th>Bình luận</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row_approved = $result_approved->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row_approved['id']); ?></td>
                <td><?php echo htmlspecialchars($row_approved['article_id']); ?></td>
                <td><?php echo htmlspecialchars($row_approved['username'] ?? 'Khách'); ?></td>
                <td><?php echo htmlspecialchars($row_approved['comment']); ?></td>
                <td><?php echo htmlspecialchars($row_approved['created_at']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
