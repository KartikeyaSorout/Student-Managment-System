# 🎓 Student Management System — PHP CRUD

A clean, dark-themed Student Management System built with PHP and MySQL.

---

## 📁 Project Structure

```
student-management/
├── index.php              ← Main page (View / Delete students)
├── setup.sql              ← Database setup script
├── includes/
│   └── db.php             ← Database connection (PDO)
└── pages/
    ├── add.php            ← Add new student
    └── edit.php           ← Edit existing student
```

---

## ⚙️ Setup Instructions

### Step 1 — Requirements
- PHP 7.4+ (or 8.x)
- MySQL 5.7+ or MariaDB
- A local server: **XAMPP**, **WAMP**, **MAMP**, or **Laragon**

---

### Step 2 — Set up the Database

1. Open **phpMyAdmin** (usually at `http://localhost/phpmyadmin`)
2. Click **SQL** tab
3. Paste the contents of `setup.sql` and click **Go**

This creates the `student_db` database and a `students` table with sample data.

---

### Step 3 — Configure Database Connection

Open `includes/db.php` and update if needed:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // your MySQL username
define('DB_PASS', '');         // your MySQL password
define('DB_NAME', 'student_db');
```

---

### Step 4 — Run the Project

1. Copy the `student-management/` folder into your server's web root:
   - XAMPP → `C:/xampp/htdocs/student-management/`
   - WAMP  → `C:/wamp64/www/student-management/`
   - MAMP  → `/Applications/MAMP/htdocs/student-management/`

2. Open your browser and go to:
   ```
   http://localhost/student-management/
   ```

---

## ✅ Features

| Feature         | Description                              |
|----------------|------------------------------------------|
| **View**        | See all students in a table with stats   |
| **Search**      | Filter by name or subject                |
| **Add**         | Register a new student with validation   |
| **Edit**        | Update any student's details             |
| **Delete**      | Remove a student with confirmation       |

---

## 🗄️ Database Table

```sql
students (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100),
    email      VARCHAR(150) UNIQUE,
    grade      VARCHAR(10),
    marks      DECIMAL(5,2),
    subject    VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
```
