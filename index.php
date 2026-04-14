<?php
require_once 'includes/db.php';

// Delete student
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$id]);
    header("Location: index.php?msg=deleted"); exit;
}

// Delete single subject row
if (isset($_GET['delete_subject']) && is_numeric($_GET['delete_subject'])) {
    $sid = (int) $_GET['delete_subject'];
    $pdo->prepare("DELETE FROM student_subjects WHERE id = ?")->execute([$sid]);
    header("Location: index.php?msg=subject_deleted"); exit;
}

$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("SELECT DISTINCT s.* FROM students s
        LEFT JOIN student_subjects ss ON ss.student_id = s.id
        WHERE s.name LIKE ? OR s.email LIKE ? OR ss.subject LIKE ?
        ORDER BY s.created_at DESC");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY created_at DESC");
}
$students = $stmt->fetchAll();

// Fetch all subjects for all students in one query
$allSubjects = [];
if ($students) {
    $ids = implode(',', array_column($students, 'id'));
    $subs = $pdo->query("SELECT * FROM student_subjects WHERE student_id IN ($ids) ORDER BY subject ASC")->fetchAll();
    foreach ($subs as $sub) {
        $allSubjects[$sub['student_id']][] = $sub;
    }
}

$total      = count($students);
$totalSubs  = array_sum(array_map('count', $allSubjects));
$allMarks   = array_merge(...(array_map(fn($s) => array_column($s, 'marks'), $allSubjects) ?: [[]]));
$avgMarks   = $allMarks ? round(array_sum($allMarks) / count($allMarks), 1) : 0;
$topMarks   = $allMarks ? max($allMarks) : 0;
$msg        = $_GET['msg'] ?? '';

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

function marksBarColor($m) {
    if ($m >= 80) return 'linear-gradient(90deg,#43e8a0,#43e8d0)';
    if ($m >= 60) return 'linear-gradient(90deg,#ffaa6a,#ffca6a)';
    return 'linear-gradient(90deg,#ff6584,#ff8fab)';
}

function avatarInitials($name) {
    $parts = explode(' ', trim($name));
    $i = '';
    foreach ($parts as $p) $i .= strtoupper(substr($p,0,1));
    return substr($i,0,2);
}

function avatarBg($name) {
    $c = [['rgba(108,99,255,0.28)','#a09aff'],['rgba(255,101,132,0.25)','#ff8fab'],['rgba(67,232,160,0.22)','#43e8a0'],['rgba(255,170,106,0.22)','#ffaa6a'],['rgba(106,200,255,0.22)','#6ac8ff']];
    return $c[ord($name[0]) % 5];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
    --bg:#060612;--surface:#0d0d1f;--card:#11112a;
    --border:rgba(255,255,255,0.07);--border2:rgba(255,255,255,0.13);
    --accent:#6c63ff;--accent2:#ff6584;--accent3:#43e8a0;
    --text:#f0f0ff;--muted:#6b6b9a;--muted2:#9494c0;
}
body{font-family:'Outfit',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;overflow-x:hidden}

/* BG */
.bg-mesh{position:fixed;inset:0;z-index:0;overflow:hidden;pointer-events:none}
.bg-mesh::before{content:'';position:absolute;width:900px;height:900px;top:-250px;left:-200px;background:radial-gradient(circle,rgba(108,99,255,0.18) 0%,transparent 65%);animation:drift1 20s ease-in-out infinite alternate}
.bg-mesh::after{content:'';position:absolute;width:700px;height:700px;bottom:-150px;right:-150px;background:radial-gradient(circle,rgba(255,101,132,0.13) 0%,transparent 65%);animation:drift2 25s ease-in-out infinite alternate}
.bg-orb3{position:absolute;width:500px;height:500px;top:45%;left:55%;transform:translate(-50%,-50%);background:radial-gradient(circle,rgba(67,232,160,0.08) 0%,transparent 65%);animation:drift3 16s ease-in-out infinite alternate}
@keyframes drift1{from{transform:translate(0,0) scale(1)}to{transform:translate(100px,70px) scale(1.2)}}
@keyframes drift2{from{transform:translate(0,0) scale(1)}to{transform:translate(-70px,-90px) scale(1.25)}}
@keyframes drift3{from{transform:translate(-50%,-50%) scale(1)}to{transform:translate(-50%,-50%) scale(1.4)}}
.bg-grid{position:fixed;inset:0;z-index:0;pointer-events:none;background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:64px 64px;mask-image:radial-gradient(ellipse at 50% 50%,black 20%,transparent 75%);-webkit-mask-image:radial-gradient(ellipse at 50% 50%,black 20%,transparent 75%)}
.particle{position:fixed;border-radius:50%;pointer-events:none;z-index:0;animation:floatUp linear infinite;opacity:0}
@keyframes floatUp{0%{transform:translateY(100vh) scale(0);opacity:0}10%{opacity:1}90%{opacity:.5}100%{transform:translateY(-10vh) scale(1);opacity:0}}

