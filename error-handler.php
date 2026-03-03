<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

$error_code = isset($_GET['code']) ? intval($_GET['code']) : 500;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Error - SK Education</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { 
            background: linear-gradient(135deg, #667eea, #764ba2); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding:20px;
        }
        .error-box { 
            background: white; 
            padding: 50px; 
            border-radius: 20px; 
            text-align: center; 
            max-width:500px;
            width:100%;
        }
        .logo-image {
            width:70px;
            height:70px;
            border-radius:50%;
            object-fit:cover;
            border:3px solid <?php echo $error_code == 404 ? '#e65100' : '#c62828'; ?>;
            margin-bottom:20px;
        }
        h1 { 
            color: <?php echo $error_code == 404 ? '#e65100' : '#c62828'; ?>; 
            margin-bottom: 15px; 
        }
        p { 
            color: #666; 
            margin-bottom: 30px; 
            font-size:16px;
        }
        .btn { 
            display: inline-block; 
            background: #4361ee; 
            color: white; 
            padding: 12px 30px; 
            text-decoration: none; 
            border-radius: 8px; 
            margin: 5px;
        }
        .btn:hover {
            background: #3046bf;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <img src="<?php echo defined('SITE_LOGO') ? SITE_LOGO : 'ss.png'; ?>" alt="SK Education" class="logo-image" onerror="this.style.display='none';">
        <h1><?php echo $error_code == 404 ? '404 - Page Not Found' : '500 - Server Error'; ?></h1>
        <p>Sorry, something went wrong. Please try again later.</p>
        <a href="index.php" class="btn">Go to Home</a>
        <a href="javascript:history.back()" class="btn" style="background:#666;">Go Back</a>
    </div>
</body>
</html>