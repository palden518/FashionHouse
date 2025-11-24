<?php
// cart.php
session_start();
require_once 'connect.php';

// Make sure cart array exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle POST actions (add, update, remove, clear)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1) Add to cart from product.php
    if (isset($_POST['add_to_cart'])) {
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity   = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        if ($product_id > 0 && $quantity > 0) {
            // Fetch product info
            $sql = "SELECT product_id, product_name, price FROM products WHERE product_id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();

            if ($product) {
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        'product_id'   => $product['product_id'],
                        'product_name' => $product['product_name'],
                        'price'        => (float)$product['price'],
                        'quantity'     => $quantity,
                    ];
                }
            }
        }
        header("Location: cart.php");
        exit;
    }

    // 2) Update quantities
    if (isset($_POST['update_cart']) && !empty($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $pid => $qty) {
            $pid = (int)$pid;
            $qty = (int)$qty;
            if ($qty > 0) {
                $_SESSION['cart'][$pid]['quantity'] = $qty;
            } else {
                unset($_SESSION['cart'][$pid]);
            }
        }
        header("Location: cart.php");
        exit;
    }

    // 3) Remove single item
    if (isset($_POST['remove_item'])) {
        $remove_id = (int)$_POST['remove_item'];
        if ($remove_id > 0 && isset($_SESSION['cart'][$remove_id])) {
            unset($_SESSION['cart'][$remove_id]);
        }
        header("Location: cart.php");
        exit;
    }

    // 4) Clear entire cart
    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        header("Location: cart.php");
        exit;
    }
}

