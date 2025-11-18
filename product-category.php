<?php
require_once("connect.php");
session_start();

// Function to get products and images for a category
function getProductsByCategory($conn, $category_name) {
    $query = "SELECT p.product_id, p.product_name, p.price, p.description, c.category_id
              FROM products p
              JOIN categories c ON p.category_id = c.category_id
              WHERE LOWER(c.category_name) = LOWER(?)
              ORDER BY p.created_at DESC
              LIMIT 6";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($product = $result->fetch_assoc()) {
        // Fetch all images for this product
        $image_query = "SELECT image_url, alt_text, is_primary FROM product_images 
                       WHERE product_id = ? 
                       ORDER BY is_primary DESC, display_order ASC";
        $img_stmt = $conn->prepare($image_query);
        $img_stmt->bind_param("i", $product['product_id']);
        $img_stmt->execute();
        $image_result = $img_stmt->get_result();
        
        $images = [];
        while ($image = $image_result->fetch_assoc()) {
            $images[] = $image;
        }
        
        $product['images'] = $images;
        $products[] = $product;
    }
    
    return $products;
}

// Get Men's and Women's products
$mens_products = getProductsByCategory($conn, 'Men');
$womens_products = getProductsByCategory($conn, 'Women');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fashion House - Shop Men & Women</title>
  <link rel="stylesheet" href="styles/header-footer.css">
  <link rel="stylesheet" href="styles/product-category.css">
  <style>
    .category-section {
      margin: 60px 0;
    }
    
    .category-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 40px;
    }
    
    .category-header h2 {
      font-size: 32px;
      font-weight: bold;
      text-transform: uppercase;
    }
    
    .view-all-btn {
      padding: 10px 20px;
      background-color: #333;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      transition: background-color 0.3s;
    }
    
    .view-all-btn:hover {
      background-color: #555;
    }
  </style>
</head>
<body>
  <header>
    <?php include 'header.inc'; ?>
  </header>

  <main>
    <div class="content">
      <?php include 'breadcrumb.inc'?>

      <!-- MEN'S SECTION -->
      <section class="category-section">
        <div class="category-header">
          <h2>Men's Collection</h2>
          <a href="product-category.php?category=men" class="view-all-btn">View All</a>
        </div>
        
        <div class="product-grid">
          <?php
          if (count($mens_products) > 0) {
              foreach ($mens_products as $product) {
                  $product_id = $product['product_id'];
                  $image = !empty($product['images']) ? $product['images'][0] : null;
                  $image_url = $image ? $image['image_url'] : 'images/placeholder.jpg';
                  $alt_text = $image ? $image['alt_text'] : $product['product_name'];
          ?>
          <div class="product-card">
            <div class="product-img">
              <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($alt_text); ?>">
            </div>
            <div class="product-info">
              <p class="name"><?php echo htmlspecialchars($product['product_name']); ?></p>
              <p class="price">$ <?php echo number_format($product['price'], 2); ?></p>
              <div class="actions">
                <img src="icons/favorite_white.svg" class="favorite-btn" data-product-id="<?php echo $product_id; ?>" alt="Add to favorites">
                <img src="icons/add_shopping_cart_white.svg" class="cart-btn" data-product-id="<?php echo $product_id; ?>" alt="Add to cart">
              </div>
            </div>
          </div>
          <?php
              }
          } else {
              echo "<p>No products found in Men's collection.</p>";
          }
          ?>
        </div>
      </section>

      <!-- WOMEN'S SECTION -->
      <section class="category-section">
        <div class="category-header">
          <h2>Women's Collection</h2>
          <a href="product-category.php?category=women" class="view-all-btn">View All</a>
        </div>
        
        <div class="product-grid">
          <?php
          if (count($womens_products) > 0) {
              foreach ($womens_products as $product) {
                  $product_id = $product['product_id'];
                  $image = !empty($product['images']) ? $product['images'][0] : null;
                  $image_url = $image ? $image['image_url'] : 'images/placeholder.jpg';
                  $alt_text = $image ? $image['alt_text'] : $product['product_name'];
          ?>
          <div class="product-card">
            <div class="product-img">
              <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($alt_text); ?>">
            </div>
            <div class="product-info">
              <p class="name"><?php echo htmlspecialchars($product['product_name']); ?></p>
              <p class="price">$ <?php echo number_format($product['price'], 2); ?></p>
              <div class="actions">
                <img src="icons/favorite_white.svg" class="favorite-btn" data-product-id="<?php echo $product_id; ?>" alt="Add to favorites">
                <img src="icons/add_shopping_cart_white.svg" class="cart-btn" data-product-id="<?php echo $product_id; ?>" alt="Add to cart">
              </div>
            </div>
          </div>
          <?php
              }
          } else {
              echo "<p>No products found in Women's collection.</p>";
          }
          ?>
        </div>
      </section>

    </div>
  </main>

  <footer>
    <?php include 'footer.inc'?>
  </footer>

  <script>
    // Handle favorite button click
    document.querySelectorAll('.favorite-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        if (!isUserLoggedIn()) {
          window.location.href = 'login.php';
        } else {
          console.log('Added product ' + productId + ' to favorites');
          // Add to favorites logic here
        }
      });
    });

    // Handle add to cart button click
    document.querySelectorAll('.cart-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        window.location.href = 'product-detail.php?product_id=' + productId;
      });
    });

    // Check if user is logged in
    function isUserLoggedIn() {
      return <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    }
  </script>
</body>
</html>