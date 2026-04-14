<?php
require_once '../includes/db.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header("Location: ../index.php"); exit; }

$stmt = $pdo->prepare("SELECT ss.*, s.name as student_name FROM student_subjects ss JOIN students s ON s.id = ss.student_id WHERE ss.id = ?");
$stmt->execute([$id]);
$sub = $stmt->fetch();
if (!$sub) { header("Location: ../index.php"); exit; }

$errors = [];
$data   = $sub;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['subject'] = trim($_POST['subject'] ?? '');
    $data['grade']   = trim($_POST['grade']   ?? '');
    $data['marks']   = trim($_POST['marks']   ?? '');

    if (!$data['subject'])                                          $errors[] = "Subject is required.";
    if (!$data['grade'])                                            $errors[] = "Grade is required.";
    if (!is_numeric($data['marks'])||$data['marks']<0||$data['marks']>100) $errors[] = "Marks must be 0–100.";

    if (empty($errors)) {
        $pdo->prepare("UPDATE student_subjects SET subject=?, grade=?, marks=? WHERE id=?")
            ->execute([$data['subject'], $data['grade'], $data['marks'], $id]);
        header("Location: ../index.php?msg=updated"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Edit Subject — SMS</title>
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
        <h1 class="page-title">Edit Subject</h1>
        <div style="display:inline-flex;align-items:center;gap:7px;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:500;background:rgba(255,101,132,.12);border:1px solid rgba(255,101,132,.28);color:#ff8fab;margin-bottom:1.5rem">
            ✏ <?= htmlspecialchars($sub['student_name']) ?> — <?= htmlspecialchars($sub['subject']) ?>
        </div>

        <div class="form-card" style="max-width:480px">
            <?php if (!empty($errors)): ?>
            <div class="errors-box">
                <?php foreach ($errors as $e): ?><p>⚠ <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <select class="form-input" name="subject" required>
                        <?php foreach (['Mathematics','Science','Computer Science','English','History','Physics','Chemistry','Biology','Economics','Geography'] as $s): ?>
                            <option value="<?= $s ?>" <?= $data['subject']===$s?'selected':'' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Grade</label>
                    <select class="form-input" name="grade" required>
                        <?php foreach (['A+','A','B+','B','C+','C','D','F'] as $g): ?>
                            <option value="<?= $g ?>" <?= $data['grade']===$g?'selected':'' ?>><?= $g ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Marks (0–100)</label>
                    <input class="form-input" type="number" name="marks" min="0" max="100" step="0.01" value="<?= htmlspecialchars($data['marks']) ?>" required>
                </div>
                <div class="divider"></div>
                <button type="submit" class="submit-btn">Save Changes</button>
            </form>
        </div>
    </main>
</div>
<script><?php include '../includes/particles.js.php'; ?></script>
</body>
</html>
