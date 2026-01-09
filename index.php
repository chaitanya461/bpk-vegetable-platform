<?php
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

// Get featured products
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          WHERE p.stock_quantity > 0 
          ORDER BY p.created_at DESC 
          LIMIT 8";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPK - Fresh Vegetables Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="hero">
        <div class="container">
            <h1>Fresh Vegetables Direct from Farm</h1>
            <p>100% Organic | Fresh Daily | Home Delivery</p>
            <a href="products/" class="btn btn-primary">Shop Now</a>
        </div>
    </section>

    <section class="featured-products">
        <div class="container">
            <h2>Fresh Vegetables</h2>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo $product['image_url'] ?: 'assets/images/default-vegetable.jpg'; ?>" 
                         alt="<?php echo $product['name']; ?>">
                    <h3><?php echo $product['name']; ?></h3>
                    <p class="price">₹<?php echo $product['price']; ?> / <?php echo $product['unit']; ?></p>
                    <a href="products/view.php?id=<?php echo $product['product_id']; ?>" 
                       class="btn btn-secondary">View Details</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <div class="feature">
                <i class="fas fa-shipping-fast"></i>
                <h3>Free Delivery</h3>
                <p>On orders above ₹500</p>
            </div>
            <div class="feature">
                <i class="fas fa-leaf"></i>
                <h3>100% Organic</h3>
                <p>Certified organic products</p>
            </div>
            <div class="feature">
                <i class="fas fa-clock"></i>
                <h3>Fresh Daily</h3>
                <p>Harvested fresh every morning</p>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
