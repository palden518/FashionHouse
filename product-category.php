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
  <link rel="stylesheet" href="styles/product-category.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
        <li><a href="product-category.php">Categories</a></li>
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
  
  <script type="text/javascript">
      // Pass the Cart Count
      window.initialCartCount = <?php echo (int)$cart_count; ?>;
      
      // Pass the Login Status
      window.userIsLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
  </script>

  <script src="scripts/product-category.js"></script>
</body>
</html>
</body>
</html>