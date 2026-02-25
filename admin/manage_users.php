<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}

$role = 'admin';
$activePage = 'users';

$users = mysqli_query($conn, "
    SELECT u.*, 
           (SELECT name FROM users WHERE id=u.linked_elder_id) as linked_elder_name,
           (SELECT name FROM users WHERE linked_elder_id=u.id AND role='caregiver') as linked_caregiver_name
    FROM users u 
    ORDER BY u.role, u.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - NepaCare Admin</title>
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
        .filter-buttons {
            margin-bottom: 20px;
        }
        .filter-buttons button {
            padding: 8px 16px;
            margin-right: 10px;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .filter-buttons button.active {
            background: #1e3a8a;
            color: white;
            border-color: #1e3a8a;
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
        .linked { background: #e8f5e9; color: #2e7d32; }
        .unlinked { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="admin-sidebar">
        <h3>NepaCare Admin</h3>
        <a href="index.php" class="<?= $activePage=='dashboard'?'active':'' ?>">Dashboard</a>
        <a href="manage_registrations.php" class="<?= $activePage=='registrations'?'active':'' ?>">Manage Registrations</a>
        <a href="manage_users.php" class="<?= $activePage=='users'?'active':'' ?>">Manage Users</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="admin-content">
        <h1>Manage Users</h1>

        <h2>User Directory</h2>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Linked To</th>
                <th>Registration Date</th>
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
                <td>
                    <?php 
                    if($user['role'] == 'caregiver' && $user['linked_elder_name']){
                        echo '<span class="badge linked">' . htmlspecialchars($user['linked_elder_name']) . ' (Elder)</span>';
                    } elseif($user['role'] == 'elder' && $user['linked_caregiver_name']){
                        echo '<span class="badge linked">' . htmlspecialchars($user['linked_caregiver_name']) . ' (Caregiver)</span>';
                    } else {
                        echo '<span class="badge unlinked">Not Linked</span>';
                    }
                    ?>
                </td>
                <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'] ?? 'now')); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>

    </div>
</div>

</body>
</html>
