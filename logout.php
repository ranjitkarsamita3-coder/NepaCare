<?php
session_start();
include 'config/db.php';
// Clear session
session_destroy();

// Clear token cookie
setcookie("remember_token", "", time() - 3600, "/");

// Also clear token in database for security
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    mysqli_query($conn, "UPDATE users SET remember_token=NULL WHERE id='$user_id'");
}

header("Location: login.php");
exit;
?>
