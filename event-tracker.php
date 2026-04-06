<?php
require 'database.php';
include 'auth.php';

$action = $_GET['action'] ?? '';

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

// Search / filter / sort params
$q = trim($_GET['q'] ?? '');
$filter_type = $_GET['type'] ?? '';
$sort = $_GET['sort'] ?? 'date_time';
$order = strtolower($_GET['order'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

$allowed_sorts = ['event_name', 'date_time', 'event_loc', 'event_type'];
if (!in_array($sort, $allowed_sorts)) $sort = 'date_time';

// Build query with basic escaping (simple app; use prepared statements for stronger safety)
$conds = [];
if ($q !== '') {
	$esc = mysqli_real_escape_string($con, $q);
	$like = "%" . $esc . "%";
	$conds[] = "(event_name LIKE '{$like}' OR event_loc LIKE '{$like}' OR description LIKE '{$like}')";
}
if ($filter_type !== '') {
	$escType = mysqli_real_escape_string($con, $filter_type);
	$conds[] = "event_type = '{$escType}'";
}

$sql = 'SELECT * FROM events';
if (count($conds) > 0) $sql .= ' WHERE ' . implode(' AND ', $conds);
$sql .= " ORDER BY {$sort} {$order}";

$events = [];
$res = mysqli_query($con, $sql);
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
		<form method="get" class="filters">
			<input type="text" name="q" placeholder="Search title, location, description" value="<?=htmlspecialchars($_GET['q'] ?? '')?>">
			<select name="type">
				<option value="">All types</option>
				<option value="Event" <?= (isset($_GET['type']) && $_GET['type']==='Event') ? 'selected' : '' ?>>Event</option>
				<option value="Competition" <?= (isset($_GET['type']) && $_GET['type']==='Competition') ? 'selected' : '' ?>>Competition</option>
				<option value="Workshop" <?= (isset($_GET['type']) && $_GET['type']==='Workshop') ? 'selected' : '' ?>>Workshop</option>
				<option value="Talks" <?= (isset($_GET['type']) && $_GET['type']==='Talks') ? 'selected' : '' ?>>Talk</option>
			</select>
			<select name="sort">
				<option value="date_time" <?= (isset($_GET['sort']) && $_GET['sort']==='date_time') ? 'selected' : '' ?>>Date</option>
				<option value="event_name" <?= (isset($_GET['sort']) && $_GET['sort']==='event_name') ? 'selected' : '' ?>>Title</option>
				<option value="event_loc" <?= (isset($_GET['sort']) && $_GET['sort']==='event_loc') ? 'selected' : '' ?>>Location</option>
				<option value="event_type" <?= (isset($_GET['sort']) && $_GET['sort']==='event_type') ? 'selected' : '' ?>>Type</option>
			</select>
			<select name="order">
				<option value="asc" <?= (isset($_GET['order']) && $_GET['order']==='asc') ? 'selected' : '' ?>>Asc</option>
				<option value="desc" <?= (isset($_GET['order']) && $_GET['order']==='desc') ? 'selected' : '' ?>>Desc</option>
			</select>
			<button class="btn" type="submit">Apply</button>
			<a class="btn" href="event-tracker.php">Reset</a>
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
