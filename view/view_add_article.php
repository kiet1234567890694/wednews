<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Bạn không có quyền truy cập vào trang này.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thêm Bài Viết</title>
    <link rel="stylesheet" href="../css/add.css">
</head>
<body>
    <h2>Thêm Bài Viết</h2>
    <form method="post" enctype="multipart/form-data" action="../admin/add_article.php">
        <!-- Tiêu đề là bắt buộc -->
        <input type="text" name="title" placeholder="Tiêu đề" required>

        <!-- Nội dung không bắt buộc -->
        <textarea name="content" placeholder="Nội dung (không bắt buộc)"></textarea>

        <!-- Ảnh đại diện là bắt buộc -->
        <label for="thumbnail">Chọn ảnh đại diện:</label>
        <input type="file" name="thumbnail" required>

        <!-- Phần này để thêm nhiều ảnh, chú thích và nội dung ảnh, không bắt buộc -->
        <div id="image-upload-section">
            <div class="image-group">
                <label for="images[]">Chọn ảnh (không bắt buộc):</label>
                <input type="file" name="images[]">
                <input type="text" name="captions[]" placeholder="Chú thích ảnh (không bắt buộc)">
                <textarea name="image_contents[]" placeholder="Nội dung ảnh (không bắt buộc)"></textarea>
            </div>
        </div>
        <button type="button" onclick="addImageField()">Thêm Ảnh</button>

        <!-- Chọn danh mục không bắt buộc -->
        <select name="category_id">
            <option value="">Chọn danh mục (không bắt buộc)</option>
            <?php
            // Lấy danh mục từ cơ sở dữ liệu
            $category_result = $conn->query("SELECT * FROM categories");
            while ($category = $category_result->fetch_assoc()) {
                echo "<option value='{$category['id']}'>{$category['name']}</option>";
            }
            ?>
        </select>

        <button type="submit">Thêm Bài Viết</button>
    </form>

    <script>
        // Hàm thêm các trường ảnh mới
        function addImageField() {
            var imageGroup = document.createElement('div');
            imageGroup.classList.add('image-group');
            imageGroup.innerHTML = `
                <label for="images[]">Chọn ảnh (không bắt buộc):</label>
                <input type="file" name="images[]">
                <input type="text" name="captions[]" placeholder="Chú thích ảnh (không bắt buộc)">
                <textarea name="image_contents[]" placeholder="Nội dung ảnh (không bắt buộc)"></textarea>
            `;
            document.getElementById('image-upload-section').appendChild(imageGroup);
        }
    </script>
</body>
</html>
