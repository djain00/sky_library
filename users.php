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

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management | Sky Library LMS</title>
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

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-blue-800">👥 User Management</h2>
            <div class="flex space-x-2">
                <a href="add_user.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition shadow-sm">➕ Add User</a>
                <a href="dashboard.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition shadow-sm">← Back to Dashboard</a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full table-auto border border-gray-300 text-sm">
                <thead class="bg-gray-200 text-center">
                    <tr>
                        <th class="p-2 border">S. No</th>
                        <th class="p-2 border">Name</th>
                        <th class="p-2 border">Username</th>
                        <th class="p-2 border">Role</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users && $users->num_rows > 0): $i = 1; ?>
                        <?php while ($row = $users->fetch_assoc()): ?>
                            <tr class="text-center hover:bg-gray-50">
                                <td class="p-2 border"><?= $i++ ?></td>
                                <td class="p-2 border"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="p-2 border"><?= htmlspecialchars($row['username']) ?></td>
                                <td class="p-2 border capitalize"><?= htmlspecialchars($row['role']) ?></td>
                                <td class="p-2 border space-x-2">
                                    <a href="edit_user.php?id=<?= $row['id'] ?>" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 text-xs">Edit</a>
                                    <a href="delete_user.php?id=<?= $row['id'] ?>" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 text-xs" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
