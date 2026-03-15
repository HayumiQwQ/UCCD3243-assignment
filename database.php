<?php
// 4 important parameters
# hostname, username, password, database_name
$con = mysqli_connect("localhost","root","","cocu_db"); 
if (mysqli_connect_errno()) 
{ 
    echo "Failed to connect to MySQL: " . mysqli_connect_error(); 
} 
?>
