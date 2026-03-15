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
	header('Location: event-tracker.php');
	exit;
}

// Update event
if ($action === 'edit' && isset($_GET['id'])) {
	$id = (int)$_GET['id'];
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$event_name = $_POST['event_name'] ?? '';
		$date_time = $_POST['date_time'] ?? '';
		$event_loc = $_POST['event_loc'] ?? '';
		$event_type = $_POST['event_type'] ?? '';
		$description = $_POST['description'] ?? '';
		$query = mysqli_prepare($con, 'UPDATE events SET event_name = ?, date_time = ?, event_loc = ?, event_type = ?, description = ? WHERE id = ?');
		mysqli_stmt_bind_param($query, 'sssssi', $event_name, $date_time, $event_loc, $event_type, $description, $id);
		mysqli_stmt_execute($query);
		mysqli_stmt_close($query);
		header('Location: event-tracker.php');
		exit;
	}
	$query = mysqli_prepare($con, 'SELECT * FROM events WHERE id = ?');
	mysqli_stmt_bind_param($query, 'i', $id);
	mysqli_stmt_execute($query);
	$res = mysqli_stmt_get_result($query);
	$event = mysqli_fetch_assoc($res);
	mysqli_stmt_close($query);
}

// Delete event
if ($action === 'delete' && isset($_GET['id'])) {
	$id = (int)$_GET['id'];
	$stmt = mysqli_prepare($con, 'DELETE FROM events WHERE id = ?');
	mysqli_stmt_bind_param($stmt, 'i', $id);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
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

	<div class="panel">
		<h2>Add Event</h2>
		<form method="post" action="?action=add">
			<label>Title<input name="event_name" required></label>
			<label>Date and Time<input type="datetime-local" name="date_time" required></label>
			<label>Location<input name="event_loc" required></label>
			<label>Event Type<select name="event_type" required>
				<option value="Event">Event</option>
				<option value="Competition">Competition</option>
				<option value="Workshop">Workshop</option>
				<option value="Talks">Talk</option>
			</select></label>
			<label>Description<textarea name="description" required></textarea></label>
			<div class="actions"><button type="submit">Add Event</button></div>
		</form>
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
							<a class="btn" href="?action=edit&id=<?=$e['id']?>">Edit</a>
							<a class="btn danger" href="?action=delete&id=<?=$e['id']?>" onclick="return confirm('Delete this event?')">Delete</a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>

	<?php if (!empty($event) && $action === 'edit'): ?>
		<div class="panel">
			<h2>Edit Event</h2>
			<form method="post" action="?action=edit&id=<?=$event['id']?>">
				<label>Title<input name="event_name" value="<?=htmlspecialchars($event['event_name'])?>" required></label>
				<label>Date and Time<input type="datetime-local" name="date_time" value="<?=htmlspecialchars($event['date_time'])?>" required></label>
				<label>Location<input name="event_loc" value="<?=htmlspecialchars($event['event_loc'])?>" required></label>
				<label>Event Type<select name="event_type" required>
					<option value="Event" <?= $event['event_type'] === 'Event' ? 'selected' : '' ?>>Event</option>
					<option value="Competition" <?= $event['event_type'] === 'Competition' ? 'selected' : '' ?>>Competition</option>
					<option value="Workshop" <?= $event['event_type'] === 'Workshop' ? 'selected' : '' ?>>Workshop</option>
					<option value="Talks" <?= $event['event_type'] === 'Talks' ? 'selected' : '' ?>>Talk</option>
				</select></label>
				<label>Description<textarea name="description" required><?=htmlspecialchars($event['description'])?></textarea></label>
				<div class="actions"><button type="submit">Save Changes</button> <a class="btn" href="event-tracker.php">Cancel</a></div>
			</form>
		</div>
	<?php endif; ?>

</div>
</body>
</html>
