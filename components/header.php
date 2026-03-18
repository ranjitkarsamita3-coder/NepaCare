<?php include_once __DIR__ . '/../config/lang.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>NepaCare - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            margin: 0; 
            padding: 0; 
        }

        h1 { 
            font-size: 32px; 
            margin-bottom: 20px; 
            text-align:center; 
        }

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
    </style>
</head>
<body <?php echo defined('STICKY_FOOTER') ? 'class="sticky-footer"' : ''; ?>>

<?php include __DIR__ . "/navbar.php"; ?>
