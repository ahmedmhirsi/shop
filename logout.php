<?php
/**
 * Logout Page
 * Destroy session and redirect to login
 */

session_start();
require_once __DIR__ . '/config/config.php';

// Destroy all session data
$_SESSION = [];

// Delete session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy session
session_destroy();

// Redirect to login
header('Location: login.php');
exit;
