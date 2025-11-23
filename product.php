<?php
// product.php

require_once "connect.php";

// 1. Get product_id from URL: product.php?id=15
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// If no valid ID → product not found
if ($product_id <= 0) {
    $product = null;
} else {
    // 2. Fetch product using product_id
    $sql = "SELECT 
                product_id,
                product_name,
                price,
                description,
                category_id,
                stock_quantity
            FROM products
            WHERE product_id = ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

// 3. Load images
$product_images = [];
if ($product) {
    $img_sql = "SELECT image_url, alt_text 
                FROM product_images 
                WHERE product_id = ? 
                ORDER BY is_primary DESC, display_order ASC";

    $stmt = $conn->prepare($img_sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $img_result = $stmt->get_result();

    while ($row = $img_result->fetch_assoc()) {
        $product_images[] = $row;
    }

    $stmt->close();
}

// 4. Load reviews
$reviews = null;
if ($product) {
    $reviews_sql = "SELECT name, rating, review, created_at 
                    FROM product_reviews 
                    WHERE product_id = ? 
                    ORDER BY created_at DESC";

    $stmt = $conn->prepare($reviews_sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $reviews = $stmt->get_result();
    $stmt->close();
}

// Category name (Men=1, Women=2)
$genderName = ($product && $product['category_id'] == 1) ? "Men" : "Women";

// 5. Define color + size options
$colors = [];
$sizes  = [];

if ($product) {
    if ($product['category_id'] == 1) {
        $colors = ['White', 'Black', 'Charcoal', 'Navy'];
        $sizes  = ['S', 'M', 'L', 'XL'];
    } else {
        $colors = ['Black', 'White', 'Blush', 'Olive'];
        $sizes  = ['S', 'M', 'L', 'XL'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>
        <?php echo $product ? htmlspecialchars($product['product_name']) . " - Fashion House" : "Product Not Found"; ?>
    </title>

    <!-- ADD HEADER-FOOTER CSS -->
    <link rel="stylesheet" href="styles/header-footer.css">
    <link rel="stylesheet" href="styles/product.css">
</head>

<body>

<header>
    <?php include 'header.inc'; ?>
</header>

<main>
    <div class="container">

        <?php if (!$product): ?>
            <h2>Product Not Found</h2>
            <p>This product does not exist.</p>
            <a href="category.php?gender=men">Back to shop</a>

        <?php else: ?>

            <!-- Breadcrumb -->
            <div class="breadcrumb" style="margin-bottom:15px;">
                <a href="home.php">Home</a> ›
                <a href="category.php">Categories</a> ›
                <a href="category.php?gender=<?php echo strtolower($genderName); ?>">
                    <?php echo $genderName; ?>
                </a> ›
                <span><?php echo htmlspecialchars($product['product_name']); ?></span>
            </div>

            <div class="product-layout">
                <!-- Image -->
                <div class="product-image">
                    <?php if (!empty($product_images)): ?>
                        <img src="<?php echo htmlspecialchars($product_images[0]['image_url']); ?>"
                             alt="<?php echo htmlspecialchars($product_images[0]['alt_text']); ?>">
                    <?php else: ?>
                        <img src="images/placeholder.jpg" alt="No image">
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <div class="product-info">
                    <div class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></div>
                    <div class="price">$<?php echo number_format($product['price'], 2); ?></div>
                    <div class="badge">
                        <?php echo ($product['stock_quantity'] > 0) ? "In Stock" : "Out of Stock"; ?>
                    </div>

                    <form method="post" action="cart.php" style="margin-top:20px;">

                        <?php if (!empty($colors)): ?>
                            <div class="section-title">Color</div>
                            <div class="options-row">
                                <?php foreach ($colors as $index => $color): ?>
                                    <label>
                                        <input type="radio" name="color" value="<?php echo $color; ?>"
                                               style="display:none;" <?php echo $index === 0 ? "checked" : ""; ?>>
                                        <span class="pill-btn <?php echo $index === 0 ? "selected" : ""; ?>">
                                            <?php echo $color; ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($sizes)): ?>
                            <div class="section-title">Size</div>
                            <div class="options-row">
                                <?php foreach ($sizes as $index => $size): ?>
                                    <label>
                                        <input type="radio" name="size" value="<?php echo $size; ?>"
                                               style="display:none;" <?php echo $index === 0 ? "checked" : ""; ?>>
                                        <span class="pill-btn <?php echo $index === 0 ? "selected" : ""; ?>">
                                            <?php echo $size; ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="section-title">Quantity</div>
                        <input type="number" name="quantity" value="1" min="1">

                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

                        <button type="submit" name="add_to_cart" class="btn-primary">Add to Cart</button>

                        <button type="submit" name="buy_now"
                                formaction="checkout.php"
                                class="btn-primary">
                            Buy Now
                        </button>
                    </form>

                    <hr style="margin-top:25px;">

                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
            </div>

            <!-- Reviews -->
            <div class="reviews">
                <h2>Customer Reviews</h2>

                <?php if ($reviews && $reviews->num_rows > 0): ?>
                    <?php while ($rev = $reviews->fetch_assoc()): ?>
                        <div class="review-item">
                            <strong><?php echo htmlspecialchars($rev['name']); ?></strong>
                            <div style="color:#f59e0b;">
                                <?php echo str_repeat("★", (int)$rev['rating']); ?>
                                <?php echo str_repeat("☆", 5 - (int)$rev['rating']); ?>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($rev['review'])); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No reviews yet.</p>
                <?php endif; ?>

                <h3>Write a Review</h3>

                <form method="post" action="submit_review.php" style="max-width:400px;display:flex;flex-direction:column;gap:10px;">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

                    <label>Your Name:
                        <input type="text" name="name" required>
                    </label>

                    <label>Rating:
                        <select name="rating" required>
                            <option value="5">★★★★★ 5 stars</option>
                            <option value="4">★★★★ 4 stars</option>
                            <option value="3">★★★ 3 stars</option>
                            <option value="2">★★ 2 stars</option>
                            <option value="1">★ 1 star</option>
                        </select>
                    </label>

                    <label>Your Review:
                        <textarea name="review" rows="4" required></textarea>
                    </label>

                    <button type="submit" class="btn-primary">Submit Review</button>
                </form>
            </div>

        <?php endif; ?>

    </div>
</main>

<footer>
    <?php include 'footer.inc'; ?>
</footer>

<script>
// Toggle selected class on color & size
document.querySelectorAll('label .pill-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        var label = e.target.closest('label');
        var input = label.querySelector('input[type="radio"]');
        var groupName = input.name;

        document.querySelectorAll('input[name="' + groupName + '"]').forEach(function(radio) {
            radio.parentElement.querySelector('.pill-btn').classList.remove('selected');
        });

        input.checked = true;
        btn.classList.add('selected');
    });
});
</script>

</body>
</html>
