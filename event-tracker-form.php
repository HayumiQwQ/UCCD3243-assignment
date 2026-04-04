<?php
require 'database.php';
include 'auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'] ?? '';
    $date_time = $_POST['date_time'] ?? '';
    $event_loc = $_POST['event_loc'] ?? '';
    $event_type = $_POST['event_type'] ?? '';
    $description = $_POST['description'] ?? '';
    $post_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($post_id > 0) {
        $stmt = mysqli_prepare($con, 'UPDATE events SET event_name = ?, date_time = ?, event_loc = ?, event_type = ?, description = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'sssssi', $event_name, $date_time, $event_loc, $event_type, $description, $post_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Event updated successfully.'];
    } else {
        $stmt = mysqli_prepare($con, 'INSERT INTO events (event_name, date_time, event_loc, event_type, description) VALUES (?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sssss', $event_name, $date_time, $event_loc, $event_type, $description);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Event added successfully.'];
    }

    header('Location: event-tracker.php');
    exit;
}

$event = null;
$dt_val = '';
if ($id > 0) {
    $stmt = mysqli_prepare($con, 'SELECT * FROM events WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $event = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    if ($event) {
        if (!empty($event['date_time'])) {
            $ts = strtotime($event['date_time']);
            if ($ts !== false) $dt_val = date('Y-m-d\TH:i', $ts);
        }
    } else {
        header('Location: event-tracker.php');
        exit;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $id > 0 ? 'Edit Event' : 'Add Event' ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1><?= $id > 0 ? 'Edit Event' : 'Add Event' ?></h1>

    <div class="panel">
        <form method="post" action="">
            <?php if ($id > 0): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>
            <label>Title<input name="event_name" value="<?= htmlspecialchars($event['event_name'] ?? '') ?>" required></label>
            <label>Date and Time<input type="datetime-local" name="date_time" value="<?= htmlspecialchars($dt_val) ?>" required></label>
            <label>Location<input name="event_loc" value="<?= htmlspecialchars($event['event_loc'] ?? '') ?>" required></label>
            <label>Event Type<select name="event_type" required>
                <option value="Event" <?= (isset($event['event_type']) && $event['event_type'] === 'Event') ? 'selected' : '' ?>>Event</option>
                <option value="Competition" <?= (isset($event['event_type']) && $event['event_type'] === 'Competition') ? 'selected' : '' ?>>Competition</option>
                <option value="Workshop" <?= (isset($event['event_type']) && $event['event_type'] === 'Workshop') ? 'selected' : '' ?>>Workshop</option>
                <option value="Talks" <?= (isset($event['event_type']) && $event['event_type'] === 'Talks') ? 'selected' : '' ?>>Talk</option>
            </select></label>
            <label>Description<textarea name="description" required><?= htmlspecialchars($event['description'] ?? '') ?></textarea></label>
            <div class="actions"><button type="submit"><?= $id > 0 ? 'Save Changes' : 'Add Event' ?></button> <a class="btn" href="event-tracker.php">Cancel</a></div>
        </form>
    </div>

</div>
</body>
</html>
