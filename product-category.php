<?php
require_once("connect.php");
session_start();

// Get cart count for logged in users
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $count_query = "SELECT COUNT(*) as count FROM shopping_cart WHERE user_id = ?";
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bind_param("i", $_SESSION['user_id']);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_row = $count_result->fetch_assoc();
    $cart_count = $count_row['count'];
}

// Fetch all categories from database
$categories_query = "SELECT category_id, category_name FROM categories ORDER BY category_name ASC";
$categories_result = $conn->query($categories_query);

// Function to get subcategories/product types for a category
function getSubcategoriesForCategory($conn, $category_id) {
    // Get all products for this category
    $query = "SELECT p.product_id, p.product_name, p.price, pi.image_url, pi.alt_text
              FROM products p
              LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = TRUE
              WHERE p.category_id = ?
              ORDER BY p.product_name ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subcategories = [];
    $seen = [];
    
    while ($row = $result->fetch_assoc()) {
        // Extract product type - take all words except first one
        $name_parts = explode(' ', $row['product_name']);
        if (count($name_parts) > 1) {
            array_shift($name_parts); // Remove first word
            $subcategory = implode(' ', $name_parts);
        } else {
            $subcategory = $row['product_name'];
        }
        
        // Only add if we haven't seen this type before
        if (!isset($seen[$subcategory])) {
            $seen[$subcategory] = true;
            $subcategories[] = [
                'subcategory' => $subcategory,
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'price' => $row['price'],
                'image_url' => $row['image_url'],
                'alt_text' => $row['alt_text']
            ];
        }
    }
    
    return $subcategories;
}

