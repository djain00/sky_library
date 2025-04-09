<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sky_library");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $year = $_POST['year'];
    $genre = $_POST['genre'];

    $stmt = $conn->prepare("INSERT INTO books (title, author, year_published, genre) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $title, $author, $year, $genre);
    $stmt->execute();

    header("Location: books.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Book | Sky Library LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-6">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-2xl shadow-xl">

        <!-- Back to Books -->
        <div class="mb-4">
            <a href="books.php" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition font-medium shadow-sm">
                ← Back to Books
            </a>
        </div>

        <h1 class="text-3xl font-bold text-blue-800 mb-6">➕ Add New Book</h1>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input name="title" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-300 outline-none transition"
                       placeholder="Book Title">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                <input name="author" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-300 outline-none transition"
                       placeholder="Author Name">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year Published</label>
                <input type="number" name="year"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-300 outline-none transition"
                       placeholder="e.g. 2022">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
                <input name="genre"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-300 outline-none transition"
                       placeholder="Genre (e.g. Fiction, History)">
            </div>

            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold shadow">
                📚 Add Book
            </button>
        </form>
    </div>
</body>
</html>
