<?php
// product.php
session_start();
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
<title><?php echo $product ? htmlspecialchars($product['product_name'])." - Fashion House" : "Product Not Found"; ?></title>

<!-- BOOTSTRAP -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="styles/header-footer.css">
<link rel="stylesheet" href="styles/product.css">

</head>
<body>

<header>
<?php include 'header.inc'; ?>
</header>

<main class="container container-custom">

<?php if(!$product): ?>
<h2>Product Not Found</h2>
<p>This product does not exist.</p>
<a href="category.php?gender=men">Back to shop</a>
<?php else: ?>

<!-- Breadcrumb -->
<nav class="mb-3 small">
<a href="home.php" class="text-decoration-none">Home</a> ›
<a href="category.php" class="text-decoration-none">Categories</a> ›
<a href="category.php?gender=<?php echo strtolower($genderName); ?>" class="text-decoration-none"><?php echo $genderName; ?></a> ›
<span><?php echo htmlspecialchars($product['product_name']); ?></span>
</nav>

<div class="row g-4">
    <!-- Product image column -->
    <div class="col-12 col-md-5">
        <div class="product-image">
            <?php if(!empty($product_images)): ?>
            <img src="<?php echo htmlspecialchars($product_images[0]['image_url']); ?>">
            <?php else: ?>
            <img src="images/placeholder.jpg">
            <?php endif; ?>
        </div>
    </div>

    <!-- Product details column -->
    <div class="col-12 col-md-7">
        <h2 class="fw-bold"><?php echo htmlspecialchars($product['product_name']); ?></h2>
        <h4 class="text-primary">$<?php echo number_format($product['price'],2); ?></h4>
        <span class="badge bg-primary-subtle text-primary fw-semibold">
            <?php echo ($product['stock_quantity']>0) ? "In Stock" : "Out of Stock"; ?>
        </span>

        <form action="cart.php" method="post" class="mt-3">

            <!-- Colors -->
            <?php if(!empty($colors)): ?>
            <p class="fw-semibold mt-3 mb-1">Color</p>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach($colors as $i=>$color): ?>
                <label>
                    <input type="radio" name="color" value="<?php echo $color; ?>" class="d-none" <?php echo $i===0?'checked':''; ?>>
                    <span class="pill-btn <?php echo $i===0?'selected':''; ?>"><?php echo $color; ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Sizes -->
            <?php if(!empty($sizes)): ?>
            <p class="fw-semibold mt-3 mb-1">Size</p>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach($sizes as $i=>$size): ?>
                <label>
                    <input type="radio" name="size" value="<?php echo $size; ?>" class="d-none" <?php echo $i===0?'checked':''; ?>>
                    <span class="pill-btn <?php echo $i===0?'selected':''; ?>"><?php echo $size; ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Quantity -->
            <p class="fw-semibold mt-3 mb-1">Quantity</p>
                <div class="d-flex align-items-center gap-2">
                    <input type="number" name="quantity" value="1" min="1" class="form-control w-25" id="quantity-input">
                    <span class="fw-bold fs-5">Total: $<span id="total-price"><?php echo number_format($product['price'],2); ?></span></span>
                </div>

            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

            <div class="d-flex gap-2 mt-4">
                <button name="add_to_cart" class="btn-primary-custom" type="submit">Add to Cart</button>
                <button name="buy_now" type="submit" formaction="checkout.php" class="btn-primary-custom">Buy Now</button>
            </div>
        </form>

        <hr class="mt-4">
        <h5>Description</h5>
        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
    </div>
</div>

<!-- Reviews -->
<div class="mt-5">
<h3>Customer Reviews</h3>
<?php if ($reviews && $reviews->num_rows > 0): ?>
<?php while ($rev = $reviews->fetch_assoc()): ?>
<div class="border-bottom py-2">
<strong><?php echo htmlspecialchars($rev['name']); ?></strong>
<div class="text-warning small"> 
<?php echo str_repeat("★", (int)$rev['rating']); ?>
<?php echo str_repeat("☆", 5-(int)$rev['rating']); ?>
</div>
<p class="mb-0"><?php echo nl2br(htmlspecialchars($rev['review'])); ?></p>
</div>
<?php endwhile; ?>
<?php else: ?><p>No reviews yet.</p><?php endif; ?>

<h4 class="mt-4">Write a Review</h4>
<form method="post" action="submit_review.php" class="d-flex flex-column gap-2" style="max-width:400px">
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
    <input type="text" name="name" placeholder="Your name" class="form-control" required>
    <select name="rating" class="form-select" required>
        <option value="5">★★★★★ 5 stars</option>
        <option value="4">★★★★ 4 stars</option>
        <option value="3">★★★ 3 stars</option>
        <option value="2">★★ 2 stars</option>
        <option value="1">★ 1 star</option>
    </select>
    <textarea name="review" rows="4" class="form-control" placeholder="Write your review..." required></textarea>
    <button class="btn-primary-custom" type="submit">Submit Review</button>
</form>
</div>

<?php endif; ?>
</main>

<footer>
<?php include 'footer.inc'; ?>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('label .pill-btn').forEach(btn => {
    btn.addEventListener('click', e => {
        const label = e.target.closest('label');
        const input = label.querySelector('input');

        document.querySelectorAll(`input[name="${input.name}"]`).forEach(r => {
            r.parentElement.querySelector('.pill-btn').classList.remove('selected');
        });

        input.checked = true;
        btn.classList.add('selected');
    });
});

const quantityInput = document.getElementById('quantity-input');
const totalPriceSpan = document.getElementById('total-price');
const unitPrice = <?php echo $product['price']; ?>;

quantityInput.addEventListener('input', () => {
    let qty = parseInt(quantityInput.value);
    if(isNaN(qty) || qty < 1) qty = 1;
    totalPriceSpan.textContent = (unitPrice * qty).toFixed(2);
});
</script>
</body>
</html>
