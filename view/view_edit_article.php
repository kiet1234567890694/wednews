<?php
session_start();
include '../config.php';
include '../admin/edit_article.php';
include '../admin/dashboard.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Bạn không có quyền truy cập vào trang này.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh Sửa Bài Viết</title>
    <link rel="stylesheet" href="../css/user.css">
</head>
<body>
    <h2>Chỉnh Sửa Bài Viết</h2>
    <form method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Tiêu đề" value="<?php echo isset($article['title']) ? htmlspecialchars($article['title']) : ''; ?>" required>
<textarea name="content" placeholder="Nội dung" required><?php echo isset($article['content']) ? htmlspecialchars($article['content']) : ''; ?></textarea>


        <!-- Thêm ảnh đại diện (thumbnail) -->
        <label for="thumbnail">Chọn ảnh đại diện:</label>
        <input type="file" name="thumbnail">

        <!-- Phần này để thêm nhiều ảnh, chú thích và nội dung ảnh -->
        <div id="image-upload-section">
            <?php foreach ($image_rows as $image): ?>
                <div class="image-group">
                    <label for="images[]">Chọn ảnh:</label>
                    <input type="file" name="images[]">
                    <input type="text" name="captions[]" value="<?php echo htmlspecialchars($image['caption']); ?>" placeholder="Chú thích ảnh">
                    <textarea name="image_contents[]" placeholder="Nội dung ảnh"><?php echo htmlspecialchars($image['image_contents']); ?></textarea>
                </div>
            <?php endforeach; ?>
            <div class="image-group">
                <label for="images[]">Chọn ảnh:</label>
                <input type="file" name="images[]">
                <input type="text" name="captions[]" placeholder="Chú thích ảnh">
                <textarea name="image_contents[]" placeholder="Nội dung ảnh"></textarea>
            </div>
        </div>
        <button type="button" onclick="addImageField()">Thêm Ảnh</button>

        <!-- Chọn danh mục -->
        <select name="category_id" required>
            <option value="">Chọn danh mục</option>
            <?php
            // Lấy danh mục từ cơ sở dữ liệu
            $category_result = $conn->query("SELECT * FROM categories");
            while ($category = $category_result->fetch_assoc()) {
                $selected = ($category['id'] == $article['category_id']) ? 'selected' : '';
                echo "<option value='{$category['id']}' $selected>{$category['name']}</option>";
            }
            ?>
        </select>
        <button type="submit">Cập Nhật Bài Viết</button>
    </form>

    <script>
        function addImageField() {
            var imageGroup = document.createElement('div');
            imageGroup.classList.add('image-group');
            imageGroup.innerHTML = `
                <label for="images[]">Chọn ảnh:</label>
                <input type="file" name="images[]">
                <input type="text" name="captions[]" placeholder="Chú thích ảnh">
                <textarea name="image_contents[]" placeholder="Nội dung ảnh"></textarea>
            `;
            document.getElementById('image-upload-section').appendChild(imageGroup);
        }
    </script>
</body>
</html>
