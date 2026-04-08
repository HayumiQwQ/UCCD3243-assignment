<?php
require 'database.php';
include 'auth.php';

$action       = $_GET['action'] ?? '';
$student_name = $_SESSION['student_name'];

// Delete merit record — only if it belongs to the logged-in student
if ($action === 'delete' && isset($_GET['id'])) {
    $id   = (int)$_GET['id'];
    $stmt = mysqli_prepare($con, 'DELETE FROM merits WHERE id = ? AND student_name = ?');
    mysqli_stmt_bind_param($stmt, 'is', $id, $student_name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Merit record deleted.'];
    header('Location: merit-tracker.php');
    exit;
}

// Search / sort params
$q     = trim($_GET['q'] ?? '');
$sort  = $_GET['sort'] ?? 'date';
$order = strtolower($_GET['order'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

$allowed_sorts = ['hours', 'description', 'date'];
if (!in_array($sort, $allowed_sorts)) $sort = 'date';

// Build query — only show records belonging to the logged-in student
$esc_name = mysqli_real_escape_string($con, $student_name);
$conds    = ["student_name = '{$esc_name}'"];

if ($q !== '') {
    $esc_q   = mysqli_real_escape_string($con, $q);
    $like    = '%' . $esc_q . '%';
    $conds[] = "(description LIKE '{$like}' OR hours LIKE '{$like}' OR date LIKE '{$like}')";
}

$sql = 'SELECT * FROM merits';
if (count($conds) > 0) $sql .= ' WHERE ' . implode(' AND ', $conds);
$sql .= " ORDER BY {$sort} {$order}";

$merits = [];
$res = mysqli_query($con, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $merits[] = $row;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Merit Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Merit Tracker</h1>
    <div class="actions" style="margin-bottom:1rem;">
        <a class="btn" href="dashboard.php">&larr; Dashboard</a>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
        <?php $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
        <div class="form <?= $f['type'] === 'success' ? 'success' : 'error' ?>" style="max-width:100%;">
            <?= htmlspecialchars($f['msg']) ?>
        </div>
    <?php endif; ?>

    <div class="panel">
        <div class="actions">
            <a class="btn" href="merit-tracker-form.php">Add New Merit Record</a>
        </div>

        <form method="get" class="filters">
            <input type="text" name="q" placeholder="Search hours, description, date"
                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">

            <select name="sort">
                <option value="date" <?= (($_GET['sort'] ?? 'date') === 'date') ? 'selected' : '' ?>>Date</option>
                <option value="hours" <?= (($_GET['sort'] ?? '') === 'hours') ? 'selected' : '' ?>>Hours</option>
                <option value="description" <?= (($_GET['sort'] ?? '') === 'description') ? 'selected' : '' ?>>Description</option>
            </select>

            <select name="order">
                <option value="desc" <?= (($_GET['order'] ?? 'desc') === 'desc') ? 'selected' : '' ?>>Newest first</option>
                <option value="asc"  <?= (($_GET['order'] ?? '') === 'asc') ? 'selected' : '' ?>>Oldest first</option>
            </select>

            <button class="btn" type="submit">Apply</button>
            <a class="btn" href="merit-tracker.php">Reset</a>
        </form>
    </div>

    <div class="panel">
        <h2>Merit Records</h2>
        <?php if (count($merits) === 0): ?>
            <p class="muted">No merit records recorded yet.</p>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>Hours</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($merits as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['hours']) ?></td>
                        <td><?= nl2br(htmlspecialchars($m['description'])) ?></td>
                        <td><?= htmlspecialchars($m['date']) ?></td>
                        <td class="nowrap">
                            <a class="btn" href="merit-tracker-form.php?id=<?= $m['id'] ?>">Edit</a>
                            <a class="btn danger"
                               href="?action=delete&id=<?= $m['id'] ?>"
                               onclick="return confirm('Delete this merit record?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>
</body>
</html>