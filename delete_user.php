<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // منع الأدمن من حذف نفسه بالخطأ لمنع فقدان الوصول للوحة الإدارة
    if ($user_id == $_SESSION['user_id']) {
        header("Location: admin_users.php?error=cannot_delete_self");
        exit();
    }
    
    $query = "DELETE FROM users WHERE id = $user_id";
    if (mysqli_query($conn, $query)) {
        header("Location: admin_users.php?success=deleted");
    } else {
        header("Location: admin_users.php?error=failed");
    }
    exit();
} else {
    header("Location: admin_users.php");
    exit();
}
?>