<?php
session_start();
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'] ?? null;
    
    // Check if user is logged in
    if(!$user_id) {
        $_SESSION['redirect_to'] = $_SERVER['HTTP_REFERER'];
        header("Location: ../auth/login.php");
        exit;
    }
    
    // Check product stock
    $stockQuery = "SELECT stock_quantity FROM products WHERE product_id = :id";
    $stockStmt = $db->prepare($stockQuery);
    $stockStmt->bindParam(':id', $product_id);
    $stockStmt->execute();
    $stock = $stockStmt->fetch(PDO::FETCH_ASSOC);
    
    if($stock['stock_quantity'] < $quantity) {
        $_SESSION['error'] = "Not enough stock available";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    // Check if product already in cart
    $checkQuery = "SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':user_id', $user_id);
    $checkStmt->bindParam(':product_id', $product_id);
    $checkStmt->execute();
    
    if($checkStmt->rowCount() > 0) {
        // Update quantity
        $updateQuery = "UPDATE cart SET quantity = quantity + :quantity 
                       WHERE user_id = :user_id AND product_id = :product_id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':quantity', $quantity);
        $updateStmt->bindParam(':user_id', $user_id);
        $updateStmt->bindParam(':product_id', $product_id);
        $updateStmt->execute();
    } else {
        // Add new item
        $insertQuery = "INSERT INTO cart (user_id, product_id, quantity) 
                       VALUES (:user_id, :product_id, :quantity)";
        $insertStmt = $db->prepare($insertQuery);
        $insertStmt->bindParam(':user_id', $user_id);
        $insertStmt->bindParam(':product_id', $product_id);
        $insertStmt->bindParam(':quantity', $quantity);
        $insertStmt->execute();
    }
    
    $_SESSION['success'] = "Product added to cart successfully";
    header("Location: ../cart/");
    exit;
}
?>
