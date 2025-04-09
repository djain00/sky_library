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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        $hashed = hash('sha256', $password);
        if ($hashed === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            header("Location: ../dashboard.php");
            exit;
        } else {
            $error = "❌ Invalid password.";
        }
    } else {
        $error = "❌ User not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Sky Library LMS</title>
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
        <h2 class="text-3xl font-bold text-center text-blue-800 mb-6">📚 Sky Library Login</h2>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded text-sm text-center font-medium shadow">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-gray-700 font-medium mb-1">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-300 outline-none transition" />
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-300 outline-none transition" />
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition shadow">
                Login
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-600">
            Don't have an account? 
            <a href="register.php" class="text-blue-600 hover:underline font-medium">Register</a>
        </p>
    </div>
</body>
</html>
