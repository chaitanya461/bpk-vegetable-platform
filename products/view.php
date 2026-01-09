<?php
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

$product_id = $_GET['id'] ?? 0;

$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          WHERE p.product_id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $product_id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $product['name']; ?> - BPK</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <section class="product-detail">
        <div class="container">
            <div class="product-image">
                <img src="<?php echo $product['image_url'] ?: '../assets/images/default-vegetable.jpg'; ?>" 
                     alt="<?php echo $product['name']; ?>">
            </div>
            <div class="product-info">
                <h1><?php echo $product['name']; ?></h1>
                <?php if($product['is_organic']): ?>
                    <span class="organic-tag">Organic</span>
                <?php endif; ?>
                <p class="category">Category: <?php echo $product['category_name']; ?></p>
                <p class="price">₹<?php echo $product['price']; ?> / <?php echo $product['unit']; ?></p>
                <p class="stock">Available: <?php echo $product['stock_quantity']; ?> <?php echo $product['unit']; ?></p>
                <p class="description"><?php echo $product['description']; ?></p>
                
                <?php if($product['stock_quantity'] > 0): ?>
                <form action="../cart/add_to_cart.php" method="POST" class="add-to-cart">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    <div class="quantity-selector">
                        <button type="button" onclick="decreaseQuantity()">-</button>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" 
                               max="<?php echo $product['stock_quantity']; ?>">
                        <button type="button" onclick="increaseQuantity()">+</button>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                </form>
                <?php else: ?>
                    <p class="out-of-stock">Out of Stock</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
    
    <script>
    function increaseQuantity() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.max);
        if(parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    }
    
    function decreaseQuantity() {
        const input = document.getElementById('quantity');
        if(parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }
    </script>
</body>
</html>
