<div class="top-nav">
    <a href="index.php" class="logo">
        <img src="assets/images/logo.png" alt="NepaCare Logo">
        <span><?php echo __('NepaCare'); ?></span>
    </a>

    <div class="nav-links">
        <a href="index.php"><?php echo __('Home'); ?></a>
        <a href="about.php"><?php echo __('About Us'); ?></a>
        <a href="contact.php"><?php echo __('Contact Us'); ?></a>
        <a href="login.php"><?php echo __('Login / Signup'); ?></a>
    </div>
    <div class="lang-switch" id="lang-switch">
        <button class="lang-btn" data-lang="en">English</button>
        <button class="lang-btn" data-lang="ne">नेपाली</button>
    </div>
</div>

<style>
    .top-nav {
        background:#7bbde3;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 20px;
    }

    .top-nav .logo {
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .top-nav .logo img {
        height: 50px;
        margin-right: 10px;
        border-radius: 8px;
    }

    .top-nav .logo span {
        color: white;
        font-size: 20px;
        font-weight: bold;
    }

    .top-nav .nav-links a {
        color:white;
        font-size:20px;
        margin:0 10px;
        text-decoration:none;
    }

    .top-nav .nav-links a:hover {
        text-decoration: underline;
    }

    .lang-switch {
        display:flex;
        gap:8px;
        align-items:center;
    }

    .lang-btn {
        background: rgba(255,255,255,0.15);
        color: white;
        border: 1px solid rgba(255,255,255,0.25);
        padding: 6px 10px;
        font-size:16px;
        border-radius:6px;
        cursor: pointer;
    }

    .lang-btn.active {
        background: white;
        color: #007BFF;
        border-color: white;
        font-weight: 600;
    }
</style>

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
