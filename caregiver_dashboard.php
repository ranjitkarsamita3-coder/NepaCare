<?php
session_start();
include 'config/db.php';
include_once __DIR__ . '/config/lang.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver'){
    header("Location: login.php");
    exit;
}

$caregiver_id = $_SESSION['user_id'];

// Get linked elder
$elder = null;
$check = mysqli_query($conn, "
    SELECT u.id, u.name, u.last_login 
    FROM users u
    JOIN users c ON c.linked_elder_id = u.id
    WHERE c.id = '$caregiver_id'
");

if ($check && mysqli_num_rows($check) === 1) {
    $elder = mysqli_fetch_assoc($check);
}

$role = 'caregiver';
$activePage = 'home';
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo __('Caregiver Dashboard'); ?> - <?php echo __('NepaCare'); ?></title>
    <link rel="stylesheet" href="assets/css/caregiverstyle.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            background: #fdf9f9;
            color: #333;
        }

        .page-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        .welcome-msg {
            font-size: 28px;
            font-weight: bold;
            background-color: #fff5f0;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .last-seen {
            background-color: #fefefe;
            padding: 20px;
            border-radius: 10px;
            font-size: 18px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px #b43113;
        }

        .last-seen h2 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 22px;
        }

        .about-section {
            font-size: 18px;
            line-height: 1.6;
            background-color: #fff5f0;
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <?php include __DIR__ . '/components/careSidebar.php'; ?>

    <div class="content">
        <div class="welcome-msg">
            <?php echo __('Welcome, '); ?><?= htmlspecialchars($_SESSION['name']) ?>!
            <?php if($elder): ?>
                <br><?php echo __('Linked Elder: '); ?><?= htmlspecialchars($elder['name']) ?>
            <?php endif; ?>
        </div>

        <?php if($elder): ?>
            <div class="last-seen">
                <h2><?php echo __('Last Seen'); ?></h2>
                <p>
                    <?php 
                        if(!empty($elder['last_login'])){
                            echo date('F j, Y, h:i A', strtotime($elder['last_login']));
                        } else {
                            echo __('No login records found.');
                        }
                    ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="about-section">
            <h2><?php echo __('About Us'); ?></h2>
            <p>
                <?php echo __('Caregiver about paragraph'); ?>
            </p>
        </div>
    </div>
</div>

</body>
</html>