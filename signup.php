<?php
session_start();
include 'config/db.php';

$message = "";

if(isset($_POST['signup'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']); // 'elder' or 'caregiver'

    // Validate phone number
    if(!preg_match("/^(97|98)[0-9]{8}$/", $phone)){
        $message = "Invalid phone number. Must start with 97 or 98 and be 10 digits.";
    }
    // Check required fields
    elseif(empty($name) || empty($email) || empty($password) || empty($role)){
        $message = "Please fill in all fields.";
    }
    // Validate name
    elseif(!preg_match("/^[a-zA-Z ]*$/",$name)){
        $message = "Name can only contain letters and spaces.";
    }
    // Validate email
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $message = "Invalid email format.";
    }
    else {
        // Escape for safety
        $email_safe = mysqli_real_escape_string($conn, $email);
        $phone_safe = mysqli_real_escape_string($conn, $phone);

        // Check if email or phone already exists
        $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email_safe' OR phone='$phone_safe'");
        if(mysqli_num_rows($query) > 0){
            $message = "Email or phone already registered. Please login.";
        } else {
            // Insert new user
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO users (name, email, phone, password, role) VALUES ('$name','$email_safe','$phone_safe','$password_hash','$role')");
            $message = "Signup successful! You can <a href='login.php'>login now</a>.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup - NepaCare</title>
    <link rel="stylesheet" href="assets/css/signupstyle.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; margin: 0; padding: 0; }
        h1 { font-size: 32px; margin-bottom: 20px; text-align:center; }
        .top-nav { background:#007BFF; display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; }
        .top-nav .logo { display: flex; align-items: center; text-decoration: none; }
        .top-nav .logo img { height: 50px; margin-right: 10px; border-radius: 8px; }
        .top-nav .logo span { color: white; font-size: 20px; font-weight: bold; }
        .top-nav .nav-links a { color:white; font-size:20px; margin:0 10px; text-decoration:none; }
        .top-nav .nav-links a:hover { text-decoration: underline; }

        .signup-container { max-width: 400px; margin: 50px auto; padding: 30px; background: #f9f9f9; border-radius: 10px; border: 1px solid #ccc; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; }
        .signup-container:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
        .signup-container input[type="text"], .signup-container input[type="email"], .signup-container input[type="password"], .password-wrapper input[type="password"], .password-wrapper input[type="text"], .signup-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            height: 40px;
        }

        .signup-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            font-size: 18px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .signup-container input[type="submit"]:hover { background: #0056b3; }

        .password-wrapper { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
        .password-wrapper .toggle-btn { height: 40px; padding: 0 12px; font-size: 14px; cursor: pointer; flex-shrink: 0; }

        .message-box { padding: 10px; margin-bottom: 15px; border-radius: 6px; font-size: 14px; }
        .message-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .message-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

<div class="top-nav">
    <a href="index.php" class="logo">
        <img src="assets/images/logo.png" alt="NepaCare Logo">
        <span>NepaCare</span>
    </a>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact Us</a>
        <a href="login.php">Login / Signup</a>
    </div>
</div>

<div class="signup-container">
    <h1>Signup - NepaCare</h1>

    <?php if($message != ""): ?>
        <div class="message-box <?= strpos($message, 'successful') !== false ? 'message-success' : 'message-error' ?>"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Name:</label>
        <input type="text" name="name" value="<?= isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>

        <label>Phone Number:</label>
        <input type="text" name="phone" value="<?= isset($_POST['phone']) ? $_POST['phone'] : ''; ?>" placeholder="Start with 97 or 98, 10 digits" required>

        <label>Password:</label>
        <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="Enter password" required>
            <button type="button" class="toggle-btn" onclick="togglePassword()">Show</button>
        </div>

        <label>Role:</label>
        <select name="role" required>
            <option value="">Select role</option>
            <option value="elder" <?= (isset($_POST['role']) && $_POST['role']=='elder')?'selected':''; ?>>Elder</option>
            <option value="caregiver" <?= (isset($_POST['role']) && $_POST['role']=='caregiver')?'selected':''; ?>>Caregiver</option>
        </select>

        <input type="submit" name="signup" value="Signup">
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<script>
function togglePassword() {
    const passField = document.getElementById("password");
    const btn = event.currentTarget;
    if(passField.type === "password"){
        passField.type = "text";
        btn.innerText = "Hide";
    } else {
        passField.type = "password";
        btn.innerText = "Show";
    }
}
</script>

</body>
</html>
