<?php
// submit_review.php
include 'connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get data from form
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $name       = isset($_POST['name']) ? trim($_POST['name']) : '';
    $rating     = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $review     = isset($_POST['review']) ? trim($_POST['review']) : '';

    // Basic validation
    if ($product_id > 0 && $name !== '' && $rating >= 1 && $rating <= 5 && $review !== '') {

        $sql = "INSERT INTO product_reviews (product_id, name, rating, review, created_at)
                VALUES (?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isis", $product_id, $name, $rating, $review);
        $stmt->execute();
        $stmt->close();
    }

    // ALWAYS redirect back to product page
    header("Location: product.php?id=" . $product_id);
    exit;

} else {
    // If opened directly → go home
    header("Location: index.php");
    exit;
}
