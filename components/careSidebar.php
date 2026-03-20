<?php require_once __DIR__ . '/../config/lang.php'; ?>

<div class="sidebar">
    <div class="logo-container" style="text-align:center; margin-bottom:20px;">
        <img src="assets/images/logo.png" alt="NepaCare Logo" class="logo">
    </div>
    <h3><?php echo __('NepaCare'); ?></h3>
    <a href="caregiver_dashboard.php"><?php echo __('Home'); ?></a>
    <a href="caregiver_reminders.php"><?php echo __('Reminders'); ?></a>
    <a href="caregiver_profile.php"><?php echo __('Profile'); ?></a>
    <a href="feedback.php"><?php echo __('Feedback'); ?></a>
    <a href="link_elder.php"><?php echo __('Link Elder'); ?></a>

    <a href="logout.php"><?php echo __('Logout'); ?></a>

    <div class="lang-switch" id="lang-switch" style="margin-top:20px;">
        <button class="lang-btn" data-lang="en">English</button>
        <button class="lang-btn" data-lang="ne">नेपाली</button>
    </div>
</div>

<script>
(function(){
    function setLangCookie(lang){
        var d=new Date(); d.setFullYear(d.getFullYear()+1);
        document.cookie = 'lang=' + lang + '; path=/; expires=' + d.toUTCString();
    }
    function getCookie(name){
        var v=document.cookie.match('(^|;)\\s*'+name+'\\s*=\\s*([^;]+)');
        return v ? v.pop() : null;
    }

    var current = getCookie('lang') || 'en';
    var btns = document.querySelectorAll('.lang-btn');
    btns.forEach(function(b){
        if(b.getAttribute('data-lang') === current) b.classList.add('active');
        b.addEventListener('click', function(){
            var l = this.getAttribute('data-lang');
            setLangCookie(l);
            btns.forEach(function(x){ x.classList.remove('active'); });
            this.classList.add('active');
            location.reload();
        });
    });
})();
</script>


