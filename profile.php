<?php
session_start();

// Handle the profile picture update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile-picture'])) {
    $userId = $_SESSION['user_id']; // Assuming the user is logged in and user_id is stored in session

    // Check if the last profile picture change was more than a week ago
    $lastChange = $_SESSION['last_picture_change']; // You can retrieve this from the database
    if (time() - strtotime($lastChange) < 7 * 24 * 60 * 60) {
        echo json_encode(['error' => 'You can change your profile picture only once a week.']);
        exit;
    }

    // Handle file upload
    $file = $_FILES['profile-picture'];
    $filePath = 'uploads/' . $file['name'];

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Update the user's profile picture in the database
        $query = "UPDATE users SET profile_picture = ?, last_picture_change = NOW() WHERE user_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$filePath, $userId]);

        echo json_encode(['success' => true, 'newImagePath' => $filePath]);
    } else {
        echo json_encode(['error' => 'Failed to upload the profile picture.']);
    }
}

// Handle updating username and password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $newUsername = $_POST['username'];
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "UPDATE users SET username = ?, password = ? WHERE user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$newUsername, $newPassword, $_SESSION['user_id']]);

    echo json_encode(['success' => true]);
}
?>
