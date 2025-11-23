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
            $sql = "SELECT product_id, product_name, price 
                    FROM products 
                    WHERE product_id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();

            if ($product) {
                // If already in cart, increase quantity
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                } else {
                    // Add new item
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
        $remove_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
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
    <link rel="stylesheet" href="styles/cart.css">
</head>

<body>

<header>
    <?php include 'header.inc'; ?>
</header>

<main>
    <div class="container">
        <h2>Your Cart</h2>

        <?php if (empty($cart_items)): ?>
            <div class="empty">
                <p>Your cart is empty.</p>
                <a href="category.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>

            <!-- UPDATE CART FORM -->
            <form method="post" action="cart.php">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th width="100">Price</th>
                            <th width="100">Quantity</th>
                            <th width="100">Subtotal</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>

                                <td>
                                    <input type="number"
                                           name="quantities[<?php echo $item['product_id']; ?>]"
                                           value="<?php echo $item['quantity']; ?>"
                                           min="1"
                                           style="width:60px;">
                                </td>

                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>

                                <td>
                                    <!-- REMOVE BUTTON (separate form!) -->
                                    <form method="post" action="cart.php" style="display:inline;">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <button type="submit" name="remove_item" class="btn btn-danger">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="3" class="total-row">Total:</td>
                            <td colspan="2" class="total-row">$<?php echo number_format($cart_total, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>

                <div style="display:flex; justify-content:space-between;">
                    <div>
                        <button type="submit" name="update_cart" class="btn btn-outline">Update Cart</button>

                        <button type="submit"
                                name="clear_cart"
                                class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to clear the entire cart?');">
                            Clear Cart
                        </button>
                    </div>

                    <div>
                        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                    </div>
                </div>
            </form>

        <?php endif; ?>
    </div>
</main>

<footer>
    <?php include 'footer.inc'; ?>
</footer>

</body>
</html>
