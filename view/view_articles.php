<?php
session_start();
include '../config.php';

// Lấy thông tin bài viết hiện tại
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Bài viết không tồn tại.");
}

// Lấy thông tin bài viết từ cơ sở dữ liệu
$stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();

if (!$article) {
    die("Bài viết không tồn tại.");
}

// Tăng lượt xem cho bài viết
$update_views_stmt = $conn->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
$update_views_stmt->bind_param("i", $id);
$update_views_stmt->execute();

// Lấy tất cả ảnh liên quan đến bài viết
$image_stmt = $conn->prepare("SELECT * FROM article_images WHERE article_id = ?");
$image_stmt->bind_param("i", $id);
$image_stmt->execute();
$image_result = $image_stmt->get_result();
?>

<?php include '../header.php'; ?>
<link rel="stylesheet" href="../css/header.css">
<link rel="stylesheet" href="../css/view_articles.css">
<main>
    <h2><?php echo htmlspecialchars($article['title']); ?></h2>
    
    <!-- Hiển thị ảnh đại diện (thumbnail) -->
    <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" style="width:100%; max-height:400px;">
    
    <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>

    <!-- Hiển thị tất cả các ảnh liên quan -->
    <h3>Ảnh Liên Quan</h3>
    <div class="related-images">
        <?php while ($image = $image_result->fetch_assoc()): ?>
            <div class="image-container">
                <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['caption']); ?>" style="width:100%; max-height:300px;">
                <p><?php echo htmlspecialchars($image['caption']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($image['image_contents'])); ?></p>
            </div>
        <?php endwhile; ?>
    </div>

    <h3>Bình luận</h3>
    <form method="post" action="../add_comment.php">
        <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
        <textarea name="comment" required></textarea>
        <button type="submit">Gửi bình luận</button>
    </form>

    <h4>Các Bình Luận</h4>
    <?php
    // Lấy các bình luận đã được duyệt
    $comment_stmt = $conn->prepare("SELECT c.comment, c.created_at, u.username FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.article_id = ? AND c.status = 'approved'");
    $comment_stmt->bind_param("i", $article['id']);
    $comment_stmt->execute();
    $comment_result = $comment_stmt->get_result();

    if ($comment_result->num_rows > 0) {
        while ($comment = $comment_result->fetch_assoc()): ?>
            <p><strong><?php echo htmlspecialchars($comment['username'] ?? 'Khách'); ?>:</strong> <?php echo htmlspecialchars($comment['comment']); ?><br><em><?php echo htmlspecialchars($comment['created_at']); ?></em></p>
        <?php endwhile;
    } else {
        echo "<p>Chưa có bình luận nào.</p>";
    }
    ?>

    <!-- Phần Bài Viết Liên Quan -->
    <h3>Bài Viết Liên Quan</h3>
    <ul>
        <?php
        // Câu truy vấn lấy các bài viết liên quan (cùng thể loại hoặc tiêu chí khác)
        $category_id = $article['category_id']; // Giả sử có cột 'category_id'
        $related_stmt = $conn->prepare("SELECT * FROM articles WHERE category_id = ? AND id != ? LIMIT 4");
        $related_stmt->bind_param("ii", $category_id, $id);
        $related_stmt->execute();
        $related_result = $related_stmt->get_result();

        while ($related_article = $related_result->fetch_assoc()): ?>
            <li>
                <a href="view_articles.php?id=<?php echo $related_article['id']; ?>">
                    <?php echo htmlspecialchars($related_article['title']); ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
</main>
<?php include '../footer.php'; ?>
