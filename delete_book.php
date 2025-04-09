<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: books.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sky_library");

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: books.php");
exit;
