<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Bạn không có quyền truy cập vào trang này.");
}

// Kiểm tra nếu ID bài viết tồn tại trong URL
if (!isset($_GET['id'])) {
    die("Không có ID bài viết được cung cấp.");
}

$article_id = $_GET['id'];

// Kiểm tra xem bài viết có tồn tại không
$sql = "SELECT * FROM articles WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Lỗi chuẩn bị SQL: " . $conn->error);
}
$stmt->bind_param("i", $article_id); // Gán tham số $article_id vào câu lệnh SQL
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra kết quả truy vấn
if (!$result || $result->num_rows === 0) {
    die("Bài viết không tồn tại.");
}

$article = $result->fetch_assoc();

// Lấy danh sách ảnh đã có trong bài viết
$image_result = $conn->query("SELECT * FROM article_images WHERE article_id = $article_id");
if (!$image_result) {
    die("Lỗi khi lấy danh sách ảnh: " . $conn->error);
}
$image_rows = $image_result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];

    // Cập nhật bài viết
    $sql = "UPDATE articles SET title = ?, content = ?, category_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Lỗi chuẩn bị SQL: " . $conn->error);
    }
    $stmt->bind_param("ssii", $title, $content, $category_id, $article_id);
    $stmt->execute();

    // Cập nhật ảnh đại diện (thumbnail) nếu có
    if ($_FILES['thumbnail']['name']) {
        // Di chuyển ảnh đại diện tới thư mục uploads
        $thumbnail_tmp_name = $_FILES['thumbnail']['tmp_name'];
        $thumbnail_name = basename($_FILES['thumbnail']['name']);
        $target_dir = "../uploads/";
        $target_thumbnail = $target_dir . $thumbnail_name;

        if (move_uploaded_file($thumbnail_tmp_name, $target_thumbnail)) {
            $sql_thumbnail = "UPDATE articles SET image = ? WHERE id = ?";
            $stmt_thumbnail = $conn->prepare($sql_thumbnail);
            $stmt_thumbnail->bind_param("si", $target_thumbnail, $article_id);
            $stmt_thumbnail->execute();
        }
    }

    // Cập nhật ảnh và chú thích
    $image_count = count($_FILES['images']['name']);
    for ($i = 0; $i < $image_count; $i++) {
        if ($_FILES['images']['name'][$i]) {
            $image_tmp_name = $_FILES['images']['tmp_name'][$i];
            $image_name = basename($_FILES['images']['name'][$i]);
            $target_file = $target_dir . $image_name;

            // Di chuyển ảnh tới thư mục uploads
            if (move_uploaded_file($image_tmp_name, $target_file)) {
                $caption = $_POST['captions'][$i];
                $image_content = $_POST['image_contents'][$i];
                $stmt_image = $conn->prepare("INSERT INTO article_images (article_id, image_path, caption, image_contents) VALUES (?, ?, ?, ?)");
                
                if (!$stmt_image) {
                    die("Lỗi chuẩn bị SQL cho ảnh: " . $conn->error);
                }
                
                $stmt_image->bind_param("isss", $article_id, $target_file, $caption, $image_content);
                $stmt_image->execute();
            }
        }
    }

    echo "Bài viết đã được cập nhật thành công!";
}
?>
