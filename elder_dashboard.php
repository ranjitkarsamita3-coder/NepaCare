<?php
session_start();
include 'config/db.php';

// Block access if not logged in or not elder
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'elder'){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Generate OTP (10 minutes)
if(isset($_POST['generate_otp'])){
    $otp = rand(100000, 999999);
    $expiry = time() + 600; // 10 minutes (600 seconds)

    mysqli_query($conn,
        "UPDATE users SET otp='$otp', otp_expires_at=FROM_UNIXTIME($expiry) WHERE id='$user_id'"
    );

    $message = "OTP generated successfully";

    $otpData = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT otp, UNIX_TIMESTAMP(otp_expires_at) AS exp FROM users WHERE id='$user_id'"));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Elder Dashboard - NepaCare</title>
    <link rel="stylesheet" href="assets/css/elderstyle.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; margin: 0; padding: 0; display: flex; }
        .sidebar { width: 200px; background: #f0f0f0; padding: 20px; height: 100vh; }
        .sidebar a { display: block; padding: 10px 0; text-decoration: none; color: #333; }
        .sidebar a:hover { background: #ddd; }
        .content { flex: 1; padding: 20px; }
        h1 { color: #2c3e50; }
        .message { background:#d4edda; padding:10px; margin-bottom:10px; }
        .otp-form {
            margin-top: 20px;
        }
        .otp-btn {
            font-family: 'Times New Roman', Times, serif;
            background: #007bff;
        }
        .otp-btn:hover {
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
    <a href="elder_dashboard.php">Home</a>
    <a href="reminders.php">Reminders</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="content">
    <h1>Welcome, <?php echo $_SESSION['name']; ?>!</h1>
    <h2>About NepaCare</h2>
    <p>
        NepaCare is a simple, easy-to-use reminder system designed especially for elders.
        You can manage your daily tasks, set reminders, and receive alerts when it's time
        to do something important. This system ensures you never miss a task and helps
        caregivers stay informed.
    </p>
    <form method="POST">
        <button name="generate_otp" class="otp-btn">Generate OTP for Caregiver</button>
    </form>
    <?php if(!empty($message) && $otpData['otp']): ?>
        <div class="otp-box">
            <strong><?= $message ?></strong><br><br>
            OTP: <b><?= $otpData['otp'] ?></b><br>
            Valid for <span id="otpTimer"></span>
        </div>
    <?php endif; ?>
</div>
</body>
<script>
<?php if($otpData['otp']): ?>
let expiry = <?= $otpData['exp'] ?> * 1000;

function otpCountdown(){
    let now = new Date().getTime();
    let diff = expiry - now;

    if(diff <= 0){
        document.getElementById("otpTimer").innerText = "Expired";
        return;
    }

    let min = Math.floor(diff / 60000);
    let sec = Math.floor((diff % 60000) / 1000);
    document.getElementById("otpTimer").innerText = min + " min " + sec + " sec";
}
setInterval(otpCountdown, 1000);
otpCountdown();
<?php endif; ?>
</script>

</html>
