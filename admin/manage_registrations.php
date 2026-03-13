<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}

$role = 'admin';
$activePage = 'registrations';
$message = "";

if(isset($_POST['delete_user'])){
    $user_id = $_POST['user_id'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$user_id'");
    $message = "User deleted successfully!";
}

if(isset($_POST['deactivate_user'])){
    $user_id = $_POST['user_id'];
    mysqli_query($conn, "UPDATE users SET status='inactive' WHERE id='$user_id'");
    $message = "User deactivated successfully!";
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Registrations - NepaCare Admin</title>
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
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-caregiver { background: #e3f2fd; color: #1976d2; }
        .badge-elder { background: #f3e5f5; color: #7b1fa2; }
        .action-buttons form {
            display: inline;
            margin-right: 5px;
        }
        button {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }
        .btn-delete { background: #dc3545; color: white; }
        .btn-delete:hover { background: #c82333; }
        .btn-deactivate { background: #ffc107; color: black; }
        .btn-deactivate:hover { background: #e0a800; }
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
        <h1>Manage Registrations</h1>

        <?php if($message): ?>
            <div class="success-msg"><?php echo $message; ?></div>
        <?php endif; ?>

        <h2>All Registered Users</h2>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Registration Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php while($user = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                <td>
                    <span class="badge badge-<?php echo $user['role']; ?>">
                        <?php echo ucfirst($user['role']); ?>
                    </span>
                </td>
                <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'] ?? 'now')); ?></td>
                <td>
                    <span style="padding: 4px 8px; border-radius: 4px; background: #d4edda; color: #155724;">
                        Active
                    </span>
                </td>
                <td class="action-buttons">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" name="deactivate_user" class="btn-deactivate" onclick="return confirm('Deactivate this user?');">Deactivate</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" name="delete_user" class="btn-delete" onclick="return confirm('Delete this user? This cannot be undone.');">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

    </div>
</div>

</body>
</html>
