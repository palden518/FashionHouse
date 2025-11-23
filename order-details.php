<?php
require_once("connect.php");
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    die("Invalid order ID");
}

// Fetch order details
$order_query = "SELECT o.order_id, o.user_id, o.order_status, o.total_amount, o.order_date
                FROM orders o
                WHERE o.order_id = ? AND o.user_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    die("Order not found");
}

// Fetch order items
$items_query = "SELECT oi.order_item_id, oi.product_id, oi.quantity, oi.unit_price, 
                       p.product_name, pi.image_url, pi.alt_text
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = TRUE
                WHERE oi.order_id = ?
                ORDER BY oi.order_item_id DESC";
$items_stmt = $conn->prepare($items_query);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

$order_items = [];
while ($item = $items_result->fetch_assoc()) {
    $item['image_url'] = $item['image_url'] ? $item['image_url'] : 'images/placeholder.jpg';
    $item['line_total'] = $item['quantity'] * $item['unit_price'];
    $order_items[] = $item;
}

// Calculate totals
$subtotal = 0;
foreach ($order_items as $item) {
    $subtotal += $item['line_total'];
}

$tax = $subtotal * 0.1;
$total = $subtotal + $tax;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fashion House - Order Details</title>
  <link rel="stylesheet" href="styles/header-footer.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    }

    .container-wrapper {
      max-width: 1000px;
      margin: 0 auto;
      padding: 40px 20px;
    }

    .page-title {
      font-size: 2.5rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--primary-color);
      margin-bottom: 40px;
      text-align: center;
    }

    .order-header {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
    }

    .order-info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }

    .info-box {
      padding: 20px;
      background-color: var(--light-bg);
      border-radius: 8px;
      border-left: 4px solid #667eea;
    }

    .info-label {
      font-weight: 600;
      color: var(--secondary-color);
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 8px;
    }

    .info-value {
      font-size: 1.2rem;
      color: var(--primary-color);
      font-weight: 700;
    }

    .status-badge {
      display: inline-block;
      padding: 8px 20px;
      border-radius: 20px;
      font-weight: 700;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
    }

    .status-pending {
      background-color: #fff3cd;
      color: #856404;
    }

    .status-completed {
      background-color: #d4edda;
      color: #155724;
    }

    .status-cancelled {
      background-color: #f8d7da;
      color: #721c24;
    }

    .order-items-section {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
    }

    .section-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .order-item {
      padding: 20px;
      border-bottom: 1px solid var(--border-color);
      display: grid;
      grid-template-columns: 100px 1fr auto;
      gap: 20px;
      align-items: center;
    }

    .order-item:last-child {
      border-bottom: none;
    }

    .item-image {
      width: 100px;
      height: 100px;
      border-radius: 8px;
      overflow: hidden;
      background-color: #e9ecef;
    }

    .item-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .item-details {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .item-name {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--primary-color);
    }

    .item-quantity {
      font-size: 0.95rem;
      color: var(--secondary-color);
    }

    .item-price {
      font-size: 0.95rem;
      color: #667eea;
      font-weight: 600;
    }

    .item-total {
      text-align: right;
    }

    .item-total-price {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--primary-color);
    }

    .item-total-label {
      font-size: 0.85rem;
      color: var(--secondary-color);
    }

    .order-summary {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
      max-width: 400px;
      margin-left: auto;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
      padding-bottom: 15px;
      border-bottom: 1px solid var(--border-color);
    }

    .summary-row.total {
      border-bottom: none;
      padding-top: 15px;
      border-top: 2px solid var(--border-color);
      font-size: 1.2rem;
      font-weight: 700;
    }

    .summary-label {
      color: var(--secondary-color);
      font-weight: 600;
    }

    .summary-value {
      color: var(--primary-color);
      font-weight: 600;
    }

    .action-buttons {
      display: flex;
      gap: 15px;
      margin-top: 30px;
      flex-wrap: wrap;
    }

    .btn-primary {
      background: var(--accent);
      color: white;
      border: none;
      padding: 12px 30px;
      border-radius: 8px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
      background-color: var(--primary-color);
      color: white;
      border: none;
      padding: 12px 30px;
      border-radius: 8px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }

    .btn-secondary:hover {
      background-color: var(--secondary-color);
      transform: translateY(-2px);
    }

    .no-items {
      text-align: center;
      padding: 40px;
      color: #6c757d;
    }

    @media (max-width: 768px) {
      .page-title {
        font-size: 1.8rem;
      }

      .order-item {
        grid-template-columns: 80px 1fr;
      }

      .item-image {
        width: 80px;
        height: 80px;
      }

      .item-total {
        grid-column: 1 / -1;
        text-align: right;
        margin-top: 10px;
      }

      .order-summary {
        max-width: 100%;
      }
    }
  </style>
