<?php
require_once("connect.php");
session_start();

// Get category and type from URL parameters
$category = isset($_GET['category']) ? $_GET['category'] : 'men';
$product_type = isset($_GET['type']) ? $_GET['type'] : null;

// Fetch category ID based on category name
$category_query = "SELECT category_id FROM categories WHERE LOWER(category_name) = LOWER(?)";
$stmt = $conn->prepare($category_query);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("s", $category);
$stmt->execute();
$category_result = $stmt->get_result();
$category_row = $category_result->fetch_assoc();

if (!$category_row) {
    die("Category not found: " . htmlspecialchars($category));
}

$category_id = $category_row['category_id'];
$category_name = htmlspecialchars($category);

// Fetch products - with optional type filter
if ($product_type) {
    // Filter by both category and type
    $products_query = "SELECT p.product_id, p.product_name, p.price, p.description 
                       FROM products p 
                       WHERE p.category_id = ? 
                       AND p.product_name LIKE ?
                       ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($products_query);
    
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    
    $like_pattern = "%" . $product_type . "%";
    $stmt->bind_param("is", $category_id, $like_pattern);
    $page_title = ucfirst($category) . "'s " . htmlspecialchars($product_type);
} else {
    // Show all products in category
    $products_query = "SELECT p.product_id, p.product_name, p.price, p.description 
                       FROM products p 
                       WHERE p.category_id = ? 
                       ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($products_query);
    
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    
    $stmt->bind_param("i", $category_id);
    $page_title = ucfirst($category) . " Products";
}

$stmt->execute();
$products_result = $stmt->get_result();

// Fetch all products to get images
$products = [];
while ($product = $products_result->fetch_assoc()) {
    $product_id = $product['product_id'];
    
    // Fetch all images for this product
    $image_query = "SELECT image_url, alt_text, is_primary FROM product_images 
                   WHERE product_id = ? 
                   ORDER BY is_primary DESC, display_order ASC";
    $img_stmt = $conn->prepare($image_query);
    
    if ($img_stmt) {
        $img_stmt->bind_param("i", $product_id);
        $img_stmt->execute();
        $image_result = $img_stmt->get_result();
        
        $images = [];
        while ($image = $image_result->fetch_assoc()) {
            $images[] = $image;
        }
        
        $product['images'] = $images;
    } else {
        $product['images'] = [];
    }
    
    $products[] = $product;
}

