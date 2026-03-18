<?php
session_start();
include 'config/db.php';
include_once __DIR__ . '/config/lang.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'elder'){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$hour = date('H');
if ($hour < 12) $greeting = __('Good morning');
elseif ($hour < 17) $greeting = __('Good afternoon');
else $greeting = __('Good evening');

$healthTips = [
    "Drink enough water every day to stay healthy.",
    "Take a short walk in the morning to boost energy.",
    "Do light stretching exercises to improve flexibility.",
    "Eat fresh fruits and vegetables for better immunity.",
    "Take short breaks and rest to reduce fatigue.",
    "Practice deep breathing for relaxation and stress relief."
];

$tipIndex = date('z') % count($healthTips);
$dailyTip = $healthTips[$tipIndex];

$role = 'elder';
$activePage = 'home';
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo __('Elder Dashboard'); ?> - <?php echo __('NepaCare'); ?></title>
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
            text-align: left;
            margin-bottom: 20px;
            background-color: #fff5f0;
            padding: 15px;
            border-radius: 10px;
        }

        .top-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .card {
            flex: 1;
            background-color: #fefefe;
            padding: 20px;
            border-radius: 10px;
            font-size: 18px;
            line-height: 1.5;
            box-shadow: 0 2px 6px #b43113;
        }
        .card h2 {
            margin-top: 0;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .wellness-buttons button {
            margin-right: 10px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
        .good { background-color: #10b981; color: white; border: none; border-radius: 6px; }
        .okay { background-color: #fbbf24; color: white; border: none; border-radius: 6px; }
        .not-well { background-color: #ef4444; color: white; border: none; border-radius: 6px; }
        .suggestion { margin-top: 10px; font-weight: bold; }

        .about-section {
            margin-top: 30px;
            font-size: 18px;
            line-height: 1.6;
        }

        .card ul {
            padding-left: 20px;
            margin: 0;
        }
        .card ul li {
            margin-bottom: 8px;
        }
    </style>
    <script>
        function showSuggestion(status) {
            let suggestionText = "";
            if (status === "good") suggestionText = "<?php echo __('Keep up the great work! Stay active today.'); ?>";
            else if (status === "okay") suggestionText = "<?php echo __('Take some rest and do light activity if possible.'); ?>";
            else if (status === "not-well") suggestionText = "<?php echo __('Please consult your caregiver or doctor if needed.'); ?>";
            document.getElementById("suggestion").innerText = suggestionText;
        }
    </script>
</head>
<body>

<div class="page-wrapper">
    <?php include 'components/sidebar.php'; ?>

    <div class="content">

        <div class="welcome-msg">
            <?php echo $greeting; ?>, <?= htmlspecialchars($_SESSION['name']) ?>!
        </div>

        <div class="top-cards">

            <div class="card">
                <h2><?php echo __('Daily Activity Reminder'); ?></h2>
                <ul>
                    <li><?php echo __('Morning Walk'); ?></li>
                    <li><?php echo __('Drink Water'); ?></li>
                    <li><?php echo __('Exercise Time'); ?></li>
                </ul>
            </div>

            <div class="card">
                <h2><?php echo __('Health Tip'); ?></h2>
                <p><?= $dailyTip ?></p>
            </div>

            <div class="card">
                <h2><?php echo __('Wellness Check'); ?></h2>
                <p><?php echo __('How are you feeling today?'); ?></p>
                <div class="wellness-buttons">
                    <button class="good" onclick="showSuggestion('good')"><?php echo __('Good'); ?></button>
                    <button class="okay" onclick="showSuggestion('okay')"><?php echo __('Okay'); ?></button>
                    <button class="not-well" onclick="showSuggestion('not-well')"><?php echo __('Not well'); ?></button>
                </div>
                <p class="suggestion" id="suggestion"></p>
            </div>

        </div>

        <div class="about-section">
            <h2><?php echo __('About Us'); ?></h2>
            <p>
                <?php echo __('Elder about paragraph'); ?>
            </p>
        </div>

    </div>
</div>

</body>
</html>