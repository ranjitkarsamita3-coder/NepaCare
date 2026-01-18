<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'elder'){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Generate OTP
if(isset($_POST['generate_otp'])){
    $otp = rand(100000, 999999);
    $expiry = time() + 600;

    mysqli_query($conn,
        "UPDATE users 
         SET otp='$otp', otp_expires_at=FROM_UNIXTIME($expiry) 
         WHERE id='$user_id'"
    );

    $message = "OTP generated successfully";
}

// Fetch OTP data
$otpData = mysqli_fetch_assoc(
    mysqli_query($conn,
    "SELECT otp, UNIX_TIMESTAMP(otp_expires_at) AS exp 
     FROM users WHERE id='$user_id'")
);

// Fetch linked caregiver
$caregiver = mysqli_fetch_assoc(
    mysqli_query($conn,
    "SELECT name, email 
     FROM users 
     WHERE linked_elder_id='$user_id' AND role='caregiver'")
);
?>

<!DOCTYPE html>
<html>
<head>
<title>Linked Caregiver - NepaCare</title>
<link rel="stylesheet" href="assets/css/elderstyle.css">
<style>
body { display:flex; font-family:'Times New Roman'; }
.sidebar { width:200px; background:#f0f0f0; padding:20px; height:100vh; }
.sidebar a { display:block; padding:10px 0; text-decoration:none; color:#333; }
.sidebar a:hover { background:#ddd; }
.content { flex:1; padding:20px; }
.otp-box { background:#e9f7ef; padding:15px; margin-top:15px; }
button { padding:10px 16px; background:#007bff; color:white; border:none; border-radius:6px; }
button:hover { background:#0056b3; }
</style>
</head>

<body>

<div class="sidebar">
    <img src="assets/images/logo.png" class="logo"><br><br>
    <a href="elder_dashboard.php">Home</a>
    <a href="reminders.php">Reminders</a>
    <a href="elder_linked.php">Linked Caregiver</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="content">

<h2>Linked Caregiver</h2>

<?php if($caregiver): ?>
    <p><strong>Name:</strong> <?= htmlspecialchars($caregiver['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($caregiver['email']) ?></p>
<?php else: ?>
    <p>No caregiver linked yet.</p>
<?php endif; ?>

<hr>

<h3>Generate OTP</h3>
<p>Share this OTP with your caregiver to link your account.</p>

<form method="POST">
    <button name="generate_otp">Generate OTP</button>
</form>

<?php if($otpData['otp'] && $otpData['exp'] > time()): ?>
<div class="otp-box">
    <strong><?= $message ?></strong><br><br>
    OTP: <b><?= $otpData['otp'] ?></b><br>
    Valid for: <span id="otpTimer"></span>
</div>
<?php endif; ?>

</div>

<script>
<?php if($otpData['otp'] && $otpData['exp'] > time()): ?>
let expiry = <?= $otpData['exp'] ?> * 1000;

function otpCountdown(){
    let now = Date.now();
    let diff = expiry - now;

    if(diff <= 0){
        document.getElementById("otpTimer").innerText = "Expired";
        return;
    }

    let m = Math.floor(diff / 60000);
    let s = Math.floor((diff % 60000) / 1000);
    document.getElementById("otpTimer").innerText = m+" min "+s+" sec";
}
setInterval(otpCountdown, 1000);
otpCountdown();
<?php endif; ?>
</script>

</body>
</html>
