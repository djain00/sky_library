# 📚 Sky Library

Sky Library is a simple, responsive Library Management System built using PHP, MySQL, and Tailwind CSS. It includes role-based access (admin/user), book and borrow management, overdue tracking, reporting, and PDF export features.

## 🚀 Features
- User authentication (Admin/User)
- View, add, edit, delete books
- Borrow and return system with due dates
- Overdue detection
- Search + pagination
- Reports (All time / Last 7 / Last 30 days)
- Export reports and borrow records to PDF
- Change password
- Fully mobile-friendly design

## ⚙️ Quick Setup
1. **Database**
   - Import `init.sql` from database folder into your mysql.
     
2. **Run Locally**
   - Place project in your local server root (e.g., `htdocs` for XAMPP)
   - Start Apache & MySQL
   - Visit: `http://localhost/sky_library`

3. **Login**
   - Username: `admin`
   - Password: `admin123`

## 📁 Key Files
- `dashboard.php` – Main dashboard
- `books.php`, `add_book.php` – Book management
- `borrowed_books.php` – Borrow system
- `report.php`, `export_report_pdf.php` – Reporting
- `change_password.php` – Password update
- `auth/` – Login & logout
- `init.sql` – Full database setup

## 🛠 Tech Used
PHP (Procedural), MySQL, Tailwind CSS, DomPDF (PDF exports)

## 📌 Notes
- Admins manage everything; users can only borrow/view own data.
- Overdue books are auto-labeled if past due.
- Works perfectly on mobile and desktop.

## **Best Suitable For School and Collages Project**
