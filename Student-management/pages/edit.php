<?php
require_once '../includes/db.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header("Location: ../index.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) { header("Location: ../index.php"); exit; }

$errors = [];
$data   = $student;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']  = trim($_POST['name']  ?? '');
    $data['email'] = trim($_POST['email'] ?? '');

    if (!$data['name'])                                     $errors[] = "Name is required.";
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";

    if (empty($errors)) {
        try {
            $pdo->prepare("UPDATE students SET name=?, email=? WHERE id=?")
                ->execute([$data['name'], $data['email'], $id]);
            header("Location: ../index.php?msg=updated"); exit;
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
<title>Edit Student — SMS</title>
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
        <a href="../index.php" class="sidebar-link active"><span>👥</span> All Students</a>
        <a href="add.php" class="sidebar-link"><span>➕</span> Add Student</a>
        <div style="height:1px;background:var(--border);margin:1.1rem 0"></div>
        <p class="sidebar-label">System</p>
        <div style="padding:.8rem;background:rgba(108,99,255,.08);border-radius:10px;border:1px solid rgba(108,99,255,.15)">
            <p style="font-size:11px;color:#a09aff;font-weight:700;margin-bottom:2px">Pro Plan</p>
            <p style="font-size:11px;color:var(--muted)">All features active</p>
        </div>
    </aside>

    <main class="main">
        <p class="page-eyebrow">Management</p>
        <h1 class="page-title">Edit Student</h1>
        <div style="display:inline-flex;align-items:center;gap:7px;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:500;background:rgba(255,101,132,.12);border:1px solid rgba(255,101,132,.28);color:#ff8fab;margin-bottom:1.5rem">
            ✏ Editing — <?= htmlspecialchars($student['name']) ?>
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
                    <input class="form-input" type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input class="form-input" type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
                </div>
                <div class="divider"></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <a href="add_subject.php?student_id=<?= $id ?>" class="submit-btn" style="text-align:center;text-decoration:none;background:rgba(108,99,255,.18);box-shadow:none;border:1px solid rgba(108,99,255,.35);color:#a09aff">
                        Manage Subjects
                    </a>
                    <button type="submit" class="submit-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </main>
</div>
<script><?php include '../includes/particles.js.php'; ?></script>
</body>
</html>
