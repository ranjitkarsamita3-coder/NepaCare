<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver'){
    header("Location: login.php");
    exit;
}

$caregiver_id = $_SESSION['user_id'];

$elder = null;
$cols = getExistingUserColumns($conn, ['id','name','email','phone','address','age']);
$cols_sql = implode(', ', $cols);
$check = mysqli_query($conn, "
    SELECT $cols_sql
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

<div class="page-wrapper"> 

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

        <?php if ($elder): ?>
            <div class="intro">
                <h2>Linked Elder Details</h2>
                <p><strong>Name:</strong> <?= htmlspecialchars($elder['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($elder['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($elder['phone'] ?? '') ?></p>
                <p><strong>Age:</strong> <?= htmlspecialchars($elder['age'] ?? '') ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($elder['address'] ?? '') ?></p>
            </div>
        <?php else: ?>
            <div class="intro">
                <h2>Linked Elder</h2>
                <p>No elder is currently linked. Go to <a href="link_elder.php">Link Elder</a> to connect with an elder.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
