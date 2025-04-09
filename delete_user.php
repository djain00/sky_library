<?php
session_start();

// Allow only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Get user ID
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = intval($_GET['id']);

// Prevent admin from deleting their own account (optional)
if ($_SESSION['user_id'] == $user_id) {
    header("Location: users.php?error=cannot_delete_self");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sky_library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: users.php");
exit;
?>