// Get subcategories for Men and Women
$mens_id = 1;
$womens_id = 2;
$mens_subcategories = getSubcategoriesForCategory($conn, $mens_id);
$womens_subcategories = getSubcategoriesForCategory($conn, $womens_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fashion House - Categories</title>
  <link rel="stylesheet" href="styles/header-footer.css">
  <link rel="stylesheet" href="styles/category.css">
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
    <?php include 'header.inc'?>
  </header>
  <script src="./scripts/dropdownscript.js"></script>
  <main>
    <div class="content">
      <ul class="breadcrumb">
        <li><a href="home.php">Home</a></li>
        <li><a href="category.php">Categories</a></li>
      </ul>
      <?php
      // Array of category IDs and their details
      $categories_data = [
          ['id' => 1, 'name' => 'Men', 'link' => 'category.php?category=Men'],
          ['id' => 2, 'name' => 'Women', 'link' => 'category.php?category=Women']
      ];

      foreach ($categories_data as $category) {
          $subcategories = getSubcategoriesForCategory($conn, $category['id']);
      ?>

      <div class="category-section">
        <h2 class="category-title">
          <a href="<?php echo $category['link']; ?>"><?php echo $category['name']; ?></a>
        </h2>

        <?php if (count($subcategories) > 0) { ?>
          <div class="carousel-container">
            <div id="carousel-<?php echo $category['id']; ?>" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                <?php
                $chunks = array_chunk($subcategories, 4);
                foreach ($chunks as $index => $chunk) {
                ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                  <div class="subcategory-grid">
                    <?php
                    foreach ($chunk as $sub) {
                        $image_url = $sub['image_url'] ? $sub['image_url'] : 'images/placeholder.jpg';
                        $alt_text = $sub['alt_text'] ? $sub['alt_text'] : $sub['subcategory'];
                    ?>
                    <div class="subcategory-item">
                      <a href="category.php?category=<?php echo urlencode($category['name']); ?>" style="display: block;">
                        <div class="subcategory-img-container">
                          <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($alt_text); ?>">
                        </div>
                        <h3 class="subcategory-name"><?php echo htmlspecialchars($sub['subcategory']); ?></h3>
                      </a>
                    </div>
                    <?php
                    }
                    ?>
                  </div>
                </div>
                <?php
                }
                ?>
              </div>

              <?php if (count($chunks) > 1) { ?>
              <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $category['id']; ?>" data-bs-slide="prev">
                <span aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $category['id']; ?>" data-bs-slide="next">
                <span aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
              </button>
              <?php } ?>
            </div>
          </div>
        <?php } else { ?>
          <div class="no-products">No subcategories available for <?php echo $category['name']; ?></div>
        <?php } ?>
      </div>

      <?php
      }
      ?>
    </div>
  </main>

  
  <footer>
    <?php include 'footer.inc'?>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let currentCartCount = <?php echo $cart_count; ?>;

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

    // Add hover effects to quantity buttons
    decreaseBtn.addEventListener('mouseover', function() {
      this.style.transform = 'scale(1.1)';
      this.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.4)';
    });
    decreaseBtn.addEventListener('mouseout', function() {
      this.style.transform = 'scale(1)';
      this.style.boxShadow = 'none';
    });

    increaseBtn.addEventListener('mouseover', function() {
      this.style.transform = 'scale(1.1)';
      this.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.4)';
    });
    increaseBtn.addEventListener('mouseout', function() {
      this.style.transform = 'scale(1)';
      this.style.boxShadow = 'none';
    });

    function openCartModal(productId, productName, productPrice) {
      if (!isUserLoggedIn()) {
        window.location.href = 'login.php';
        return;
      }

      document.getElementById('productIdInput').value = productId;
      document.getElementById('productName').textContent = productName;
      document.getElementById('productPrice').textContent = '$' + parseFloat(productPrice).toFixed(2);
      document.getElementById('quantityInput').value = 1;
      document.getElementById('quantityFormInput').value = 1;

      modal.show();
    }

    document.getElementById('addToCartForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('add-to-cart.php', {
        method: 'POST',
        body: formData
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        console.log('Response from add-to-cart.php:', data);
        
        if (data.success) {
          // Update cart count
          if (data.cart_count !== undefined) {
            currentCartCount = parseInt(data.cart_count);
            console.log('Cart count updated to:', currentCartCount);
            updateCartBadge();
          }
          
          showNotification(data.message, 'success');
          modal.hide();
          
          // Reset form
          document.getElementById('quantityInput').value = 1;
          document.getElementById('quantityFormInput').value = 1;
        } else {
          showNotification(data.message || 'Error adding to cart', 'error');
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        showNotification('Error adding to cart. Please try again.', 'error');
      });
    });

    function showNotification(message, type = 'success') {
      const notification = document.createElement('div');
      notification.className = `toast-notification ${type === 'error' ? 'error' : ''}`;
      notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
      `;
      document.body.appendChild(notification);

      setTimeout(() => {
        notification.style.animation = 'slideIn 0.4s reverse';
        setTimeout(() => {
          notification.remove();
        }, 400);
      }, 3500);
    }

    function updateCartBadge() {
      // Try multiple ways to find the cart icon
      let cartIcon = document.querySelector('a[href*="cart"]');
      
      if (!cartIcon) {
        cartIcon = document.querySelector('.cart-link');
      }
      
      if (!cartIcon) {
        cartIcon = document.querySelector('[class*="cart"]');
      }
      
      if (cartIcon) {
        // Make sure icon has relative positioning
        if (cartIcon.style.position !== 'relative') {
          cartIcon.style.position = 'relative';
        }
        
        // Remove old badge
        const oldBadge = cartIcon.querySelector('.cart-badge');
        if (oldBadge) {
          oldBadge.remove();
        }
        
        // Create new badge if count > 0
        if (currentCartCount > 0) {
          const badge = document.createElement('span');
          badge.className = 'cart-badge';
          badge.textContent = currentCartCount;
          badge.style.cssText = `
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            animation: pop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
            z-index: 99;
          `;
          cartIcon.appendChild(badge);
          console.log('Badge created/updated with count:', currentCartCount);
        }
      } else {
        console.warn('Cart icon element not found in header');
      }
    }

    function isUserLoggedIn() {
      return <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    }

    // Initialize cart badge on page load
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Page loaded, cart count:', currentCartCount);
      if (currentCartCount > 0) {
        setTimeout(() => {
          updateCartBadge();
        }, 100);
      }
    });

    // Also try updating when page is fully loaded
    window.addEventListener('load', function() {
      if (currentCartCount > 0) {
        updateCartBadge();
      }
    });

  </script>
</body>
</html>