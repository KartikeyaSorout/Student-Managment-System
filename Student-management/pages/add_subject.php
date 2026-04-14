<?php
require_once '../includes/db.php';

$student_id = isset($_GET['student_id']) && is_numeric($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
if (!$student_id) { header("Location: ../index.php"); exit; }

$student = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$student->execute([$student_id]);
$student = $student->fetch();
if (!$student) { header("Location: ../index.php"); exit; }

$errors = [];
$data   = ['subject'=>'','grade'=>'','marks'=>''];
$isNew  = ($_GET['msg'] ?? '') === 'new'; // came straight from add.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['subject'] = trim($_POST['subject'] ?? '');
    $data['grade']   = trim($_POST['grade']   ?? '');
    $data['marks']   = trim($_POST['marks']   ?? '');

    if (!$data['subject'])                                        $errors[] = "Subject is required.";
    if (!$data['grade'])                                          $errors[] = "Grade is required.";
    if (!is_numeric($data['marks'])||$data['marks']<0||$data['marks']>100) $errors[] = "Marks must be 0–100.";

    if (empty($errors)) {
        $pdo->prepare("INSERT INTO student_subjects (student_id, subject, grade, marks) VALUES (?, ?, ?, ?)")
            ->execute([$student_id, $data['subject'], $data['grade'], $data['marks']]);

        if (isset($_POST['add_another'])) {
            header("Location: add_subject.php?student_id=$student_id&added=1");
        } else {
            header("Location: ../index.php?msg=subject_added");
        }
        exit;
    }
}

// Existing subjects for this student
$existing = $pdo->prepare("SELECT * FROM student_subjects WHERE student_id = ? ORDER BY subject ASC");
$existing->execute([$student_id]);
$existing = $existing->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Add Subject — SMS</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
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
    <a href="../index.php" class="back-btn">← Back to All Students</a>
    <div class="nav-avatar">AD</div>
</nav>

