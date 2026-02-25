<?php
session_start();
include 'config/db.php';

// If a user is logged in, clear their remember token in DB
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    mysqli_query($conn, "UPDATE users SET remember_token=NULL WHERE id='$user_id'");
}

// Clear session data and destroy session
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}
session_destroy();

// Remove remember cookie from client
setcookie("remember_token", "", time() - 3600, "/");

header("Location: login.php");
exit;
?>
