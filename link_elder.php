<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver'){
    header("Location: login.php");
    exit;
}

$caregiver_id = $_SESSION['user_id'];
$message = "";

$linked_elder = null;
$res = mysqli_query($conn,"SELECT id,name,email FROM users WHERE id=(SELECT linked_elder_id FROM users WHERE id='$caregiver_id')");
if($res && mysqli_num_rows($res)==1){
    $linked_elder = mysqli_fetch_assoc($res);
}

if(isset($_POST['link_elder'])){
    $phone = trim($_POST['elder_phone']);
    $otp = trim($_POST['otp']);
    $res2 = mysqli_query($conn,"SELECT * FROM users WHERE phone='$phone' AND otp='$otp' AND role='elder'");
    if($res2 && mysqli_num_rows($res2)==1){
        $elder = mysqli_fetch_assoc($res2);
        mysqli_query($conn,"UPDATE users SET linked_elder_id='{$elder['id']}' WHERE id='$caregiver_id'");
        $message="Linked to elder: {$elder['name']}";
        $linked_elder = $elder;
    } else {
        $message="Invalid phone or OTP.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Link Elder - NepaCare</title>
    <link rel="stylesheet" href="assets/css/caregiverstyle.css">
</head>
<body>
<div class="page-wrapper">
    <?php include __DIR__ . '/components/careSidebar.php'; ?>

    <div class="content">
        <?php if($message): ?>
            <div class="msg"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if($linked_elder): ?>
            <h2>Currently Linked Elder</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($linked_elder['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($linked_elder['email']); ?></p>
        <?php endif; ?>

        <hr>

        <h2>Link Elder</h2>
        <form method="POST">
            <label>Elder Phone Number</label>
            <input type="text" name="elder_phone" required>
            <label>OTP</label>
            <input type="text" name="otp" required>
            <button name="link_elder">Link Elder</button>
        </form>
    </div>
</div>
</body>
</html>
