<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="../css/user.css">
</head>
<body>
<main>
    <h2>Đăng ký</h2>
    
    <form method="post" action="../register.php">
        <input type="text" name="username" placeholder="Tên người dùng" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Đăng ký</button>
    </form>
    <a href="../view/view_login.php">Đăng nhập</a>
</main>
</body>
</html>
