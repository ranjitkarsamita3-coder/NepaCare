<?php
session_start();
include 'config/db.php';

// Only caregiver
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver'){
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
    <link rel="stylesheet" href="assets/css/caregiverstyle.css">
</head>
<body>

<div class="page-wrapper"> <!-- Flex container -->

    <?php include __DIR__ . '/components/careSidebar.php'; ?>

    <div class="content">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>

        <div class="intro">
            <h2>About NepaCare</h2>
            <p>
                NepaCare is a simple reminder and care-support system designed
                especially for elders in Nepal. As a caregiver, you can help elders
                by managing their reminders, checking schedules, and supporting daily tasks.
            </p>
        </div>
    </div>

</div>

</body>
</html>
