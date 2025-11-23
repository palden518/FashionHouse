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

// Fetch user data
$user_query = "SELECT user_id, email, first_name, last_name, phone, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    die("User not found");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'update_profile') {
        $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        
        // Validation
        if (empty($first_name) || empty($last_name)) {
            $message = "First name and last name are required.";
            $message_type = "danger";
        } else {
            $update_query = "UPDATE users SET first_name = ?, last_name = ?, phone = ? WHERE user_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssi", $first_name, $last_name, $phone, $user_id);
            
            if ($stmt->execute()) {
                // Update session data
                $user['first_name'] = $first_name;
                $user['last_name'] = $last_name;
                $user['phone'] = $phone;
                
                $message = "Profile updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error updating profile. Please try again.";
                $message_type = "danger";
            }
        }
    } elseif ($action === 'change_password') {
        $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        // Validation
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $message = "All password fields are required.";
            $message_type = "danger";
        } elseif ($new_password !== $confirm_password) {
            $message = "New passwords do not match.";
            $message_type = "danger";
        } elseif (strlen($new_password) < 6) {
            $message = "Password must be at least 6 characters long.";
            $message_type = "danger";
        } else {
            // Verify current password
            $verify_query = "SELECT password FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($verify_query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $verify_result = $stmt->get_result();
            $verify_user = $verify_result->fetch_assoc();
            
            if (password_verify($current_password, $verify_user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $password_query = "UPDATE users SET password = ? WHERE user_id = ?";
                $stmt = $conn->prepare($password_query);
                $stmt->bind_param("si", $hashed_password, $user_id);
                
                if ($stmt->execute()) {
                    $message = "Password changed successfully!";
                    $message_type = "success";
                } else {
                    $message = "Error changing password. Please try again.";
                    $message_type = "danger";
                }
            } else {
                $message = "Current password is incorrect.";
                $message_type = "danger";
            }
        }
    }
}

