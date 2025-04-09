<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sky_library");

$id = $_GET['id'] ?? '';
if ($id) {
    $stmt = $conn->prepare("DELETE FROM borrowed_books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: borrowed_books.php");
exit;
