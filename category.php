<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fashion House - Categories</title>
  <link rel="stylesheet" href="styles/header-footer.css">
  <link rel="stylesheet" href="styles/category.css">
</head>
<body>
  <header>
    <?php include 'header.inc'?>
  </header>

  <main>
    <div class="content">
    <?php include 'breadcrumb.inc'?>
      <h1 class="page-title">CATEGORIES</h1>

      <div class="category-section">
        <h2 class="category-title"><a href="subcategory-men.php">Men</a></h2>
        <!-- Repeat this block for each Category NEED TO DO IT WITH PHP-->
          <div class="carousel-wrapper">
            <button class="arrow left" onclick="this.nextElementSibling.scrollBy({left: -300, behavior: 'smooth'})">&#8592;</button>
            <div class="products-scroll">
              <!-- END OF REPEAT-->
                <!-- Repeat this block for each product NEED TO DO IT WITH PHP-->
              <div class="product">
                <a href = "product-category.php">
                <img src="images/cover/category_shirt.jpg" alt="shirt category">
                <h3>Shirt</h3>
                </a>
              </div>
              <!-- END OF REPEAT-->
              <div class="product">
                <a href = "product-category.php">
                <img src="images/cover/category_t-shirt.jpg" alt="t-shirt category">
                <h3>T-shirt</h3>
                </a>
              </div><!-- You go the point its copy and paste = need php-->
              <div class="product">
                <a href = "product-category.php">
                <img src="" alt="">
                <h3>Hoodies</h3>
                </a>
              </div>
              <div class="product">
                <a href = "product-category.php">
                <img src="" alt="">
                <h3>Pants</h3>
                </a>
              </div>
              <div class="product">
                <a href = "product-category.php">
                <img src="" alt="">
                <h3>Denim</h3>
                </a>
              </div>
              <div class="product">
                <a href = "product-category.php">
                <img src="" alt="">
                <h3>Shorts</h3>
                </a>
              </div>
            </div>
            <button class="arrow right" onclick="this.previousElementSibling.scrollBy({left: 300, behavior: 'smooth'})">&#8594;</button>
          </div>
      </div>
      <!--exact same for women so we can even add accessories, kids or whatever-->
      <div class="category-section">
        <h2 class="category-title"><a href="subcategory-men.php">Women</a></h2>
        <!-- Repeat this block for each Category NEED TO DO IT WITH PHP-->
          <div class="carousel-wrapper">
            <button class="arrow left" onclick="this.nextElementSibling.scrollBy({left: -300, behavior: 'smooth'})">&#8592;</button>
            <div class="products-scroll">
              <!-- END OF REPEAT-->
                <!-- Repeat this block for each product NEED TO DO IT WITH PHP-->
              <div class="product">
                <a href = "product-category.php">
                <img src="images/cover/category_dress.jpg" alt="dresses category">
                <h3>Dress</h3>
                </a>
              </div>
              <!-- END OF REPEAT-->
              <div class="product">
                <a href = "product-category.php">
                <img src="images/cover/category_top.jpg" alt="top category">
                <h3>Top</h3>
                </a>
              </div><!-- You go the point its copy and paste = need php-->
              <div class="product">
                <a href = "product-category.php">
                <img src="" alt="">
                <h3>T-shirt</h3>
                </a>
              </div>
              <div class="product">
                <a href = "product-category.php">
                <img src="" alt="">
                <h3>Blouse</h3>
                </a>
              </div>
              <div class="product">
                <a href = "product-category.php">
                <img src="" alt="">
                <h3>Pants</h3>
                </a>
              </div>
              <div class="product">
                <a href = "product-category.php">
                <img src="" alt="">
                <h3>Denim</h3>
                </a>
              </div>
              <div class="product">
                <a href = "product-category.php">
                <img src="" alt="">
                <h3>Shorts</h3>
                </a>
              </div>
              <div class="product">
                <a href = "product-category.php">
                <img src="" alt="">
                <h3>Skirt</h3>
                </a>
              </div>
            </div>
            <button class="arrow right" onclick="this.previousElementSibling.scrollBy({left: 300, behavior: 'smooth'})">&#8594;</button>
          </div>
      </div>
    </div>
  </main>

  <footer>
    <?php include 'footer.inc'?>
  </footer>
  
</body>
</html>