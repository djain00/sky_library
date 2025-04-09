<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sky_library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($username) || empty($name) || empty($password) || empty($confirm)) {
        $error = "❌ All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "❌ Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "❌ Password must be at least 6 characters.";
    } else {
        $check = $conn->query("SELECT id FROM users WHERE username = '$username'");
        if ($check->num_rows > 0) {
            $error = "❌ Username already taken.";
        } else {
            $hashed = hash('sha256', $password);
            $conn->query("INSERT INTO users (username, password, name, role) VALUES ('$username', '$hashed', '$name', 'user')");
            $success = "✅ Account created! You can now <a href='login.php' class='underline text-blue-600'>Login here</a>.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Sky Library LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-10 rounded-2xl shadow-xl w-full max-w-md">
        <h2 class="text-3xl font-bold text-center text-blue-800 mb-6">📝 Create Your Account</h2>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-800 p-3 mb-4 rounded text-sm text-center font-medium shadow"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded text-sm text-center font-medium shadow"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-gray-700 font-medium mb-1">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-300 outline-none transition">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Full Name</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-300 outline-none transition">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-300 outline-none transition">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-300 outline-none transition">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition shadow">
                ➕ Create Account
            </button>
        </form>

        <p class="mt-5 text-center text-sm text-gray-600">
            Already have an account? 
            <a href="login.php" class="text-blue-600 hover:underline font-medium">Login</a>
        </p>
    </div>
</body>
</html>
