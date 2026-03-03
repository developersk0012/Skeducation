<?php
require_once 'config.php';
redirectIfLoggedIn();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $userId = trim($_POST['user_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    if (empty($name) || empty($email) || empty($userId) || empty($password)) {
        $error = 'Please fill all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } elseif (strlen($userId) < 4) {
        $error = 'User ID must be at least 4 characters';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match';
    } elseif (userExists($userId, $email)) {
        $error = 'User ID or Email already exists';
    } else {
        $userData = [
            'name' => $name,
            'email' => $email,
            'user_id' => $userId,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $result = createUser($userData);
        
        if ($result && isset($result['name'])) {
            $_SESSION['user_id'] = $result['name'];
            $_SESSION['user_name'] = $name;
            $_SESSION['user_login_id'] = $userId;
            
            sendToTelegram('🎉 *NEW USER REGISTERED*', [
                'name' => $name,
                'user_id' => $userId,
                'email' => $email,
                'action' => 'Account created'
            ]);
            
            header('Location: welcome.php');
            exit();
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - SK Education</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial; }
        body { background:#f5f5f5; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:15px; }
        .signup-box { background:white; width:100%; max-width:400px; padding:30px; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,0.1); text-align:center; }
        .logo-container { margin-bottom:20px; }
        .logo-image { width:70px; height:70px; border-radius:50%; object-fit:cover; border:3px solid #4361ee; margin-bottom:10px; }
        h2 { color:#4361ee; margin-bottom:10px; }
        .subtitle { color:#666; margin-bottom:25px; font-size:14px; }
        .error { background:#ffebee; color:#c62828; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; }
        .form-group { margin-bottom:20px; text-align:left; }
        label { display:block; margin-bottom:5px; color:#555; font-weight:600; font-size:14px; }
        input { width:100%; padding:12px 15px; border:2px solid #e0e0e0; border-radius:10px; font-size:15px; }
        input:focus { outline:none; border-color:#4361ee; }
        button { width:100%; padding:14px; background:#4361ee; color:white; border:none; border-radius:10px; font-size:16px; font-weight:600; cursor:pointer; }
        button:hover { background:#3046bf; }
        .link { text-align:center; margin-top:20px; color:#666; font-size:14px; }
        .link a { color:#4361ee; text-decoration:none; font-weight:600; }
    </style>
</head>
<body>
    <div class="signup-box">
        <div class="logo-container">
            <img src="<?php echo SITE_LOGO; ?>" alt="SK Education" class="logo-image" onerror="this.style.display='none';">
        </div>
        <h2>Create Account</h2>
        <div class="subtitle">Join us today - It's free!</div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Enter your full name" 
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email" 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>User ID</label>
                <input type="text" name="user_id" placeholder="Choose a user ID (min 4 chars)" 
                       value="<?php echo htmlspecialchars($_POST['user_id'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Min 6 characters" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Re-enter password" required>
            </div>
            <button type="submit">Sign Up</button>
        </form>
        
        <div class="link">
            Already have an account? <a href="index.php">Login</a>
        </div>
    </div>
</body>
</html>