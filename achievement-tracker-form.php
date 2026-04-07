<?php
require 'database.php';
include 'auth.php';

$id           = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$student_name = $_SESSION['student_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title            = $_POST['title']            ?? '';
    $achievement_type = $_POST['achievement_type'] ?? '';
    $date_received    = $_POST['date_received']    ?? '';
    $organisation     = $_POST['organisation']     ?? '';
    $description      = $_POST['description']      ?? '';
    $post_id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($post_id > 0) {
        // UPDATE — only if the record belongs to the logged-in student
        $stmt = mysqli_prepare($con,
            'UPDATE achievements
             SET title = ?, achievement_type = ?, date_received = ?, organisation = ?, description = ?
             WHERE id = ? AND student_name = ?'
        );
        mysqli_stmt_bind_param($stmt, 'sssssis',
            $title, $achievement_type, $date_received, $organisation, $description,
            $post_id, $student_name
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Achievement updated successfully.'];
    } else {
        // INSERT — tie the record to the logged-in student by name
        $stmt = mysqli_prepare($con,
            'INSERT INTO achievements (student_name, title, achievement_type, date_received, organisation, description)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param($stmt, 'ssssss',
            $student_name, $title, $achievement_type, $date_received, $organisation, $description
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Achievement added successfully.'];
    }

    header('Location: achievement-tracker.php');
    exit;
}

// Load existing record for editing
$achievement = null;
if ($id > 0) {
    $stmt = mysqli_prepare($con,
        'SELECT * FROM achievements WHERE id = ? AND student_name = ?'
    );
    mysqli_stmt_bind_param($stmt, 'is', $id, $student_name);
    mysqli_stmt_execute($stmt);
    $res         = mysqli_stmt_get_result($stmt);
    $achievement = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$achievement) {
        // Record not found or doesn't belong to this student
        header('Location: achievement-tracker.php');
        exit;
    }
}

$types = ['Award', 'Certificate', 'Recognition', 'Scholarship', 'Other'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $id > 0 ? 'Edit Achievement' : 'Add Achievement' ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1><?= $id > 0 ? 'Edit Achievement' : 'Add Achievement' ?></h1>

    <div class="panel">
        <form method="post" action="">
            <?php if ($id > 0): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif; ?>

            <label>Title
                <input name="title"
                       value="<?= htmlspecialchars($achievement['title'] ?? '') ?>"
                       required>
            </label>

            <label>Achievement Type
                <select name="achievement_type" required>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= $t ?>"
                            <?= (isset($achievement['achievement_type']) && $achievement['achievement_type'] === $t) ? 'selected' : '' ?>>
                            <?= $t ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>Date Received
                <input type="date" name="date_received"
                       value="<?= htmlspecialchars($achievement['date_received'] ?? '') ?>"
                       required>
            </label>

            <label>Issuing Organisation
                <input name="organisation"
                       value="<?= htmlspecialchars($achievement['organisation'] ?? '') ?>"
                       required>
            </label>

            <label>Description
                <textarea name="description" required><?= htmlspecialchars($achievement['description'] ?? '') ?></textarea>
            </label>

            <div class="actions">
                <button type="submit"><?= $id > 0 ? 'Save Changes' : 'Add Achievement' ?></button>
                <a class="btn" href="achievement-tracker.php">Cancel</a>
            </div>
        </form>
    </div>

</div>
</body>
</html>