// Fetch user orders
$orders_query = "SELECT order_id, order_date, order_status, total_amount FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 5";
$stmt = $conn->prepare($orders_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$orders = [];
while ($order = $orders_result->fetch_assoc()) {
    $orders[] = $order;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fashion House - My Profile</title>
  <link rel="stylesheet" href="styles/header-footer.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #333;
      --secondary-color: #555;
      --light-bg: #f8f9fa;
      --border-color: #dee2e6;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--light-bg);
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

    .alert {
      margin-bottom: 30px;
      border-radius: 8px;
      border: none;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .nav-tabs {
      border-bottom: 2px solid var(--border-color);
      margin-bottom: 30px;
    }

    .nav-link {
      color: var(--secondary-color);
      border: none;
      font-weight: 600;
      padding: 12px 24px;
      transition: all 0.3s ease;
      border-bottom: 3px solid transparent;
      margin-bottom: -2px;
    }

    .nav-link:hover {
      color: var(--primary-color);
      border-bottom-color: var(--primary-color);
    }

    .nav-link.active {
      color: var(--primary-color);
      border-bottom-color: var(--primary-color);
      background-color: transparent;
    }

    .tab-content {
      background-color: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .form-group {
      margin-bottom: 25px;
    }

    .form-label {
      font-weight: 600;
      color: var(--primary-color);
      margin-bottom: 8px;
      display: block;
    }

    .form-control {
      border: 2px solid var(--border-color);
      border-radius: 6px;
      padding: 10px 15px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(51, 51, 51, 0.15);
    }

    .form-control:disabled {
      background-color: var(--light-bg);
      color: #6c757d;
    }

    .info-group {
      padding: 20px;
      background-color: var(--light-bg);
      border-radius: 6px;
      margin-bottom: 20px;
    }

    .info-label {
      font-weight: 600;
      color: var(--secondary-color);
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 5px;
    }

    .info-value {
      font-size: 1.1rem;
      color: var(--primary-color);
      word-break: break-word;
    }

    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      padding: 10px 30px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: var(--secondary-color);
      border-color: var(--secondary-color);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-secondary {
      background-color: #6c757d;
      border-color: #6c757d;
      padding: 10px 30px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      background-color: #5a6268;
      border-color: #5a6268;
    }

    .form-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .order-card {
      padding: 20px;
      border: 2px solid var(--border-color);
      border-radius: 8px;
      margin-bottom: 20px;
      transition: all 0.3s ease;
    }

    .order-card:hover {
      border-color: var(--primary-color);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .order-id {
      font-weight: 700;
      color: var(--primary-color);
      font-size: 1.1rem;
    }

    .order-status {
      padding: 6px 15px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
      text-transform: uppercase;
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

    .order-details {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 15px;
      padding-top: 15px;
      border-top: 1px solid var(--border-color);
    }

    .order-detail-item {
      font-size: 0.9rem;
    }

    .order-detail-label {
      font-weight: 600;
      color: var(--secondary-color);
      margin-bottom: 3px;
    }

    .order-detail-value {
      color: var(--primary-color);
      font-size: 1.05rem;
    }

    .no-orders {
      text-align: center;
      padding: 40px;
      color: #6c757d;
    }

    @media (max-width: 768px) {
      .container-wrapper {
        padding: 20px 15px;
      }

      .page-title {
        font-size: 1.8rem;
        margin-bottom: 30px;
      }

      .nav-link {
        padding: 10px 15px;
        font-size: 0.9rem;
      }

      .tab-content {
        padding: 20px;
      }

      .form-row {
        grid-template-columns: 1fr;
      }

      .order-header {
        flex-direction: column;
        align-items: flex-start;
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
      <h1 class="page-title">My Profile</h1>

      <?php if (!empty($message)) { ?>
      <div class="alert alert-<?php echo $message_type; ?>" role="alert">
        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
        <?php echo htmlspecialchars($message); ?>
      </div>
      <?php } ?>

      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
            <i class="fas fa-user"></i> Profile Information
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
            <i class="fas fa-lock"></i> Change Password
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="false">
            <i class="fas fa-shopping-bag"></i> Order History
          </button>
        </li>
      </ul>

      <div class="tab-content">
        <!-- Profile Information Tab -->
        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="info-group">
            <div class="info-label">Email Address</div>
            <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
          </div>

          <div class="info-group">
            <div class="info-label">Member Since</div>
            <div class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></div>
          </div>

          <form method="POST" action="">
            <input type="hidden" name="action" value="update_profile">
            
            <div class="form-row">
              <div class="form-group">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
              </div>

              <div class="form-group">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
              </div>
            </div>

            <div class="form-group">
              <label for="phone" class="form-label">Phone Number</label>
              <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>

            <div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
              </button>
            </div>
          </form>
        </div>

        <!-- Change Password Tab -->
        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
          <form method="POST" action="">
            <input type="hidden" name="action" value="change_password">
            
            <div class="form-group">
              <label for="current_password" class="form-label">Current Password</label>
              <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>

            <div class="form-group">
              <label for="new_password" class="form-label">New Password</label>
              <input type="password" class="form-control" id="new_password" name="new_password" required>
              <small class="form-text text-muted">Minimum 6 characters</small>
            </div>

            <div class="form-group">
              <label for="confirm_password" class="form-label">Confirm New Password</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-primary">
              <i class="fas fa-key"></i> Change Password
            </button>
          </form>
        </div>

        <!-- Order History Tab -->
        <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
          <?php if (count($orders) > 0) { ?>
            <?php foreach ($orders as $order) { 
              $status_class = 'status-' . strtolower($order['order_status']);
            ?>
            <div class="order-card">
              <div class="order-header">
                <div class="order-id">Order #<?php echo htmlspecialchars($order['order_id']); ?></div>
                <a href="order-details.php?order_id=<?php echo $order['order_id']; ?>">
  View Details
</a>
                <span class="order-status <?php echo $status_class; ?>">
                  <?php echo htmlspecialchars($order['order_status']); ?>
                </span>
              </div>
              <div class="order-details">
                <div class="order-detail-item">
                  <div class="order-detail-label">Order Date</div>
                  <div class="order-detail-value"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></div>
                </div>
                <div class="order-detail-item">
                  <div class="order-detail-label">Total Amount</div>
                  <div class="order-detail-value">$<?php echo number_format($order['total_amount'], 2); ?></div>
                </div>
              </div>
            </div>
            <?php } ?>
          <?php } else { ?>
            <div class="no-orders">
              <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 20px; display: block;"></i>
              <p>No orders yet. Start shopping!</p>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <?php include 'footer.inc'?>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>