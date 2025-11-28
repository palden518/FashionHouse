<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fashion House - Help</title>
  <link rel="stylesheet" href="styles/header-footer.css">
  <link rel="stylesheet" href="styles/help-customer.css">
</head>
<body>
  <header>
    <?php include 'header.inc'?>
  </header>
<script src="./scripts/dropdownscript.js"></script>
  <main>

    <div class="content">
      <h1 class="page-title">HELP</h1>

      <section class="return">
        <h2>Return policy</h2>
        <h3>How Long Do I Have to Make a Return?</h3>
        <p> Our house policy accept a delay of a month from reception to return items* in new condition.</p>
        <h3>How Can I Return Items?</h3>
        <p>Send us a mail at : <a href="mailto:fashionhouse@gmail.com">fashionhouse@gmail.com</a></p>
        <h3>What Are the Return Methods and Return Fees?</h3>
        <ul>
          <li>Self-return your item and directly pay logistic return fee to the provider**</li>
          <li>AusPost return in store drop-off</li>
          <li>Return shipping is free on your first return**</li>
        </ul>
        <p class="except">* except customised items</p>
        <p class="except">** for more detail <a href="mailto:fashionhouse@gmail.com">contact our team</a></p>
      </section>

      <section class="order">
        <h2>How to order</h2>
        <ul>
          <li> Step 1 : Add item(s) in your Shopping Cart.</li>
          <li> Step 2 : Check out when item selection is completed.</li>
          <li> Step 3 : Log in your Fashion House account.</li>
          <li> Step 4 : Complete shipping and billing information.</li>
          <li> Step 5 : Fill in payment information to complete the purchase.</li>
      </section>

      <section class="size">
      <h2>Size guide</h2>
      <img src="images/size_guide.webp" alt="Size guide chart">
      </section>
    </div>
  </main>

  <footer>
    <?php include 'footer.inc'?>
  </footer>
  
</body>
</html>