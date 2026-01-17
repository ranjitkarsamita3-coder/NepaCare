<?php
session_start();
include 'config/db.php';

$message = "";

/* AUTO LOGIN USING REMEMBER ME */
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = mysqli_real_escape_string($conn, $_COOKIE['remember_token']);
    $query = mysqli_query($conn, "SELECT * FROM users WHERE remember_token='$token' LIMIT 1");

    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['name']    = $user['name'];

        header("Location: " . ($user['role'] === 'elder' ? "elder_dashboard.php" : "caregiver_dashboard.php"));
        exit;
    }
}

/* LOGIN */
if (isset($_POST['login'])) {
    $login_input = trim($_POST['login_input']);
    $password    = trim($_POST['password']);

    if (empty($login_input) || empty($password)) {
        $message = "Please fill in all fields.";
    } else {
        $login_input_safe = mysqli_real_escape_string($conn, $login_input);

        // Determine if email or phone
        if (filter_var($login_input_safe, FILTER_VALIDATE_EMAIL)) {
            $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$login_input_safe' LIMIT 1");
        } elseif (preg_match('/^(97|98)[0-9]{8}$/', $login_input_safe)) {
            $query = mysqli_query($conn, "SELECT * FROM users WHERE phone='$login_input_safe' LIMIT 1");
        } else {
            $message = "Enter a valid email or phone number starting with 97 or 98.";
        }

        // Only check password if $query exists
        if (isset($query)) {
            if (mysqli_num_rows($query) === 1) {
                $user = mysqli_fetch_assoc($query);

                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role']    = $user['role'];
                    $_SESSION['name']    = $user['name'];

                    // Remember me
                    if (isset($_POST['remember'])) {
                        $token = bin2hex(random_bytes(16));
                        mysqli_query($conn, "UPDATE users SET remember_token='$token' WHERE id='{$user['id']}'");
                        setcookie("remember_token", $token, time() + (86400 * 30), "/");
                    }

                    header("Location: " . ($user['role'] === 'elder' ? "elder_dashboard.php" : "caregiver_dashboard.php"));
                    exit;
                } else {
                    $message = "Incorrect password.";
                }
            } else {
                $message = "User not found.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - NepaCare</title>
    <link rel="stylesheet" href="assets/css/loginstyle.css">
</head>
<body>

<!-- NAVBAR -->
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

<!-- LOGIN BOX -->
<div class="login-container">
    <h1>Login - NepaCare</h1>

    <?php if ($message): ?>
        <div class="message-box message-error"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Email or Phone:</label>
        <input type="text" name="login_input" value="<?= isset($_POST['login_input']) ? $_POST['login_input'] : ''; ?>" required>

        <label>Password:</label>
        <div class="password-wrapper">
            <input type="password" name="password" id="password" required>
            <button type="button" class="toggle-btn" onclick="togglePassword(this)">Show</button>
        </div>

        <div class="remember">
            <input type="checkbox" name="remember" id="remember" <?= isset($_POST['remember']) ? 'checked' : ''; ?>>
            <label for="remember">Remember Me</label>
        </div>

        <input type="submit" name="login" value="Login">
    </form>

    <p class="signup-link">
        Don’t have an account? <a href="signup.php">Signup here</a>
    </p>
</div>

<script>
function togglePassword(btn) {
    const field = document.getElementById("password");
    if (field.type === "password") {
        field.type = "text";
        btn.innerText = "Hide";
    } else {
        field.type = "password";
        btn.innerText = "Show";
    }
}
</script>

</body>
</html>
