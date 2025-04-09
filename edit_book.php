<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sky_library");

if (!isset($_GET['id'])) {
    header("Location: books.php");
    exit;
}

$id = $_GET['id'];

// Get existing book
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    echo "Book not found.";
    exit;
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $year = $_POST['year'];
    $genre = $_POST['genre'];

    $update = $conn->prepare("UPDATE books SET title = ?, author = ?, year_published = ?, genre = ? WHERE id = ?");
    $update->bind_param("ssisi", $title, $author, $year, $genre, $id);
    $update->execute();

    header("Location: books.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">✏️ Edit Book</h1>
        <form method="POST" class="space-y-4">
            <input name="title" value="<?= htmlspecialchars($book['title']) ?>" class="w-full p-2 border rounded" required>
            <input name="author" value="<?= htmlspecialchars($book['author']) ?>" class="w-full p-2 border rounded" required>
            <input name="year" type="number" value="<?= $book['year_published'] ?>" class="w-full p-2 border rounded">
            <input name="genre" value="<?= htmlspecialchars($book['genre']) ?>" class="w-full p-2 border rounded">
            <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update Book</button>
        </form>
        <a href="books.php" class="block mt-4 text-blue-500 hover:underline">← Back to Books</a>
    </div>
</body>
</html>
