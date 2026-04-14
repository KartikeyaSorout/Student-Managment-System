<?php
require_once '../includes/db.php';
$errors = [];
$data   = ['name'=>'','email'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']  = trim($_POST['name']  ?? '');
    $data['email'] = trim($_POST['email'] ?? '');

    if (!$data['name'])                                     $errors[] = "Name is required.";
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (name, email) VALUES (?, ?)");
            $stmt->execute([$data['name'], $data['email']]);
            $newId = $pdo->lastInsertId();
            header("Location: add_subject.php?student_id=$newId&msg=new");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Email already exists or a database error occurred.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Add Student — SMS</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
<?php include '../includes/common.css.php'; ?>
</style>
</head>
<body>
<div class="bg-mesh"><div class="bg-orb3"></div></div>
<div class="bg-grid"></div>
<div id="particles"></div>

<nav class="navbar">
    <div class="logo-icon">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
            <path d="M8 1L1 5l7 4 7-4-7-4z" stroke="#fff" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M1 10l7 4 7-4" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
    </div>
    <span style="font-weight:700;font-size:15px;letter-spacing:-.3px">Student MS</span>
    <a href="../index.php" class="back-btn">← Back</a>
    <div class="nav-avatar">AD</div>
</nav>

<div class="layout">
    <aside class="sidebar">
        <p class="sidebar-label">Navigation</p>
        <a href="../index.php" class="sidebar-link"><span>👥</span> All Students</a>
        <a href="add.php" class="sidebar-link active"><span>➕</span> Add Student</a>
        <div style="height:1px;background:var(--border);margin:1.1rem 0"></div>
        <p class="sidebar-label">System</p>
        <div style="padding:.8rem;background:rgba(108,99,255,.08);border-radius:10px;border:1px solid rgba(108,99,255,.15)">
            <p style="font-size:11px;color:#a09aff;font-weight:700;margin-bottom:2px">Pro Plan</p>
            <p style="font-size:11px;color:var(--muted)">All features active</p>
        </div>
    </aside>

    <main class="main">
        <p class="page-eyebrow">Step 1 of 2</p>
        <h1 class="page-title">Add Student</h1>

        <!-- Step indicator -->
        <div style="display:flex;align-items:center;gap:0;margin-bottom:2rem;max-width:400px">
            <div style="display:flex;align-items:center;gap:8px">
                <div style="width:28px;height:28px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700">1</div>
                <span style="font-size:13px;font-weight:500;color:var(--text)">Student Info</span>
            </div>
            <div style="flex:1;height:1px;background:var(--border);margin:0 12px"></div>
            <div style="display:flex;align-items:center;gap:8px">
                <div style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.07);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--muted)">2</div>
                <span style="font-size:13px;color:var(--muted)">Add Subjects</span>
            </div>
        </div>

        <div class="form-card" style="max-width:480px">
            <?php if (!empty($errors)): ?>
            <div class="errors-box">
                <?php foreach ($errors as $e): ?><p>⚠ <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input class="form-input" type="text" name="name" placeholder="e.g. Aarav Sharma" value="<?= htmlspecialchars($data['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input class="form-input" type="email" name="email" placeholder="student@example.com" value="<?= htmlspecialchars($data['email']) ?>" required>
                </div>
                <div class="divider"></div>
                <button type="submit" class="submit-btn">Next: Add Subjects →</button>
            </form>
        </div>
    </main>
</div>
<script><?php include '../includes/particles.js.php'; ?></script>
</body>
</html>
