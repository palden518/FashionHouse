<?php
require_once("connect.php");
session_start();

// Fetch all categories from database
$categories_query = "SELECT category_id, category_name FROM categories ORDER BY category_name ASC";
$categories_result = $conn->query($categories_query);

// Function to get subcategories/product types for a category
function getSubcategoriesForCategory($conn, $category_id) {
    // Get distinct product names or types for a category (assuming product_name contains the type)
    $query = "SELECT DISTINCT 
              SUBSTRING_INDEX(p.product_name, ' ', -1) as subcategory,
              p.product_id,
              pi.image_url,
              pi.alt_text
              FROM products p
              LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = TRUE
              WHERE p.category_id = ?
              GROUP BY SUBSTRING_INDEX(p.product_name, ' ', -1)
              ORDER BY p.product_name ASC
              LIMIT 8";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subcategories = [];
    while ($row = $result->fetch_assoc()) {
        $subcategories[] = $row;
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #333;
      --secondary-color: #555;
      --light-bg: #f8f9fa;
      --border-color: #dee2e6;
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

    .page-title {
      font-size: 2.5rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--primary-color);
      margin-bottom: 60px;
      text-align: center;
    }

    .category-section {
      margin-bottom: 80px;
    }

    .category-title {
      font-size: 2rem;
      font-weight: 700;
      text-transform: uppercase;
      color: var(--primary-color);
      margin-bottom: 30px;
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .category-title a {
      text-decoration: none;
      color: var(--primary-color);
      transition: color 0.3s ease;
      border-bottom: 3px solid transparent;
      padding-bottom: 5px;
    }

    .category-title a:hover {
      color: var(--secondary-color);
      border-bottom-color: var(--secondary-color);
    }

    .carousel-container {
      position: relative;
      padding: 20px 0;
    }

    .carousel-inner {
      background-color: var(--light-bg);
      border-radius: 8px;
      overflow: hidden;
    }

    .carousel-item {
      padding: 20px;
    }

    .subcategory-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 20px;
    }

    .subcategory-item {
      position: relative;
      text-align: center;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .subcategory-item a {
      text-decoration: none;
      color: inherit;
    }

    .subcategory-img-container {
      position: relative;
      width: 100%;
      padding-bottom: 100%;
      overflow: hidden;
      border-radius: 8px;
      background-color: #e9ecef;
      margin-bottom: 15px;
    }

    .subcategory-img-container img {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .subcategory-item:hover .subcategory-img-container img {
      transform: scale(1.1);
    }

    .subcategory-name {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--primary-color);
      transition: color 0.3s ease;
    }

    .subcategory-item:hover .subcategory-name {
      color: var(--secondary-color);
    }

    .carousel-control-prev,
    .carousel-control-next {
      width: auto;
      height: auto;
      top: 50%;
      transform: translateY(-50%);
      background-color: transparent;
      border: none;
      font-size: 2rem;
      color: var(--primary-color);
      opacity: 0.7;
      transition: all 0.3s ease;
      z-index: 10;
    }

    .carousel-control-prev:hover,
    .carousel-control-next:hover {
      opacity: 1;
      transform: translateY(-50%) scale(1.2);
    }

    .carousel-control-prev {
      left: -50px;
    }

    .carousel-control-next {
      right: -50px;
    }

    .no-products {
      padding: 40px;
      text-align: center;
      color: #6c757d;
      font-size: 1.1rem;
      background-color: var(--light-bg);
      border-radius: 8px;
    }

    @media (max-width: 768px) {
      .page-title {
        font-size: 2rem;
      }

      .category-title {
        font-size: 1.5rem;
      }

      .subcategory-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
      }

      .carousel-control-prev {
        left: 0;
      }

      .carousel-control-next {
        right: 0;
      }

      .carousel-item {
        padding: 10px;
      }
    }

    @media (max-width: 576px) {
      .content {
        padding: 20px 10px;
      }

      .page-title {
        font-size: 1.5rem;
        margin-bottom: 40px;
      }

      .category-title {
        font-size: 1.3rem;
        margin-bottom: 20px;
      }

      .subcategory-grid {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 12px;
      }

      .subcategory-name {
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <?php include 'header.inc'?>
  </header>

  <main>
    <div class="content">
      <?php include 'breadcrumb.inc'?>
      <h1 class="page-title">CATEGORIES</h1>

      <?php
      // Array of category IDs and their details
      $categories_data = [
          ['id' => 1, 'name' => 'Men', 'link' => 'subcategory-men.php'],
          ['id' => 2, 'name' => 'Women', 'link' => 'subcategory-women.php']
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
                      <a href="product-category.php?category=<?php echo urlencode($category['name']); ?>&type=<?php echo urlencode($sub['subcategory']); ?>">
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
                <span aria-hidden="true">&larr;</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $category['id']; ?>" data-bs-slide="next">
                <span aria-hidden="true">&rarr;</span>
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

 
</body>
</html>