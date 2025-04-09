<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sky_library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$success = $error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Get the current password hash from database
    $result = $conn->query("SELECT password FROM users WHERE id = $user_id");
    $row = $result->fetch_assoc();

    // Verify using MySQL hash (SHA2)
    $hashed_input = hash('sha256', $current);
    if ($hashed_input !== $row['password']) {
        $error = "❌ Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $error = "❌ New passwords do not match.";
    } elseif (strlen($new) < 6) {
        $error = "❌ Password must be at least 6 characters.";
    } else {
        $new_hashed = hash('sha256', $new);
        $conn->query("UPDATE users SET password = '$new_hashed' WHERE id = $user_id");
        $success = "✅ Password changed successfully.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password | Sky Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">🔐 Change Password</h2>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-800 p-3 mb-4 rounded"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block font-semibold mb-1">Current Password</label>
                <input type="password" name="current_password" required class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block font-semibold mb-1">New Password</label>
                <input type="password" name="new_password" required class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block font-semibold mb-1">Confirm New Password</label>
                <input type="password" name="confirm_password" required class="w-full border p-2 rounded">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                🔁 Update Password
            </button>
        </form>

        <a href="dashboard.php" class="inline-block mt-4 text-blue-600 hover:underline">← Back to Dashboard</a>
    </div>
</body>
</html>
