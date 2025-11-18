<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fashion House - Women's fashion</title>
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
      <h1 class="page-title">WOMEN'S FASHION</h1>
<!-- Repeat this block for each category NEED TO DO IT WITH PHP-->
      <div class="category-section">
        <h2 class="category-title"><a href = "subcategory.php">Dresses</a></h2>
          <div class="carousel-wrapper">
            <button class="arrow left" onclick="this.nextElementSibling.scrollBy({left: -300, behavior: 'smooth'})">&#8592;</button>
            <div class="products-scroll">
              <!-- END OF REPEAT-->
              <!-- Repeat this block for each product NEED TO DO IT WITH PHP-->
              <div class="product">
                <a href="product-name-page">
                <img src="images/cover/category_dress.jpg" alt="dress product">
                <h3>product name</h3>
                </a>
              </div>
              <!-- END OF REPEAT-->
              <div class="product">
                <a href="product-name-page">
                <img src="images/cover/category_dress.jpg" alt="dress product">
                <h3>product name</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
            </div>
            <button class="arrow right" onclick="this.previousElementSibling.scrollBy({left: 300, behavior: 'smooth'})">&#8594;</button>
          </div>
      </div>
      <div class="category-section">
        <h2 class="category-title"><a href = "subcategory.php">Top</a></h2>
          <div class="carousel-wrapper">
            <button class="arrow left" onclick="this.nextElementSibling.scrollBy({left: -300, behavior: 'smooth'})">&#8592;</button>
            <div class="products-scroll">
              <div class="product">
                <a href="product-name-page">
                <img src="images/cover/category_top.jpg" alt="top product">
                <h3>product name</h3>
                </a>
              </div>
              <div class="product">
                <a href="product-name-page">
                <img src="#" alt="top product">
                <h3>product name</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
            </div>
            <button class="arrow right" onclick="this.previousElementSibling.scrollBy({left: 300, behavior: 'smooth'})">&#8594;</button>
          </div>
        </div> 
        <div class="category-section">
        <h2 class="category-title"><a href = "subcategory.php">T-shirt</a></h2>
          <div class="carousel-wrapper">
            <button class="arrow left" onclick="this.nextElementSibling.scrollBy({left: -300, behavior: 'smooth'})">&#8592;</button>
            <div class="products-scroll">
              <div class="product">
                <a href="product-name-page">
                <img src="" alt="t-shirt product">
                <h3>product name</h3>
                </a>
              </div>
              <div class="product">
                <a href="product-name-page">
                <img src="#" alt="#">
                <h3>product name</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
            </div>
            <button class="arrow right" onclick="this.previousElementSibling.scrollBy({left: 300, behavior: 'smooth'})">&#8594;</button>
          </div>
      </div>   <div class="category-section">
        <h2 class="category-title">Blouse</h2>
          <div class="carousel-wrapper">
            <button class="arrow left" onclick="this.nextElementSibling.scrollBy({left: -300, behavior: 'smooth'})">&#8592;</button>
            <div class="products-scroll">
              <div class="product">
                <a href="product-name-page">
                <img src="#" alt="#">
                <h3>product name</h3>
                </a>
              </div>
              <div class="product">
                <a href="product-name-page">
                <img src="#" alt="#">
                <h3>product name</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
            </div>
            <button class="arrow right" onclick="this.previousElementSibling.scrollBy({left: 300, behavior: 'smooth'})">&#8594;</button>
          </div>
      </div> 
      <div class="category-section">
        <h2 class="category-title">Denim</h2>
          <div class="carousel-wrapper">
            <button class="arrow left" onclick="this.nextElementSibling.scrollBy({left: -300, behavior: 'smooth'})">&#8592;</button>
            <div class="products-scroll">
              <div class="product">
                <a href="product-name-page">
                <img src="#" alt="#">
                <h3>product name</h3>
                </a>
              </div>
              <div class="product">
                <a href="product-name-page">
                <img src="#" alt="#">
                <h3>product name</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
                </a>
              </div>
              <div class="product">
                <a href="#">
                <img src="#" alt="#">
                <h3>#</h3>
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