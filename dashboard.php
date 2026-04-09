<?php
require 'database.php';
include 'auth.php';

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Dashboard</h1>

    <div class="panel">
        <h2>Modules</h2>
        <ul style="list-style:none;padding:0;margin:0;">
            <li style="margin-bottom:8px;"><a class="btn" href="event-tracker.php">Event Tracker</a></li><br>
            <li style="margin-bottom:8px;"><a class="btn" href="achievement-tracker.php">Achievement Tracker</a></li><br>
            <li style="margin-bottom:8px;"><a class="btn" href="merit-tracker.php">Merit Tracker</a></li><br>
        </ul>
    </div>

</div>
</body>
</html>
