<?php
require_once 'config.php';
redirectIfLoggedIn();

$error = '';
$connected = testFirebaseConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = trim($_POST['user_id'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($userId) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $user = getUser($userId);
        
        if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['key'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_login_id'] = $user['user_id'];
            
            sendToTelegram('🔐 *USER LOGIN*', [
                'name' => $user['name'],
                'user_id' => $user['user_id'],
                'action' => 'Logged in'
            ]);
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid User ID or Password';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SK Education</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial; }
        body { background:#f5f5f5; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:15px; }
        .login-box { background:white; width:100%; max-width:350px; padding:30px; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,0.1); text-align:center; }
        .logo-container { margin-bottom:20px; }
        .logo-image { width:80px; height:80px; border-radius:50%; object-fit:cover; border:3px solid #4361ee; margin-bottom:10px; }
        h2 { color:#4361ee; margin-bottom:30px; }
        .error { background:#ffebee; color:#c62828; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; }
        .warning { background:#fff3e0; color:#e65100; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; }
        .form-group { margin-bottom:20px; }
        input { width:100%; padding:12px 15px; border:2px solid #e0e0e0; border-radius:10px; font-size:15px; }
        input:focus { outline:none; border-color:#4361ee; }
        button { width:100%; padding:14px; background:#4361ee; color:white; border:none; border-radius:10px; font-size:16px; font-weight:600; cursor:pointer; }
        button:hover { background:#3046bf; }
        .link { text-align:center; margin-top:20px; color:#666; font-size:14px; }
        .link a { color:#4361ee; text-decoration:none; font-weight:600; }
        .loader { border:3px solid #f3f3f3; border-top:3px solid #4361ee; border-radius:50%; width:20px; height:20px; animation:spin 1s linear infinite; display:inline-block; margin-right:8px; }
        @keyframes spin { 0% { transform:rotate(0deg); } 100% { transform:rotate(360deg); } }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo-container">
            <img src="<?php echo SITE_LOGO; ?>" alt="SK Education" class="logo-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <h2 style="display:none;">SK Education</h2>
        </div>
        <h2>Login to Your Account</h2>
        
        <?php if (!$connected): ?>
        <div class="warning">
            <span class="loader"></span>
            Connecting to database...
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <input type="text" name="user_id" placeholder="User ID or Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="link">New user? <a href="signup.php">Create account</a></div>
    </div>
    
    <script>
        <?php if (!$connected): ?>
        setTimeout(function() { location.reload(); }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>