<div class="layout">
    <aside class="sidebar">
        <p class="sidebar-label">Navigation</p>
        <a href="../index.php" class="sidebar-link"><span>👥</span> All Students</a>
        <a href="add.php" class="sidebar-link"><span>➕</span> Add Student</a>
        <div style="height:1px;background:var(--border);margin:1.1rem 0"></div>
        <p class="sidebar-label">System</p>
        <div style="padding:.8rem;background:rgba(108,99,255,.08);border-radius:10px;border:1px solid rgba(108,99,255,.15)">
            <p style="font-size:11px;color:#a09aff;font-weight:700;margin-bottom:2px">Pro Plan</p>
            <p style="font-size:11px;color:var(--muted)">All features active</p>
        </div>
    </aside>

    <main class="main">
        <?php if ($isNew): ?>
        <p class="page-eyebrow">Step 2 of 2</p>
        <?php else: ?>
        <p class="page-eyebrow">Manage Subjects</p>
        <?php endif; ?>
        <h1 class="page-title">Add Subject</h1>
        <p style="font-size:13px;color:var(--muted);margin-top:-.5rem;margin-bottom:1.75rem">
            Student: <strong style="color:var(--text)"><?= htmlspecialchars($student['name']) ?></strong>
            &nbsp;·&nbsp; <?= htmlspecialchars($student['email']) ?>
        </p>

        <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success">✓ Subject added! Add another below or <a href="../index.php" style="color:#43e8a0">go back to dashboard</a>.</div>
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start;max-width:900px">

            <!-- Form -->
            <div class="form-card">
                <p style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:1.2rem">New Subject Entry</p>
                <?php if (!empty($errors)): ?>
                <div class="errors-box">
                    <?php foreach ($errors as $e): ?><p>⚠ <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
                </div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <select class="form-input" name="subject" required>
                            <option value="">Select subject</option>
                            <?php foreach (['Mathematics','Science','Computer Science','English','History','Physics','Chemistry','Biology','Economics','Geography'] as $sub): ?>
                                <option value="<?= $sub ?>" <?= $data['subject']===$sub?'selected':'' ?>><?= $sub ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Grade</label>
                        <select class="form-input" name="grade" required>
                            <option value="">Select grade</option>
                            <?php foreach (['A+','A','B+','B','C+','C','D','F'] as $g): ?>
                                <option value="<?= $g ?>" <?= $data['grade']===$g?'selected':'' ?>><?= $g ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Marks (0–100)</label>
                        <input class="form-input" type="number" name="marks" min="0" max="100" step="0.01" placeholder="e.g. 87.5" value="<?= htmlspecialchars($data['marks']) ?>" required>
                    </div>
                    <div class="divider"></div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <button type="submit" name="add_another" value="1" class="submit-btn" style="background:rgba(108,99,255,.2);box-shadow:none;border:1px solid rgba(108,99,255,.35);color:#a09aff">+ Add Another</button>
                        <button type="submit" class="submit-btn">Save & Finish</button>
                    </div>
                </form>
            </div>

            <!-- Existing subjects list -->
            <div>
                <p style="font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin-bottom:.75rem">
                    Subjects Added (<?= count($existing) ?>)
                </p>
                <?php if (empty($existing)): ?>
                    <div style="background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:12px;padding:1.5rem;text-align:center;color:var(--muted);font-size:13px">
                        No subjects yet
                    </div>
                <?php else: ?>
                <div style="background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:12px;overflow:hidden">
                    <table style="width:100%;border-collapse:collapse">
                        <thead>
                            <tr style="background:rgba(255,255,255,.02)">
                                <th style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);padding:.65rem 1rem;text-align:left;border-bottom:1px solid var(--border)">Subject</th>
                                <th style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);padding:.65rem 1rem;text-align:left;border-bottom:1px solid var(--border)">Grade</th>
                                <th style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);padding:.65rem 1rem;text-align:left;border-bottom:1px solid var(--border)">Marks</th>
                                <th style="border-bottom:1px solid var(--border)"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($existing as $sub):
                                $gc = gradeColor($sub['grade']);
                            ?>
                            <tr style="border-bottom:1px solid rgba(255,255,255,.04)">
                                <td style="padding:.7rem 1rem;font-size:13px;color:var(--text);font-weight:500"><?= htmlspecialchars($sub['subject']) ?></td>
                                <td style="padding:.7rem 1rem">
                                    <span style="font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;background:<?= $gc['bg'] ?>;color:<?= $gc['color'] ?>;border:1px solid <?= $gc['border'] ?>">
                                        <?= htmlspecialchars($sub['grade']) ?>
                                    </span>
                                </td>
                                <td style="padding:.7rem 1rem;font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--text)"><?= $sub['marks'] ?>%</td>
                                <td style="padding:.7rem 1rem">
                                    <a href="../index.php?delete_subject=<?= $sub['id'] ?>" onclick="return confirm('Remove <?= htmlspecialchars($sub['subject']) ?>?')"
                                       style="font-size:11px;color:#ff8fab;text-decoration:none;padding:3px 8px;border-radius:6px;border:1px solid rgba(255,101,132,.2);background:rgba(255,101,132,.08)">✕</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <?php if ($isNew && !empty($existing)): ?>
                <a href="../index.php?msg=added" class="submit-btn" style="display:block;text-align:center;margin-top:1rem;text-decoration:none">✓ Done — Go to Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<script><?php include '../includes/particles.js.php'; ?></script>
</body>
</html>
<?php
function gradeColor($grade) {
    $map = [
        'A+' => ['bg'=>'rgba(67,232,160,0.15)',  'color'=>'#43e8a0', 'border'=>'rgba(67,232,160,0.32)'],
        'A'  => ['bg'=>'rgba(108,99,255,0.18)',  'color'=>'#a09aff', 'border'=>'rgba(108,99,255,0.35)'],
        'B+' => ['bg'=>'rgba(255,170,106,0.15)', 'color'=>'#ffaa6a', 'border'=>'rgba(255,170,106,0.32)'],
        'B'  => ['bg'=>'rgba(255,170,106,0.1)',  'color'=>'#ffaa6a', 'border'=>'rgba(255,170,106,0.22)'],
        'C+' => ['bg'=>'rgba(106,200,255,0.15)', 'color'=>'#6ac8ff', 'border'=>'rgba(106,200,255,0.32)'],
        'C'  => ['bg'=>'rgba(106,200,255,0.1)',  'color'=>'#6ac8ff', 'border'=>'rgba(106,200,255,0.22)'],
        'D'  => ['bg'=>'rgba(255,101,132,0.12)', 'color'=>'#ff8fab', 'border'=>'rgba(255,101,132,0.28)'],
        'F'  => ['bg'=>'rgba(255,101,132,0.2)',  'color'=>'#ff6584', 'border'=>'rgba(255,101,132,0.38)'],
    ];
    return $map[$grade] ?? $map['F'];
}
?>
