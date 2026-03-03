<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] === 'saten@12345') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin_category.php?cat=science');
        exit();
    } else {
        $error = 'Invalid password';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SK Education</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial; }
        body { background:#f5f5f5; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .login-box { 
            background:white; 
            width:90%; 
            max-width:350px; 
            padding:30px; 
            border-radius:20px; 
            box-shadow:0 10px 30px rgba(0,0,0,0.1);
            text-align:center;
        }
        .logo-image {
            width:70px;
            height:70px;
            border-radius:50%;
            object-fit:cover;
            border:3px solid #4361ee;
            margin-bottom:15px;
        }
        h2 { 
            color:#4361ee; 
            margin-bottom:20px; 
        }
        .error { 
            background:#ffebee; 
            color:#c62828; 
            padding:10px; 
            border-radius:8px; 
            margin-bottom:20px; 
        }
        input { 
            width:100%; 
            padding:12px; 
            margin:10px 0; 
            border:2px solid #e0e0e0; 
            border-radius:8px; 
        }
        button { 
            width:100%; 
            padding:12px; 
            background:#4361ee; 
            color:white; 
            border:none; 
            border-radius:8px; 
            cursor:pointer; 
        }
        button:hover { 
            background:#3046bf; 
        }
    </style>
</head>
<body>
    <div class="login-box">
        <img src="<?php echo SITE_LOGO; ?>" alt="SK Education" class="logo-image">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Enter admin password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>