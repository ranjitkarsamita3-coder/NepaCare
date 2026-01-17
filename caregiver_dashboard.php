<?php
session_start();
include 'config/db.php';

// Block access if not logged in or not caregiver
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver') {
    header("Location: login.php");
    exit;
}

$caregiver_id = $_SESSION['user_id'];

// Fetch linked elder
$elder = null;
$check = mysqli_query($conn, "
    SELECT u.id, u.name 
    FROM users u
    JOIN users c ON c.linked_elder_id = u.id
    WHERE c.id = '$caregiver_id'
");

if ($check && mysqli_num_rows($check) === 1) {
    $elder = mysqli_fetch_assoc($check);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Caregiver Dashboard - NepaCare</title>
    <link rel="stylesheet" href="assets/css/elderstyle.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; margin: 0; padding: 0; display: flex; }
        .sidebar {
            width: 200px;
            background: #f0f0f0;
            padding: 20px;
            height: 100vh;
        }
        .sidebar a {
            display: block;
            padding: 10px 0;
            text-decoration: none;
            color: #333;
        }
        .sidebar a:hover {
            background: #ddd;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        h1 { color: #2c3e50; }
        .about { margin-top: 50px; font-size: 14px; color: #555; }
        .btn{
            display: inline-block;
            padding: 10px 16px;
            margin-top: 10px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
        }
        .btn:hover{
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container" style="text-align:center; margin-bottom:20px;">
        <img src="assets/images/logo.png" alt="NepaCare" class="logo">
    </div>

    <h3>NepaCare</h3>
    <a href="caregiver_dashboard.php">Home</a>
    <a href="link_elder.php">Linked Elder</a>
    <a href="caregiver_reminders.php">Reminders</a>
    <a href="caregiver_profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<!-- CONTENT -->
<div class="content">

    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> !</h1>

    <!-- INTRO -->
    <div class="intro">
        <h2>About NepaCare</h2>
        <p>
            <strong>NepaCare</strong> is a simple reminder and care-support system designed
            especially for elders in Nepal.
            As a caregiver, you can help elders by managing their reminders,
            checking schedules, and supporting daily tasks.
        </p>
    </div>

    <!-- LINKED ELDER CARD -->
    <div class="card">
        <?php if ($elder): ?>
            <h2>Linked Elder</h2>
            <p>You are currently assisting:</p>
            <strong><?php echo htmlspecialchars($elder['name']); ?></strong>

            <br><br>
            <a href="caregiver_reminders.php" class="btn">Manage Reminders</a>

        <?php else: ?>
            <h2>No Elder Linked</h2>
            <p>You are not linked to any elder yet.</p>

            <a href="link_elder.php" class="btn">Link an Elder</a>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
