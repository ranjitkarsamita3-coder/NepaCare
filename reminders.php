<?php
session_start();
include 'config/db.php';

// Block access if not logged in or not elder
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'elder'){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// MARK DONE
if(isset($_POST['mark_done'])){
    $rem_id = $_POST['reminder_id'];
    mysqli_query($conn, "UPDATE reminders SET done=1 WHERE id='$rem_id' AND user_id='$user_id'");
    $message = "Reminder marked as done!";
}

// DELETE REMINDER
if(isset($_POST['delete_reminder'])){
    $rem_id = $_POST['reminder_id'];
    mysqli_query($conn, "DELETE FROM reminders WHERE id='$rem_id' AND user_id='$user_id'");
    $message = "Reminder deleted successfully!";
}

// EDIT REMINDER
if(isset($_POST['edit_reminder'])){
    $rem_id = $_POST['reminder_id'];
    $new_text = trim($_POST['reminder_text']);
    $new_date = $_POST['reminder_date'];
    $new_time = $_POST['reminder_time'];

    if(!empty($new_text) && !empty($new_date) && !empty($new_time)){
        mysqli_query($conn, "UPDATE reminders 
                             SET reminder_text='$new_text', reminder_date='$new_date', reminder_time='$new_time', done=0 
                             WHERE id='$rem_id' AND user_id='$user_id'");
        $message = "Reminder updated successfully!";
    } else {
        $message = "All fields are required for editing.";
    }
}

// ADD REMINDER
if(isset($_POST['add_reminder'])){
    $reminder_text = trim($_POST['reminder_text']);
    $reminder_date = $_POST['reminder_date'];
    $reminder_time = $_POST['reminder_time'];

    if(empty($reminder_text) || empty($reminder_date) || empty($reminder_time)){
        $message = "All fields are required.";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO reminders (user_id, reminder_text, reminder_date, reminder_time)
                                        VALUES ('$user_id','$reminder_text','$reminder_date','$reminder_time')");
        $message = $insert ? "Reminder added successfully!" : "Failed to add reminder.";
    }
}

// EDIT FORM PREFILL
$edit_reminder = null;
if(isset($_GET['edit_id'])){
    $edit_id = $_GET['edit_id'];
    $res = mysqli_query($conn, "SELECT * FROM reminders WHERE id='$edit_id' AND user_id='$user_id'");
    if($res && mysqli_num_rows($res) > 0){
        $edit_reminder = mysqli_fetch_assoc($res);
    }
}

// FETCH REMINDERS (ALL: pending + done)
$result = mysqli_query($conn, "
    SELECT * FROM reminders 
    WHERE user_id='$user_id'
    ORDER BY reminder_date ASC, reminder_time ASC
");

$missed = mysqli_query($conn, "
    SELECT * FROM reminders
    WHERE user_id='$user_id'
    AND done=0
    AND CONCAT(reminder_date,' ',reminder_time) < NOW()
");

$reminders = [];
$jsArr = [];

if($result && mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        $reminders[] = $row;

        // Only pending reminders for alarm
        if($row['done'] == 0){
            $reminder_datetime = $row['reminder_date'] . ' ' . $row['reminder_time'];
            if(strtotime($reminder_datetime) >= time()){
            $jsArr[] = [
                "id"   => $row['id'],
                "time" => $reminder_datetime,
                "msg"  => $row['reminder_text']
            ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reminders - NepaCare</title>
    <link rel="stylesheet" href="assets/css/elderstyle.css">
    <link rel="stylesheet" href="assets/css/caregiverstyle.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; margin: 0; padding: 0; display: flex; }
        /* Fix default light buttons */
        button {
            font-family: 'Times New Roman', Times, serif;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Make Delete button red */
        button[name="delete_reminder"] {
            background-color: #dc3545;
        }

        button[name="delete_reminder"]:hover {
            background-color: #c82333;
        }

        /* Make Done button green */
        button[name="mark_done"] {
            background-color: #28a745;
        }

        button[name="mark_done"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container" style="text-align:center; margin-bottom:20px;">
        <img src="assets/images/logo.png" alt="NepaCare" class="logo">
    </div>

    <h3>NepaCare</h3>
    <a href="elder_dashboard.php">Home</a>
    <a href="reminders.php">Reminders</a>
    <a href="profile.php">Profile</a>
    <a href="elder_linked.php">Linked Caregiver</a> 
    <a href="logout.php">Logout</a>
</div>

<div class="content">
<h1>Manage Reminders</h1>
    <?php if(mysqli_num_rows($missed) > 0): ?>
    <div style="background:#ffe6e6; padding:15px; margin-bottom:20px; border-radius:8px;">
        <h3 style="color:red;">⚠ Missed Reminders</h3>

        <?php while($m = mysqli_fetch_assoc($missed)): ?>
            <p>
                <b><?php echo htmlspecialchars($m['reminder_text']); ?></b>
                (<?php echo $m['reminder_date']." ".$m['reminder_time']; ?>)
            </p>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

<?php if($message != ""): ?>
    <div class="success"><?php echo $message; ?></div>
<?php endif; ?>

<h2><?php echo $edit_reminder ? "Edit Reminder" : "Add Reminder"; ?></h2>
<form method="POST">
    <input type="text" name="reminder_text" placeholder="Reminder message" 
           value="<?php echo $edit_reminder ? $edit_reminder['reminder_text'] : ''; ?>" required><br>
    <input type="date" name="reminder_date" 
           value="<?php echo $edit_reminder ? $edit_reminder['reminder_date'] : ''; ?>" required><br>
    <input type="time" name="reminder_time" 
           value="<?php echo $edit_reminder ? $edit_reminder['reminder_time'] : ''; ?>" required><br>

    <?php if($edit_reminder): ?>
        <input type="hidden" name="reminder_id" value="<?php echo $edit_reminder['id']; ?>">
        <button type="submit" name="edit_reminder">Update Reminder</button>
        <a href="reminders.php">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add_reminder" class="primary-btn">Add Reminder </button>
    <?php endif; ?>
</form>

<h2>My Reminders</h2>
<table>
<tr>
    <th>S.N</th>
    <th>Message</th>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
    <th>Mark Done</th>
    <th>Edit</th>
    <th>Delete</th>
</tr>

<?php
if(!empty($reminders)){
    foreach($reminders as $index => $row){
        $done_class = $row['done'] ? "done" : "";
        echo "<tr class='$done_class'>";
        echo "<td>" . ($index + 1) . "</td>";
        echo "<td>{$row['reminder_text']}</td>";
        echo "<td>{$row['reminder_date']}</td>";
        echo "<td>{$row['reminder_time']}</td>";
        $reminderTime = strtotime($row['reminder_date'].' '.$row['reminder_time']);
        $isPast = $reminderTime < time();
        echo "<td>";
        if ($row['done'] == 1) {
            echo "<span style='color:green;'>Done</span>";
        } elseif ($isPast) {
            echo "<span style='color:red;'>Missed</span>";
        } else {
            echo "<span style='color:orange;'>Pending</span>";
        }
        echo "</td>";

        // MARK DONE
        echo "<td>";
        if($row['done'] == 0){
            echo "<form method='POST' style='margin:0;'>
                    <input type='hidden' name='reminder_id' value='{$row['id']}'>
                    <button type='submit' name='mark_done'>Done</button>
                  </form>";
        } else {
            echo "-";
        }
        echo "</td>";

        // EDIT
        echo "<td><a href='reminders.php?edit_id={$row['id']}'>Edit</a></td>";

        // DELETE
        echo "<td>
                <form method='POST' style='margin:0;'>
                    <input type='hidden' name='reminder_id' value='{$row['id']}'>
                    <button type='submit' name='delete_reminder'>Delete</button>
                </form>
              </td>";

        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No reminders yet.</td></tr>";
}
?>
</table>

<audio id="reminderSound" src="assets/sound/reminder.mp3" preload="auto"></audio>

<script>
var reminders = <?php echo json_encode($jsArr); ?>;
var reminderIntervals = {};

function checkReminders(){
    var now = new Date();

    reminders.forEach(function(r){
        var t = new Date(r.time);

        if (!r.alerted && t <= now) {
            r.alerted = true;

            document.getElementById("popupText").innerText = r.msg;
            document.getElementById("popupReminderId").value = r.id;

            document.getElementById("reminderPopup").style.display = "block";

            setInterval(function(){
                document.getElementById("reminderSound").play();
            }, 5000);
        }
    });
}
setInterval(checkReminders, 1000);
</script>

</div>
</body>
<div id="reminderPopup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6);">
    <div style="background:#fff; padding:20px; max-width:400px; margin:150px auto; text-align:center; border-radius:10px;">
        <h2>Reminder</h2>
        <p id="popupText"></p>

        <form method="POST">
            <input type="hidden" name="reminder_id" id="popupReminderId">
            <button name="mark_done" style="padding:10px 20px; font-size:18px;">
                Mark as Done
            </button>
        </form>
    </div>
</div>
</html>