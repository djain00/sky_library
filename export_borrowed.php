<?php
require 'vendor/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Database connection
$conn = new mysqli("localhost", "root", "", "sky_library");

// Fetch borrowed books with joined data
$query = "
    SELECT 
        bb.id, b.title, b.author, u.name AS borrower_name,
        bb.borrow_date, bb.due_date, bb.return_date
    FROM borrowed_books bb
    JOIN books b ON bb.book_id = b.id
    JOIN users u ON bb.user_id = u.id
    ORDER BY bb.borrow_date DESC
";
$result = $conn->query($query);

// HTML content for the PDF
$html = '<h2 style="text-align:center;">📚 Borrowed Books Report</h2>';
$html .= '<table border="1" cellpadding="8" cellspacing="0" width="100%">
<tr>
    <th>Title</th>
    <th>Author</th>
    <th>Borrower</th>
    <th>Borrow Date</th>
    <th>Due Date</th>
    <th>Status</th>
</tr>';

while ($row = $result->fetch_assoc()) {
    $status = $row['return_date'] 
        ? 'Returned on ' . $row['return_date'] 
        : (date('Y-m-d') > $row['due_date'] ? '❗ Overdue' : 'Borrowed');

    $html .= "<tr>
        <td>" . htmlspecialchars($row['title']) . "</td>
        <td>" . htmlspecialchars($row['author']) . "</td>
        <td>" . htmlspecialchars($row['borrower_name']) . "</td>
        <td>" . $row['borrow_date'] . "</td>
        <td>" . $row['due_date'] . "</td>
        <td>" . $status . "</td>
    </tr>";
}
$html .= '</table>';

// Setup DomPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render and stream
$dompdf->render();
$dompdf->stream("borrowed_books_report.pdf", ["Attachment" => false]);
exit;
