<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Web Tin Tức</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="./css/header.css">
</head>
<body>
    <header class="header">
        <div class="logo">
            <span>VNEXPRESS</span>
        </div>
        <div class="date"><?php echo date("l, d/m/Y"); ?></div>
        <nav class="menu">
        <i class="fas fa-bell icon"></i>
            <a href="../index.php">Mới nhất</a>
            <a href="#">Tin theo khu vực</a>
            <a href="#">International</a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="../view/view_login.php">Đăng nhập</a>
                <a href="../view/view_register.php">Đăng ký</a>
            <?php else: ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="../admin/dashboard.php">Quản trị</a>
                <?php endif; ?>
                <a href="../logout.php">Đăng xuất</a>
            <?php endif; ?>          
        </nav>
    </header>
    <nav class="nav">
    <a href="../index.php">Trang chủ</a>
    <a href="thoisu.php">Thời sự</a>
    <a href="gocnhin.php">Góc nhìn</a>
    <a href="thegioi.php">Thế giới</a>
    <a href="kinhdoanh.php">Kinh doanh</a>
    <a href="batdongsan.php">Bất động sản</a>
    <a href="khoahoc.php">Khoa học</a>
    <a href="giaitri.php">Giải trí</a>
    <a href="thethao.php">Thể thao</a>
    <a href="phapluat.php">Pháp luật</a>
    <a href="giaoduc.php">Giáo dục</a>
    <a href="suckhoe.php">Sức khỏe</a>
    <a href="doisong.php">Đời sống</a>
</nav>

</body>
</html>
