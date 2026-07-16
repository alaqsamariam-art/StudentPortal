<?php

// ===============================
// Clean User Input
// ===============================

function clean($data)
{
    global $conn;

    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);

    return $data;
}

// ===============================
// Redirect
// ===============================

function redirect($page)
{
    header("Location: $page");
    exit();
}

// ===============================
// Show Alert
// ===============================

function showMessage($message, $type = "success")
{
    return "<div class='alert alert-$type'>$message</div>";
}

// ===============================
// Check Login
// ===============================

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

?>