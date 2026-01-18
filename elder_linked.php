<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'elder'){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = 'elder';
$activePage = 'linked';
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
<link rel="stylesheet" href="assets/css/caregiverstyle.css">
<style>
.page-wrapper { display:flex; min-height:100vh; }
.otp-box { background:#e9f7ef; padding:15px; margin-top:15px; border-radius:8px; }
h2 { color: #de7c67; }
</style>
</head>
<body>

<div class="page-wrapper">
    <?php include 'components/sidebar.php'; ?>

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

    </div>
</div>

</body>
</html>
