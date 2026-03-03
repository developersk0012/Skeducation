<?php
require_once 'config.php';
session_start();
$username = $_SESSION['user_name'] ?? 'User';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome - SK Education</title>
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
        .welcome-box { 
            background: white; 
            padding: 50px; 
            border-radius: 20px; 
            text-align: center; 
            max-width:500px;
            width:100%;
        }
        .logo-image {
            width:80px;
            height:80px;
            border-radius:50%;
            object-fit:cover;
            border:3px solid #4361ee;
            margin-bottom:20px;
        }
        h1 { 
            color: #4361ee; 
            margin-bottom: 20px; 
        }
        p { 
            color: #666; 
            margin-bottom: 30px; 
            font-size:18px;
        }
        .btn { 
            display: inline-block; 
            background: #4361ee; 
            color: white; 
            padding: 15px 40px; 
            text-decoration: none; 
            border-radius: 10px; 
            font-size:18px;
            transition:0.3s;
        }
        .btn:hover {
            background: #3046bf;
            transform:translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="welcome-box">
        <img src="<?php echo SITE_LOGO; ?>" alt="SK Education" class="logo-image">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>Your account has been created successfully.</p>
        <a href="dashboard.php" class="btn">Go to Dashboard</a>
    </div>
</body>
</html>