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
    $complaint_id = intval($_GET['id']);
    
    // استخدام استعلام آمن بعد تمرير القيمة لـ intval
    $query = "DELETE FROM complaints WHERE id = $complaint_id";
    if (mysqli_query($conn, $query)) {
        header("Location: admin_complaints.php?success=deleted");
    } else {
        header("Location: admin_complaints.php?error=failed");
    }
    exit();
} else {
    header("Location: admin_complaints.php");
    exit();
}
?>