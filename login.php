<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("connect.php");
session_start();

// Debug: Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: user-profile.php");
    exit;
}

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? true : false;

    // Validation
    if (empty($email) || empty($password)) {
        $message = "Please enter both email and password.";
        $message_type = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $message_type = "danger";
    } else {
        // Query user by email
        $query = "SELECT user_id, email, password, first_name, last_name, dateofbirth FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verify password - handle both hashed and plain text passwords
        $password_valid = false;
        if ($user) {
            // Try to verify as hashed password first
            if (password_verify($password, $user['password'])) {
                $password_valid = true;
            } 
            // Fallback for plain text passwords (for testing/migration)
            elseif ($user['password'] === $password) {
                $password_valid = true;
                // Hash the password for security
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $update_query = "UPDATE users SET password = ? WHERE user_id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("si", $hashed, $user['user_id']);
                $update_stmt->execute();
            }
        }

        if ($user && $password_valid) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            if ($user['dateofbirth']) {
                $_SESSION['dateofbirth'] = $user['dateofbirth'];
            }

            // Remember me functionality
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expiry = time() + (30 * 24 * 60 * 60); // 30 days
                setcookie('remember_token', $token, $expiry, '/', '', false, true);
                // You can store this token in database for additional security if needed
            }

            $message = "Login successful! Redirecting...";
            $message_type = "success";
            header("refresh:2;url=user-profile.php");
        } else {
            $message = "Invalid email or password.";
            $message_type = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fashion House - Login</title>
  <link rel="stylesheet" href="styles/header-footer.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #333;
      --secondary-color: #555;
      --light-bg: #f8f9fa;
      --border-color: #dee2e6;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    .login-container {
      width: 100%;
      max-width: 450px;
    }

    .login-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
      overflow: hidden;
    }

    .login-header {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
      color: white;
      padding: 40px 30px;
      text-align: center;
    }

    .login-header h1 {
      font-size: 2rem;
      font-weight: 700;
      margin: 0;
      margin-bottom: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .login-header p {
      margin: 0;
      font-size: 0.95rem;
      opacity: 0.9;
    }

    .login-body {
      padding: 40px 30px;
    }

    .alert {
      border: none;
      border-radius: 8px;
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 15px 20px;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border-left: 4px solid #28a745;
    }

    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
      border-left: 4px solid #dc3545;
    }

    .form-group {
      margin-bottom: 25px;
      position: relative;
    }

    .form-label {
      font-weight: 600;
      color: var(--primary-color);
      margin-bottom: 10px;
      display: block;
      font-size: 0.95rem;
    }

    .form-control {
      border: 2px solid var(--border-color);
      border-radius: 8px;
      padding: 12px 15px;
      font-size: 1rem;
      transition: all 0.3s ease;
      height: auto;
    }

    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }

    .form-control::placeholder {
      color: #999;
    }

    .input-group {
      position: relative;
    }

    .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: var(--secondary-color);
      transition: color 0.3s ease;
      border: none;
      background: none;
      padding: 0;
      font-size: 1.1rem;
    }

    .password-toggle:hover {
      color: var(--primary-color);
    }

    .form-check {
      margin-bottom: 25px;
    }

    .form-check-input {
      width: 18px;
      height: 18px;
      border: 2px solid var(--border-color);
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 0.35rem;
    }

    .form-check-input:checked {
      background-color: #667eea;
      border-color: #667eea;
    }

    .form-check-input:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }

    .form-check-label {
      cursor: pointer;
      margin-left: 8px;
      color: var(--secondary-color);
      font-size: 0.95rem;
      user-select: none;
    }

    .forgot-password {
      text-align: right;
      margin-bottom: 25px;
    }

    .forgot-password a {
      color: #667eea;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .forgot-password a:hover {
      color: #764ba2;
      text-decoration: underline;
    }

    .btn-login {
      width: 100%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .divider {
      display: flex;
      align-items: center;
      margin: 30px 0;
      color: #999;
      font-size: 0.9rem;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background-color: var(--border-color);
    }

    .divider span {
      padding: 0 15px;
    }

    .signup-link {
      text-align: center;
      color: var(--secondary-color);
      font-size: 0.95rem;
    }

    .signup-link a {
      color: #667eea;
      text-decoration: none;
      font-weight: 700;
      transition: color 0.3s ease;
    }

    .signup-link a:hover {
      color: #764ba2;
      text-decoration: underline;
    }

    .social-login {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-top: 25px;
    }

    .social-btn {
      border: 2px solid var(--border-color);
      background: white;
      color: var(--primary-color);
      padding: 10px;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      font-size: 0.9rem;
    }

    .social-btn:hover {
      border-color: #667eea;
      color: #667eea;
      transform: translateY(-2px);
    }

    @media (max-width: 576px) {
      .login-header {
        padding: 30px 20px;
      }

      .login-header h1 {
        font-size: 1.5rem;
      }

      .login-body {
        padding: 30px 20px;
      }

      .form-group {
        margin-bottom: 20px;
      }

      .social-login {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <header>
    <?php include 'header.inc'; ?>
  </header>

  <main>
    <div class="login-container">
      <div class="login-card">
        <div class="login-header">
          <h1><i class="fas fa-shopping-bag"></i> Fashion House</h1>
          <p>Welcome Back</p>
        </div>

        <div class="login-body">
          <?php if (!empty($message)) { ?>
          <div class="alert alert-<?php echo $message_type; ?>" role="alert">
            <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
          </div>
          <?php } ?>

          <form method="POST" action="">
            <div class="form-group">
              <label for="email" class="form-label">
                <i class="fas fa-envelope"></i> Email Address
              </label>
              <input 
                type="email" 
                class="form-control" 
                id="email" 
                name="email" 
                placeholder="Enter your email"
                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                required
              >
            </div>

            <div class="form-group">
              <label for="password" class="form-label">
                <i class="fas fa-lock"></i> Password
              </label>
              <div class="input-group">
                <input 
                  type="password" 
                  class="form-control" 
                  id="password" 
                  name="password" 
                  placeholder="Enter your password"
                  required
                >
                <button type="button" class="password-toggle" onclick="togglePassword()">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>

            <div class="form-check">
              <input 
                type="checkbox" 
                class="form-check-input" 
                id="remember" 
                name="remember"
              >
              <label class="form-check-label" for="remember">
                Remember me
              </label>
            </div>

            <div class="forgot-password">
              <a href="forgot-password.php">
                <i class="fas fa-question-circle"></i> Forgot password?
              </a>
            </div>

            <button type="submit" class="btn-login">
              <i class="fas fa-sign-in-alt"></i> Login
            </button>
          </form>

          <div class="divider">
            <span>Don't have an account?</span>
          </div>

          <div class="signup-link">
            <p>Create a new account to get started</p>
            <a href="register.php">
              <i class="fas fa-user-plus"></i> Sign Up Now
            </a>
          </div>

          <div class="social-login">
            <button class="social-btn" onclick="alert('Google login coming soon')">
              <i class="fab fa-google"></i> Google
            </button>
            <button class="social-btn" onclick="alert('Facebook login coming soon')">
              <i class="fab fa-facebook"></i> Facebook
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <?php include 'footer.inc'?>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleBtn = document.querySelector('.password-toggle');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
      } else {
        passwordInput.type = 'password';
        toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
      }
    }

    // Prevent form submission on social buttons
    document.querySelectorAll('.social-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
      });
    });
  </script>
</body>
</html>