<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$activePage = 'profile';
$message = "";

$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);
$linked = null;
if($user['role'] == 'elder'){
    $linkedResult = mysqli_query($conn,
        "SELECT name, email FROM users WHERE linked_elder_id = '$user_id'"
    );
    $linked = mysqli_fetch_assoc($linkedResult);
}
if($user['role'] == 'caregiver' && !empty($user['linked_elder_id'])){
    $linkedResult = mysqli_query($conn,
        "SELECT name, email FROM users WHERE id = '".$user['linked_elder_id']."'"
    );
    $linked = mysqli_fetch_assoc($linkedResult);
}
if(isset($_POST['update_profile'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $password = trim($_POST['password']);

    if(empty($name) || empty($phone) || empty($email)){
        $message = "Name, email, and phone cannot be empty.";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $message = "Invalid email format.";
    }
    elseif(!preg_match("/^(97|98)[0-9]{8}$/", $phone)){
        $message = "Invalid phone number. Must start with 97 or 98 and be 10 digits.";
    }
    elseif($age !== '' && (!is_numeric($age) || (int)$age <= 0 || (int)$age > 120)){
        $message = "Invalid age.";
    }
    else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE (phone='$phone' OR email='$email') AND id!='$user_id'");
        if(mysqli_num_rows($check) > 0){
            $message = "Email or phone already in use by another user.";
        } else {
            $fields = [
                "name='$name'",
                "email='$email'",
                "phone='$phone'"
            ];

            if (userColumnExists($conn, 'address')) {
                $fields[] = "address='$address'";
            }
            if (userColumnExists($conn, 'age')) {
                $fields[] = "age=" . ($age !== '' ? (int)$age : 'NULL');
            }

            if(!empty($password)){
                $hashedPass = password_hash($password, PASSWORD_DEFAULT);
                $fields[] = "password='$hashedPass'";
            }

            $fields_sql = implode(', ', $fields);
            $update = mysqli_query($conn, "UPDATE users SET $fields_sql WHERE id='$user_id'");

            if($update){
                $message = "Profile updated successfully!";
                $_SESSION['name'] = $name;
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
    <link rel="stylesheet" href="assets/css/caregiverstyle.css">
    <style>
        .page-wrapper { display: flex; min-height: 100vh; }
        .profile-box { background:#f9f9f9; padding:20px; max-width:400px; border:1px solid #ccc; }
        .profile-box h3 { margin-top:0; color:#2c3e50; }
        input, button, select { font-size:16px; padding:6px; margin:5px 0; width:100%; box-sizing:border-box; }
        .success { background:#d4edda; padding:10px; margin-bottom:10px; }
        .password-wrapper { position: relative; }
        .password-wrapper input { padding-right: 40px; }
        .eye { position: absolute; right: 10px; top: 15px; cursor: pointer; font-size: 18px; user-select: none; }
    </style>
</head>
<body>

<div class="page-wrapper">
    <?php include 'components/sidebar.php'; ?>

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

            <label>Address</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" placeholder="Enter your address">

            <label>Age</label>
            <input type="number" name="age" value="<?php echo htmlspecialchars($user['age'] ?? ''); ?>" min="1" max="120" placeholder="Enter your age">

            <label>Change Password (leave blank to keep current)</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" placeholder="New password">
                <span class="eye" onclick="togglePassword()">👁</span>
            </div>
            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        <p><strong>Current Time:</strong> <span id="currentTime"></span></p>
    </div>
</div>
<script>
function togglePassword() {
    var passwordField = document.getElementById('password');
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
    } else {
        passwordField.type = 'password';
    }
}

function updateTime(){
    var now = new Date();
    document.getElementById('currentTime').innerText = now.toLocaleString();
}
setInterval(updateTime, 1000);
updateTime();
</script>

    </div>
</div>

</body>
</html>
