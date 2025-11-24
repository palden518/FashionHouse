<!-- LOGO -->
<div class="logo">
    <a href="home.php">
        <img src="icons/logo/logo-white.svg" alt="Fashion House Logo">
        FASHION HOUSE
    </a>
</div>

<!-- NAVIGATION -->
<nav>
    <ul>
        <li><a href="product-category.php">Categories</a></li>
        <li><a href="category.php?category=Men"><p>Men</p></a></li>
        <li><a href="category.php?category=Women"><p>Women</p></a></li>
    </ul>
</nav>

<!-- RIGHT SIDE HEADER -->
<div class="header-right">

    <!-- SEARCH BAR -->
    <div class="searchbar">
        <form action="/search" method="get">
            <input type="search" id="search-input" name="query" placeholder="Search..." />
            <button type="submit"><img src="icons/search.svg"></button>
        </form>
    </div>

    <!-- ACCOUNT AREA -->
    <div class="account">

      <?php if (isset($_SESSION['user_id'])): ?>

        <div class="account-wrapper">

            <!-- Account Icon -->
            <a href="user-profile.php" class="account-icon-link">
                <img src="icons/account_circle_white.svg" alt="Account" class="account-icon">
            </a>

            <!-- Show only first name -->
            <span class="user-name">
                <?php echo htmlspecialchars($_SESSION['first_name']); ?>
            </span>

        </div>

        <?php else: ?>

          <a href="login.php" class="account-icon-link">
            <img src="icons/account_circle_white.svg" alt="Login" class="account-icon">
          </a>

          <a href="login.php" class="text-link">Login</a>
          <a href="register.php" class="text-link">Register</a>

          <?php endif; ?>

      <!-- Cart Icon -->
      <a href="cart.php" class="cart-link">
        <img src="icons/shopping_cart_white.svg" alt="Cart" class="cart">
      </a>

    </div>


</div>
