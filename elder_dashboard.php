<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'elder'){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

$cols = getExistingUserColumns($conn, ['email','phone','address','age']);
$user = [];
if (!empty($cols)) {
    $cols_sql = implode(', ', $cols);
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT $cols_sql FROM users WHERE id='$user_id'"));
}

$role = 'elder';
$activePage = 'home';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Elder Dashboard - NepaCare</title>
    <link rel="stylesheet" href="assets/css/caregiverstyle.css">
    <style>
        .page-wrapper { display: flex; min-height: 100vh; }
        h1 { color: #b43113; }
        .message { background:#d4edda; padding:10px; margin-bottom:10px; }
        .otp-btn {
            font-family: 'Times New Roman', Times, serif;
            background: #3b82f6;
            color: white;
            padding: 10px 18px;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .otp-btn:hover { background: #2563eb; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="page-wrapper">
    <?php include 'components/sidebar.php'; ?>

    <div class="content">
    <h1>Welcome, <?php echo $_SESSION['name']; ?>!</h1>

    <div class="intro">
        <h2>Your Details</h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? '') ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? '') ?></p>
        <p><strong>Age:</strong> <?= htmlspecialchars($user['age'] ?? '') ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($user['address'] ?? '') ?></p>
    </div>

    <h2>About NepaCare</h2>
    <p>
        NepaCare is a simple, easy-to-use reminder system designed especially for elders.
        You can manage your daily tasks, set reminders, and receive alerts when it's time
        to do something important. This system ensures you never miss a task and helps
        caregivers stay informed.
    </p>
    </div>
</div>

</body>
</html>
