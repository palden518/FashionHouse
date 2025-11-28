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
  <link rel="stylesheet" href="styles/category1.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 
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
        <li><a href="product-category.php">Categories</a></li>

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
  <footer>
    <?php include 'footer.inc'?>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript">
      window.userIsLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
  </script>
  <script src="category_modal_quantites.js"></script>
</body>
</html>