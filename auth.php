<?php 
// Auth - CHECK FOR USER AUTHENTICATION LOGIN

// Start new session
session_start(); 

// if username is NOT set, will redirect user to login.php
if(!isset($_SESSION["student_name"])){ 
header("Location: login.php"); 
exit(); } 
?> 