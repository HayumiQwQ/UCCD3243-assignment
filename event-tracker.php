<?php
require 'database.php';
include 'auth.php';

$action = $_GET['action'] ?? '';

// Add event
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
	$event_name = $_POST['event_name'] ?? '';
	$date_time = $_POST['date_time'] ?? '';
	$event_loc = $_POST['event_loc'] ?? '';
	$event_type = $_POST['event_type'] ?? '';
	$description = $_POST['description'] ?? '';

	$query = mysqli_prepare($con, 'INSERT INTO events (event_name, date_time, event_loc, event_type, description) VALUES (?, ?, ?, ?, ?)');
	mysqli_stmt_bind_param($query, 'sssss', $event_name, $date_time, $event_loc, $event_type, $description);
	mysqli_stmt_execute($query);
	mysqli_stmt_close($query);
	$_SESSION['flash'] = ['type' => 'success', 'msg' => 'Event added successfully.'];
	header('Location: event-tracker.php');
	exit;
}

// Delete event
if ($action === 'delete' && isset($_GET['id'])) {
	$id = (int)$_GET['id'];
	$stmt = mysqli_prepare($con, 'DELETE FROM events WHERE id = ?');
	mysqli_stmt_bind_param($stmt, 'i', $id);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	$_SESSION['flash'] = ['type' => 'success', 'msg' => 'Event deleted.'];
	header('Location: event-tracker.php');
	exit;
}

// Fetch events
$events = [];
$res = mysqli_query($con, 'SELECT * FROM events');
if ($res) {
	while ($row = mysqli_fetch_assoc($res)) {
		$events[] = $row;
	}
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Event Tracker</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
	<h1>Event Tracker</h1>
	<div class="actions" style="margin-bottom:1rem;">
		<a class="btn" href="dashboard.php">&larr; Dashboard</a>
	</div>

	<?php if (!empty($_SESSION['flash'])): ?>
		<?php $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
		<div class="form <?= $f['type'] === 'success' ? 'success' : 'error' ?>" style="max-width:100%;">
			<?=htmlspecialchars($f['msg'])?>
		</div>
	<?php endif; ?>

	<div class="panel">
		<div class="actions"><a class="btn" href="event-tracker-form.php">Add New Event</a></div>
	</div>

	<div class="panel">
		<h2>Events</h2>
		<?php if (count($events) === 0): ?>
			<p class="muted">No events yet.</p>
		<?php else: ?>
			<table>
				<thead>
				<tr><th>Title</th><th>Date</th><th>Location</th><th>Event Type</th><th>Description</th><th>Actions</th></tr>
				</thead>
				<tbody>
				<?php foreach ($events as $e): ?>
					<tr>
						<td><?=htmlspecialchars($e['event_name'])?></td>
						<td><?=htmlspecialchars($e['date_time'])?></td>
						<td><?=htmlspecialchars($e['event_loc'])?></td>
						<td><?=htmlspecialchars($e['event_type'])?></td>
						<td><?=nl2br(htmlspecialchars($e['description']))?></td>
						<td class="nowrap">
							<a class="btn" href="event-tracker-form.php?id=<?=$e['id']?>">Edit</a>
							<a class="btn danger" href="?action=delete&id=<?=$e['id']?>" onclick="return confirm('Delete this event?')">Delete</a>
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
