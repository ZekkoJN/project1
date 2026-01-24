<?php
session_start();

// Check user role before destroying session
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Destroy session
session_destroy();

// Redirect based on role
if ($is_admin) {
    header("Location: login-admin.php");
} else {
    header("Location: login.php");
}
exit();
