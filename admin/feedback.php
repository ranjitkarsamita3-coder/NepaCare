<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}

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

$role = 'admin';
$activePage = 'feedback';
$message = '';

$feedbacks = mysqli_query($conn, "SELECT f.*, u.name AS user_name, u.role AS user_role FROM feedback f LEFT JOIN users u ON u.id = f.user_id ORDER BY f.created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Feedback - NepaCare Admin</title>
    <link rel="stylesheet" href="../assets/css/caregiverstyle.css">
    <style>
        .page-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar {
            width: 250px;
            background-color: #1e3a8a;
            color: #fefefe;
            padding: 30px 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .admin-sidebar h3 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
        }
        .admin-sidebar a {
            display: block;
            padding: 12px 15px;
            margin-bottom: 10px;
            text-decoration: none;
            color: #fefefe;
            font-size: 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .admin-sidebar a:hover, .admin-sidebar a.active {
            background-color: #3478e5;
            color: #fd866b;
            transform: translateX(5px);
        }
        .admin-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
        .admin-content h1 {
            color: #b43113;
            margin-bottom: 20px;
        }
        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #f4f0f0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        th {
            background-color: #1e3a8a;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover { background-color: #f9f9f9; }
        .label {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .label-elder { background: #f3e5f5; color: #7b1fa2; }
        .label-caregiver { background: #e3f2fd; color: #1976d2; }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="admin-sidebar">
        <div class="logo-container" style="text-align:center; margin-bottom:20px;">
            <img src="../assets/images/logo.png" alt="NepaCare Logo" class="logo" style="max-width:100px; height:auto; border-radius:15px; border:2px solid #fff;" />
        </div>
        <h3>NepaCare Admin</h3>
        <a href="index.php" class="<?= $activePage=='dashboard'?'active':'' ?>">Dashboard</a>
        <a href="manage_registrations.php" class="<?= $activePage=='registrations'?'active':'' ?>">Manage Registrations</a>
        <a href="manage_users.php" class="<?= $activePage=='users'?'active':'' ?>">Manage Users</a>
        <a href="feedback.php" class="<?= $activePage=='feedback'?'active':'' ?>">Feedback</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="admin-content">
        <h1>Feedback</h1>


        <?php if($feedbacks && mysqli_num_rows($feedbacks) > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Submitted</th>
                </tr>
                <?php while($row = mysqli_fetch_assoc($feedbacks)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['user_name'] ?? 'Unknown'); ?></td>
                        <td>
                            <?php
                                $roleLabel = $row['user_role'] ?? 'unknown';
                                $class = $roleLabel === 'elder' ? 'label-elder' : ($roleLabel === 'caregiver' ? 'label-caregiver' : 'label-unread');
                            ?>
                            <span class="label <?php echo $class; ?>"><?php echo ucfirst(htmlspecialchars($roleLabel)); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($row['subject']); ?></td>
                        <td style="max-width: 350px; white-space: pre-wrap; word-break: break-word;"><?php echo htmlspecialchars($row['message']); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No feedback entries have been submitted yet.</p>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
