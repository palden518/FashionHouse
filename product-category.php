<?php
require_once("connect.php");
session_start();

// Get category and type from URL parameters
$category = isset($_GET['category']) ? $_GET['category'] : 'men';
$product_type = isset($_GET['type']) ? $_GET['type'] : null;

// Fetch category ID based on category name
$category_query = "SELECT category_id FROM categories WHERE LOWER(category_name) = LOWER(?)";
$stmt = $conn->prepare($category_query);
$stmt->bind_param("s", $category);
$stmt->execute();
$category_result = $stmt->get_result();
$category_row = $category_result->fetch_assoc();

if (!$category_row) {
    die("Category not found");
}

$category_id = $category_row['category_id'];
$category_name = ucfirst($category);

// Fetch products - with optional type filter
if ($product_type) {
    // Filter by both category and type
    $products_query = "SELECT p.product_id, p.product_name, p.price, p.description 
                       FROM products p 
                       WHERE p.category_id = ? 
                       AND p.product_name LIKE ?
                       ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($products_query);
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
    $img_stmt->bind_param("i", $product_id);
    $img_stmt->execute();
    $image_result = $img_stmt->get_result();
    
    $images = [];
    while ($image = $image_result->fetch_assoc()) {
        $images[] = $image;
    }
    
    $product['images'] = $images;
    $products[] = $product;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fashion House - <?php echo htmlspecialchars($page_title); ?></title>
  <link rel="stylesheet" href="styles/header-footer.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #333;
      --secondary-color: #555;
      --light-bg: #f8f9fa;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: white;
    }

    .content {
      max-width: 1400px;
      margin: 0 auto;
      padding: 40px 20px;
    }

    .page-header {
      margin-bottom: 50px;
    }

    .breadcrumb-nav {
      margin-bottom: 30px;
      font-size: 0.95rem;
    }

    .breadcrumb-nav a {
      color: var(--secondary-color);
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .breadcrumb-nav a:hover {
      color: var(--primary-color);
    }

    .page-title {
      font-size: 2.5rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--primary-color);
      margin-bottom: 30px;
    }

    .filter-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      background-color: var(--light-bg);
      border-radius: 8px;
      margin-bottom: 40px;
    }

    .filter-tag {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
    }

    .tag {
      background-color: var(--primary-color);
      color: white;
      padding: 8px 15px;
      border-radius: 20px;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .tag-remove {
      cursor: pointer;
      font-weight: bold;
      transition: transform 0.2s ease;
    }

    .tag-remove:hover {
      transform: scale(1.2);
    }

    .products-count {
      color: #6c757d;
      font-size: 0.95rem;
    }

    .product-card {
      transition: all 0.3s ease;
      border: none;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .product-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .product-img-container {
      position: relative;
      width: 100%;
      padding-bottom: 125%;
      overflow: hidden;
      background-color: #e9ecef;
    }

    .product-img-container img {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .product-card:hover .product-img-container img {
      transform: scale(1.05);
    }

    .product-info {
      padding: 20px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .product-name {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--primary-color);
      margin-bottom: 10px;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
    }

    .product-price {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--secondary-color);
      margin-bottom: 15px;
    }

    .actions {
      display: flex;
      gap: 15px;
      justify-content: flex-end;
    }

    .actions img {
      width: 28px;
      height: 28px;
      cursor: pointer;
      transition: transform 0.3s ease, filter 0.3s ease;
      filter: brightness(0.8);
    }

    .actions img:hover {
      transform: scale(1.2);
      filter: brightness(1);
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 30px;
    }

    .no-products {
      padding: 60px 40px;
      text-align: center;
      color: #6c757d;
      font-size: 1.2rem;
      grid-column: 1 / -1;
      background-color: var(--light-bg);
      border-radius: 8px;
    }

    .back-link {
      display: inline-block;
      margin-top: 30px;
      padding: 12px 25px;
      background-color: var(--primary-color);
      color: white;
      text-decoration: none;
      border-radius: 4px;
      transition: all 0.3s ease;
    }

    .back-link:hover {
      background-color: var(--secondary-color);
      transform: translateY(-2px);
    }

    @media (max-width: 992px) {
      .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
      }

      .page-title {
        font-size: 2rem;
      }

      .filter-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }
    }

    @media (max-width: 576px) {
      .content {
        padding: 20px 10px;
      }

      .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
      }

      .page-title {
        font-size: 1.8rem;
      }

      .filter-info {
        padding: 15px;
      }

      .tag {
        padding: 6px 12px;
        font-size: 0.85rem;
      }
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
              <img src="icons/add_shopping_cart_white.svg" class="cart-btn" data-product-id="<?php echo $product_id; ?>" alt="Add to cart" title="Add to cart">
            </div>
          </div>
        </div>
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

  <footer>
    <?php include 'footer.inc'?>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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