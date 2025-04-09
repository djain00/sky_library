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

$filter = $_GET['filter'] ?? 'all';
$today = date('Y-m-d');
$start_date = '1970-01-01';

if ($filter === '7days') {
    $start_date = date('Y-m-d', strtotime('-7 days'));
} elseif ($filter === '30days') {
    $start_date = date('Y-m-d', strtotime('-30 days'));
}

$total_query = $conn->query("SELECT COUNT(*) as total FROM borrowed_books WHERE borrow_date >= '$start_date'");
$total_borrowed = $total_query->fetch_assoc()['total'];

$returned_query = $conn->query("SELECT COUNT(*) as total FROM borrowed_books WHERE return_date IS NOT NULL AND borrow_date >= '$start_date'");
$returned = $returned_query->fetch_assoc()['total'];

$still_borrowed = $total_borrowed - $returned;

$overdue_query = $conn->query("SELECT COUNT(*) as total FROM borrowed_books WHERE return_date IS NULL AND due_date < '$today' AND borrow_date >= '$start_date'");
$overdue = $overdue_query->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports | Sky Library LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-4xl bg-white p-8 rounded-2xl shadow-xl">
        <h2 class="text-3xl font-bold text-blue-800 mb-6">📊 Library Reports</h2>

        <form method="GET" class="mb-6 flex flex-wrap gap-4 items-center">
            <label class="font-semibold text-gray-700">Filter:</label>
            <select name="filter" onchange="this.form.submit()" class="border-gray-300 px-4 py-2 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Time</option>
                <option value="7days" <?= $filter === '7days' ? 'selected' : '' ?>>Last 7 Days</option>
                <option value="30days" <?= $filter === '30days' ? 'selected' : '' ?>>Last 30 Days</option>
            </select>
        </form>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-blue-100 p-6 rounded-xl text-center shadow">
                <p class="text-lg font-semibold text-blue-800">📚 Total Borrowed</p>
                <p class="text-4xl font-bold"><?= $total_borrowed ?></p>
            </div>
            <div class="bg-green-100 p-6 rounded-xl text-center shadow">
                <p class="text-lg font-semibold text-green-800">✅ Returned</p>
                <p class="text-4xl font-bold"><?= $returned ?></p>
            </div>
            <div class="bg-yellow-100 p-6 rounded-xl text-center shadow">
                <p class="text-lg font-semibold text-yellow-800">⏳ Still Borrowed</p>
                <p class="text-4xl font-bold"><?= $still_borrowed ?></p>
            </div>
            <div class="bg-red-100 p-6 rounded-xl text-center shadow">
                <p class="text-lg font-semibold text-red-800">❗ Overdue</p>
                <p class="text-4xl font-bold"><?= $overdue ?></p>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap justify-between items-center gap-4">
            <a href="export_report_pdf.php?filter=<?= $filter ?>" target="_blank"
               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition shadow-sm">
               📄 Export as PDF
            </a>
            <a href="dashboard.php" class="text-blue-600 hover:underline text-sm">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
