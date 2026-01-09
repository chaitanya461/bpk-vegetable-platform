<?php
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Get products with pagination
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          WHERE p.stock_quantity > 0 
          LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$countQuery = "SELECT COUNT(*) FROM products WHERE stock_quantity > 0";
$totalProducts = $db->query($countQuery)->fetchColumn();
$totalPages = ceil($totalProducts / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Vegetables - BPK</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <section class="products">
        <div class="container">
            <h1>All Vegetables</h1>
            
            <div class="filters">
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <?php
                    $catQuery = "SELECT * FROM categories";
                    $catStmt = $db->query($catQuery);
                    while($category = $catStmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$category['category_id']}'>{$category['name']}</option>";
                    }
                    ?>
                </select>
                <select id="sortFilter">
                    <option value="newest">Newest First</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                </select>
            </div>

            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <?php if($product['is_organic']): ?>
                        <span class="organic-badge">Organic</span>
                    <?php endif; ?>
                    <img src="<?php echo $product['image_url'] ?: '../assets/images/default-vegetable.jpg'; ?>" 
                         alt="<?php echo $product['name']; ?>">
                    <h3><?php echo $product['name']; ?></h3>
                    <p class="category"><?php echo $product['category_name']; ?></p>
                    <p class="price">₹<?php echo $product['price']; ?> / <?php echo $product['unit']; ?></p>
                    <p class="stock">Stock: <?php echo $product['stock_quantity']; ?> <?php echo $product['unit']; ?></p>
                    <a href="view.php?id=<?php echo $product['product_id']; ?>" 
                       class="btn btn-secondary">View Details</a>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" 
                       class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
    
    <script>
    // Filter functionality
    document.getElementById('categoryFilter').addEventListener('change', function() {
        window.location.href = 'category.php?id=' + this.value;
    });
    
    document.getElementById('sortFilter').addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
    });
    </script>
</body>
</html>
