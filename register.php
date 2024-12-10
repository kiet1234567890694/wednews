<?php
session_start();
include './config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Kiểm tra xem tên người dùng hoặc email đã tồn tại chưa
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "Tên người dùng hoặc email đã tồn tại!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $username, $password, $email);
        
        if ($stmt->execute()) {
            echo "Đăng ký thành công!";
        } else {
            echo "Lỗi: " . $stmt->error;
        }
    }
    $check_stmt->close();
    $stmt->close();
    $conn->close();
}
?>
