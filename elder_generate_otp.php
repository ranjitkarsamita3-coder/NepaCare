<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'elder') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = 'elder';
$activePage = 'linked';
$otp = rand(100000, 999999);
$expiry = date("Y-m-d H:i:s", time() + 300); // 5 minutes

mysqli_query($conn, "
    UPDATE users 
    SET otp='$otp', otp_expiry='$expiry'
    WHERE id='$user_id'
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate OTP - NepaCare</title>
    <link rel="stylesheet" href="assets/css/caregiverstyle.css">
    <style>
        .page-wrapper { display: flex; min-height: 100vh; }
        .otp-box {
            font-size: 36px;
            letter-spacing: 6px;
            background: #f0f0f0;
            padding: 20px;
            display: inline-block;
            border-radius: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <?php include 'components/sidebar.php'; ?>

    <div class="content" style="text-align: center;">

<h1>Link Caregiver</h1>
<p>Share this OTP with your caregiver</p>

<div class="otp-box">
    <?php echo $otp; ?>
</div>

<p>OTP valid for 5 minutes</p>

<a href="elder_dashboard.php">Back</a>

    </div>
</div>

</body>
</html>
