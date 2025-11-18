<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables from .env file
$env_file = __DIR__ . '/.env';

echo "<!-- DEBUG: Looking for .env at: $env_file -->";

if (file_exists($env_file)) {
    echo "<!-- DEBUG: .env file found -->";
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $value = trim($value, '"\'');
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
} else {
    echo "<!-- DEBUG: .env file NOT found at $env_file -->";
}



// Database configuration from .env
$servername = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');

echo "<!-- DEBUG: DB_HOST=$servername, DB_USERNAME=$username, DB_PASSWORD=$password, DB_DATABASE=$database -->";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($conn) {
    $conn->set_charset("utf8mb4");

}


?>