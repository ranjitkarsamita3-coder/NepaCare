<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver'){
    header("Location: login.php");
    exit;
}

$caregiver_id = $_SESSION['user_id'];
$message = "";

if(isset($_POST['link_elder'])){
    $elder_phone = trim($_POST['elder_phone']);
    $otp = trim($_POST['otp']);

    // Check if elder exists and OTP matches
    $res = mysqli_query($conn, "SELECT * FROM users WHERE phone='$elder_phone' AND otp='$otp' AND role='elder'");
    if($res && mysqli_num_rows($res) === 1){
        $elder = mysqli_fetch_assoc($res);

        // Link caregiver to elder
        mysqli_query($conn, "UPDATE users SET linked_elder_id='{$elder['id']}' WHERE id='$caregiver_id'");
        $message = "Linked successfully to elder: {$elder['name']}";
    } else {
        $message = "OTP or phone number invalid!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Link Elder - NepaCare</title>
    <link rel="stylesheet" href="assets/css/elderstyle.css">
    <link rel="stylesheet" href="assets/css/caregiverstyle.css">   
    <style>
        body { font-family: 'Times New Roman', Times, serif; margin: 0; padding: 0; display: flex; }
        .sidebar {
            width: 200px;
            background: #f0f0f0;
            padding: 20px;
            height: 100vh;
        }
        .sidebar a {
            display: block;
            padding: 10px 0;
            text-decoration: none;
            color: #333;
        }
        .sidebar a:hover {
            background: #ddd;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        h1 { color: #2c3e50; }
        .about { margin-top: 50px; font-size: 14px; color: #555; }
        .btn{
            display: inline-block;
            padding: 10px 16px;
            margin-top: 10px;
            background: #007bff;
            color: blue;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
        }
        .btn:hover{
            background: #0056b3;
        }
    </style>
</head>
<body>
    
<div class="sidebar">
    <div class="logo-container" style="text-align:center; margin-bottom:20px;">
        <img src="assets/images/logo.png" alt="NepaCare" class="logo">
    </div>

    <h3>NepaCare</h3>
    <a href="caregiver_dashboard.php">Home</a>
    <a href="link_elder.php">Link Elder</a>
    <a href="caregiver_reminders.php">Reminders</a>
    <a href="caregiver_profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="content">
    <h1>Link Elder</h1>
    <?php if($message): ?>
        <div class="msg"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Elder Phone Number:</label><br>
        <input type="text" name="elder_phone" required><br>

        <label>OTP:</label><br>
        <input type="text" name="otp" required><br>

        <button name="link_elder">Link Elder</button>
    </form>
</div>
</body>
</html>
