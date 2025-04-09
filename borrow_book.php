<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sky_library");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];
    $user_id = $_POST['user_id'];
    $borrow_date = $_POST['borrow_date'];
    $due_date = date('Y-m-d', strtotime($borrow_date . ' +14 days'));

    $stmt = $conn->prepare("INSERT INTO borrowed_books (book_id, user_id, borrow_date, due_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $book_id, $user_id, $borrow_date, $due_date);
    $stmt->execute();

    header("Location: borrowed_books.php");
    exit;
}

$books = $conn->query("SELECT id, title FROM books");
$users = $conn->query("SELECT id, name FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Borrow a Book | Sky Library LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">📘 Borrow a Book</h2>
            <a href="borrowed_books.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">← Back</a>
        </div>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block font-semibold mb-1">Select Book</label>
                <select name="book_id" required class="w-full px-3 py-2 border rounded">
                    <?php while ($book = $books->fetch_assoc()): ?>
                        <option value="<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block font-semibold mb-1">Select Borrower</label>
                <select name="user_id" required class="w-full px-3 py-2 border rounded">
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block font-semibold mb-1">Borrow Date</label>
                <input type="date" name="borrow_date" value="<?= date('Y-m-d') ?>" required class="w-full px-3 py-2 border rounded">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    ➕ Borrow
                </button>
            </div>
        </form>
    </div>
</body>
</html>
