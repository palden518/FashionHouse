<?php
header('Content-Type: application/json');
require_once("connect.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Please login to add items to cart']));
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if ($product_id <= 0 || $quantity <= 0) {
    die(json_encode(['success' => false, 'message' => 'Invalid product or quantity']));
}

$product_query = "SELECT product_id, stock_quantity FROM products WHERE product_id = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();

if (!$product) {
    die(json_encode(['success' => false, 'message' => 'Product not found']));
}

if ($product['stock_quantity'] < $quantity) {
    die(json_encode(['success' => false, 'message' => 'Insufficient stock available']));
}

$check_query = "SELECT cart_id, quantity FROM shopping_cart WHERE user_id = ? AND product_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $user_id, $product_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$existing_item = $check_result->fetch_assoc();

if ($existing_item) {
    $new_quantity = $existing_item['quantity'] + $quantity;
    
    if ($new_quantity > $product['stock_quantity']) {
        die(json_encode(['success' => false, 'message' => 'Cannot add more items. Stock limit reached.']));
    }
    
    $update_query = "UPDATE shopping_cart SET quantity = ? WHERE cart_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ii", $new_quantity, $existing_item['cart_id']);
    $update_stmt->execute();
} else {
    $insert_query = "INSERT INTO shopping_cart (user_id, product_id, quantity, selected_variations) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $variations = json_encode([]);
    $insert_stmt->bind_param("iiis", $user_id, $product_id, $quantity, $variations);
    $insert_stmt->execute();
}

$count_query = "SELECT COUNT(*) as count FROM shopping_cart WHERE user_id = ?";
$count_stmt = $conn->prepare($count_query);
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$cart_count = intval($count_row['count']);

echo json_encode([
    'success' => true,
    'message' => 'Added to cart successfully!',
    'cart_count' => $cart_count
]);
?>