<?php
// profile.php - User Profile Page
error_reporting(E_ALL);
ini_set('display_errors', 0);
session_start();

require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Get user details
$db = Database::getInstance();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $education = trim($_POST['education'] ?? '');
        $occupation = trim($_POST['occupation'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $website = trim($_POST['website'] ?? '');
        
        $update = $db->prepare("UPDATE users SET full_name = ?, phone = ?, bio = ?, education = ?, occupation = ?, location = ?, website = ?, updated_at = NOW() WHERE id = ?");
        $update->bind_param("sssssssi", $full_name, $phone, $bio, $education, $occupation, $location, $website, $user_id);
        
        if ($update->execute()) {
            $message = "✅ Profile updated successfully!";
            $message_type = 'success';
            
            // Refresh user data
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
        } else {
            $message = "❌ Failed to update profile!";
            $message_type = 'error';
        }
    }
    
    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        if (password_verify($current, $user['password'])) {
            if ($new === $confirm) {
                if (strlen($new) >= 6) {
                    $hashed = password_hash($new, PASSWORD_DEFAULT);
                    $update = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $update->bind_param("si", $hashed, $user_id);
                    
                    if ($update->execute()) {
                        $message = "✅ Password changed successfully!";
                        $message_type = 'success';
                    } else {
                        $message = "❌ Failed to change password!";
                        $message_type = 'error';
                    }
                } else {
                    $message = "❌ Password must be at least 6 characters!";
                    $message_type = 'error';
                }
            } else {
                $message = "❌ New passwords do not match!";
                $message_type = 'error';
            }
        } else {
            $message = "❌ Current password is incorrect!";
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - SK Education</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            background: white;
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #333;
            font-size: 24px;
        }
        
        .header h1 i {
            color: #667eea;
            margin-right: 10px;
        }
        
        .nav-links a {
            color: #666;
            text-decoration: none;
            margin-left: 20px;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .nav-links a:hover {
            background: #f0f0f0;
            color: #333;
        }
        
        .nav-links a.active {
            background: #667eea;
            color: white;
        }
        
        /* Main Grid */
        .profile-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 25px;
        }
        
        /* Sidebar */
        .sidebar {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            font-weight: bold;
        }
        
        .username {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .email {
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .joined {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .joined p {
            color: #555;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .joined i {
            color: #667eea;
            width: 20px;
        }
        
        /* Main Content */
        .main-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        
        .tab-btn {
            padding: 10px 20px;
            border: none;
            background: none;
            font-size: 16px;
            font-weight: 500;
            color: #666;
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 5px;
        }
        
        .tab-btn:hover {
            background: #f0f0f0;
        }
        
        .tab-btn.active {
            background: #667eea;
            color: white;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }
        
        label i {
            color: #667eea;
            width: 20px;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        input:focus, textarea:focus, select:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }
        
        textarea {
            height: 100px;
            resize: vertical;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.3);
        }
        
        .btn-small {
            padding: 10px 20px;
            font-size: 14px;
            width: auto;
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: slideDown 0.5s;
        }
        
        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b8daff;
            color: #004085;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .info-box i {
            margin-right: 8px;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        /* Activity List */
        .activity-list {
            list-style: none;
        }
        
        .activity-item {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
        }
        
        .activity-details {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 500;
            color: #333;
        }
        
        .activity-time {
            font-size: 12px;
            color: #999;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: white;
        }
        
        @media (max-width: 768px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-user-circle"></i> 
                My Profile
            </h1>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="profile.php" class="active">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
        
        <!-- Message -->
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Profile Grid -->
        <div class="profile-grid">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="avatar">
                    <?php echo strtoupper(substr($user['username'] ?? 'U', 0, 1)); ?>
                </div>
                
                <div class="username">
                    <?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?>
                </div>
                
                <div class="email">
                    <i class="fas fa-envelope"></i> 
                    <?php echo htmlspecialchars($user['email']); ?>
                </div>
                
                <div class="joined">
                    <p><i class="fas fa-calendar"></i> Joined: <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
                    <p><i class="fas fa-clock"></i> Last Login: <?php echo $user['last_login'] ? date('d M Y', strtotime($user['last_login'])) : 'Never'; ?></p>
                    <p><i class="fas fa-tag"></i> Role: <?php echo ucfirst($user['role'] ?? 'User'); ?></p>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="main-content">
                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value">0</div>
                        <div class="stat-label">Courses</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">0</div>
                        <div class="stat-label">Certificates</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">0</div>
                        <div class="stat-label">Hours</div>
                    </div>
                </div>
                
                <!-- Tabs -->
                <div class="tabs">
                    <button class="tab-btn active" onclick="showTab('edit')">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                    <button class="tab-btn" onclick="showTab('password')">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                    <button class="tab-btn" onclick="showTab('activity')">
                        <i class="fas fa-history"></i> Activity
                    </button>
                </div>
                
                <!-- Edit Profile Tab -->
                <div id="edit-tab" class="tab-content active">
                    <form method="POST">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Full Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" placeholder="Enter your full name">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Phone Number</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+91 98765 43210">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-graduation-cap"></i> Education</label>
                            <input type="text" name="education" value="<?php echo htmlspecialchars($user['education'] ?? ''); ?>" placeholder="e.g., B.Tech, MCA, 12th Pass">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-briefcase"></i> Occupation</label>
                            <input type="text" name="occupation" value="<?php echo htmlspecialchars($user['occupation'] ?? ''); ?>" placeholder="e.g., Student, Teacher, Developer">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> Location</label>
                            <input type="text" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" placeholder="e.g., Mumbai, India">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-globe"></i> Website</label>
                            <input type="url" name="website" value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>" placeholder="https://example.com">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-info-circle"></i> Bio</label>
                            <textarea name="bio" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>
                
                <!-- Change Password Tab -->
                <div id="password-tab" class="tab-content">
                    <form method="POST">
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-key"></i> New Password</label>
                            <input type="password" name="new_password" required>
                            <small style="color: #666;">Minimum 6 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-check-circle"></i> Confirm New Password</label>
                            <input type="password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn">
                            <i class="fas fa-sync"></i> Update Password
                        </button>
                    </form>
                    
                    <div class="info-box">
                        <i class="fas fa-shield-alt"></i> 
                        Password should be strong and unique. Never share your password with anyone.
                    </div>
                </div>
                
                <!-- Activity Tab -->
                <div id="activity-tab" class="tab-content">
                    <ul class="activity-list">
                        <li class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="activity-details">
                                <div class="activity-title">Account created</div>
                                <div class="activity-time"><?php echo date('d M Y, h:i A', strtotime($user['created_at'])); ?></div>
                            </div>
                        </li>
                        
                        <?php
                        // Get recent activity
                        $activity = $db->prepare("SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
                        $activity->bind_param("i", $user_id);
                        $activity->execute();
                        $activities = $activity->get_result();
                        
                        if ($activities->num_rows > 0):
                            while ($log = $activities->fetch_assoc()):
                        ?>
                        <li class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-<?php echo $log['action'] == 'login' ? 'sign-in-alt' : 'circle'; ?>"></i>
                            </div>
                            <div class="activity-details">
                                <div class="activity-title"><?php echo htmlspecialchars($log['description']); ?></div>
                                <div class="activity-time"><?php echo date('d M Y, h:i A', strtotime($log['created_at'])); ?></div>
                            </div>
                        </li>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <li class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="activity-details">
                                <div class="activity-title">No recent activity</div>
                                <div class="activity-time">Start using the platform</div>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>© 2026 SK Education. All rights reserved.</p>
        </div>
    </div>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
        function showTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tab + '-tab').classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }
        
        // Password match validation
        document.querySelector('input[name="confirm_password"]')?.addEventListener('input', function() {
            const newPass = document.querySelector('input[name="new_password"]').value;
            const confirmPass = this.value;
            
            if (newPass && confirmPass) {
                if (newPass === confirmPass) {
                    this.style.borderColor = '#28a745';
                } else {
                    this.style.borderColor = '#dc3545';
                }
            }
        });
    </script>
</body>
</html>