<?php
include_once __DIR__ . '/../config/lang.php';

if(!isset($role)) $role = 'guest';
if(!isset($activePage)) $activePage = '';
?>

<style>

.lang-switch{
    display:flex;
    gap:15px;
    margin:25px 20px;
}

.lang-switch button{
    padding:6px 12px;
    border:none;
    background:#2c3e50;
    color:white;
    cursor:pointer;
    border-radius:4px;
}
.lang-btn{
    flex:1;
    background:#3c4f63;
    color:white;
    border:none;
    border-radius:8px;
    padding:14px 0;
    font-size:18px;
    font-weight:600;
    cursor:pointer;
}

.lang-btn:hover{
    background:#2c3e50;
}

.lang-btn.active{
    background:#4f8df5;
}

</style>

<div class="sidebar">

    <div class="logo-container" style="text-align:center; margin-bottom:20px;">
        <img src="assets/images/logo.png" alt="NepaCare" class="logo">
    </div>

    <h3><?php echo __('NepaCare'); ?></h3>

    <?php if($role == 'elder'): ?>
        <a href="elder_dashboard.php" class="<?= $activePage=='home'?'active':'' ?>"><?php echo __('Home'); ?></a>
        <a href="reminders.php" class="<?= $activePage=='reminders'?'active':'' ?>"><?php echo __('Reminders'); ?></a>
        <a href="profile.php" class="<?= $activePage=='profile'?'active':'' ?>"><?php echo __('Profile'); ?></a>
        <a href="feedback.php" class="<?= $activePage=='feedback'?'active':'' ?>"><?php echo __('Feedback'); ?></a>
        <a href="elder_linked.php" class="<?= $activePage=='linked'?'active':'' ?>"><?php echo __('Linked Caregiver'); ?></a>

    <?php elseif($role == 'caregiver'): ?>

        <a href="caregiver_dashboard.php" class="<?= $activePage=='home'?'active':'' ?>"><?php echo __('Home'); ?></a>
        <a href="assigned_elders.php" class="<?= $activePage=='elders'?'active':'' ?>"><?php echo __('Assigned Elders'); ?></a>
        <a href="profile.php" class="<?= $activePage=='profile'?'active':'' ?>"><?php echo __('Profile'); ?></a>
        <a href="feedback.php" class="<?= $activePage=='feedback'?'active':'' ?>"><?php echo __('Feedback'); ?></a>
        <a href="tasks.php" class="<?= $activePage=='tasks'?'active':'' ?>"><?php echo __('Tasks'); ?></a>

    <?php endif; ?>

    <a href="logout.php"><?php echo __('Logout'); ?></a>

    <!-- Language Buttons -->

    <div class="lang-switch" id="lang-switch">
        <button class="lang-btn" data-lang="en">English</button>
        <button class="lang-btn" data-lang="ne">नेपाली</button>
    </div>

</div>

<script>

// Language switcher using cookie

(function(){

    function setLangCookie(lang){
        var d=new Date();
        d.setFullYear(d.getFullYear()+1);
        document.cookie = 'lang=' + lang + '; path=/; expires=' + d.toUTCString();
    }

    function getCookie(name){
        var v=document.cookie.match('(^|;)\\s*'+name+'\\s*=\\s*([^;]+)');
        return v ? v.pop() : null;
    }

    var current = getCookie('lang') || 'en';

    var btns = document.querySelectorAll('.lang-btn');

    btns.forEach(function(b){

        if(b.getAttribute('data-lang') === current){
            b.classList.add('active');
        }

        b.addEventListener('click', function(){

            var l = this.getAttribute('data-lang');

            setLangCookie(l);

            btns.forEach(function(x){
                x.classList.remove('active');
            });

            this.classList.add('active');

            location.reload();

        });

    });

})();

</script>