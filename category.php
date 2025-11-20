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
  <link rel="stylesheet" href="styles/category.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
</head>
<body>
  <header>
    <?php include 'header.inc'?>
  </header>

  <main>
    <div class="content">
      <ul class="breadcrumb">
        <li><a href="home.php">Home</a></li>
        <li><a href="category.php">Categories</a></li>
      </ul>
      <h1 class="page-title">CATEGORIES</h1>

      <?php
      // Array of category IDs and their details
      $categories_data = [
          ['id' => 1, 'name' => 'Men', 'link' => 'product-category.php?category=Men'],
          ['id' => 2, 'name' => 'Women', 'link' => 'product-category.php?category=Women']
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