// Calculate totals
$cart_items = $_SESSION['cart'];
$cart_total = 0;
foreach ($cart_items as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart - Fashion House</title>
<link rel="stylesheet" href="styles/header-footer.css">
<style>
:root { --primary-color: #333; --secondary-color: #555; --light-bg: #f8f9fa; --border-color: #dee2e6; --accent: linear-gradient(135deg, #667eea 0%, #764ba2 100%); } * { transition: all 0.3s ease; } body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; } .container-wrapper { max-width: 1200px; margin: 0 auto; padding: 40px 20px; } .page-title { font-size: 2.5rem; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: var(--primary-color); margin-bottom: 40px; text-align: center; } .cart-container { display: grid; grid-template-columns: 1fr 350px; gap: 30px; margin-bottom: 30px; } .cart-items-section { background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); overflow: hidden; } .cart-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; font-size: 1.3rem; font-weight: 700; display: flex; align-items: center; gap: 12px; } .cart-item { padding: 20px 25px; border-bottom: 1px solid var(--border-color); display: grid; grid-template-columns: 50% 1fr auto; gap: 20px; align-items: center; } .cart-item:last-child { border-bottom: none; } .item-details { display: flex; flex-direction: column; gap: 8px; } .item-name { font-size: 1.1rem; font-weight: 700; color: var(--primary-color); } .item-price { font-size: 1.2rem; font-weight: 700; background: var(--accent); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; } .item-actions { display: flex; align-items: center; gap: 15px; } .quantity-control { display: flex; align-items: center; gap: 8px; background-color: var(--light-bg); border-radius: 8px; padding: 8px; } .quantity-btn { width: 30px; height: 30px; border: none; background: white; color: var(--primary-color); border-radius: 6px; cursor: pointer; font-weight: bold; transition: all 0.3s ease; } .quantity-btn:hover { background: #667eea; color: white; } .quantity-input { width: 50px; text-align: center; border: none; background: transparent; font-weight: 600; font-size: 1rem; } .quantity-input:focus { outline: none; } .item-total { font-size: 1.2rem; font-weight: 700; color: var(--primary-color); min-width: 80px; text-align: right; } .remove-btn { background-color: #dc3545; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 600; transition: all 0.3s ease; } .remove-btn:hover { background-color: #c82333; transform: translateY(-2px); } .cart-summary { background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); padding: 30px; height: fit-content; position: sticky; top: 20px; } .summary-title { font-size: 1.3rem; font-weight: 700; color: var(--primary-color); margin-bottom: 20px; text-transform: uppercase; letter-spacing: 0.5px; } .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color); font-size: 0.95rem; } .summary-row.total { font-size: 1.3rem; font-weight: 700; color: var(--primary-color); border-bottom: none; padding-top: 15px; margin-top: 15px; border-top: 2px solid var(--border-color); } .summary-label { color: var(--secondary-color); font-weight: 600; } .summary-value { color: var(--primary-color); font-weight: 600; } .btn { padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; transition: all 0.3s ease; font-size: 0.95rem; text-decoration: none; display: inline-block; } .btn-primary { background: var(--accent); color: white; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); width: 100%; text-align: center; margin-top: 20px; } .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); } .btn-secondary { background-color: var(--primary-color); color: white; margin-top: 10px; width: 100%; text-align: center; } .btn-secondary:hover { background-color: var(--secondary-color); transform: translateY(-2px); } .btn-danger { background-color: #dc3545; color: white; margin-top: 10px; width: 100%; text-align: center; } .btn-danger:hover { background-color: #c82333; transform: translateY(-2px); } .empty-cart { text-align: center; padding: 80px 40px; background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); } .empty-cart-icon { font-size: 4rem; color: #ccc; margin-bottom: 20px; } .empty-cart-text { color: #6c757d; font-size: 1.2rem; margin-bottom: 30px; } .continue-shopping { background: var(--accent); color: white; padding: 15px 40px; border-radius: 8px; text-decoration: none; display: inline-block; font-weight: 700; transition: all 0.3s ease; } .continue-shopping:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); } .action-buttons { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 30px; } @media (max-width: 992px) { .cart-container { grid-template-columns: 1fr; } .cart-summary { position: static; } } @media (max-width: 768px) { .cart-item { grid-template-columns: 60px 1fr; } .item-actions { grid-column: 1 / -1; flex-direction: column; align-items: stretch; margin-top: 15px; } .item-image { width: 60px; height: 60px; } .page-title { font-size: 1.8rem; } }
</style>
<script>
function updateItemTotal(input) {
    const quantity = parseInt(input.value);
    const itemTotalDiv = input.closest('.cart-item').querySelector('.item-total');
    const price = parseFloat(itemTotalDiv.getAttribute('data-price'));
    itemTotalDiv.textContent = '$' + (price * quantity).toFixed(2);
    updateCartSummary();
}

function increaseQuantity(btn){
    const input = btn.previousElementSibling;
    input.value = parseInt(input.value) + 1;
    updateItemTotal(input);
}
function decreaseQuantity(btn){
    const input = btn.nextElementSibling;
    if(parseInt(input.value) > 1){
        input.value = parseInt(input.value) - 1;
        updateItemTotal(input);
    }
}

// Update subtotal and total in the cart summary
function updateCartSummary() {
    let subtotal = 0;
    document.querySelectorAll('.item-total').forEach(function(div){
        subtotal += parseFloat(div.textContent.replace('$',''));
    });

    const tax = subtotal * 0.1;
    const total = subtotal + tax;

    document.querySelector('.summary-row .summary-value').textContent = '$' + subtotal.toFixed(2); // Subtotal
    document.querySelector('.summary-row.total .summary-value').textContent = '$' + total.toFixed(2); // Total
}
</script>
</head>
<body>
<header>
<?php include 'header.inc'; ?>
</header>
<main>
<div class="container-wrapper">
    <h1 class="page-title">
        <i class="fas fa-shopping-cart"></i> Your Cart
    </h1>

    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <div class="empty-cart-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <p class="empty-cart-text">Your cart is empty</p>
            <a href="category.php" class="continue-shopping">
                <i class="fas fa-shopping-bag"></i> Continue Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <!-- CART ITEMS -->
            <div class="cart-items-section">
                <div class="cart-header">
                    <i class="fas fa-box"></i> Items in Cart (<?php echo count($cart_items); ?>)
                </div>

                <form method="post" action="cart.php">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="item-details">
                            <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                            <div class="item-price">$<?php echo number_format($item['price'], 2); ?></div>
                        </div>

                        <div class="item-actions">
                            <div class="quantity-control">
                                <button type="button" class="quantity-btn" onclick="decreaseQuantity(this)">−</button>
                                <input type="number" class="quantity-input" 
                                  name="quantities[<?php echo $item['product_id']; ?>]"
                                  value="<?php echo $item['quantity']; ?>" min="1"
                                  onchange="updateItemTotal(this)">
                                <button type="button" class="quantity-btn" onclick="increaseQuantity(this)">+</button>
                            </div>

                            <div class="item-total" data-price="<?php echo $item['price']; ?>">
                                $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>

                            <button type="submit" name="remove_item" value="<?php echo $item['product_id']; ?>" class="remove-btn">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div style="padding: 25px; display: flex; gap: 15px; flex-wrap: wrap;">
                        <button type="submit" name="update_cart" class="btn btn-secondary">
                            <i class="fas fa-sync"></i> Update Cart
                        </button>

                        <button type="submit" name="clear_cart" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to clear the entire cart?');">
                            <i class="fas fa-trash-alt"></i> Clear Cart
                        </button>
                    </div>
                </form>
            </div>

            <!-- CART SUMMARY -->
            <div class="cart-summary">
                <div class="summary-title">
                    <i class="fas fa-receipt"></i> Order Summary
                </div>

                <div class="summary-row">
                    <span class="summary-label">Subtotal</span>
                    <span class="summary-value">$<?php echo number_format($cart_total, 2); ?></span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Shipping</span>
                    <span class="summary-value">Free</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Tax (10%)</span>
                    <span class="summary-value">$<?php echo number_format($cart_total * 0.1, 2); ?></span>
                </div>

                <div class="summary-row total">
                    <span>Total</span>
                    <span>$<?php echo number_format($cart_total + ($cart_total * 0.1), 2); ?></span>
                </div>

                <a href="checkout.php" class="btn btn-primary">
                    <i class="fas fa-credit-card"></i> Proceed to Checkout
                </a>

                <a href="category.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
</main>
<footer>
<?php include 'footer.inc'; ?>
</footer>
</body>
</html>
