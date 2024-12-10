<?php
session_start();
include './config.php';  // Kết nối đến database
// Số bài viết mỗi trang
$limit = 5;

// Lấy số trang hiện tại từ URL, mặc định là trang 1 nếu không có
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Đảm bảo page là ít nhất 1

// Tính toán offset cho câu lệnh SQL
$offset = ($page - 1) * $limit;

// Truy vấn để đếm tổng số bài viết trong danh mục "Thời sự"
$countQuery = $conn->query("
    SELECT COUNT(*) AS total 
    FROM articles a
    INNER JOIN categories c ON a.category_id = c.id
    WHERE c.name = 'Thời sự' AND a.status = 'on'
");

$totalArticles = $countQuery->fetch_assoc()['total'];  // Lấy tổng số bài viết
$totalPages = ceil($totalArticles / $limit);  // Tính số trang

// Truy vấn lấy bài viết trong danh mục "Thời sự"
$stmt = $conn->prepare("
    SELECT a.id, a.title, a.content, a.image, a.created_at 
    FROM articles a
    INNER JOIN categories c ON a.category_id = c.id
    WHERE c.name = 'Thời sự' AND a.status = 'on'
    ORDER BY a.created_at DESC 
    LIMIT ? OFFSET ?
");

$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Hiển thị bài viết
?>

<?php include 'header.php'; ?>
<link rel="stylesheet" href="./css/index.css">

<main>
    <div class="container">
        <div class="row mb">
            <!-- Phần bài viết chính -->
            <div class="boxtrai">
                <h2>Bài Viết Thời Sự</h2>
                <?php
                while ($article = $result->fetch_assoc()):
                ?>
                    <div class="article">
                        <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                        <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                        <p><?php echo substr($article['content'], 0, 100) . '...'; ?></p>
                        <a href="../view/view_articles.php?id=<?php echo $article['id']; ?>">Đọc thêm</a>
                    </div>
                <?php endwhile; ?>

                <!-- Liên kết phân trang -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>">Trang trước</a>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>">Trang sau</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tin nổi bật với hình ảnh -->
            <div class="boxphai">
                <div class="boxtile">Tin Nổi Bật</div>
                <div class="boxcontent top10">
                    <?php
                    // Truy vấn lấy các bài viết nổi bật (theo lượt xem)
                    $stmt_popular = $conn->prepare("SELECT * FROM articles ORDER BY views DESC LIMIT 4");
                    if (!$stmt_popular) {
                        die("Lỗi câu truy vấn: " . $conn->error);  // Hiển thị lỗi nếu có
                    }
                    $stmt_popular->execute();
                    $result_popular = $stmt_popular->get_result();

                    // Hiển thị bài viết nổi bật
                    while ($popular_article = $result_popular->fetch_assoc()): ?>
                        <div class="row mb10 news-item">
                            <img src="<?php echo htmlspecialchars($popular_article['image']); ?>" alt="<?php echo htmlspecialchars($popular_article['title']); ?>" style="width:80px; height:80px;">
                            <div class="text">
                                <a href="../view/view_articles.php?id=<?php echo $popular_article['id']; ?>"><?php echo htmlspecialchars($popular_article['title']); ?></a>
                                <div class="meta">
                                    <span><?php echo $popular_article['created_at']; ?></span>
                                    <span><?php echo $popular_article['views']; ?> lượt xem</span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
