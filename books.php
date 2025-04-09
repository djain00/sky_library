<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$role = $_SESSION['role'];

$conn = new mysqli("localhost", "root", "", "sky_library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($role === 'admin' && isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM books WHERE id = $id");
    header("Location: books.php");
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : "";

$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$searchQuery = "";
$params = [];

if (!empty($search)) {
    $searchQuery = "WHERE title LIKE ? OR author LIKE ? OR genre LIKE ?";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
}

$countStmt = $conn->prepare("SELECT COUNT(*) FROM books $searchQuery");
if (!empty($searchQuery)) {
    $countStmt->bind_param("sss", ...$params);
}
$countStmt->execute();
$countStmt->bind_result($totalBooks);
$countStmt->fetch();
$countStmt->close();

$totalPages = ceil($totalBooks / $limit);

$sql = "SELECT * FROM books $searchQuery ORDER BY id DESC LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
if (!empty($searchQuery)) {
    $stmt->bind_param("sss", ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Books | Sky Library LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-6">
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-2xl shadow-xl">

        <!-- Back to Dashboard -->
        <div class="mb-4">
            <a href="dashboard.php" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition font-medium shadow-sm">
                ← Back to Dashboard
            </a>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-blue-800">📘 Books Library</h1>
            <?php if ($role === 'admin'): ?>
                <a href="add_book.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium shadow">
                    ➕ Add New Book
                </a>
            <?php endif; ?>
        </div>

        <!-- Search -->
        <form method="GET" class="mb-6 flex gap-2">
            <input type="text" name="search" placeholder="Search by title, author, or genre"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-300 outline-none transition"
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium shadow">
                🔍 Search
            </button>
        </form>

        <!-- Books Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-blue-100 text-blue-900 font-semibold">
                    <tr>
                        <th class="p-3 border">S. No</th>
                        <th class="p-3 border text-left">Title</th>
                        <th class="p-3 border text-left">Author</th>
                        <th class="p-3 border text-left">Genre</th>
                        <?php if ($role === 'admin'): ?>
                            <th class="p-3 border text-center">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php if ($result->num_rows > 0): $i = $offset + 1; ?>
                        <?php while ($book = $result->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-blue-50">
                                <td class="p-3 text-center"><?= $i++ ?></td>
                                <td class="p-3"><?= htmlspecialchars($book['title']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($book['author']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($book['genre']) ?></td>
                                <?php if ($role === 'admin'): ?>
                                    <td class="p-3 text-center space-x-2">
                                        <a href="edit_book.php?id=<?= $book['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                                        <a href="books.php?delete=<?= $book['id'] ?>&search=<?= urlencode($search) ?>&page=<?= $page ?>"
                                           onclick="return confirm('Are you sure you want to delete this book?')"
                                           class="text-red-600 hover:underline">Delete</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $role === 'admin' ? 5 : 4 ?>" class="p-5 text-center text-gray-500">
                                No books found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-6 flex justify-center space-x-1">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"
                       class="px-4 py-2 border rounded-lg text-sm font-medium <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 hover:bg-blue-100' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
