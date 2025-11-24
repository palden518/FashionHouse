<?php
session_start();
require_once 'connect.php';

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$cart_items = $_SESSION['cart'];
$cart_total = 0.0;

foreach ($cart_items as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}

$errors = [];
$success = false;

// Simulated user_id (replace with actual logged-in user ID from session)
$user_id = $_SESSION['user_id'] ?? null;
$user_fullname = "";
$user_email = "";

if (isset($_SESSION['user_id'])) {
    $query = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE user_id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $query->bind_result($db_first, $db_last, $db_email);
    $query->fetch();
    $query->close();

    $user_fullname = trim($db_first . " " . $db_last);
    $user_email = $db_email;
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $payment   = trim($_POST['payment_method'] ?? '');

    // Basic validation
    if ($full_name === '')  $errors[] = "Full name is required.";
    if ($email === '')      $errors[] = "Email is required.";
    if ($address === '')    $errors[] = "Shipping address is required.";
    if ($payment === '')    $errors[] = "Payment method is required.";

    if (empty($errors)) {
        // 1) Insert into orders table
        $stmt = $conn->prepare("
            INSERT INTO orders (user_id, order_status, total_amount, Address, Payment)
            VALUES (?, 'pending', ?, ?, ?)
        ");
        $stmt->bind_param("idss", $user_id, $cart_total, $address, $payment);

        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;
            $stmt->close();

            // 2) Insert each cart item into order_items table
            $stmt_item = $conn->prepare("
                INSERT INTO order_items 
                (order_id, product_id, quantity, unit_price, selected_variations, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");

            foreach ($cart_items as $item) {
                // Example: store variations as JSON string
                $variations = json_encode($item['variations'] ?? []);

                $stmt_item->bind_param(
                    "iiids",
                    $order_id,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price'],
                    $variations
                );
                $stmt_item->execute();
            }
            $stmt_item->close();

            $success = true;
            $_SESSION['cart'] = []; // clear cart after checkout
        } else {
            $errors[] = "Failed to create order. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout - Fashion House</title>
<link rel="stylesheet" href="styles/header-footer.css">
<link rel="stylesheet" href="checkout.css">
</head>
<body>

<header>
    <?php include 'header.inc'; ?>
</header>

<main>
<div class="container">

<?php if ($success): ?>
    <div class="success" style="grid-column:1/-1;">
        <h2>Thank you for your purchase! 🎉</h2>
        <p>Your order has been placed successfully.</p>
        <a href="home.php" class="btn btn-primary">Back to Home</a>
    </div>
<?php else: ?>

    <!-- ORDER SUMMARY -->
    <div class="checkout-summary">
        <h2>Order Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th width="60">Qty</th>
                    <th width="100">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo (int)$item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total">Total: $<?php echo number_format($cart_total, 2); ?></div>
    </div>

    <!-- CHECKOUT FORM -->
    <div class="checkout-form">
        <h2>Checkout</h2>

        <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $e): ?>
                <div>• <?php echo htmlspecialchars($e); ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="post" action="checkout.php">
            <div class="field">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? $user_fullname); ?>" required>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? $user_email); ?>" required>
            </div>

            <div class="field">
                <label for="address">Shipping Address</label>
                <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
            </div>

            <div class="field">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="">-- Select --</option>
                    <option value="card"   <?php echo (($_POST['payment_method'] ?? '') === 'card') ? 'selected' : ''; ?>>Credit / Debit Card</option>
                    <option value="paypal" <?php echo (($_POST['payment_method'] ?? '') === 'paypal') ? 'selected' : ''; ?>>PayPal</option>
                    <option value="cod"    <?php echo (($_POST['payment_method'] ?? '') === 'cod') ? 'selected' : ''; ?>>Cash on Delivery</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Place Order</button>
        </form>
    </div>

<?php endif; ?>

</div>
</main>

<footer>
    <?php include 'footer.inc'; ?>
</footer>
</body>
</html>
