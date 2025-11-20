    <div class = "logo">
     <a href="home.php"><img src="icons/logo/logo-white.svg" alt="Fashion House Logo">FASHION HOUSE</a>
    </div>
    <nav>
      <ul>
        <li><a href="category.php"><p>Categories</p></a></li> <!--Men + Women (mendatory) -->
        <li><a href="product-category.php?category=Men"><p>Men</p></a></li> 
        <li><a href="product-category.php?category=Women"><p>Women</p></a> </li>
      </ul>
    </nav>
    <div class="header-right">
      <div class="searchbar">
        <form action="/search" method="get">
          <input type="search" id="search-input" name="query" placeholder="Search..." />
          <button type="submit"><img src="icons/search.svg"></button>
        </form>
      </div>
      <div class="account">
        <a href="customer.php"><img class="account" src="icons/account_circle_white.svg"></a>
        <a href="cart.php"><img class="cart" src="icons/shopping_cart_white.svg"></a>
      </div>
    </div>