</head>
<body>
  <header>
    <?php include 'header.inc'; ?>
  </header>
<script src="./scripts/dropdownscript.js"></script>
  <main>
    <div class="container-wrapper">
      <h1 class="page-title">
        <i class="fas fa-receipt"></i> Order Details
      </h1>

      <!-- Order Header -->
      <div class="order-header">
        <div class="order-info-grid">
          <div class="info-box">
            <div class="info-label">Order ID</div>
            <div class="info-value">#<?php echo $order['order_id']; ?></div>
          </div>

          <div class="info-box">
            <div class="info-label">Order Date</div>
            <div class="info-value"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></div>
          </div>

          <div class="info-box">
            <div class="info-label">Status</div>
            <div class="info-value">
              <span class="status-badge status-<?php echo strtolower($order['order_status']); ?>">
                <?php echo ucfirst($order['order_status']); ?>
              </span>
            </div>
          </div>

          <div class="info-box">
            <div class="info-label">Order Total</div>
            <div class="info-value">$<?php echo number_format($order['total_amount'], 2); ?></div>
          </div>
        </div>
      </div>

      <!-- Order Items -->
      <div class="order-items-section">
        <h2 class="section-title">
          <i class="fas fa-box"></i> Order Items
        </h2>

        <?php if (count($order_items) > 0) { ?>
          <?php foreach ($order_items as $item) { ?>
          <div class="order-item">
            <div class="item-image">
              <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['alt_text'] ?? $item['product_name']); ?>">
            </div>

            <div class="item-details">
              <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
              <div class="item-quantity">Quantity: <strong><?php echo $item['quantity']; ?></strong></div>
              <div class="item-price">Price: $<?php echo number_format($item['unit_price'], 2); ?> each</div>
            </div>

            <div class="item-total">
              <div class="item-total-label">Subtotal</div>
              <div class="item-total-price">$<?php echo number_format($item['line_total'], 2); ?></div>
            </div>
          </div>
          <?php } ?>
        <?php } else { ?>
          <div class="no-items">
            <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 20px; display: block;"></i>
            <p>No items in this order</p>
          </div>
        <?php } ?>
      </div>

      <!-- Order Summary -->
      <div class="order-summary">
        <h3 class="section-title" style="margin-bottom: 20px;">
          <i class="fas fa-calculator"></i> Summary
        </h3>

        <div class="summary-row">
          <span class="summary-label">Subtotal</span>
          <span class="summary-value">$<?php echo number_format($subtotal, 2); ?></span>
        </div>

        <div class="summary-row">
          <span class="summary-label">Shipping</span>
          <span class="summary-value">Free</span>
        </div>

        <div class="summary-row">
          <span class="summary-label">Tax (10%)</span>
          <span class="summary-value">$<?php echo number_format($tax, 2); ?></span>
        </div>

        <div class="summary-row total">
          <span>Total</span>
          <span>$<?php echo number_format($total, 2); ?></span>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="action-buttons">
        <button onclick="history.back()" class="btn-secondary">
          <i class="fas fa-arrow-left"></i> Back to Orders
        </button>
        <a href="home.php" class="btn-primary">
          <i class="fas fa-shopping-bag"></i> Continue Shopping
        </a>
      </div>
    </div>
  </main>

  <footer>
    <?php include 'footer.inc'?>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>