/* NAVBAR */
.navbar{position:fixed;top:0;left:0;right:0;z-index:200;height:64px;display:flex;align-items:center;padding:0 1.5rem;gap:14px;background:rgba(6,6,18,0.78);backdrop-filter:blur(24px);border-bottom:1px solid var(--border);animation:slideDown .5s ease}
@keyframes slideDown{from{transform:translateY(-100%);opacity:0}to{transform:translateY(0);opacity:1}}
.logo-icon{width:34px;height:34px;border-radius:9px;flex-shrink:0;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;box-shadow:0 4px 18px rgba(108,99,255,.45);animation:iconPulse 3s ease-in-out infinite}
@keyframes iconPulse{0%,100%{box-shadow:0 4px 18px rgba(108,99,255,.45)}50%{box-shadow:0 4px 30px rgba(108,99,255,.75)}}
.nav-badge{padding:3px 11px;border-radius:20px;font-size:11px;font-weight:500;background:rgba(108,99,255,.14);border:1px solid rgba(108,99,255,.28);color:#a09aff}
.nav-avatar{width:34px;height:34px;border-radius:50%;margin-left:auto;background:linear-gradient(135deg,rgba(108,99,255,.38),rgba(255,101,132,.28));border:1px solid rgba(108,99,255,.4);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#a09aff}

/* LAYOUT */
.layout{display:flex;padding-top:64px;min-height:100vh;position:relative;z-index:10}

/* SIDEBAR */
.sidebar{width:240px;flex-shrink:0;background:rgba(8,8,20,.9);backdrop-filter:blur(24px);border-right:1px solid var(--border);padding:1.5rem 1rem;min-height:calc(100vh - 64px);position:sticky;top:64px;height:calc(100vh - 64px);animation:sideIn .4s ease .15s both}
@keyframes sideIn{from{opacity:0;transform:translateX(-24px)}to{opacity:1;transform:translateX(0)}}
.sidebar-label{font-size:10px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);padding:0 .75rem;margin-bottom:.5rem}
.sidebar-link{display:flex;align-items:center;gap:10px;padding:.6rem .85rem;border-radius:10px;font-family:'Outfit',sans-serif;font-size:14px;font-weight:400;margin-bottom:3px;text-decoration:none;background:transparent;color:var(--muted2);transition:all .2s}
.sidebar-link:hover{background:rgba(108,99,255,.12);color:var(--text)}
.sidebar-link.active{background:rgba(108,99,255,.15);border:1px solid rgba(108,99,255,.28);color:#a09aff;font-weight:500}

/* MAIN */
.main{flex:1;padding:2rem 1.75rem;min-width:0}
.page-eyebrow{font-size:11px;color:var(--muted);font-weight:600;letter-spacing:.12em;text-transform:uppercase;margin-bottom:6px}
.page-title{font-size:30px;font-weight:800;letter-spacing:-1px;margin-bottom:2rem;background:linear-gradient(135deg,var(--text) 40%,var(--muted2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}

/* ALERT */
.alert{padding:.85rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;font-size:13px;font-weight:500;display:flex;align-items:center;gap:9px;animation:cardIn .35s ease}
.alert-success{background:rgba(67,232,160,.12);border:1px solid rgba(67,232,160,.3);color:var(--accent3)}
@keyframes cardIn{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}

/* STATS */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:2rem}
.stat-card{background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:14px;padding:1.1rem 1.25rem;position:relative;overflow:hidden;transition:transform .25s,border-color .25s,box-shadow .25s;animation:cardIn .5s ease both}
.stat-card:hover{transform:translateY(-3px);border-color:var(--border2);box-shadow:0 8px 28px rgba(108,99,255,.12)}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;border-radius:14px 14px 0 0}
.stat-card:nth-child(1)::before{background:linear-gradient(90deg,var(--accent),#a09aff)}
.stat-card:nth-child(2)::before{background:linear-gradient(90deg,var(--accent3),#43e8d0)}
.stat-card:nth-child(3)::before{background:linear-gradient(90deg,var(--accent2),#ffaa6a)}
.stat-card:nth-child(4)::before{background:linear-gradient(90deg,#a09aff,var(--accent2))}
.stat-label{font-size:10px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
.stat-value{font-size:28px;font-weight:800;letter-spacing:-1px}

/* TOP BAR */
.top-bar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:1.5rem}
.section-title{font-weight:600;font-size:17px;color:var(--text)}
.search-wrap{position:relative;display:inline-block}
.search-input{background:rgba(255,255,255,.05);border:1px solid var(--border);border-radius:10px;color:var(--text);font-family:'Outfit',sans-serif;font-size:13px;padding:.55rem 1rem .55rem 2.4rem;width:230px;outline:none;transition:border-color .2s,background .2s,box-shadow .2s}
.search-input::placeholder{color:var(--muted)}
.search-input:focus{border-color:rgba(108,99,255,.5);background:rgba(108,99,255,.07);box-shadow:0 0 0 3px rgba(108,99,255,.12)}
.search-icon{position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none}

/* BUTTONS */
.btn-primary{display:inline-flex;align-items:center;gap:7px;padding:.5rem 1.1rem;border-radius:9px;border:none;cursor:pointer;font-family:'Outfit',sans-serif;font-size:13px;font-weight:600;background:linear-gradient(135deg,var(--accent),#8a83ff);color:#fff;box-shadow:0 4px 16px rgba(108,99,255,.38);transition:all .2s;text-decoration:none}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 22px rgba(108,99,255,.55)}
.btn-sm{padding:.35rem .85rem;font-size:12px}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:.35rem .85rem;border-radius:8px;font-family:'Outfit',sans-serif;font-size:12px;font-weight:500;text-decoration:none;transition:all .2s;cursor:pointer;border:none}
.btn-edit{background:rgba(108,99,255,.1);border:1px solid rgba(108,99,255,.25);color:#a09aff}
.btn-edit:hover{background:rgba(108,99,255,.22)}
.btn-danger{background:rgba(255,101,132,.09);border:1px solid rgba(255,101,132,.22);color:#ff8fab}
.btn-danger:hover{background:rgba(255,101,132,.2)}
.btn-add-subject{background:rgba(67,232,160,.1);border:1px solid rgba(67,232,160,.25);color:#43e8a0}
.btn-add-subject:hover{background:rgba(67,232,160,.2)}

/* TABLE WRAPPER */
.table-wrap{background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:16px;overflow:hidden;animation:cardIn .4s ease}

/* STUDENT SECTION */
.student-section{border-bottom:1px solid var(--border);animation:cardIn .4s ease both}
.student-section:last-child{border-bottom:none}

/* STUDENT HEADER ROW */
.student-header{display:flex;align-items:center;gap:14px;padding:1rem 1.25rem;background:rgba(108,99,255,.04);border-bottom:1px solid var(--border);cursor:pointer;transition:background .2s;user-select:none}
.student-header:hover{background:rgba(108,99,255,.09)}
.avatar{width:36px;height:36px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;position:relative}
.avatar::after{content:'';position:absolute;inset:-2px;border-radius:50%;background:conic-gradient(from 0deg,var(--accent),var(--accent2),var(--accent3),var(--accent));z-index:-1;opacity:.5;animation:spinBorder 5s linear infinite}
@keyframes spinBorder{to{transform:rotate(360deg)}}
.student-name{font-weight:600;font-size:15px;color:var(--text)}
.student-email{font-size:12px;color:var(--muted)}
.subject-count{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:rgba(108,99,255,.14);border:1px solid rgba(108,99,255,.28);color:#a09aff;margin-left:auto}
.toggle-icon{color:var(--muted);font-size:12px;margin-left:8px;transition:transform .25s}
.toggle-icon.open{transform:rotate(180deg)}

/* SUBJECT TABLE */
.subject-table-wrap{overflow:hidden;transition:max-height .35s cubic-bezier(.4,0,.2,1),opacity .3s}
.subject-table-wrap.collapsed{max-height:0!important;opacity:0}
table{width:100%;border-collapse:collapse}
thead tr{background:rgba(255,255,255,.02)}
th{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);padding:.7rem 1.25rem;text-align:left;border-bottom:1px solid var(--border)}
td{padding:.75rem 1.25rem;font-size:13px;border-bottom:1px solid rgba(255,255,255,.04);color:var(--text)}
tr:last-child td{border-bottom:none}
tbody tr{transition:background .15s}
tbody tr:hover{background:rgba(108,99,255,.05)}

.grade-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;letter-spacing:.03em}
.marks-wrap{display:flex;align-items:center;gap:10px}
.marks-bar-bg{width:80px;height:4px;background:rgba(255,255,255,.07);border-radius:2px;overflow:hidden}
.marks-bar-fill{height:100%;border-radius:2px}

/* STUDENT FOOTER (avg) */
.student-footer{display:flex;align-items:center;justify-content:flex-end;gap:20px;padding:.6rem 1.25rem;background:rgba(255,255,255,.015);border-top:1px solid rgba(255,255,255,.05);font-size:12px;color:var(--muted)}
.student-footer strong{color:var(--text)}

/* EMPTY */
.empty-state{text-align:center;padding:4rem 2rem;color:var(--muted);animation:cardIn .4s ease}

/* BACK BTN */
.back-btn{display:inline-flex;align-items:center;gap:7px;padding:.45rem 1rem;border-radius:9px;text-decoration:none;font-size:13px;font-weight:500;background:rgba(255,255,255,.06);border:1px solid var(--border);color:var(--muted2);transition:all .2s}
.back-btn:hover{background:rgba(255,255,255,.1);color:var(--text)}

::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:3px}
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
    <span class="nav-badge"><?= $total ?> students</span>
    <div class="nav-avatar">AD</div>
</nav>

<div class="layout">
    <aside class="sidebar">
        <p class="sidebar-label">Navigation</p>
        <a href="index.php" class="sidebar-link active"><span>👥</span> All Students</a>
        <a href="pages/add.php" class="sidebar-link"><span>➕</span> Add Student</a>
        <div style="height:1px;background:var(--border);margin:1.1rem 0"></div>
        <p class="sidebar-label">System</p>
        <div style="padding:.8rem;background:rgba(108,99,255,.08);border-radius:10px;border:1px solid rgba(108,99,255,.15)">
            <p style="font-size:11px;color:#a09aff;font-weight:700;margin-bottom:2px">Pro Plan</p>
            <p style="font-size:11px;color:var(--muted)">All features active</p>
        </div>
    </aside>

    <main class="main">
        <p class="page-eyebrow">Dashboard</p>
        <h1 class="page-title">Student Overview</h1>

        <?php if ($msg === 'deleted'):        ?><div class="alert alert-success">✓ Student deleted.</div>
        <?php elseif ($msg === 'added'):      ?><div class="alert alert-success">✓ Student added successfully.</div>
        <?php elseif ($msg === 'updated'):    ?><div class="alert alert-success">✓ Student updated successfully.</div>
        <?php elseif ($msg === 'subject_deleted'): ?><div class="alert alert-success">✓ Subject removed.</div>
        <?php elseif ($msg === 'subject_added'):   ?><div class="alert alert-success">✓ Subject added.</div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card" style="animation-delay:0s"><p class="stat-label">Total Students</p><p class="stat-value"><?= $total ?></p></div>
            <div class="stat-card" style="animation-delay:.08s"><p class="stat-label">Total Subjects</p><p class="stat-value"><?= $totalSubs ?></p></div>
            <div class="stat-card" style="animation-delay:.16s"><p class="stat-label">Avg Marks</p><p class="stat-value"><?= $avgMarks ?>%</p></div>
            <div class="stat-card" style="animation-delay:.24s"><p class="stat-label">Top Score</p><p class="stat-value"><?= $topMarks ?>%</p></div>
        </div>

        <!-- Top bar -->
        <div class="top-bar">
            <p class="section-title"><?= $search ? count($students).' results' : $total.' students' ?></p>
            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                <form method="GET" style="display:flex;gap:8px;align-items:center">
                    <div class="search-wrap">
                        <svg class="search-icon" width="13" height="13" viewBox="0 0 16 16" fill="none">
                            <circle cx="7" cy="7" r="5" stroke="rgba(107,107,154,1)" stroke-width="1.5"/>
                            <path d="M11 11l3 3" stroke="rgba(107,107,154,1)" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <input class="search-input" type="text" name="search" placeholder="Search name, email, subject…" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <button type="submit" class="btn-primary btn-sm">Search</button>
                    <?php if ($search): ?><a href="index.php" class="btn-primary btn-sm" style="background:rgba(255,255,255,.07);box-shadow:none;border:1px solid var(--border)">Clear</a><?php endif; ?>
                </form>
                <a href="pages/add.php" class="btn-primary">+ Add Student</a>
            </div>
        </div>

        <!-- Table -->
        <?php if (empty($students)): ?>
        <div class="empty-state">
            <p style="font-size:40px;margin-bottom:12px">🎓</p>
            <p style="font-size:15px;font-weight:500;margin-bottom:6px">No students found</p>
            <p style="font-size:13px;margin-bottom:1.5rem">Add your first student to get started</p>
            <a href="pages/add.php" class="btn-primary">+ Add Student</a>
        </div>
        <?php else: ?>
        <div class="table-wrap">
            <?php foreach ($students as $i => $s):
                $ac   = avatarBg($s['name']);
                $subs = $allSubjects[$s['id']] ?? [];
                $avg  = $subs ? round(array_sum(array_column($subs,'marks')) / count($subs), 1) : '-';
                $secId = 'sec-'.$s['id'];
            ?>
            <div class="student-section" style="animation-delay:<?= $i*.06 ?>s">

                <!-- Student header (clickable to expand/collapse) -->
                <div class="student-header" onclick="toggleSection('<?= $secId ?>')">
                    <div class="avatar" style="background:<?= $ac[0] ?>;color:<?= $ac[1] ?>">
                        <?= htmlspecialchars(avatarInitials($s['name'])) ?>
                    </div>
                    <div>
                        <p class="student-name"><?= htmlspecialchars($s['name']) ?></p>
                        <p class="student-email"><?= htmlspecialchars($s['email']) ?></p>
                    </div>
                    <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:rgba(67,232,160,0.1);border:1px solid rgba(67,232,160,0.25);color:#43e8a0;font-family:'JetBrains Mono',monospace">
                        ID #<?= str_pad($s['id'], 4, '0', STR_PAD_LEFT) ?>
                    </span>
                    <span class="subject-count"><?= count($subs) ?> subject<?= count($subs)!=1?'s':'' ?></span>
                    <div style="display:flex;align-items:center;gap:8px;margin-left:8px">
                        <a href="pages/add_subject.php?student_id=<?= $s['id'] ?>" class="btn-ghost btn-add-subject" onclick="event.stopPropagation()">+ Subject</a>
                        <a href="pages/edit.php?id=<?= $s['id'] ?>" class="btn-ghost btn-edit" onclick="event.stopPropagation()">✏ Edit</a>
                        <a href="index.php?delete=<?= $s['id'] ?>" class="btn-ghost btn-danger" onclick="event.stopPropagation();return confirm('Delete <?= htmlspecialchars($s['name']) ?> and all their subjects?')">✕ Delete</a>
                    </div>
                    <span class="toggle-icon open" id="icon-<?= $secId ?>">▾</span>
                </div>

                <!-- Subject table -->
                <div class="subject-table-wrap" id="<?= $secId ?>" style="max-height:600px;opacity:1">
                    <?php if (empty($subs)): ?>
                    <div style="padding:1.5rem 1.25rem;color:var(--muted);font-size:13px;text-align:center">
                        No subjects added yet. <a href="pages/add_subject.php?student_id=<?= $s['id'] ?>" style="color:#a09aff;text-decoration:none">Add one →</a>
                    </div>
                    <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Grade</th>
                                <th>Marks</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subs as $j => $sub):
                                $gc = gradeColor($sub['grade']);
                            ?>
                            <tr>
                                <td style="font-family:'JetBrains Mono',monospace;font-size:12px;color:#43e8a0;font-weight:600"><?= str_pad($s['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td style="color:var(--muted);font-size:12px"><?= $j+1 ?></td>
                                <td style="font-weight:500"><?= htmlspecialchars($sub['subject']) ?></td>
                                <td><span class="grade-badge" style="background:<?= $gc['bg'] ?>;color:<?= $gc['color'] ?>;border:1px solid <?= $gc['border'] ?>"><?= htmlspecialchars($sub['grade']) ?></span></td>
                                <td style="font-family:'JetBrains Mono',monospace;font-size:13px"><?= $sub['marks'] ?>%</td>
                                <td>
                                    <div class="marks-bar-bg">
                                        <div class="marks-bar-fill" style="width:<?= $sub['marks'] ?>%;background:<?= marksBarColor($sub['marks']) ?>"></div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex;gap:6px">
                                        <a href="pages/edit_subject.php?id=<?= $sub['id'] ?>" class="btn-ghost btn-edit">✏</a>
                                        <a href="index.php?delete_subject=<?= $sub['id'] ?>" class="btn-ghost btn-danger" onclick="return confirm('Remove <?= htmlspecialchars($sub['subject']) ?>?')">✕</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="student-footer">
                        <span>Subjects: <strong><?= count($subs) ?></strong></span>
                        <span>Average: <strong><?= $avg ?>%</strong></span>
                        <span>Best: <strong><?= max(array_column($subs,'marks')) ?>%</strong></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</div>

<script>
// Floating particles
const pc = document.getElementById('particles');
['#6c63ff','#ff6584','#43e8a0','#ffaa6a','#6ac8ff'].forEach((color,i) => {
    for (let j=0;j<4;j++) {
        const p = document.createElement('div');
        p.className = 'particle';
        const size = Math.random()*4+2;
        p.style.cssText = `width:${size}px;height:${size}px;left:${Math.random()*100}%;background:${color};box-shadow:0 0 ${size*2}px ${color};animation-duration:${Math.random()*20+15}s;animation-delay:${Math.random()*20}s`;
        pc.appendChild(p);
    }
});

// Toggle expand/collapse
function toggleSection(id) {
    const wrap = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    const isOpen = !wrap.classList.contains('collapsed');
    if (isOpen) {
        wrap.style.maxHeight = wrap.scrollHeight + 'px';
        requestAnimationFrame(() => {
            wrap.style.maxHeight = '0px';
            wrap.style.opacity = '0';
        });
        wrap.classList.add('collapsed');
        icon.classList.remove('open');
    } else {
        wrap.classList.remove('collapsed');
        wrap.style.maxHeight = '600px';
        wrap.style.opacity = '1';
        icon.classList.add('open');
    }
}
</script>
</body>
</html>
