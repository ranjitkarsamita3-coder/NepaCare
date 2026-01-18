<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver'){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$activePage = 'profile';
$message = "";

$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);

if(isset($_POST['update_profile'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    if(empty($name) || empty($email) || empty($phone)){
        $message = "Name, email and phone cannot be empty.";
    } elseif(!preg_match("/^(97|98)[0-9]{8}$/",$phone)){
        $message = "Phone must start with 97 or 98 and be 10 digits.";
    } elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $message = "Invalid email format.";
    } else {
        $check = mysqli_query($conn,"SELECT id FROM users WHERE (phone='$phone' OR email='$email') AND id!='$user_id'");
        if(mysqli_num_rows($check)>0){
            $message="Email or phone already in use.";
        } else {
            if($password){
                $pass=password_hash($password,PASSWORD_DEFAULT);
                mysqli_query($conn,"UPDATE users SET name='$name', email='$email', phone='$phone', password='$pass' WHERE id='$user_id'");
            } else {
                mysqli_query($conn,"UPDATE users SET name='$name', email='$email', phone='$phone' WHERE id='$user_id'");
            }
            $message="Profile updated successfully!";
            $_SESSION['name']=$name;
            $result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
            $user = mysqli_fetch_assoc($result);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Caregiver Profile - NepaCare</title>
    <link rel="stylesheet" href="assets/css/caregiverstyle.css">
    <style>
        .profile-box { background:#f9f9f9; padding:20px; max-width:400px; border:1px solid #ccc; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <?php include __DIR__ . '/components/careSidebar.php'; ?>

    <div class="content">
        <h1>Profile</h1>

        <?php if($message): ?>
            <div class="msg"><?php echo $message; ?></div>
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
        </div>
    </div>
</div>
</body>
</html>
