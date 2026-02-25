<?php
session_start();
include 'config/db.php';

$message = "";

if(isset($_POST['signup'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if(!preg_match("/^(97|98)[0-9]{8}$/", $phone)){
        $message = "Invalid phone number. Must start with 97 or 98 and be 10 digits.";
    }
    elseif(empty($name) || empty($email) || empty($password) || empty($role)){
        $message = "Please fill in all fields.";
    }
    elseif(!preg_match("/^[a-zA-Z ]*$/",$name)){
        $message = "Name can only contain letters and spaces.";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $message = "Invalid email format.";
    }
    else {
        $email_safe = mysqli_real_escape_string($conn, $email);
        $phone_safe = mysqli_real_escape_string($conn, $phone);

        $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email_safe' OR phone='$phone_safe'");
        if(mysqli_num_rows($query) > 0){
            $message = "Email or phone already registered. Please login.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO users (name, email, phone, password, role) VALUES ('$name','$email_safe','$phone_safe','$password_hash','$role')");
            $message = "Signup successful! You can <a href='login.php'>login now</a>.";
        }
    }
}
?>

<?php include "components/header.php"; ?>
<link rel="stylesheet" href="assets/css/colors.css">
<link rel="stylesheet" href="assets/css/signupstyle.css">

<div class="signup-container dark-card">
    <h1>Signup - NepaCare</h1>

    <?php if($message): ?>
        <div class="message-box <?= strpos($message, 'successful') !== false ? 'message-success' : 'message-error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" value="<?= isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
        <input type="text" name="phone" placeholder="Phone (97 or 98...)" value="<?= isset($_POST['phone']) ? $_POST['phone'] : ''; ?>" required>
        
        <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <span class="eye" onclick="togglePassword()">👁</span>
        </div>

        <select name="role" required>
            <option value="">Select Role</option>
            <option value="elder" <?= (isset($_POST['role']) && $_POST['role']=='elder')?'selected':''; ?>>Elder</option>
            <option value="caregiver" <?= (isset($_POST['role']) && $_POST['role']=='caregiver')?'selected':''; ?>>Caregiver</option>
        </select>

        <input type="submit" name="signup" value="Signup">
    </form>

    <p class="signup-link">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</div>

<script>
function togglePassword() {
    const field = document.getElementById("password");
    field.type = field.type === "password" ? "text" : "password";
}
</script>

<?php include "components/footer.php"; ?>
