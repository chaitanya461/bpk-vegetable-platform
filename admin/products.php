<?php
session_start();
require_once '../config/database.php';

// Check admin authentication
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category_id = $_POST['category_id'];
        $stock_quantity = $_POST['stock_quantity'];
        $unit = $_POST['unit'];
        $is_organic = isset($_POST['is_organic']) ? 1 : 0;
        
        $query = "INSERT INTO products (name, description, price, category_id, 
                  stock_quantity, unit, is_organic) 
                  VALUES (:name, :description, :price, :category_id, 
                  :stock_quantity, :unit, :is_organic)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':stock_quantity', $stock_quantity);
        $stmt->bindParam(':unit', $unit);
        $stmt->bindParam(':is_organic', $is_organic);
        
        if($stmt->execute()) {
            $_SESSION['success'] = "Product added successfully";
        }
    }
}

// Get all products
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          ORDER BY p.created_at DESC";
$stmt = $db->query($query);
$products = $stmt->fetchAll(PDO::FETCH_ASSOBJ);

// Get categories for dropdown
$catQuery = "SELECT * FROM categories";
$catStmt = $db->query($catQuery);
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - BPK Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>
        
        <main class="admin-main">
            <h1>Manage Products</h1>
            
            <!-- Add Product Form -->
            <section class="add-product-form">
                <h2>Add New Product</h2>
                <form method="POST">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Product Name" required>
                    </div>
                    <div class="form-group">
                        <textarea name="description" placeholder="Description" required></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="number" name="price" placeholder="Price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <input type="number" name="stock_quantity" placeholder="Stock Quantity" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <select name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category['category_id']; ?>">
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="unit" required>
                                <option value="kg">kg</option>
                                <option value="piece">Piece</option>
                                <option value="bunch">Bunch</option>
                                <option value="dozen">Dozen</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_organic"> Organic Product
                        </label>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                </form>
            </section>
            
            <!-- Products List -->
            <section class="products-list">
                <h2>All Products</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                        <tr>
                            <td><?php echo $product['product_id']; ?></td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['category_name']; ?></td>
                            <td>₹<?php echo $product['price']; ?></td>
                            <td><?php echo $product['stock_quantity']; ?> <?php echo $product['unit']; ?></td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" 
                                   class="btn btn-small">Edit</a>
                                <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" 
                                   class="btn btn-small btn-danger" 
                                   onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
