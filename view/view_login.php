
<link rel="stylesheet" href="../css/user.css">

<main>
    <h2>Đăng Nhập</h2>
    
    <?php
    // Kiểm tra xem có thông báo lỗi không
    if (isset($_GET['error'])) {
        echo "<p style='color: red;'>Tên đăng nhập hoặc mật khẩu không đúng!</p>";
    }
    ?>
    
    <form method="post" action="../login.php">
        <input type="text" name="username" required placeholder="Tên đăng nhập">
        <input type="password" name="password" required placeholder="Mật khẩu">
        <button type="submit">Đăng Nhập</button>
    </form>
</main>

<?php include '../footer.php'; ?>
