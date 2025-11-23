<?php
require_once("connect.php");
session_start();

/*
 * CATEGORY / GENDER HANDLING
 */

// Read from URL
$gender_param   = isset($_GET['gender'])   ? strtolower(trim($_GET['gender']))   : null;
$category_param = isset($_GET['category']) ? strtolower(trim($_GET['category'])) : null;
$product_type   = isset($_GET['type'])     ? trim($_GET['type']) : null;

// Decide category key (gender preferred)
if ($gender_param) {
    $category_key = $gender_param;
} elseif ($category_param) {
    $category_key = $category_param;
} else {
    $category_key = 'men'; // default
}

// Map gender/category → category_id
switch ($category_key) {

    case 'women':
        $category_id   = 2;
        $category_slug = 'women';
        $category_name = 'Women';
        break;

    default:
        $category_id   = 1;
        $category_slug = 'men';
        $category_name = 'Men';
        break;
}

// Build Page Title
$page_title = $product_type ? 
    $category_name . "'s " . htmlspecialchars($product_type)
    : $category_name . " Products";

/* 
 * FETCH PRODUCTS (restricted by category_id)
 */

// Bottoms-first ordering
$order_case = "
    CASE 
        WHEN LOWER(p.product_name) LIKE '%pants%' 
          OR LOWER(p.product_name) LIKE '%trousers%'
          OR LOWER(p.product_name) LIKE '%jeans%'
          OR LOWER(p.product_name) LIKE '%shorts%' 
        THEN 1
        ELSE 2
    END
";

// SQL: with or without type filter
if ($product_type) {

    $products_query = "
        SELECT p.product_id, p.product_name, p.price, p.description,
               $order_case AS item_order
        FROM products p
        WHERE p.category_id = ?
          AND LOWEr(p.product_name) LIKE ?
        ORDER BY item_order ASC, p.product_name ASC
    ";

    $stmt = $conn->prepare($products_query);
    $like_pattern = "%" . strtolower($product_type) . "%";
    $stmt->bind_param("is", $category_id, $like_pattern);

} else {

    $products_query = "
        SELECT p.product_id, p.product_name, p.price, p.description,
               $order_case AS item_order
        FROM products p
        WHERE p.category_id = ?
        ORDER BY item_order ASC, p.product_name ASC
    ";

    $stmt = $conn->prepare($products_query);
    $stmt->bind_param("i", $category_id);
}

$stmt->execute();
$products_result = $stmt->get_result();

// Attach images to products
$products = [];
while ($product = $products_result->fetch_assoc()) {

    $product_id = $product['product_id'];

    $image_query = "
        SELECT image_url, alt_text, is_primary
        FROM product_images
        WHERE product_id = ?
        ORDER BY is_primary DESC, display_order ASC
    ";

    $img_stmt = $conn->prepare($image_query);
    $img_stmt->bind_param("i", $product_id);
    $img_stmt->execute();
    $image_result = $img_stmt->get_result();

    $images = [];
    while ($img = $image_result->fetch_assoc()) { 
        $images[] = $img;
    }

    $product['images'] = $images;
    $products[] = $product;
}

$breadcrumb_category = htmlspecialchars($category_slug);
$breadcrumb_type     = $product_type ? htmlspecialchars($product_type) : '';
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

  <style>
    /* Make whole card clickable */
    .product-card-link { text-decoration:none; color:inherit; }
    .product-card:hover { transform:scale(1.02); transition:.2s; }
  </style>
</head>

<body>
<header>
    <?php include 'header.inc'; ?>
</header>

<main>
<div class="content">

    <!-- Breadcrumb -->
    <ul class="breadcrumb">
      <li><a href="home.php">Home</a></li>
      <li><a href="category.php">Categories</a></li>

      <li><a href="category.php?gender=<?php echo $breadcrumb_category; ?>">
        <?php echo ucfirst($breadcrumb_category); ?>
      </a></li>

      <?php if ($breadcrumb_type): ?>
        <li><a href="category.php?gender=<?php echo $breadcrumb_category; ?>&type=<?php echo $breadcrumb_type; ?>">
          <?php echo $breadcrumb_type; ?>
        </a></li>
      <?php endif; ?>
    </ul>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title"><?php echo htmlspecialchars($page_title); ?></h1>

        <div class="filter-info">
            <?php if ($product_type): ?>
                <span style="font-weight:600;color:var(--primary-color);">Filters:</span>
                <div class="tag">
                    <?php echo $category_name . " - " . htmlspecialchars($product_type); ?>
                    <a href="category.php?gender=<?php echo $category_slug; ?>" class="tag-remove">✕</a>
                </div>
            <?php else: ?>
                <span style="color:#6c757d;">Showing all <?php echo $category_name; ?> products</span>
            <?php endif; ?>

            <div class="products-count">
                <?php echo count($products); ?> product<?php echo count($products)==1?'':'s'; ?>
            </div>
        </div>
    </div>

    <!-- Product Grid -->
    <div class="product-grid">
    <?php
      if (count($products) > 0) {
        foreach ($products as $product) {

          $product_id = $product['product_id'];

          $image = !empty($product['images']) ? $product['images'][0] : null;
          $image_url = $image ? $image['image_url'] : "images/placeholder.jpg";
          $alt       = $image ? $image['alt_text'] : $product['product_name'];
    ?>

    <!-- CLICKABLE PRODUCT CARD -->
    <a href="product.php?id=<?php echo $product_id; ?>" class="product-card-link">
    <div class="product-card">

      <div class="product-img-container">
        <img src="<?php echo htmlspecialchars($image_url); ?>"
             alt="<?php echo htmlspecialchars($alt); ?>">
      </div>

      <div class="product-info">
        <p class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></p>
        <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
      </div>

    </div>
    </a>

    <?php }
      } else {
          echo "
            <div class='no-products'>
              No products found.<br>
              <a href='category.php?gender=$category_slug' style='color:var(--primary-color);text-decoration:underline;'>
                View all $category_name products
              </a>
            </div>";
      }
    ?>
    </div>

</div>
</main>

<footer>
    <?php include 'footer.inc'; ?>
</footer>

</body>
</html>
