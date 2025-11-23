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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #333;
      --secondary: #555;
      --accent: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --light: #f8f9fa;
      --border: #dee2e6;
    }

    * {
      transition: all 0.3s ease;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .breadcrumb {
      list-style: none;
      padding: 0;
      margin-bottom: 30px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      font-size: 0.9rem;
    }

    .breadcrumb li {
      display: flex;
      align-items: center;
    }

    .breadcrumb li:not(:last-child)::after {
      content: '/';
      margin-left: 10px;
      color: #999;
    }

    .breadcrumb a {
      color: #667eea;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .breadcrumb a:hover {
      color: #764ba2;
      text-decoration: underline;
    }

    /* HERO SECTION */
    .hero {
      height: 100vh;
      background: linear-gradient(135deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.5) 100%), 
                  linear-gradient(135deg, #912bae 50%, #e23846  100%);
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: white;
      position: relative;
      overflow: hidden;
      margin-bottom: 0;
    }

    .hero::before {
      content: '';
      position: absolute;
      width: 400px;
      height: 400px;
      background: rgba(255,255,255,0.1);
      border-radius: 50%;
      top: -200px;
      right: -200px;
      animation: float 6s ease-in-out infinite;
    }

    .hero::after {
      content: '';
      position: absolute;
      width: 300px;
      height: 300px;
      background: rgba(255,255,255,0.05);
      border-radius: 50%;
      bottom: -150px;
      left: -150px;
      animation: float 8s ease-in-out infinite reverse;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(30px); }
    }

    .hero-content {
      position: relative;
      z-index: 2;
      max-width: 700px;
      animation: slideUp 1s ease-out;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .hero h1 {
      font-size: 4.5rem;
      font-weight: 800;
      margin-bottom: 20px;
      letter-spacing: -2px;
      text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }

    .hero p {
      font-size: 1.3rem;
      margin-bottom: 40px;
      opacity: 0.95;
      font-weight: 300;
    }

    .hero-buttons {
      display: flex;
      gap: 20px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn-hero {
      padding: 15px 40px;
      border: none;
      border-radius: 50px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 1rem;
      text-decoration: none;
      display: inline-block;
    }

    .btn-hero-primary {
      background: white;
      color: #ea66b1ff;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .btn-hero-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    }

    .btn-hero-secondary {
      background: transparent;
      color: white;
      border: 2px solid white;
    }

    .btn-hero-secondary:hover {
      background: white;
      color: #667eea;
    }

    /* CATEGORIES SECTION */
    .categories {
      padding: 80px 0;
      background: white;
    }

    .section-title {
      font-size: 3rem;
      font-weight: 800;
      text-align: center;
      color: var(--primary);
      margin-bottom: 60px;
      position: relative;
      display: inline-block;
      width: 100%;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: -20px;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 4px;
      background: var(--accent);
      border-radius: 2px;
    }

    .categories-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 40px;
    }

    .category-card {
      position: relative;
      height: 400px;
      border-radius: 16px;
      overflow: hidden;
      cursor: pointer;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      text-decoration: none;
      display: block;
    }

    .category-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }

    .category-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      z-index: -1;
    }

    .category-bg {
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      transition: transform 0.5s ease;
    }

    .category-card:hover .category-bg {
      transform: scale(1.1);
    }

    .category-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.7) 100%);
      display: flex;
      align-items: flex-end;
      padding: 30px;
      color: white;
      transition: all 0.3s ease;
    }

    .category-info h3 {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .category-info p {
      font-size: 0.95rem;
      opacity: 0.9;
    }

    /* FEATURED PRODUCTS SECTION */
    .featured {
      padding: 80px 0;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 30px;
    }

    .product-card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 5px 20px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
    }

    .product-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.12);
    }

    .product-image-container {
      position: relative;
      width: 100%;
      padding-bottom: 125%;
      overflow: hidden;
      background: #e9ecef;
    }

    .product-image {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .product-card:hover .product-image {
      transform: scale(1.08);
    }

    .product-info {
      padding: 20px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .product-category {
      font-size: 0.75rem;
      color: #667eea;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 8px;
    }

    .product-name {
      font-size: 1rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 10px;
    }

    .product-price {
      font-size: 1.3rem;
      font-weight: 800;
      background: var(--accent);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    /* CTA SECTION */
    .cta {
      padding: 100px 0;
      background: var(--accent);
      color: white;
      text-align: center;
    }

    .cta h2 {
      font-size: 2.8rem;
      font-weight: 800;
      margin-bottom: 20px;
    }

    .cta p {
      font-size: 1.2rem;
      margin-bottom: 40px;
      opacity: 0.95;
    }

    .cta-btn {
      background: white;
      color: #667eea;
      padding: 15px 50px;
      border: none;
      border-radius: 50px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .cta-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    }

    .content {
      padding: 0;
    }

    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.5rem;
      }

      .hero p {
        font-size: 1.1rem;
      }

      .section-title {
        font-size: 2rem;
      }

      .categories-grid,
      .products-grid {
        gap: 20px;
      }

      .category-card {
        height: 300px;
      }

      .cta h2 {
        font-size: 2rem;
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
    <!-- HERO SECTION -->
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

    <!-- CATEGORIES SECTION -->
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

    <!-- FEATURED PRODUCTS SECTION -->
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

    <!-- CTA SECTION -->
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