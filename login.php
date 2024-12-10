<?php
session_start();
include './config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Lấy thông tin người dùng từ cơ sở dữ liệu
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // So sánh mật khẩu đã nhập với mật khẩu đã mã hóa
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role']; // Lưu vai trò người dùng
            header("Location: index.php");
            exit(); // Thoát sau khi chuyển hướng
        } else {
            echo "Tên đăng nhập hoặc mật khẩu không đúng!";
        }
    } else {
        echo "Tên đăng nhập hoặc mật khẩu không đúng!";
    }
}
?>