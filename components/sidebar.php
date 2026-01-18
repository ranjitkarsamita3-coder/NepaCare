<?php
if(!isset($role)) $role = 'guest';
if(!isset($activePage)) $activePage = '';
?>

<div class="sidebar">
    <div class="logo-container" style="text-align:center; margin-bottom:20px;">
        <img src="assets/images/logo.png" alt="NepaCare" class="logo">
    </div>

    <h3>NepaCare</h3>

    <?php if($role == 'elder'): ?>
        <a href="elder_dashboard.php" class="<?= $activePage=='home'?'active':'' ?>">Home</a>
        <a href="reminders.php" class="<?= $activePage=='reminders'?'active':'' ?>">Reminders</a>
        <a href="profile.php" class="<?= $activePage=='profile'?'active':'' ?>">Profile</a>
        <a href="elder_linked.php" class="<?= $activePage=='linked'?'active':'' ?>">Linked Caregiver</a>
    <?php elseif($role == 'caregiver'): ?>
        <a href="caregiver_dashboard.php" class="<?= $activePage=='home'?'active':'' ?>">Home</a>
        <a href="assigned_elders.php" class="<?= $activePage=='elders'?'active':'' ?>">Assigned Elders</a>
        <a href="profile.php" class="<?= $activePage=='profile'?'active':'' ?>">Profile</a>
        <a href="tasks.php" class="<?= $activePage=='tasks'?'active':'' ?>">Tasks</a>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
</div>
