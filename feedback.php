<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['elder', 'caregiver'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
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

// Fetch past feedback
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
        .feedback-form label { display: block; margin-top: 15px; font-weight: 600; }
        .feedback-form .input-box {
            width: 100%;
            padding: 12px;
            border: 1px solid #fd866b;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
        }
        .feedback-form textarea.input-box { min-height: 140px; resize: vertical; }
        .notice {
            padding: 12px 16px;
            border-radius: 8px;
            background: #d4edda;
            color: #155724;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .notice.error { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { padding: 12px 15px; border: 1px solid #ddd; vertical-align: top; }
        th { background-color: #1e3a8a; color: #fff; text-align: left; }
        td.message-cell { width: 50%; white-space: pre-wrap; word-wrap: break-word; }
        td.subject-cell { width: 30%; white-space: pre-wrap; word-wrap: break-word; }
        tr:hover { background-color: #f9f9f9; }
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
            <div class="notice<?php echo ($notice === 'Please enter a message.') ? ' error' : ''; ?>">
                <?php echo htmlspecialchars($notice); ?>
            </div>
        <?php endif; ?>

        <div class="feedback-form">
            <form method="POST">
                <label for="subject">Subject (optional)</label>
                <input type="text" id="subject" name="subject" 
                    value="<?php echo isset($subject)?htmlspecialchars($subject):''; ?>" 
                    placeholder="Subject" class="input-box">

                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Tell us what you think" class="input-box"></textarea>

                <button type="submit">Send Feedback</button>
            </form>
        </div>

        <?php if ($feedbacks && mysqli_num_rows($feedbacks) > 0): ?>
            <div class="feedback-list">
                <h2>Your past feedback</h2>
                <table>
                    <tr>
                        <th>Date</th>
                        <th class="subject-cell">Subject</th>
                        <th class="message-cell">Message</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($feedbacks)): ?>
                        <tr>
                            <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                            <td class="subject-cell"><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td class="message-cell"><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>