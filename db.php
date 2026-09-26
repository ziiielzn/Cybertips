<?php
// Connection settings come from environment variables (Railway MySQL plugin),
// falling back to local XAMPP defaults.
$servername = getenv('MYSQLHOST') ?: "localhost";
$port = (int) (getenv('MYSQLPORT') ?: 3306);
$username = getenv('MYSQLUSER') ?: "root";
$password = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : "";
$dbname = getenv('MYSQLDATABASE') ?: "cyber_tips";

// mysqli connection (used by login.php / register.php)
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// PDO connection (used by the session, profile and progress endpoints)
$pdo = new PDO(
    "mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4",
    $username,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

// Make sure the tables exist on a fresh database
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) DEFAULT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    last_picture_change TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
$conn->query("CREATE TABLE IF NOT EXISTS user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    current_slide INT DEFAULT 0,
    completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY user_course (user_id, course_id)
)");
?>
