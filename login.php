<?php 
session_start(); 
?> 
<!DOCTYPE html> 
<html> 
<head> 
<meta charset="utf-8"> 
<title>User Login</title> 
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="styles.css">
 
</head> 
<body> 
<div class="container">
<?php 
require('database.php'); 

// Display success message if password was reset
if(isset($_GET['reset_success'])){
    echo "<p style='color:green;text-align: center;'>Password reset successful! You can now log in.</p>";
}

// if else (to check if login form being submitted)
if (isset($_POST['student_name'])){ 
    $student_name = stripslashes($_REQUEST['student_name']); 
    $student_name = mysqli_real_escape_string($con,$student_name); 
    $password = stripslashes($_REQUEST['password']); 
    $password = mysqli_real_escape_string($con,$password); 
    
    // retrieve users table from DB
        $query = "SELECT *  
                FROM `students`  
                WHERE name='$student_name' 
                AND _password='" . md5($password) . "'" 
                ; 
    $result = mysqli_query($con,$query) or die(mysqli_error($con)); 
    $rows = mysqli_num_rows($result); 
    if($rows==1){ // rows == 1 if the username, password match to any of one rows in data table
        $_SESSION['student_name'] = $student_name; 
                
        header("Location: event-tracker.php"); 
        exit(); 
    }else{ 
        echo "<div class='form'> 
        <h3>Name/password is incorrect.</h3> 
        <br/>Click here to <a href='login.php'>Login</a></div>"; 
    } 
}else{ 
?> 
<div class="form"> 
<h1>User Log In</h1> 
<form action="" method="post" name="login"> 
<input type="text" name="student_name" placeholder="Your Full Name" required /><br> 
<input type="password" name="password" placeholder="Password" required /><br> 
<input name="submit" type="submit" value="Login" /> 
</form> 
<p>Not registered yet? <a href='registration.php'>Register Here</a></p> 
<p>Forgot password? <a href='forgot_password.php'>Click here</a></p>
</div>
<?php } ?> 
</div>
</body> 
</html> 