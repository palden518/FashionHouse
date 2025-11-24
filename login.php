<?php
session_start();
require_once 'connect.php';

// Escape output (XSS protection)
function clean($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$errors = [];
$email = "";

// ----------------------
// HANDLE LOGIN SUBMIT
// ----------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // ----------------------
    // VALIDATION
    // ----------------------

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($password)) {
        $errors[] = "Please enter your password.";
    }

    if (empty($errors)) {

        // Prepared statement prevents SQL injection
        $stmt = $conn->prepare("
            SELECT user_id, password, first_name 
            FROM users 
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // If email exists
        if ($stmt->num_rows === 1) {

            $stmt->bind_result($uid, $hashed_password, $fname);
            $stmt->fetch();

            // Check password
            if (password_verify($password, $hashed_password)) {

                // Login success → security best practice
                session_regenerate_id(true);

                $_SESSION['user_id'] = $uid;
                $_SESSION['first_name'] = $fname;

                header("Location: home.php");
                exit;

            } else {
                $errors[] = "Incorrect email or password.";
            }
        } else {
            $errors[] = "Incorrect email or password.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fashion House</title>

    <!-- PAGE CSS -->
    <link rel="stylesheet" href="styles/header-footer.css">
    <link rel="stylesheet" href="styles/forms.css">
</head>

<body>

<header>
    <?php include 'header.inc'; ?>
</header>

<main>
    <div class="content">

        <div class="form-container">

            <h2>Login</h2>

            <!-- ERROR MESSAGES -->
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $e) echo clean($e) . "<br>"; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" 
                           name="email" 
                           required 
                           value="<?= clean($email); ?>">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" 
                           name="password" 
                           required>
                </div>

                <button type="submit" class="button-primary">Login</button>

            </form>

        </div>

    </div>
</main>

<footer>
    <?php include 'footer.inc'; ?>
</footer>

</body>
</html>
