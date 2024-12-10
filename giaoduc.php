<?php
session_start();
include './config.php'; // Kết nối cơ sở dữ liệu

// Lấy danh mục "Giáo dục" từ bảng categories
$category_name = 'Giáo dục'; // Tên danh mục

// Lấy thông tin danh mục "Giáo dục" từ cơ sở dữ liệu
$categoryQuery = $conn->prepare("SELECT id FROM categories WHERE name = ?");
$categoryQuery->bind_param("s", $category_name);
$categoryQuery->execute();
$categoryResult = $categoryQuery->get_result();
$category = $categoryResult->fetch_assoc();
$category_id = $category['id'];

// Định nghĩa số lượng bài viết trên mỗi trang
$limit = 5;

// Lấy số trang hiện tại từ URL, mặc định là trang 1 nếu không có
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Đảm bảo trang ít nhất là 1

// Tính toán offset cho câu truy vấn
$offset = ($page - 1) * $limit;

// Truy vấn để lấy tổng số bài viết trong danh mục "Giáo dục"
$countQuery = $conn->prepare("SELECT COUNT(*) AS total FROM articles WHERE category_id = ?");
$countQuery->bind_param("i", $category_id);
$countQuery->execute();
$countResult = $countQuery->get_result();
$totalArticles = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalArticles / $limit);

// Truy vấn để lấy bài viết trong danh mục "Giáo dục" cho trang hiện tại
$stmt = $conn->prepare("SELECT * FROM articles WHERE category_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param("iii", $category_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giáo Dục</title>
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <div class="container">
        <div class="row mb">
            <!-- Phần bài viết chính -->
            <div class="boxtrai">
                <h2>Bài Viết Giáo Dục</h2>

                <?php
                // Hiển thị bài viết trong danh mục "Giáo dục"
                while ($article = $result->fetch_assoc()):
                ?>
                    <div class="article">
                        <h3><?php echo $article['title']; ?></h3>
                        <img src="<?php echo $article['image']; ?>" alt="<?php echo $article['title']; ?>">
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

            <!-- Tin nổi bật -->
            <div class="boxphai">
                <div class="boxtile">Tin Nổi Bật</div>
                <div class="boxcontent top10">
                    <?php
                    // Truy vấn bài viết nổi bật (theo lượt xem)
                    $stmt_popular = $conn->prepare("SELECT * FROM articles ORDER BY views DESC LIMIT 4");
                    $stmt_popular->execute();
                    $result_popular = $stmt_popular->get_result();

                    // Hiển thị bài viết nổi bật
                    while ($popular_article = $result_popular->fetch_assoc()):
                    ?>
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

</body>
</html>
