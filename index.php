<?php
session_start();
include './config.php';
?>

<?php include 'header.php'; ?>
<link rel="stylesheet" href="./css/index.css">

<main>
    <div class="container">
        <div class="row mb">
            <!-- Phần bài viết chính -->
            <div class="boxtrai">
    <h2>Các Bài Viết Mới Nhất</h2>
    <?php
    // Define the number of articles per page
    $limit = 5;

    // Get the current page number from the URL, defaulting to page 1 if not set
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max($page, 1); // Ensure page is at least 1

    // Calculate the offset for the SQL query
    $offset = ($page - 1) * $limit;

    // Query to get the total count of articles
    $countQuery = $conn->query("SELECT COUNT(*) AS total FROM articles");
    $totalArticles = $countQuery->fetch_assoc()['total'];
    $totalPages = ceil($totalArticles / $limit);

    // Query to fetch articles for the current page
    $stmt = $conn->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT ? OFFSET ?");
    if (!$stmt) {
        die("Lỗi câu truy vấn: " . $conn->error);
    }
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display articles
    while ($article = $result->fetch_assoc()):
    ?>
        <div class="article">
            <h3><?php echo $article['title']; ?></h3>
            <img src="<?php echo $article['image']; ?>" alt="<?php echo $article['title']; ?>">
            <p><?php echo substr($article['content'], 0, 100) . '...'; ?></p>
            <a href="../view/view_articles.php?id=<?php echo $article['id']; ?>">Đọc thêm</a>
        </div>
    <?php endwhile; ?>

    <!-- Pagination Links -->
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
                    // Câu truy vấn lấy các bài viết nổi bật (theo lượt xem)
                    $stmt_popular = $conn->prepare("SELECT * FROM articles ORDER BY views DESC LIMIT 4");
                    if (!$stmt_popular) {
                        die("Lỗi câu truy vấn: " . $conn->error);  // Hiển thị lỗi nếu có
                    }
                    $stmt_popular->execute();
                    $result_popular = $stmt_popular->get_result();

                    // Hiển thị bài viết nổi bật
                    while ($popular_article = $result_popular->fetch_assoc()): ?>
                        <div class="row mb10 news-item">
                            <img src="<?php echo $popular_article['image']; ?>" alt="<?php echo $popular_article['title']; ?>" style="width:80px; height:80px;">
                            <div class="text">
                                <a href="../view/view_articles.php?id=<?php echo $popular_article['id']; ?>"><?php echo $popular_article['title']; ?></a>
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
