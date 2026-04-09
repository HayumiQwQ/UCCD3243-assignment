<?php
// logout.php - destroy session and clear all cookies
session_start();

// Clear session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Delete all other cookies set for the site
if (!empty($_COOKIE)) {
    foreach ($_COOKIE as $name => $value) {
        // Attempt to remove cookie on root path
        setcookie($name, '', time() - 42000, '/');
        // A second attempt with default params to be thorough
        setcookie($name, '', time() - 42000);
    }
}

// Unset and destroy the session
session_unset();
session_destroy();

// Clear runtime cookie array
$_COOKIE = array();

// Redirect to login page
header('Location: login.php');
exit();

