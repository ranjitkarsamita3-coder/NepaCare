<?php
session_start();
include '../config/db.php';

if(isset($_SESSION['admin_id'])){
    header("Location: index.php");
    exit;
}

$message = "";

if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if($email === 'admin@nepacare.com' && $password === 'admin123'){
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_name'] = 'Administrator';
        header("Location: index.php");
        exit;
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - NepaCare</title>
    <link rel="stylesheet" href="../assets/css/loginstyle.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; background-color: #f5f5f5; }
        .login-container { 
            max-width: 400px; 
            margin: 100px auto; 
            background: white; 
            padding: 40px; 
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .login-container h1 { 
            text-align: center; 
            color: #1e3a8a;
            margin-bottom: 30px;
        }
        .form-group { margin-bottom: 20px; }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600;
            color: #333;
        }
        input { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 15px;
        }
        input:focus { 
            outline: none; 
            border-color: #1e3a8a;
            box-shadow: 0 0 5px rgba(30, 58, 138, 0.3);
        }
        button { 
            width: 100%; 
            padding: 12px; 
            background: #1e3a8a; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover { background: #1e3a8a; }
        .message { 
            padding: 10px; 
            margin-bottom: 20px; 
            background: #f8d7da; 
            color: #721c24; 
            border-radius: 5px;
            text-align: center;
        }
        .back-link { 
            text-align: center; 
            margin-top: 20px;
        }
        .back-link a { 
            color: #1e3a8a; 
            text-decoration: none;
        }
        .back-link a:hover { text-decoration: underline; }
        .remember-box { position: relative; display: inline-block; padding-left: 28px; cursor: pointer; user-select: none; color: #333; }
        .remember-box input { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
        .remember-box .checkmark { position: absolute; left: 0; top: 0; height: 18px; width: 18px; background: #fff; border: 1px solid #ccc; border-radius: 4px; }
        .remember-box input:checked ~ .checkmark { background: #1e3a8a; border-color: #1e3a8a; }
        .remember-box .checkmark:after { content: ""; position: absolute; display: none; left: 5px; top: 1px; width: 5px; height: 10px; border: solid #fff; border-width: 0 2px 2px 0; transform: rotate(45deg); }
        .remember-box input:checked ~ .checkmark:after { display: block; }
        .remember-container { display: block; margin-top: 8px; margin-bottom: 16px; }
    </style>
</head>
<body>

<div class="login-container">
    <h1>Admin Login</h1>
    
    <?php if($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" required>
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
        </div>
        <div class="form-group remember-container">
            <label class="remember-box">Remember me
                <input type="checkbox" name="remember" value="1">
                <span class="checkmark"></span>
            </label>
        </div>
        
        <button type="submit" name="login">Login</button>
    </form>
    
    <div class="back-link">
        <a href="../index.php">← Back to Home</a>
    </div>
</div>

</body>
</html>
