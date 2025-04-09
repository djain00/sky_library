<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "sky_library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search_sql = "";
if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    $search_sql = "AND (
        books.title LIKE '%$search_safe%' OR 
        users.name LIKE '%$search_safe%' OR 
        borrowed_books.status LIKE '%$search_safe%'
    )";
}

$count_sql = "
    SELECT COUNT(*) AS total
    FROM borrowed_books
    INNER JOIN books ON borrowed_books.book_id = books.id
    INNER JOIN users ON borrowed_books.user_id = users.id
    WHERE 1=1
";
if ($role !== 'admin') {
    $count_sql .= " AND borrowed_books.user_id = $user_id ";
}
$count_sql .= " $search_sql";

$total_result = $conn->query($count_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

$sql = "
    SELECT borrowed_books.*, books.title, books.author, users.name AS borrower_name
    FROM borrowed_books
    INNER JOIN books ON borrowed_books.book_id = books.id
    INNER JOIN users ON borrowed_books.user_id = users.id
    WHERE 1=1
";
if ($role !== 'admin') {
    $sql .= " AND borrowed_books.user_id = $user_id ";
}
$sql .= " $search_sql ORDER BY borrowed_books.borrow_date DESC LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrowed Books | Sky Library LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-6">
    <div class="max-w-7xl mx-auto bg-white p-8 rounded-2xl shadow-xl">

        <!-- Top Buttons -->
        <div class="flex justify-between items-center mb-6">
            <div class="space-x-2">
                <a href="dashboard.php" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition font-medium shadow-sm">
                    ← Back to Dashboard
                </a>
            </div>
            <h2 class="text-3xl font-bold text-blue-800">📖 Borrowed Books</h2>
            <div class="flex space-x-2">
                <a href="borrow_book.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition shadow-sm">➕ Borrow a Book</a>
                <a href="export_borrowed.php" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition shadow-sm">📤 Export as PDF</a>
            </div>
        </div>

        <!-- Search -->
        <form method="GET" class="mb-6 flex">
            <input type="text" name="search" placeholder="Search by title, borrower, or status..." value="<?= htmlspecialchars($search) ?>"
                   class="flex-1 border border-gray-300 px-4 py-2 rounded-l-lg focus:ring-2 focus:ring-blue-300 outline-none transition">
            <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded-r-lg hover:bg-blue-700 transition">
                🔍 Search
            </button>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full table-auto border border-gray-300 text-sm">
                <thead class="bg-gray-200 text-center">
                    <tr>
                        <th class="p-2 border">#</th>
                        <th class="p-2 border">Title</th>
                        <th class="p-2 border">Author</th>
                        <?php if ($role === 'admin'): ?>
                            <th class="p-2 border">Borrower</th>
                        <?php endif; ?>
                        <th class="p-2 border">Borrow Date</th>
                        <th class="p-2 border">Return Date</th>
                        <th class="p-2 border">Status</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): $i = $offset + 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            $status = $row['return_date'] ? 'Returned' : 'Not Returned';
                            $isOverdue = false;
                            if (!$row['return_date']) {
                                $borrow_date = new DateTime($row['borrow_date']);
                                $now = new DateTime();
                                $interval = $borrow_date->diff($now)->days;
                                $isOverdue = $interval > 14;
                            }
                        ?>
                        <tr class="text-center <?= $isOverdue ? 'bg-red-50' : '' ?>">
                            <td class="p-2 border"><?= $i++ ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($row['title']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($row['author']) ?></td>
                            <?php if ($role === 'admin'): ?>
                                <td class="p-2 border"><?= htmlspecialchars($row['borrower_name']) ?></td>
                            <?php endif; ?>
                            <td class="p-2 border"><?= $row['borrow_date'] ?></td>
                            <td class="p-2 border"><?= $row['return_date'] ? $row['return_date'] : '—' ?></td>
                            <td class="p-2 border">
                                <?php if ($status === 'Returned'): ?>
                                    <span class="text-green-600 font-semibold">Returned</span>
                                <?php else: ?>
                                    <span class="text-red-600 font-semibold">Not Returned</span>
                                    <?php if ($isOverdue): ?>
                                        <span class="ml-2 bg-red-600 text-white px-2 py-0.5 rounded text-xs">Overdue</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="p-2 border space-x-1">
                                <?php if ($role === 'admin'): ?>
                                    <?php if (!$row['return_date']): ?>
                                        <a href="mark_returned.php?id=<?= $row['id'] ?>" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 text-xs">Mark Returned</a>
                                    <?php endif; ?>
                                    <a href="edit_borrow.php?id=<?= $row['id'] ?>" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 text-xs">Edit</a>
                                    <a href="delete_borrow.php?id=<?= $row['id'] ?>" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 text-xs" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $role === 'admin' ? 8 : 7 ?>" class="p-4 text-center text-gray-500">No borrowed books found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-6 space-x-1">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                   class="px-3 py-1 border rounded-lg <?= $i == $page ? 'bg-blue-600 text-white' : 'hover:bg-gray-200' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>

    </div>
</body>
</html>
