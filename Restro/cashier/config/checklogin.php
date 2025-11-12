<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../../includes/config.php');

function check_login() {
    if (!isset($_SESSION['admin_id']) && !isset($_SESSION['staff_id'])) {
        header("Location: ../index.php");
        exit();
    }
}
?>
