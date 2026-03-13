<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['elder', 'caregiver'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Ensure feedback table exists (creates or updates schema if missing)
$createTableSql = "CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_role VARCHAR(20) NOT NULL,
    subject VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $createTableSql);

$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($message === '') {
        $notice = 'Please enter a message.';
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO feedback (user_id, user_role, subject, message, is_read) VALUES (?, ?, ?, ?, 0)");
        mysqli_stmt_bind_param($stmt, 'isss', $user_id, $role, $subject, $message);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $notice = 'Your feedback has been submitted.';
        $subject = '';
        $message = '';
    }
}

$feedbacks = mysqli_query($conn, "SELECT * FROM feedback WHERE user_id = '$user_id' ORDER BY created_at DESC");

$activePage = 'feedback';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Feedback - NepaCare</title>
    <link rel="stylesheet" href="assets/css/caregiverstyle.css">
    <style>
        .feedback-form {
            max-width: 700px;
            margin-top: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }
        .feedback-form label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
        }
        .feedback-form textarea {
            width: 100%;
            min-height: 140px;
            padding: 10px;
            border: 1px solid #fd866b;
            border-radius: 8px;
            font-size: 15px;
            resize: vertical;
        }
        .feedback-list {
            margin-top: 30px;
        }
        .notice {
            padding: 12px 16px;
            border-radius: 8px;
            background: #d4edda;
            color: #155724;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .notice.error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <?php if ($role === 'caregiver'): ?>
        <?php include __DIR__ . '/components/careSidebar.php'; ?>
    <?php else: ?>
        <?php include __DIR__ . '/components/sidebar.php'; ?>
    <?php endif; ?>

    <div class="content">
        <h1>Feedback</h1>
        <p>Have a suggestion or issue? Send it here and our team will review it.</p>

        <?php if ($notice): ?>
            <div class="notice<?php echo ($notice === 'Please enter a message.') ? ' error' : ''; ?>"><?php echo htmlspecialchars($notice); ?></div>
        <?php endif; ?>

        <div class="feedback-form">
            <form method="POST">
                <label for="subject">Subject (optional)</label>
                <input type="text" id="subject" name="subject" value="<?php echo isset($subject)?htmlspecialchars($subject):''; ?>" placeholder="Subject">

                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Tell us what you think..."><?php echo isset($message)?htmlspecialchars($message):''; ?></textarea>

                <button type="submit">Send Feedback</button>
            </form>
        </div>

        <?php if ($feedbacks && mysqli_num_rows($feedbacks) > 0): ?>
            <div class="feedback-list">
                <h2>Your past feedback</h2>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>Message</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($feedbacks)): ?>
                        <tr>
                            <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
