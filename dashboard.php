<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$name = $_SESSION['name'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Sky Library LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-6">
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-lg">
        <!-- Header -->
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-bold text-blue-800">📚 Sky Library</h1>
                <p class="text-gray-600 mt-1">Welcome, <span class="font-semibold"><?= htmlspecialchars($name) ?></span> 
                    <span class="text-sm text-white bg-blue-600 px-2 py-0.5 rounded-full ml-2"><?= $role ?></span>
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="change_password.php" class="text-blue-600 hover:underline text-sm">🔐 Change Password</a>
                <a href="auth/logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 text-sm transition">Logout</a>
            </div>
        </div>

        <!-- First Row: Common Features -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
            <!-- View Books -->
            <a href="books.php" class="bg-blue-100 hover:bg-blue-200 transition p-6 rounded-xl shadow text-center">
                <div class="text-3xl mb-2">📘</div>
                <h3 class="font-semibold text-lg text-blue-800">View Books</h3>
            </a>

            <!-- Borrowed Books -->
            <a href="borrowed_books.php" class="bg-yellow-100 hover:bg-yellow-200 transition p-6 rounded-xl shadow text-center">
                <div class="text-3xl mb-2">📖</div>
                <h3 class="font-semibold text-lg text-yellow-800">Borrowed Books</h3>
            </a>
        </div>

        <!-- Second Row: Admin Features Centered -->
        <?php if ($role === 'admin'): ?>
        <div class="flex flex-wrap justify-center gap-6">
            <!-- Add New Book -->
            <a href="add_book.php" class="bg-green-100 hover:bg-green-200 transition p-6 rounded-xl shadow text-center w-64">
                <div class="text-3xl mb-2">➕</div>
                <h3 class="font-semibold text-lg text-green-800">Add New Book</h3>
            </a>

            <!-- Manage Users -->
            <a href="users.php" class="bg-purple-100 hover:bg-purple-200 transition p-6 rounded-xl shadow text-center w-64">
                <div class="text-3xl mb-2">👥</div>
                <h3 class="font-semibold text-lg text-purple-800">Manage Users</h3>
            </a>

            <!-- Reports -->
            <a href="report.php" class="bg-pink-100 hover:bg-pink-200 transition p-6 rounded-xl shadow text-center w-64">
                <div class="text-3xl mb-2">📊</div>
                <h3 class="font-semibold text-lg text-pink-800">Reports</h3>
            </a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
