<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sky_library");

$id = $_GET['id'] ?? '';
if (!$id) {
    header("Location: borrowed_books.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $book_id = $_POST['book_id'];
    $user_id = $_POST['user_id'];
    $borrow_date = $_POST['borrow_date'];
    $return_date = $_POST['return_date'] ?: null;

    $stmt = $conn->prepare("UPDATE borrowed_books SET book_id = ?, user_id = ?, borrow_date = ?, return_date = ? WHERE id = ?");
    $stmt->bind_param("iissi", $book_id, $user_id, $borrow_date, $return_date, $id);
    $stmt->execute();

    header("Location: borrowed_books.php");
    exit;
}

// Fetch existing record
$stmt = $conn->prepare("SELECT * FROM borrowed_books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$borrow = $stmt->get_result()->fetch_assoc();

$books = $conn->query("SELECT id, title FROM books ORDER BY title");
$users = $conn->query("SELECT id, name FROM users ORDER BY name");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Borrow Record</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">✏️ Edit Borrow Record</h1>
        <a href="borrowed_books.php" class="text-blue-500 hover:underline mb-4 inline-block">← Back</a>

        <form method="POST" class="bg-white p-6 rounded shadow space-y-4">
            <div>
                <label class="block mb-1 font-medium">Book</label>
                <select name="book_id" required class="w-full border p-2 rounded">
                    <?php while ($book = $books->fetch_assoc()): ?>
                        <option value="<?= $book['id'] ?>" <?= $book['id'] == $borrow['book_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($book['title']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block mb-1 font-medium">Borrower</label>
                <select name="user_id" required class="w-full border p-2 rounded">
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <option value="<?= $user['id'] ?>" <?= $user['id'] == $borrow['user_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block mb-1 font-medium">Borrow Date</label>
                <input type="date" name="borrow_date" value="<?= $borrow['borrow_date'] ?>" required class="w-full border p-2 rounded" />
            </div>

            <div>
                <label class="block mb-1 font-medium">Return Date (optional)</label>
                <input type="date" name="return_date" value="<?= $borrow['return_date'] ?>" class="w-full border p-2 rounded" />
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Record</button>
        </form>
    </div>
</body>
</html>
