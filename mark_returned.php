<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: borrowed_books.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sky_library");
$id = $_GET['id'];

$stmt = $conn->prepare("UPDATE borrowed_books SET return_date = CURRENT_DATE WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: borrowed_books.php");
exit;
