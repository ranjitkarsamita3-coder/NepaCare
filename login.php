<?php
session_start();
include 'config/db.php';
include_once __DIR__ . '/config/lang.php';

$message = "";

/* AUTO LOGIN WITH REMEMBER TOKEN */
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {

    $token = mysqli_real_escape_string($conn, $_COOKIE['remember_token']);
    $query = mysqli_query($conn, "SELECT * FROM users WHERE remember_token='$token' LIMIT 1");

    if (mysqli_num_rows($query) === 1) {

        $user = mysqli_fetch_assoc($query);

        /* CHECK STATUS */
        if ($user['status'] == 'inactive') {
            $message = __('Your account has been deactivated by admin.');
        } else {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['name']    = $user['name'];

            mysqli_query($conn, "UPDATE users SET last_login = NOW() WHERE id='{$user['id']}'");

            header("Location: " . ($user['role'] === 'elder' ? "elder_dashboard.php" : "caregiver_dashboard.php"));
            exit;
        }
    }
}

/* LOGIN FORM */
if (isset($_POST['login'])) {

    $login_input = trim($_POST['login_input']);
    $password    = trim($_POST['password']);

    if (empty($login_input) || empty($password)) {

        $message = __('Please fill in all fields.');

    } else {

        $login_input_safe = mysqli_real_escape_string($conn, $login_input);

        if (filter_var($login_input_safe, FILTER_VALIDATE_EMAIL)) {

            $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$login_input_safe' LIMIT 1");

        } elseif (preg_match('/^(97|98)[0-9]{8}$/', $login_input_safe)) {

            $query = mysqli_query($conn, "SELECT * FROM users WHERE phone='$login_input_safe' LIMIT 1");

        } else {

            $message = __('Enter a valid email or phone number starting with 97 or 98.');
        }

        if (isset($query) && mysqli_num_rows($query) === 1) {

            $user = mysqli_fetch_assoc($query);

            /* PASSWORD VERIFY */
            if (password_verify($password, $user['password'])) {

                /* STATUS CHECK */
                if ($user['status'] == 'inactive') {

                    $message = __('Your account has been deactivated by admin.');

                } else {

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role']    = $user['role'];
                    $_SESSION['name']    = $user['name'];

                    mysqli_query($conn, "UPDATE users SET last_login = NOW() WHERE id='{$user['id']}'");

                    /* REMEMBER ME */
                    if (isset($_POST['remember'])) {

                        $token = bin2hex(random_bytes(16));

                        mysqli_query($conn, "UPDATE users SET remember_token='$token' WHERE id='{$user['id']}'");

                        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

                        setcookie("remember_token", $token, time() + (86400 * 30), "/", "", $secure, true);
                    }

                    header("Location: " . ($user['role'] === 'elder' ? "elder_dashboard.php" : "caregiver_dashboard.php"));
                    exit;
                }

            } else {

                $message = __('Incorrect password.');
            }

        } else {

            $message = __('User not found.');
        }
    }
}
?>

<?php include "components/header.php"; ?>
<link rel="stylesheet" href="assets/css/colors.css">
<link rel="stylesheet" href="assets/css/loginstyle.css">

<div class="login-container dark-card">

    <h1><?php echo __('Welcome Back'); ?></h1>

    <?php if ($message): ?>
        <div class="message-box message-error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="text"
               name="login_input"
               placeholder="<?php echo __('Email or Phone'); ?>"
               value="<?= isset($_POST['login_input']) ? htmlspecialchars($_POST['login_input']) : ''; ?>"
               required>

        <div class="password-wrapper">

            <input type="password"
                   name="password"
                   id="password"
                   placeholder="<?php echo __('Password'); ?>"
                   required>

            <span class="eye" onclick="togglePassword()">👁</span>

        </div>

        <div style="margin:8px 0 16px 0;">

            <label style="font-size:14px;">
                <input type="checkbox" name="remember" value="1"> <?php echo __('Remember me'); ?>
            </label>

        </div>

        <input type="submit" name="login" value="<?php echo __('Login'); ?>">

    </form>

    <p class="signup-link">
        <?php echo __("Don't have an account?"); ?> <a href="signup.php"><?php echo __('Sign up'); ?></a>
    </p>

</div>

<script>
function togglePassword() {
    const field = document.getElementById("password");
    field.type = field.type === "password" ? "text" : "password";
}
</script>

<?php include "components/footer.php"; ?>
```
