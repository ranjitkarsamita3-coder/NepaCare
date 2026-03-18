<?php
session_start();
include 'config/db.php';
require_once 'config/lang.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver'){
    header("Location: login.php");
    exit;
}

$caregiver_id = $_SESSION['user_id'];
$message = "";

$elder = null;
$check = mysqli_query($conn, "
    SELECT u.id, u.name 
    FROM users u
    JOIN users c ON c.linked_elder_id = u.id
    WHERE c.id = '$caregiver_id'
");
if($check && mysqli_num_rows($check) === 1){
    $elder = mysqli_fetch_assoc($check);
    $elder_id = $elder['id'];
} else {
    die("No linked elder found.");
}

if(isset($_POST['add_reminder'])){
    $text = trim($_POST['reminder_text']);
    $date = $_POST['reminder_date'];
    $time = $_POST['reminder_time'];
    $frequency = $_POST['reminder_frequency'];
    if($text && $date && $time){
        mysqli_query($conn, "INSERT INTO reminders (user_id, reminder_text, reminder_date, reminder_time, reminder_frequency) VALUES ('$elder_id','$text','$date','$time','$frequency')");
        $message = "Reminder added.";
    }
}
if(isset($_POST['update_reminder'])){
    $id = $_POST['reminder_id'];
    $text = trim($_POST['reminder_text']);
    $date = $_POST['reminder_date'];
    $time = $_POST['reminder_time'];
    $frequency = $_POST['reminder_frequency'];
    mysqli_query($conn, "UPDATE reminders SET reminder_text='$text', reminder_date='$date', reminder_time='$time', reminder_frequency='$frequency', done=0 WHERE id='$id' AND user_id='$elder_id'");
    $message = "Reminder updated.";
}
if(isset($_POST['delete_reminder'])){
    $id = $_POST['reminder_id'];
    mysqli_query($conn, "DELETE FROM reminders WHERE id='$id' AND user_id='$elder_id'");
    $message = "Reminder deleted.";
}

$edit = null;
if(isset($_GET['edit_id'])){
    $id = $_GET['edit_id'];
    $res = mysqli_query($conn, "SELECT * FROM reminders WHERE id='$id' AND user_id='$elder_id'");
    $edit = mysqli_fetch_assoc($res);
}

$result = mysqli_query($conn, "SELECT * FROM reminders WHERE user_id='$elder_id' ORDER BY reminder_date ASC, reminder_time ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Caregiver Reminders - NepaCare</title>
    <link rel="stylesheet" href="assets/css/caregiverstyle.css">
</head>
<body>
<div class="page-wrapper">
    <?php include __DIR__ . '/components/careSidebar.php'; ?>

    <div class="content">
        <h1>Reminders for <?php echo htmlspecialchars($elder['name']); ?></h1>

        <?php if($message): ?>
            <div class="msg"><?php echo $message; ?></div>
        <?php endif; ?>

        <h2><?php echo $edit ? "Edit Reminder" : "Add Reminder"; ?></h2>
        <form method="POST">
            <input type="text" name="reminder_text" placeholder="Message" value="<?php echo $edit['reminder_text'] ?? ''; ?>" required>
            <input type="date" name="reminder_date" value="<?php echo $edit['reminder_date'] ?? ''; ?>" required>
            <input type="time" name="reminder_time" value="<?php echo $edit['reminder_time'] ?? ''; ?>" required>
            <select name="reminder_frequency" required>
                <option value="">Select Frequency</option>
                <option value="once" <?php echo ($edit && $edit['reminder_frequency'] == 'once') ? 'selected' : ''; ?>>Once</option>
                <option value="daily" <?php echo ($edit && $edit['reminder_frequency'] == 'daily') ? 'selected' : ''; ?>>Daily</option>
                <option value="weekly" <?php echo ($edit && $edit['reminder_frequency'] == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
                <option value="monthly" <?php echo ($edit && $edit['reminder_frequency'] == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
            </select>
            <?php if($edit): ?>
                <input type="hidden" name="reminder_id" value="<?php echo $edit['id']; ?>">
                <button name="update_reminder">Update</button>
                <a href="caregiver_reminders.php" class="btn">Cancel</a>
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
                <th>Frequency</th>
                <th>Status</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            <?php $i=1; while($row=mysqli_fetch_assoc($result)): ?>
                <tr class="<?php echo $row['done']?'done':''; ?>">
                    <td><?php echo $i; ?></td>
                    <td><?php echo htmlspecialchars($row['reminder_text']); ?></td>
                    <td><?php echo $row['reminder_date']; ?></td>
                    <td><?php echo $row['reminder_time']; ?></td>
                    <td><?php echo ucfirst($row['reminder_frequency'] ?? 'once'); ?></td>
                    <td><?php echo $row['done']?'Done':'Pending'; ?></td>
                    <td><a href="caregiver_reminders.php?edit_id=<?php echo $row['id']; ?>">Edit</a></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="reminder_id" value="<?php echo $row['id']; ?>">
                            <button name="delete_reminder">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php $i++; endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
