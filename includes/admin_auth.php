<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'admin') {
    // If not logged in, or not admin, redirect to index or login
    header("location: " . SITE_URL . "/index.php");
    exit;
}
?>
