<?php
session_start();
include 'config/db.php';
session_destroy();

setcookie("remember_token", "", time() - 3600, "/");

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    mysqli_query($conn, "UPDATE users SET remember_token=NULL WHERE id='$user_id'");
}

header("Location: login.php");
exit;
?>
