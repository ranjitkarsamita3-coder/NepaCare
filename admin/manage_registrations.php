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

/* DELETE USER */
if(isset($_POST['delete_user'])){
    $user_id = $_POST['user_id'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$user_id'");
    $message = "User deleted successfully!";
}

/* DEACTIVATE USER */
if(isset($_POST['deactivate_user'])){
    $user_id = $_POST['user_id'];
    mysqli_query($conn, "UPDATE users SET status='inactive' WHERE id='$user_id'");
    $message = "User deactivated successfully!";
}

/* ACTIVATE USER */
if(isset($_POST['activate_user'])){
    $user_id = $_POST['user_id'];
    mysqli_query($conn, "UPDATE users SET status='active' WHERE id='$user_id'");
    $message = "User activated successfully!";
}

/* FETCH USERS */
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Registrations - NepaCare Admin</title>
<link rel="stylesheet" href="../assets/css/caregiverstyle.css">

<style>

.page-wrapper{
display:flex;
min-height:100vh;
}

/* SIDEBAR */

.admin-sidebar{
width:250px;
background:#1e3a8a;
color:#fff;
padding:30px 20px;
}

.admin-sidebar h3{
text-align:center;
margin-bottom:30px;
}

.admin-sidebar a{
display:block;
padding:12px 15px;
margin-bottom:10px;
color:white;
text-decoration:none;
border-radius:8px;
transition:0.3s;
}

.admin-sidebar a:hover,
.admin-sidebar a.active{
background:#3478e5;
color:#fd866b;
}

/* CONTENT */

.admin-content{
flex:1;
padding:30px;
}

.admin-content h1{
color:#b43113;
margin-bottom:20px;
}

.success-msg{
background:#d4edda;
color:#155724;
padding:12px;
border-radius:6px;
margin-bottom:20px;
}

/* TABLE */

table{
width:100%;
border-collapse:collapse;
background:#f4f0f0;
border-radius:10px;
overflow:hidden;
}

th{
background:#1e3a8a;
color:white;
padding:15px;
text-align:left;
}

td{
padding:12px;
border-bottom:1px solid #ddd;
}

tr:hover{
background:#f9f9f9;
}

/* BADGES */

.badge{
padding:4px 10px;
border-radius:20px;
font-size:12px;
font-weight:600;
}

.badge-caregiver{
background:#e3f2fd;
color:#1976d2;
}

.badge-elder{
background:#f3e5f5;
color:#7b1fa2;
}

/* BUTTONS */

button{
padding:6px 12px;
border:none;
border-radius:5px;
cursor:pointer;
font-size:13px;
font-weight:600;
}

.btn-delete{
background:#dc3545;
color:white;
}

.btn-delete:hover{
background:#c82333;
}

.btn-deactivate{
background:#ffc107;
}

.btn-activate{
background:#28a745;
color:white;
}

</style>
</head>

<body>

<div class="page-wrapper">

<!-- SIDEBAR -->

<div class="admin-sidebar">

<div style="text-align:center;margin-bottom:20px;">
<img src="../assets/images/logo.png" style="max-width:100px;border-radius:15px;border:2px solid white;">
</div>

<h3>NepaCare Admin</h3>

<a href="index.php">Dashboard</a>
<a href="manage_registrations.php" class="active">Manage Registrations</a>
<a href="manage_users.php">Manage Users</a>
<a href="feedback.php">Feedback</a>
<a href="logout.php">Logout</a>

</div>


<!-- CONTENT -->

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

<!-- STATUS -->

<td>

<?php if($user['status'] == 'inactive'): ?>

<span style="background:#f8d7da;color:#721c24;padding:4px 10px;border-radius:5px;">
Inactive
</span>

<?php else: ?>

<span style="background:#d4edda;color:#155724;padding:4px 10px;border-radius:5px;">
Active
</span>

<?php endif; ?>

</td>


<!-- ACTION BUTTONS -->

<td>

<?php if($user['status'] == 'active'): ?>

<form method="POST" style="display:inline;">
<input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
<button type="submit" name="deactivate_user" class="btn-deactivate"
onclick="return confirm('Deactivate this user?');">
Deactivate
</button>
</form>

<?php else: ?>

<form method="POST" style="display:inline;">
<input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
<button type="submit" name="activate_user" class="btn-activate"
onclick="return confirm('Activate this user?');">
Activate
</button>
</form>

<?php endif; ?>

<form method="POST" style="display:inline;">
<input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
<button type="submit" name="delete_user" class="btn-delete"
onclick="return confirm('Delete this user?');">
Delete
</button>
</form>

</td>

</tr>

<?php endwhile; ?>

</table>

</div>
</div>

</body>
</html>