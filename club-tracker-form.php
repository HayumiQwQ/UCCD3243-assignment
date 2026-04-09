<?php
include("auth.php");
include("database.php");

$student_name = $_SESSION['student_name'];
$query = "SELECT id FROM students WHERE name='$student_name'";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);
$student_id = $user['id'];

$editData = [];


// Get data to be display
$id = $_GET['id'];

$edit_query = "SELECT * FROM club_tracker 
               WHERE club_tracker_id='$id' 
               AND student_id='$student_id'";

$result = mysqli_query($con, $edit_query);
$editData = mysqli_fetch_assoc($result);


// Update the record
if(isset($_POST['update'])){

    $club_name = mysqli_real_escape_string($con,$_POST['club_name']);
    $club_role = mysqli_real_escape_string($con,$_POST['club_role']);
    $join_date = $_POST['join_date'];

    $update_query = "UPDATE club_tracker SET
                        club_name='$club_name',
                        club_role='$club_role',
                        join_date='$join_date'
                     WHERE club_tracker_id='$id'
                     AND student_id='$student_id'";

    mysqli_query($con,$update_query) or die(mysqli_error($con));

    header("Location: club-tracker.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Club Tracker</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<h1 id=club_heading>Edit Club Tracker</h1>

<div class="container">

<form method="POST" id=club_form>

<label>Club Name:</label>
<input type="text" name="club_name" required
value="<?php echo htmlspecialchars($editData['club_name']); ?>">

<label>Role:</label>
<select name="club_role" required>

<option value="President"
<?php if($editData['club_role']=="President") echo "selected"; ?>>
President
</option>

<option value="Vice President"
<?php if($editData['club_role']=="Vice President") echo "selected"; ?>>
Vice President
</option>

<option value="Treasurer"
<?php if($editData['club_role']=="Treasurer") echo "selected"; ?>>
Treasurer
</option>

<option value="Secretary"
<?php if($editData['club_role']=="Secretary") echo "selected"; ?>>
Secretary
</option>

<option value="Member"
<?php if($editData['club_role']=="Member") echo "selected"; ?>>
Member
</option>

</select>

<label>Join Date:</label>
<input type="date" name="join_date" required value="<?php echo $editData['join_date'] ?? ''; ?>">

<br><br>

<button type="submit" name="update" id=club_button>Update Club Tracker</button>
<a href="club-tracker.php" class="btn cancel-btn">Cancel</a>

</form>

</div>

</body>
</html>