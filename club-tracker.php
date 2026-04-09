<?php
include("auth.php");      // session check
include("database.php");  // DB connection

$student_name = $_SESSION['student_name'];
$query = "SELECT id FROM students WHERE name='$student_name'";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);
$student_id = $user['id'];

$editData = [];

// Add New Club Tracker
if (isset($_POST['new'])) {
    $club_tracker_id = $_POST['club_tracker_id'];
    $club_name = mysqli_real_escape_string($con, $_POST['club_name']);
    $club_role = mysqli_real_escape_string($con, $_POST['club_role']);
    $join_date = $_POST['join_date'];
    
    $ins_query="INSERT INTO club_tracker (student_id, club_name, club_role, join_date)VALUES ('$student_id','$club_name','$club_role','$join_date')";

    mysqli_query($con, $ins_query) or die(mysqli_error($con));
    
    header("Location: club-tracker.php");
    exit();
}


// Delete Selected Club Tracker
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $del_query= "DELETE FROM club_tracker WHERE club_tracker_id='$id' AND student_id='$student_id'";


    mysqli_query($con, $del_query);

    header("Location: club-tracker.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Club Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1 id=club_heading>Club Tracker Module</h1>
<div class="actions" style="margin-bottom:1rem;">
        <a class="btn" href="dashboard.php">&larr; Dashboard</a>
    </div>

<div class="container">

<form method="POST" id=club_form>
    <input type="hidden" name="club_tracker_id" value="<?php echo $editData['club_tracker_id'] ?? ''; ?>">

    <label>Club Name:</label>
    <input type="text" name="club_name" required
           value="<?php echo $editData['club_name'] ?? ''; ?>">

    <label>Role:</label>
    <select name="club_role" required>
    <option value="">-- Select Role --</option>

    <option value="President"
        <?php if(($editData['club_role'] ?? '') == "President") echo "selected"; ?>>
        President
    </option>

    <option value="Vice President"
        <?php if(($editData['club_role'] ?? '') == "Vice President") echo "selected"; ?>>
        Vice President
    </option>

    <option value="Treasurer"
        <?php if(($editData['club_role'] ?? '') == "Treasurer") echo "selected"; ?>>
        Treasurer
    </option>

    <option value="Secretary"
        <?php if(($editData['club_role'] ?? '') == "Secretary") echo "selected"; ?>>
        Secretary
    </option>

    <option value="Member"
        <?php if(($editData['club_role'] ?? '') == "Member") echo "selected"; ?>>
        Member
    </option>
    </select>

    <label>Join Date:</label>
    <input type="date" name="join_date" required
           value="<?php echo $editData['join_date'] ?? ''; ?>">

           <br>

    <button type="submit" name="new" id=club_button>
        <?php echo $editData ? "Update Club" : "Add Club"; ?>
    </button>
</form>

<h2 id=club_h2>Membership Information</h2>
<br>
<table id=club_table>
    <tr>
        <th>Club Name</th>
        <th>Role</th>
        <th>Join Date</th>
        <th>Actions</th>
    </tr>
<?php 
$sel_query= "SELECT * FROM club_tracker WHERE student_id='$student_id' ORDER BY club_tracker_id desc;";
$view_result = mysqli_query($con,$sel_query);
?>

    <?php while ($row = mysqli_fetch_assoc($view_result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['club_name']); ?></td>
            <td><?php echo htmlspecialchars($row['club_role']); ?></td>
            <td><?php echo $row['join_date']; ?></td>
            <td>
                <a href="club-tracker-form.php?id=<?php echo $row['club_tracker_id']; ?>">Edit</a> |
                <a href="?delete=<?php echo $row['club_tracker_id']; ?>"
                   onclick="return confirm('Delete this record?')">Delete</a>
            </td>
        </tr>
    <?php } ?>

</table>

</div>

</body>
</html>