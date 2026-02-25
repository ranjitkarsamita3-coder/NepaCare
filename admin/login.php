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
            <input type="email" name="email" placeholder="admin@nepacare.com" required>
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
        </div>
        
        <button type="submit" name="login">Login</button>
    </form>
    
    <div class="back-link">
        <a href="../index.php">← Back to Home</a>
    </div>
</div>

</body>
</html>
