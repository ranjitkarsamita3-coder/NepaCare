<?php
session_start();
include 'config/db.php';

// Block access if not logged in or not elder
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'elder'){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Set role and active page for sidebar
$role = 'elder';
$activePage = 'home';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Elder Dashboard - NepaCare</title>
    <link rel="stylesheet" href="assets/css/elderstyle.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; margin: 0; padding: 0; display: flex; }
        .sidebar { width: 220px; background: #f0f0f0; padding: 20px; height: 100vh; }
        .sidebar a { display: block; padding: 10px 0; text-decoration: none; color: #333; }
        .sidebar a:hover, .sidebar a.active { background: #ddd; }
        .content { flex: 1; padding: 20px; }
        h1 { color: #2c3e50; }
        .message { background:#d4edda; padding:10px; margin-bottom:10px; }
        .otp-btn {
            font-family: 'Times New Roman', Times, serif;
            background: #007bff;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
        }
        .otp-btn:hover { background: #0056b3; }
    </style>
</head>
<body>

<?php include 'components/sidebar.php'; ?>

<div class="content">
    <h1>Welcome, <?php echo $_SESSION['name']; ?>!</h1>
    <h2>About NepaCare</h2>
    <p>
        NepaCare is a simple, easy-to-use reminder system designed especially for elders.
        You can manage your daily tasks, set reminders, and receive alerts when it's time
        to do something important. This system ensures you never miss a task and helps
        caregivers stay informed.
    </p>
</div>
</body>
</html>