// To have multiple level for breadcrumb
$category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : '';
$type     = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fashion House - <?php echo htmlspecialchars($page_title); ?></title>
  <link rel="stylesheet" href="styles/header-footer.css">
  <link rel="stylesheet" href="styles/product-category.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .modal-backdrop {
      background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
      border: none;
      border-radius: 12px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 12px 12px 0 0;
    }

    .modal-title {
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .btn-close-white {
      filter: brightness(0) invert(1);
    }

    .quantity-group {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 20px 0;
    }

    .quantity-group label {
      font-weight: 600;
      color: #333;
    }

    .quantity-control {
      display: flex;
      align-items: center;
      gap: 5px;
      background-color: #f8f9fa;
      border-radius: 6px;
      padding: 5px;
    }

    .quantity-btn {
      width: 35px;
      height: 35px;
      border: none;
      background-color: white;
      color: #333;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .quantity-btn:hover {
      background-color: #667eea;
      color: white;
    }

    .quantity-input {
      width: 60px;
      text-align: center;
      border: none;
      background: transparent;
      font-weight: 600;
      font-size: 1rem;
    }

    .quantity-input:focus {
      outline: none;
    }

    .add-to-cart-btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      padding: 12px 30px;
      border-radius: 8px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      width: 100%;
      margin-top: 20px;
    }

    .add-to-cart-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .cancel-btn {
      background-color: #6c757d;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .cancel-btn:hover {
      background-color: #5a6268;
    }

    .toast-notification {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background-color: #28a745;
      color: white;
      padding: 15px 25px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      z-index: 9999;
      animation: slideIn 0.3s ease;
    }

    .toast-notification.error {
      background-color: #dc3545;
    }

    @keyframes slideIn {
      from {
        transform: translateX(400px);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
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
    <div class="content">
      <ul class="breadcrumb">
        <li><a href="home.php">Home</a></li>
        <li><a href="category.php">Categories</a></li>

        <?php if ($category): ?>
          <li>
            <a href="product-category.php?category=<?php echo urlencode($category); ?>">
              <?php echo $category; ?>
            </a>
          </li>
        <?php endif; ?>

        <?php if ($category && $type): ?>
          <li>
            <a href="product-category.php?category=<?php echo urlencode($category); ?>&type=<?php echo urlencode($type); ?>">
              <?php echo $type; ?>
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <div class="page-header">
        <h1 class="page-title"><?php echo htmlspecialchars($page_title); ?></h1>
        
        <?php if ($product_type) { ?>
        <div class="filter-info">
          <div class="filter-tag">
            <span style="font-weight: 600; color: var(--primary-color);">Filters:</span>
            <div class="tag">
              <?php echo htmlspecialchars($category_name); ?> - <?php echo htmlspecialchars($product_type); ?>
              <a href="product-category.php?category=<?php echo urlencode($category); ?>" class="tag-remove" title="Remove filter">✕</a>
            </div>
          </div>
          <div class="products-count"><?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?></div>
        </div>
        <?php } else { ?>
        <div class="filter-info">
          <span style="color: #6c757d;">Showing all <?php echo htmlspecialchars($category_name); ?> products</span>
          <div class="products-count"><?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?></div>
        </div>
        <?php } ?>
      </div>

      <div class="product-grid">
        <?php
        if (count($products) > 0) {
            foreach ($products as $product) {
                $product_id = $product['product_id'];
                $image = !empty($product['images']) ? $product['images'][0] : null;
                $image_url = $image ? $image['image_url'] : 'images/placeholder.jpg';
                $alt_text = $image ? $image['alt_text'] : $product['product_name'];
        ?>
        <a href="product.php?id=<?php echo $product_id; ?>" class="product-card-link">
          <div class="product-card">
            <div class="product-img-container">
              <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($alt_text); ?>">
            </div>
            <div class="product-info">
              <div>
                <p class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></p>
                <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
              </div>
              <div class="actions">
                <img src="icons/favorite_white.svg" class="favorite-btn" data-product-id="<?php echo $product_id; ?>" alt="Add to favorites" title="Add to favorites">
                <img src="icons/add_shopping_cart_white.svg" class="cart-btn" onclick="product.php?id=<?php echo $product_id; ?>">
              </div>
            </div>
          </div>
          </a>
        <?php
            }
        } else {
            echo "<div class='no-products'>No products found matching your filters. <br><a href='product-category.php?category=" . urlencode($category) . "' style='color: var(--primary-color); text-decoration: underline;'>View all " . htmlspecialchars($category_name) . " products</a></div>";
        }
        ?>
      </div>

      <?php if ($product_type) { ?>
      <a href="product-category.php?category=<?php echo urlencode($category); ?>" class="back-link">← View All <?php echo htmlspecialchars($category_name); ?> Products</a>
      <?php } ?>
    </div>
  </main>

  <!-- Add to Cart Modal
  <div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addToCartLabel">
            <i class="fas fa-shopping-cart"></i> Add to Cart
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <h6 id="productName" style="font-weight: 700; color: #333;"></h6>
          <p id="productPrice" style="font-size: 1.3rem; color: #667eea; font-weight: 700; margin: 10px 0;"></p>
          
          <div class="quantity-group">
            <label for="quantity">Quantity:</label>
            <div class="quantity-control">
              <button type="button" class="quantity-btn" id="decreaseBtn">−</button>
              <input type="number" class="quantity-input" id="quantityInput" value="1" min="1">
              <button type="button" class="quantity-btn" id="increaseBtn">+</button>
            </div>
          </div>

          <form id="addToCartForm" method="POST" action="add-to-cart.php">
            <input type="hidden" id="productIdInput" name="product_id">
            <input type="hidden" id="quantityFormInput" name="quantity" value="1">
            <button type="submit" class="add-to-cart-btn">
              <i class="fas fa-check-circle"></i> Add to Cart
            </button>
          </form>
        </div>
      </div>
    </div>
  </div> -->

  <footer>
    <?php include 'footer.inc'?>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Modal and quantity control
    const modal = new bootstrap.Modal(document.getElementById('addToCartModal'));
    const quantityInput = document.getElementById('quantityInput');
    const decreaseBtn = document.getElementById('decreaseBtn');
    const increaseBtn = document.getElementById('increaseBtn');
    const quantityFormInput = document.getElementById('quantityFormInput');

    decreaseBtn.addEventListener('click', function() {
      let value = parseInt(quantityInput.value);
      if (value > 1) {
        quantityInput.value = value - 1;
        quantityFormInput.value = value - 1;
      }
    });

    increaseBtn.addEventListener('click', function() {
      let value = parseInt(quantityInput.value);
      quantityInput.value = value + 1;
      quantityFormInput.value = value + 1;
    });

    quantityInput.addEventListener('change', function() {
      if (this.value < 1) this.value = 1;
      quantityFormInput.value = this.value;
    });

    // Handle favorite button click
    document.querySelectorAll('.favorite-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        if (!isUserLoggedIn()) {
          window.location.href = 'login.php';
        } else {
          console.log('Added product ' + productId + ' to favorites');
          showNotification('Added to favorites!', 'success');
        }
      });
    });

    // Handle add to cart button click
    document.querySelectorAll('.cart-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (!isUserLoggedIn()) {
          window.location.href = 'login.php';
          return;
        }

        const productId = this.dataset.productId;
        const productName = this.dataset.productName;
        const productPrice = this.dataset.productPrice;

        // Set modal values
        document.getElementById('productIdInput').value = productId;
        document.getElementById('productName').textContent = productName;
        document.getElementById('productPrice').textContent = '$' + parseFloat(productPrice).toFixed(2);
        document.getElementById('quantityInput').value = 1;
        document.getElementById('quantityFormInput').value = 1;

        // Show modal
        modal.show();
      });
    });

    // Handle form submission
    document.getElementById('addToCartForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('add-to-cart.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification(data.message, 'success');
          modal.hide();
        } else {
          showNotification(data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding to cart', 'error');
      });
    });

    // Show notification
    function showNotification(message, type = 'success') {
      const notification = document.createElement('div');
      notification.className = `toast-notification ${type === 'error' ? 'error' : ''}`;
      notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
      `;
      document.body.appendChild(notification);

      setTimeout(() => {
        notification.remove();
      }, 3000);
    }

    // Check if user is logged in
    function isUserLoggedIn() {
      return <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    }
  </script>
</body>
</html>