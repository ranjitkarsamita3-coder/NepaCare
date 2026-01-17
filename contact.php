<!DOCTYPE html>
<html>
<head>
    <title>Contact Us - NepaCare</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            margin: 0; 
            padding: 0; 
        }

        h1 { font-size: 32px; margin-bottom: 20px; text-align:center; }
        a.button { 
            display:inline-block; 
            padding:10px 20px; 
            font-size:20px; 
            background:#007BFF; 
            color:white; 
            border-radius:8px; 
            margin-top:20px; 
            text-decoration:none;
        }

        .section { 
            border:1px solid #ccc; 
            padding:20px; 
            margin:20px auto; 
            max-width:700px;
            border-radius:8px; 
            background:#f9f9f9; 
        }

        .section h2 { font-size:24px; margin-bottom:10px; }
        .section p { font-size:18px; line-height:1.5; }

        /* Top Navigation Bar */
        .top-nav {
            background:#007BFF;
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
    </style>
</head>
<body>
<!-- Navigation -->
<div class="top-nav">
    <!-- Logo -->
    <a href="index.php" class="logo">
        <img src="assets/images/logo.png" alt="NepaCare Logo">
        <span>NepaCare</span>
    </a>

    <!-- Links -->
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact Us</a>
        <a href="login.php">Login / Signup</a>
    </div>
</div>

<h1>Contact NepaCare</h1>

<div class="section">
    <p>
        For any inquiries, feedback, or support, please contact us: <br><br>
        <strong>Email:</strong> support@nepacare.com <br>
        <strong>Phone:</strong> +977-9800000000 <br>
        <strong>Address:</strong> Kathmandu, Nepal
    </p>
</div>

</body>
</html>
