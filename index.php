<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome | Sky Library LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-10 rounded-2xl shadow-xl text-center max-w-md w-full">
        <div class="mb-6">
            <h1 class="text-4xl font-bold text-blue-800 mb-2">📚 Sky Library</h1>
            <p class="text-gray-600">Manage your library with ease.</p>
        </div>
        <div class="flex justify-center space-x-4">
            <a href="auth/login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition font-semibold shadow">
                Login
            </a>
            <a href="auth/register.php" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition font-semibold shadow">
                Register
            </a>
        </div>
    </div>
</body>
</html>
