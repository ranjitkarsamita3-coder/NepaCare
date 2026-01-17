<?php
session_start();
include 'config/db.php';

// Only caregiver
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver') {
    header("Location: login.php");
    exit;
}

$caregiver_id = $_SESSION['user_id'];
$message = "";

// Get linked elder
$elder_q = mysqli_query($conn, "
    SELECT u.id, u.name 
    FROM users u
    JOIN users c ON c.linked_elder_id = u.id
    WHERE c.id = '$caregiver_id'
");

if (!$elder_q || mysqli_num_rows($elder_q) === 0) {
    die("No elder linked yet.");
}

$elder = mysqli_fetch_assoc($elder_q);
$elder_id = $elder['id'];

/* ---------------- ADD REMINDER ---------------- */
if (isset($_POST['add_reminder'])) {
    $text = trim($_POST['reminder_text']);
    $date = $_POST['reminder_date'];
    $time = $_POST['reminder_time'];

    if ($text && $date && $time) {
        mysqli_query($conn, "
            INSERT INTO reminders (user_id, reminder_text, reminder_date, reminder_time)
            VALUES ('$elder_id', '$text', '$date', '$time')
        ");
        $message = "Reminder added for elder.";
    }
}

/* ---------------- DELETE REMINDER ---------------- */
if (isset($_POST['delete_reminder'])) {
    $id = $_POST['reminder_id'];
    mysqli_query($conn, "DELETE FROM reminders WHERE id='$id' AND user_id='$elder_id'");
    $message = "Reminder deleted.";
}

/* ---------------- EDIT REMINDER ---------------- */
if (isset($_POST['update_reminder'])) {
    $id = $_POST['reminder_id'];
    $text = trim($_POST['reminder_text']);
    $date = $_POST['reminder_date'];
    $time = $_POST['reminder_time'];

    mysqli_query($conn, "
        UPDATE reminders 
        SET reminder_text='$text', reminder_date='$date', reminder_time='$time', done=0
        WHERE id='$id' AND user_id='$elder_id'
    ");
    $message = "Reminder updated.";
}

/* ---------------- EDIT PREFILL ---------------- */
$edit = null;
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $res = mysqli_query($conn, "SELECT * FROM reminders WHERE id='$id' AND user_id='$elder_id'");
    $edit = mysqli_fetch_assoc($res);
}

/* ---------------- FETCH REMINDERS ---------------- */
$result = mysqli_query($conn, "
    SELECT * FROM reminders 
    WHERE user_id='$elder_id'
    ORDER BY reminder_date ASC, reminder_time ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Caregiver Reminders - NepaCare</title>
    <link rel="stylesheet" href="assets/css/elderstyle.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; margin:0; padding:0; display:flex; }
        .sidebar { width:200px; background:#f0f0f0; padding:20px; height:100vh; }
        .sidebar a { display:block; padding:10px 0; text-decoration:none; color:#333; }
        .sidebar a:hover { background:#ddd; }
        .content { flex:1; padding:20px; }
        h1, h2 { color:#2c3e50; }
        .success, .msg { background:#d4edda; padding:10px; margin-bottom:10px; }
        input, button { font-size:16px; padding:6px; margin:5px 0; }
        button { background:#007bff; color:white; border:none; border-radius:6px; cursor:pointer; }
        button:hover { background:#0056b3; }
        button[name="delete_reminder"] { background:#dc3545; }
        button[name="delete_reminder"]:hover { background:#c82333; }
        button[name="update_reminder"] { background:#28a745; }
        button[name="update_reminder"]:hover { background:#218838; }
        button[name="add_reminder"] { background:#007bff; }
        button[name="add_reminder"]:hover { background:#0056b3; }
        table { border-collapse:collapse; width:100%; max-width:900px; }
        th, td { border:1px solid #ccc; padding:10px; text-align:center; }
        .done { text-decoration:line-through; color:red; }
        form { display:inline; margin:0; }
        a { text-decoration:none; color:blue; margin:0 5px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container" style="text-align:center; margin-bottom:20px;">
        <img src="assets/images/logo.png" alt="NepaCare" class="logo">
    </div>
    <h3>NepaCare</h3>
    <a href="caregiver_dashboard.php">Home</a>
    <a href="link_elder.php">Linked Elder</a>
    <a href="caregiver_reminders.php">Reminders</a>
    <a href="caregiver_profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="content">

<h2>Reminders for <?php echo htmlspecialchars($elder['name']); ?></h2>

<?php if ($message): ?>
    <div class="msg"><?php echo $message; ?></div>
<?php endif; ?>

<h3><?php echo $edit ? "Edit Reminder" : "Add Reminder"; ?></h3>
<form method="POST">
    <input type="text" name="reminder_text" placeholder="Message" value="<?php echo $edit['reminder_text'] ?? ''; ?>" required><br>
    <input type="date" name="reminder_date" value="<?php echo $edit['reminder_date'] ?? ''; ?>" required><br>
    <input type="time" name="reminder_time" value="<?php echo $edit['reminder_time'] ?? ''; ?>" required><br>

    <?php if ($edit): ?>
        <input type="hidden" name="reminder_id" value="<?php echo $edit['id']; ?>">
        <button name="update_reminder">Update</button>
        <a href="caregiver_reminders.php">Cancel</a>
    <?php else: ?>
        <button name="add_reminder">Add Reminder</button>
    <?php endif; ?>
</form>

<table>
<tr>
    <th>S.N</th>
    <th>Message</th>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
    <th>Edit</th>
    <th>Delete</th>
</tr>

<?php
$i = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $cls = $row['done'] ? "done" : "";
    echo "<tr class='$cls'>";
    echo "<td>$i</td>";
    echo "<td>{$row['reminder_text']}</td>";
    echo "<td>{$row['reminder_date']}</td>";
    echo "<td>{$row['reminder_time']}</td>";
    echo "<td>" . ($row['done'] ? "Done" : "Pending") . "</td>";
    echo "<td><a href='caregiver_reminders.php?edit_id={$row['id']}'>Edit</a></td>";
    echo "<td>
            <form method='POST'>
                <input type='hidden' name='reminder_id' value='{$row['id']}'>
                <button name='delete_reminder'>Delete</button>
            </form>
          </td>";
    echo "</tr>";
    $i++;
}
?>
</table>

</div>
</body>
</html>
