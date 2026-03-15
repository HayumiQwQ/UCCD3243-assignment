<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>User Registration</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <?php
        // Need DB file (required: if file does not exist, raise fatal error)
        require('database.php');

        // Form submission checking (Logic Tier- Check if registration form can be submitted - to Data Tier)
        if (isset($_REQUEST['student_name'])) {

            // Declare variable, assign value from form
            // Process/format the input
            // Filter the user input
            $student_name = stripslashes($_REQUEST['student_name']);
            $student_name = mysqli_real_escape_string($con, $student_name);
            $email = stripslashes($_REQUEST['email']);
            $email = mysqli_real_escape_string($con, $email);
            $password = stripslashes($_REQUEST['password']);
            $password = mysqli_real_escape_string($con, $password);
            $reg_date = date("Y-m-d H:i:s");
            $dob = stripslashes($_REQUEST['dob']);
            $dob = mysqli_real_escape_string($con, $dob);
            // SQL  query - Prepare statement of INSERT data
            $query = "INSERT into students (name, email, dob, _password) 
    VALUES ('$student_name', '$email', '$dob', '" . md5($password) . "')";
            // Execute query
            $result = mysqli_query($con, $query);
            // If executed successfully
            if ($result) {
                echo "<div class='form'> 
        <h3>You are registered successfully.</h3> 
        <br/>Click here to <a href='login.php'>Login</a></div>";
            }
        } else { // else shows the form
            ?>

            <div class="panel">
                <div class="form">
                    <h1>User Registration</h1>
                    <form name="registration" action="" method="post">
                        <input type="text" name="student_name" placeholder="Your Full Name" required /><br>
                        <input type="email" name="email" placeholder="Email" required /><br>
                        <input type="password" name="password" placeholder="Password" required /><br>
                        <input type="date" name="dob" placeholder="Date of Birth" required /><br>
                        <input type="submit" name="submit" value="Register" />
                    </form>
                </div>
            </div>

        <?php } ?>
    </div>
</body>

</html>