<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fashion House - Men's fashion</title>
  <link rel="stylesheet" href="styles/header-footer.css">
  <link rel="stylesheet" href="styles/category.css">
  <link rel="stylesheet" href="styles/product-category.css">
</head>
<body>
  <header>
    <?php include 'header.inc'?>
  </header>

  <main>

    <div class="content">
    <?php include 'breadcrumb.inc'?>
      <h1 class="page-title">MEN'S FASHION (subcategory name)</h1>
      <!-- Repeat this block for each Category NEED TO DO IT WITH PHP-->
      <div class="subcategory-section">
        <a href = "product-category.php"><h2 class="category-title">Shirt</h2></a>
          <div class="carousel-wrapper">
            <button class="arrow left" onclick="this.nextElementSibling.scrollBy({left: -300, behavior: 'smooth'})">&#8592;</button>
              <div class="products-scroll">
               <!-- END OF REPEAT-->
                <!-- Repeat this block for each product NEED TO DO IT WITH PHP-->
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 1</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" alt="fav"></img>
                      <img src="icons/add_shopping_cart_white.svg" alt="cart" ></img>
                    </div>
                  </div>
                </div>
               <!-- END OF REPEAT-->
               <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 2</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 3</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 4</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 5</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 6</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
              </div>
            <button class="arrow right" onclick="this.previousElementSibling.scrollBy({left: 300, behavior: 'smooth'})">&#8594;</button>
          </div>
      </div>
      <!-- Same for each caroussel of category-->
      <div class="subcategory-section">
        <a href = "product-category.php"><h2 class="category-title">T-Shirt</h2></a>
          <div class="carousel-wrapper">
            <button class="arrow left" onclick="this.nextElementSibling.scrollBy({left: -300, behavior: 'smooth'})">&#8592;</button>
              <div class="products-scroll">
               <!-- END OF REPEAT-->
                <!-- Repeat this block for each product NEED TO DO IT WITH PHP-->
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 1</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" alt="fav"></img>
                      <img src="icons/add_shopping_cart_white.svg" alt="cart" ></img>
                    </div>
                  </div>
                </div>
               <!-- END OF REPEAT-->
               <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 2</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 3</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 4</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 5</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 6</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
              </div>
            <button class="arrow right" onclick="this.previousElementSibling.scrollBy({left: 300, behavior: 'smooth'})">&#8594;</button>
          </div>
      </div>
      <div class="subcategory-section">
        <a href = "product-category.php"><h2 class="category-title">Denim</h2></a>
          <div class="carousel-wrapper">
            <button class="arrow left" onclick="this.nextElementSibling.scrollBy({left: -300, behavior: 'smooth'})">&#8592;</button>
              <div class="products-scroll">
               <!-- END OF REPEAT-->
                <!-- Repeat this block for each product NEED TO DO IT WITH PHP-->
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 1</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" alt="fav"></img>
                      <img src="icons/add_shopping_cart_white.svg" alt="cart" ></img>
                    </div>
                  </div>
                </div>
               <!-- END OF REPEAT-->
               <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 2</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 3</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 4</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 5</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 6</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
              </div>
            <button class="arrow right" onclick="this.previousElementSibling.scrollBy({left: 300, behavior: 'smooth'})">&#8594;</button>
          </div>
      </div>
      <div class="subcategory-section">
        <a href = "product-category.php"><h2 class="category-title">Hoodies</h2></a>
          <div class="carousel-wrapper">
            <button class="arrow left" onclick="this.nextElementSibling.scrollBy({left: -300, behavior: 'smooth'})">&#8592;</button>
              <div class="products-scroll">
               <!-- END OF REPEAT-->
                <!-- Repeat this block for each product NEED TO DO IT WITH PHP-->
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 1</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" alt="fav"></img>
                      <img src="icons/add_shopping_cart_white.svg" alt="cart" ></img>
                    </div>
                  </div>
                </div>
               <!-- END OF REPEAT-->
               <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 2</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 3</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 4</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 5</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name 6</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
                </div>
                <div class="product-card">
                  <div class="product-img">
                    <img src="" alt="">
                  </div>
                  <div class="product-info">
                    <p class="name">Item name</p>
                    <p class="price">$ price</p>
                    <div class="actions">
                      <img src="icons/favorite_white.svg" ></img>
                      <img src="icons/add_shopping_cart_white.svg" ></img>
                    </div>
                  </div>
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