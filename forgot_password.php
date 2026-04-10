<?php
require('database.php');

$message = '';

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);

    $check_user = mysqli_query($con, "SELECT * FROM students WHERE email='$email'");
    if (mysqli_num_rows($check_user) > 0) {
        $token = bin2hex(random_bytes(50));

        mysqli_query($con, "INSERT INTO password_resets (email, token) VALUES ('$email', '$token')");

        $message = "Password reset link: <a href='reset_password.php?token=$token'>Reset Password</a>";
    } else {
        $message = "Email not found.";
    }
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
        <?php if (!empty($message)) { echo '<div class="flash">' . $message . '</div>'; } ?>
        <form action="" method="post" name="forgot_password">
            <input type="email" name="email" placeholder="Enter your email" required /><br>
            <input name="submit" type="submit" value="Submit" />
        </form>
    </div>
</div>

</body>
</html>