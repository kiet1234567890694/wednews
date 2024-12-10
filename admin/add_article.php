<?php
session_start();
include '../config.php';

// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Bạn không có quyền truy cập vào trang này.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $title = $_POST['title'];
    $content = isset($_POST['content']) ? $_POST['content'] : ''; // Nội dung không bắt buộc
    $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : null;
    $user_id = $_SESSION['user_id'];

    // Xử lý ảnh đại diện (thumbnail)
    $thumbnail_name = $_FILES['thumbnail']['name'];
    if (!empty($thumbnail_name)) {
        $thumbnail_tmp_name = $_FILES['thumbnail']['tmp_name'];
        $target_dir = "../uploads/";
        $target_thumbnail = $target_dir . basename($thumbnail_name);

        // Di chuyển ảnh đại diện
        if (!move_uploaded_file($thumbnail_tmp_name, $target_thumbnail)) {
            die("Lỗi upload ảnh đại diện.");
        }
    } else {
        $target_thumbnail = null; // Không có ảnh đại diện
    }

    // Thêm bài viết vào bảng articles
    $sql = "INSERT INTO articles (title, content, image, category_id, user_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Lỗi chuẩn bị SQL: " . $conn->error);
    }

    $stmt->bind_param("sssii", $title, $content, $target_thumbnail, $category_id, $user_id);

    if ($stmt->execute()) {
        $article_id = $stmt->insert_id; // Lấy ID bài viết mới thêm vào

        // Xử lý upload ảnh phụ (nếu có)
        if (isset($_FILES['images']['name'])) {
            $image_count = count($_FILES['images']['name']);
            for ($i = 0; $i < $image_count; $i++) {
                $image_tmp_name = $_FILES['images']['tmp_name'][$i];
                $image_name = basename($_FILES['images']['name'][$i]);
                $target_file = $target_dir . $image_name;

                if (move_uploaded_file($image_tmp_name, $target_file)) {
                    $caption = isset($_POST['captions'][$i]) ? $_POST['captions'][$i] : ''; // Chú thích ảnh không bắt buộc
                    $image_content = isset($_POST['image_contents'][$i]) ? $_POST['image_contents'][$i] : ''; // Nội dung ảnh không bắt buộc
                    
                    // Lưu thông tin ảnh vào cơ sở dữ liệu
                    $stmt_image = $conn->prepare("INSERT INTO article_images (article_id, image_path, caption, image_contents) VALUES (?, ?, ?, ?)");
                    $stmt_image->bind_param("isss", $article_id, $target_file, $caption, $image_content);
                    $stmt_image->execute();
                }
            }
        }

        echo "Tin tức đã được thêm thành công!";
        // Sau khi thêm bài viết thành công, bạn có thể reload trang hoặc redirect về trang danh sách bài viết
        echo '<script>setTimeout(function(){ window.location.reload(); }, 2000);</script>';
    } else {
        echo "Lỗi: " . $stmt->error;
    }
}
?>
