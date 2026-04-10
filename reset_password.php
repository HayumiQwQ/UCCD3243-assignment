<?php 
require('database.php'); 

$message = '';
$show_form = true;
$email = '';

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($con, $_GET['token']);

    $query = mysqli_query($con, "SELECT * FROM password_resets WHERE token='$token' LIMIT 1");
    if (mysqli_num_rows($query) == 1) {
        $row = mysqli_fetch_assoc($query);
        $email = $row['email'];
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['password'])) {
            $password = mysqli_real_escape_string($con, $_POST['password']);
            $hashed_password = md5($password);
            mysqli_query($con, "UPDATE students SET _password='$hashed_password' WHERE email='$email'");
            mysqli_query($con, "DELETE FROM password_resets WHERE email='$email'");
            header("Location: login.php?reset_success=1");
            exit();
        }
    } else {
        $message = 'Invalid or expired token.';
        $show_form = false;
    }
} else {
    $message = 'No reset token provided.';
    $show_form = false;
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <div class="form">
        <h1>Password reset</h1>
        <?php if (!empty($message)) { echo '<div class="flash">' . htmlspecialchars($message) . '</div>'; } ?>
        <?php if ($show_form) { ?>
        <form action="" method="post" name="password_reset">
            <input type="password" name="password" placeholder="Enter new password" required /><br>
            <input name="submit" type="submit" value="Reset Password" />
        </form>
        <?php } ?>
    </div>
</div>

</body>
</html>