<?php
require 'database.php';
include 'auth.php';

$id           = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$student_name = $_SESSION['student_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hours       = $_POST['hours'] ?? '';
    $description = $_POST['description'] ?? '';
    $date        = $_POST['date'] ?? '';
    $post_id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($post_id > 0) {
        // UPDATE — only if the record belongs to the logged-in student
        $stmt = mysqli_prepare($con,
            'UPDATE merits
             SET hours = ?, description = ?, date = ?
             WHERE id = ? AND student_name = ?'
        );
        mysqli_stmt_bind_param($stmt, 'issis',
            $hours, $description, $date, $post_id, $student_name
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Merit record updated successfully.'];
    } else {
        // INSERT — tie the record to the logged-in student by name
        $stmt = mysqli_prepare($con,
            'INSERT INTO merits (student_name, hours, description, date)
             VALUES (?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param($stmt, 'siss',
            $student_name, $hours, $description, $date
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Merit record added successfully.'];
    }

    header('Location: merit-tracker.php');
    exit;
}

// Load existing record for editing
$merit = null;
if ($id > 0) {
    $stmt = mysqli_prepare($con,
        'SELECT * FROM merits WHERE id = ? AND student_name = ?'
    );
    mysqli_stmt_bind_param($stmt, 'is', $id, $student_name);
    mysqli_stmt_execute($stmt);
    $res   = mysqli_stmt_get_result($stmt);
    $merit = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$merit) {
        header('Location: merit-tracker.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $id > 0 ? 'Edit Merit Record' : 'Add Merit Record' ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1><?= $id > 0 ? 'Edit Merit Record' : 'Add Merit Record' ?></h1>

    <div class="panel">
        <form method="post" action="">
            <?php if ($id > 0): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif; ?>

            <label>Contribution Hours
                <input type="number" name="hours"
                       value="<?= htmlspecialchars($merit['hours'] ?? '') ?>"
                       required>
            </label>

            <label>Activity Description
                <textarea name="description" required><?= htmlspecialchars($merit['description'] ?? '') ?></textarea>
            </label>

            <label>Date
                <input type="date" name="date"
                       value="<?= htmlspecialchars($merit['date'] ?? '') ?>"
                       required>
            </label>

            <div class="actions">
                <button type="submit"><?= $id > 0 ? 'Save Changes' : 'Add Merit Record' ?></button>
                <a class="btn" href="merit-tracker.php">Cancel</a>
            </div>
        </form>
    </div>

</div>
</body>
</html>