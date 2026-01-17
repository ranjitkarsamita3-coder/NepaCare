<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch user info
$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);
// Fetch linked account info
$linked = null;
// If elder → find caregiver
if($user['role'] == 'elder'){
    $linkedResult = mysqli_query($conn,
        "SELECT name, email FROM users WHERE linked_elder_id = '$user_id'"
    );
    $linked = mysqli_fetch_assoc($linkedResult);
}
// If caregiver → find elder
if($user['role'] == 'caregiver' && !empty($user['linked_elder_id'])){
    $linkedResult = mysqli_query($conn,
        "SELECT name, email FROM users WHERE id = '".$user['linked_elder_id']."'"
    );
    $linked = mysqli_fetch_assoc($linkedResult);
}
// Handle profile update
if(isset($_POST['update_profile'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']); // optional

    if(empty($name) || empty($phone) || empty($email)){
        $message = "Name, email, and phone cannot be empty.";
    } 
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $message = "Invalid email format.";
    }
    elseif(!preg_match("/^(97|98)[0-9]{8}$/", $phone)){
        $message = "Invalid phone number. Must start with 97 or 98 and be 10 digits.";
    }
    else {
        // Check if phone or email is unique (exclude current user)
        $check = mysqli_query($conn, "SELECT id FROM users WHERE (phone='$phone' OR email='$email') AND id!='$user_id'");
        if(mysqli_num_rows($check) > 0){
            $message = "Email or phone already in use by another user.";
        } else {
            if(!empty($password)){
                $hashedPass = password_hash($password, PASSWORD_DEFAULT);
                $update = mysqli_query($conn, "UPDATE users 
                    SET name='$name', email='$email', phone='$phone', password='$hashedPass'
                    WHERE id='$user_id'");
            } else {
                $update = mysqli_query($conn, "UPDATE users 
                    SET name='$name', email='$email', phone='$phone'
                    WHERE id='$user_id'");
            }

            if($update){
                $message = "Profile updated successfully!";
                $_SESSION['name'] = $name; // update session name
                $result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
                $user = mysqli_fetch_assoc($result);
            } else {
                $message = "Failed to update profile.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile - NepaCare</title>
    <link rel="stylesheet" href="assets/css/elderstyle.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; margin:0; padding:0; display:flex; }
        .sidebar { width:200px; background:#f0f0f0; padding:20px; height:100vh; }
        .sidebar a { display:block; padding:10px 0; text-decoration:none; color:#333; }
        .sidebar a:hover { background:#ddd; }
        .content { flex:1; padding:20px; }
        .profile-box { background:#f9f9f9; padding:20px; max-width:400px; border:1px solid #ccc; }
        input, button { font-size:16px; padding:6px; margin:5px 0; width:100%; box-sizing:border-box; }
        .success { background:#d4edda; padding:10px; margin-bottom:10px; }
        .profile-box h3{
            margin-top:0;
            color:#2c3e50;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <!-- Logo -->
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
    <h1>Profile</h1>

    <?php if($message != ""): ?>
        <div class="success"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="profile-box">
        <form method="POST">
            <label>Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="Start with 97 or 98, 10 digits" required>

            <label>Change Password (leave blank to keep current)</label>
            <input type="password" name="password" placeholder="New password">
            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        <p><strong>Current Time:</strong> <span id="currentTime"></span></p>
    </div>
    <div class="profile-box" style="margin-top:20px;">
        <h3>Linked Account</h3>

        <?php if(!empty($linked)): ?>
            <p><strong>Name:</strong> <?= htmlspecialchars($linked['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($linked['email']) ?></p>
        <?php else: ?>
            <p style="color:red;">Not linked yet</p>
        <?php endif; ?>
    </div>
</div>
<script>
// Live clock for elder
function updateTime(){
    var now = new Date();
    document.getElementById('currentTime').innerText = now.toLocaleString();
}
setInterval(updateTime, 1000);
updateTime();
</script>

</body>
</html>
