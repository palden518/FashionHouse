<?php
session_start();
require_once 'connect.php';

// Escape output (XSS protection)
function clean($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$errors = [];
$success = "";

// Pre-fill variables
$first_name = $last_name = $email = $phone = $dob = "";

// ---------------------------
// HANDLE REGISTRATION SUBMIT
// ---------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect and trim user input
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $dob        = trim($_POST['dob']);
    $password   = $_POST['password'];
    $confirm    = $_POST['confirm_password'];

    // ---------------------------
    //        VALIDATION
    // ---------------------------

    // First name + Last name (alphabet only)
    if (!preg_match("/^[a-zA-Z ]+$/", $first_name)) {
        $errors[] = "First name must contain only alphabetic characters.";
    }

    if (!preg_match("/^[a-zA-Z ]+$/", $last_name)) {
        $errors[] = "Last name must contain only alphabetic characters.";
    }

    // Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address format.";
    }

    // Phone: must be +61 + 10 digits
    if (!preg_match("/^\+61\d{10}$/", $phone)) {
        $errors[] = "Phone must start with +61 and contain exactly 10 digits after it.";
    }

    // DOB: must be >= 16 years old
    if (empty($dob)) {
        $errors[] = "Please select a date of birth.";
    } else {
        $today = new DateTime();
        $birthdate = new DateTime($dob);
        $age = $today->diff($birthdate)->y;

        if ($age < 16) {
            $errors[] = "You must be at least 16 years old to register.";
        }
    }

    // Password: 8 chars + 1 number + 1 symbol
    if (!preg_match("/^(?=.*[0-9])(?=.*[\W_]).{8,}$/", $password)) {
        $errors[] = "Password must be at least 8 characters long and include 1 number and 1 special symbol.";
    }

    // Confirm password
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // ---------------------------
    // CHECK IF EMAIL EXISTS
    // ---------------------------
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "This email is already registered.";
        }

        $stmt->close();
    }

    // ---------------------------
    // INSERT NEW USER
    // ---------------------------
    if (empty($errors)) {

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (email, password, first_name, last_name, phone, dateofbirth)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $email, $hashed, $first_name, $last_name, $phone, $dob);

        if ($stmt->execute()) {

            // Auto-login new user
            $_SESSION["user_id"] = $stmt->insert_id;
            $_SESSION["first_name"] = $first_name;

            header("Location: home.php");
            exit;

        } else {
            $errors[] = "Database error: Unable to register user.";
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
    <title>Register - Fashion House</title>

    <!-- CSS FILES -->
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

            <h2>Create an Account</h2>

            <!-- ERROR MESSAGES -->
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $e) echo clean($e) . "<br>"; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">

                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" required 
                           value="<?= clean($first_name); ?>">
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" required 
                           value="<?= clean($last_name); ?>">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required 
                           value="<?= clean($email); ?>">
                </div>

                <div class="form-group">
                    <label>Phone Number (+61XXXXXXXXXX)</label>
                    <input type="text" 
                           name="phone" 
                           required
                           pattern="\+61\d{10}"
                           title="Phone must start with +61 and contain exactly 10 digits."
                           value="<?= clean($phone); ?>">
                </div>

                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" required value="<?= clean($dob); ?>">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" 
                           name="password" 
                           required
                           pattern="^(?=.*[0-9])(?=.*[\W_]).{8,}$"
                           title="At least 8 characters, including 1 number and 1 special symbol.">
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>

                <button type="submit" class="button-primary">Register</button>

            </form>

        </div>

    </div>
</main>

<footer>
    <?php include 'footer.inc'; ?>
</footer>

</body>
</html>
