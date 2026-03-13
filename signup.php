<?php
session_start();
include 'config/db.php';
include_once __DIR__ . '/config/lang.php';

$message = "";

if(isset($_POST['signup'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if(!preg_match("/^(97|98)[0-9]{8}$/", $phone)){
        $message = __('Invalid phone number. Must start with 97 or 98 and be 10 digits.');
    }
    elseif(empty($name) || empty($email) || empty($password) || empty($role)){
        $message = __('Please fill in all fields.');
    }
    elseif(!preg_match("/^[a-zA-Z ]*$/",$name)){
        $message = __('Name can only contain letters and spaces.');
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $message = __('Invalid email format.');
    }
    elseif($age !== '' && (!is_numeric($age) || (int)$age <= 0 || (int)$age > 120)){
        $message = __('Invalid age.');
    }
    else {
        $email_safe = mysqli_real_escape_string($conn, $email);
        $phone_safe = mysqli_real_escape_string($conn, $phone);
        $address_safe = mysqli_real_escape_string($conn, $address);
        $age_val = $age !== '' ? (int)$age : 'NULL';

        $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email_safe' OR phone='$phone_safe'");
        if(mysqli_num_rows($query) > 0){
            $message = __('Email or phone already registered. Please login.');
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $columns = ['name', 'email', 'phone', 'password', 'role'];
            $values = [
                "'$name'",
                "'$email_safe'",
                "'$phone_safe'",
                "'$password_hash'",
                "'$role'"
            ];

            if (userColumnExists($conn, 'address')) {
                $columns[] = 'address';
                $values[] = "'$address_safe'";
            }
            if (userColumnExists($conn, 'age')) {
                $columns[] = 'age';
                $values[] = $age_val;
            }

            $columns_sql = implode(', ', $columns);
            $values_sql = implode(', ', $values);

            mysqli_query($conn, "INSERT INTO users ($columns_sql) VALUES ($values_sql)");
            $message = __('Signup successful! You can <a href=\'login.php\'>login now</a>.');
        }
    }
}
?>

<?php include "components/header.php"; ?>
<link rel="stylesheet" href="assets/css/colors.css">
<link rel="stylesheet" href="assets/css/signupstyle.css">

<div class="signup-container dark-card">
    <h1><?php echo __('Signup - NepaCare'); ?></h1>

    <?php if($message): ?>
        <div class="message-box <?= strpos($message, 'successful') !== false ? 'message-success' : 'message-error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="<?php echo __('Full Name'); ?>" value="<?= isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
        <input type="email" name="email" placeholder="<?php echo __('Email'); ?>" value="<?= isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
        <input type="text" name="phone" placeholder="<?php echo __('Phone (97 or 98...)'); ?>" value="<?= isset($_POST['phone']) ? $_POST['phone'] : ''; ?>" required>
        <input type="text" name="address" placeholder="<?php echo __('Address'); ?>" value="<?= isset($_POST['address']) ? $_POST['address'] : ''; ?>">
        <input type="text" name="age" placeholder="<?php echo __('Age'); ?>" value="<?= isset($_POST['age']) ? $_POST['age'] : ''; ?>" maxlength="3">
        <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <span class="eye" onclick="togglePassword()">👁</span>
        </div>

        <select name="role" required>
            <option value=""><?php echo __('Select Role'); ?></option>
            <option value="elder" <?= (isset($_POST['role']) && $_POST['role']=='elder')?'selected':''; ?>><?php echo __('Elder'); ?></option>
            <option value="caregiver" <?= (isset($_POST['role']) && $_POST['role']=='caregiver')?'selected':''; ?>><?php echo __('Caregiver'); ?></option>
        </select>

        <input type="submit" name="signup" value="<?php echo __('Sign up'); ?>">
    </form>

    <p class="signup-link">
        <?php echo __('Already have an account?'); ?> <a href="login.php"><?php echo __('Login here'); ?></a>
    </p>
</div>

<script>
function togglePassword() {
    const field = document.getElementById("password");
    field.type = field.type === "password" ? "text" : "password";
}
</script>

<?php include "components/footer.php"; ?>
