<?php
session_start();
session_destroy(); // Hủy tất cả session hiện tại
header("Location: ../index.php"); // Chuyển hướng về trang chủ
exit(); // Thoát ngay sau khi chuyển hướng
?>
