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
    <style>
        body { font-family: Arial, sans-serif; margin:0; background:#f3f4f6; }

        .container {
            max-width: 1100px;
            margin: 25px auto;
            padding: 20px;
            background:white;
            border-radius:8px;
            display:flex;
            flex-wrap:wrap;
            gap:30px;
        }

        .summary, .form-area {
            flex:1;
            min-width:320px;
        }

        table { width:100%; border-collapse:collapse; margin-bottom:10px; }
        th, td { padding:10px; border-bottom:1px solid #e5e7eb; }
        th { background:#f9fafb; font-weight:600; }

        .total {
            text-align:right;
            font-weight:bold;
            font-size:18px;
            padding-top:10px;
        }

        .field { margin-bottom:12px; }
        .field label { font-weight:600; margin-bottom:5px; display:block; }
        .field input, .field textarea, .field select {
            width:100%;
            padding:10px;
            border:1px solid #d1d5db;
            border-radius:5px;
        }

        .btn {
            padding:12px 18px;
            border:none;
            border-radius:5px;
            cursor:pointer;
            font-size:15px;
        }
        .btn-primary {
            background:#2563eb;
            color:white;
        }

        .success {
            background:#dcfce7;
            color:#15803d;
            padding:15px;
            border-radius:6px;
            margin-bottom:15px;
        }

        .error {
            background:#fee2e2;
            color:#b91c1c;
            padding:12px;
            border-radius:5px;
            margin-bottom:10px;
        }
    </style>
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

