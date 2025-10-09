<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);  // Hash password before storing

    // Insert user data into the database
    $sql = "INSERT INTO users (email, password) VALUES ('$email', '$hashedPassword')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Account created successfully!";
        header("Location: login.html");  // Redirect to login page
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
