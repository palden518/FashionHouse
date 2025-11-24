<?php
require_once("connect.php");
session_start();

// Get featured products
$featured_query = "SELECT p.product_id, p.product_name, p.price, c.category_name, pi.image_url, pi.alt_text
                   FROM products p
                   JOIN categories c ON p.category_id = c.category_id
                   LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = TRUE
                   ORDER BY RAND()
                   LIMIT 8";
$featured_result = $conn->query($featured_query);
$featured_products = [];
while ($product = $featured_result->fetch_assoc()) {
    $featured_products[] = $product;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fashion House - Home</title>
  <link rel="stylesheet" href="styles/header-footer.css">
  <link rel="stylesheet" href="styles/home.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <header>
    <?php include 'header.inc'?>
  </header>
  <script src="./scripts/dropdownscript.js"></script>

  <main>
    
    <section class="hero">
      <div class="hero-content">
        <h1>Fashion Redefined</h1>
        <p>Discover premium quality clothing and accessories for every occasion</p>
        <div class="hero-buttons">
          <a href="product-category.php" class="btn-hero btn-hero-primary">Shop Now</a>
          <a href="#featured" class="btn-hero btn-hero-secondary">Learn More</a>
        </div>
      </div>
    </section>


    <section class="categories">
      <div class="content">
        <h2 class="section-title">Shop by Category</h2>
        <div class="categories-grid">
          <a href="category.php?category=Men" class="category-card">
            <div class="category-bg" style="background-image: linear-gradient(135deg, #5d1463ff 0%, #764ba2 100%);"></div>
            <div class="category-overlay">
              <div class="category-info">
                <h3>Men's Collection</h3>
                <p>Premium style for modern gentlemen</p>
              </div>
            </div>
          </a>

          <a href="category.php?category=Women" class="category-card">
            <div class="category-bg" style="background-image: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"></div>
            <div class="category-overlay">
              <div class="category-info">
                <h3>Women's Collection</h3>
                <p>Elegance meets comfort</p>
              </div>
            </div>
          </a>
        </div>
      </div>
    </section>

   
    <section class="featured" id="featured">
      <div class="content">
        <h2 class="section-title">Featured Products</h2>
        <div class="products-grid">
          <?php foreach ($featured_products as $product) { 
            $image_url = $product['image_url'] ? $product['image_url'] : 'images/placeholder.jpg';
          ?>
          <div class="product-card">
            <div class="product-image-container">
              <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($product['alt_text'] ?? $product['product_name']); ?>" class="product-image">
            </div>
            <div class="product-info">
              <div>
                <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                <div class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
              </div>
              <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </section>


    <section class="cta">
      <div class="content">
        <h2>Ready to Upgrade Your Style?</h2>
        <p>Explore our complete collection of premium fashion and accessories</p>
        <a href="product-category.php" class="cta-btn">Browse All Categories</a>
      </div>
    </section>
  </main>

  <footer>
    <?php include 'footer.inc'?>
  </footer>
  
</body>
</html>