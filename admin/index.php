<?php
require_once '../includes/config.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
} else {
    // Redirect to main login page
    header("Location: ../login.php");
    exit();
}
?>