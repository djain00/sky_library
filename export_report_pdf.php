<?php
require 'vendor/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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

$total_borrowed = $conn->query("SELECT COUNT(*) as total FROM borrowed_books WHERE borrow_date >= '$start_date'")->fetch_assoc()['total'];
$returned = $conn->query("SELECT COUNT(*) as total FROM borrowed_books WHERE return_date IS NOT NULL AND borrow_date >= '$start_date'")->fetch_assoc()['total'];
$still_borrowed = $total_borrowed - $returned;
$overdue = $conn->query("SELECT COUNT(*) as total FROM borrowed_books WHERE return_date IS NULL AND due_date < '$today' AND borrow_date >= '$start_date'")->fetch_assoc()['total'];

$html = "
<h2>📊 Library Report (" . strtoupper($filter) . ")</h2>
<hr>
<p><strong>Total Borrowed:</strong> $total_borrowed</p>
<p><strong>Returned:</strong> $returned</p>
<p><strong>Still Borrowed:</strong> $still_borrowed</p>
<p><strong>Overdue:</strong> $overdue</p>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("library_report.pdf", ["Attachment" => 0]);
exit;
