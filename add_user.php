<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sky_library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    if (empty($name) || empty($username) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $username, $hashedPassword, $role);

        if ($stmt->execute()) {
            $success = "User created successfully!";
        } else {
            $error = "Username already exists or failed to create user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User | Sky Library LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-lg bg-white p-8 rounded-2xl shadow-xl">

        <h2 class="text-2xl font-bold text-blue-800 mb-6">➕ Add New User</h2>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4 border border-red-200"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4 border border-green-200"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block mb-1 font-medium">Name</label>
                <input type="text" name="name" class="w-full border-gray-300 px-4 py-2 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
            </div>
            <div>
                <label class="block mb-1 font-medium">Username</label>
                <input type="text" name="username" class="w-full border-gray-300 px-4 py-2 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
            </div>
            <div>
                <label class="block mb-1 font-medium">Password</label>
                <input type="password" name="password" class="w-full border-gray-300 px-4 py-2 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
            </div>
            <div>
                <label class="block mb-1 font-medium">Role</label>
                <select name="role" class="w-full border-gray-300 px-4 py-2 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="users.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition shadow-sm">⬅ Back</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm">Create User</button>
            </div>
        </form>
    </div>
</body>
</html>
