<?php
session_start();
include '../config/db.php';

// Log logout activity
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $username = $_SESSION['user_name'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    $stmt = $conn->prepare("INSERT INTO user_logs (user_id, username, user_ip, action) VALUES (?, ?, ?, 'LOGOUT')");
    $stmt->bind_param("iss", $uid, $username, $ip);
    $stmt->execute();
}

session_destroy();
header("Location: ../index.php");
exit();
?>