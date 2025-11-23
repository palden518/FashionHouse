<<<<<<< HEAD
<?php
// checkout.php
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

    // If no errors → process fake order
    if (empty($errors)) {
        // (Optional) Save order to DB later
        // For now: simulate payment success
        $success = true;
        $_SESSION['cart'] = []; // clear cart after checkout
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Fashion House</title>
    <link rel="stylesheet" href="styles/header-footer.css">
    <link rel="stylesheet" href="styles/checkout.css">
</head>
<body>

<header>
    <?php include 'header.inc'; ?>
</header>

<main>
    <div class="container">

        <!-- SUCCESS MESSAGE -->
        <?php if ($success): ?>
            <div class="success" style="width:100%;">
                <h2>Thank you for your purchase! 🎉</h2>
                <p>Your order has been placed successfully.</p>
                <a href="home.php" class="btn btn-primary">Back to Home</a>
            </div>

        <?php else: ?>

            <!-- ORDER SUMMARY -->
            <div class="summary">
                <h2>Order Summary</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th width="80">Qty</th>
                            <th width="110">Subtotal</th>
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

                <div class="total">
                    Total: $<?php echo number_format($cart_total, 2); ?>
                </div>
            </div>

            <!-- CHECKOUT FORM -->
            <div class="form-area">
                <h2>Checkout</h2>

                <!-- ERROR DISPLAY -->
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
                        <input type="text" name="full_name" id="full_name"
                            value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                            required>
                    </div>

                    <div class="field">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            required>
                    </div>

                    <div class="field">
                        <label for="address">Shipping Address</label>
                        <textarea id="address" name="address" rows="3" required><?php
                            echo htmlspecialchars($_POST['address'] ?? '');
                        ?></textarea>
                    </div>

                    <div class="field">
                        <label for="payment_method">Payment Method</label>
                        <select name="payment_method" id="payment_method" required>
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

=======
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
$user_id = $_SESSION['user_id'] ?? 1;

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
<style>
:root {
    --primary-color: #333;
    --secondary-color: #555;
    --light-bg: #f8f9fa;
    --border-color: #dee2e6;
    --accent: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    margin:0;
}

.container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

.checkout-summary, .checkout-form {
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    padding: 30px;
}

.checkout-summary h2, .checkout-form h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 20px;
    text-transform: uppercase;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}
th, td {
    padding: 12px;
    border-bottom: 1px solid var(--border-color);
}
th {
    background: var(--light-bg);
    font-weight: 700;
}

.total {
    text-align: right;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-color);
}

.field { margin-bottom: 15px; }
.field label {
    font-weight: 600;
    margin-bottom: 5px;
    display: block;
}
.field input, .field textarea, .field select {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
}

.btn {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.btn-primary {
    background: var(--accent);
    color: white;
    width: 100%;
    text-align: center;
    margin-top: 10px;
    display: inline-block;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.success {
    background:#dcfce7;
    color:#15803d;
    padding:20px;
    border-radius:10px;
    text-align:center;
}

.error {
    background:#fee2e2;
    color:#b91c1c;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
}

@media (max-width: 992px) {
    .container {
        grid-template-columns: 1fr;
    }
}
</style>
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
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
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
>>>>>>> 3999134 (integrated project with product details and cart)
