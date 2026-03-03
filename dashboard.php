<?php
require_once 'config.php';
requireLogin();

$userName = $_SESSION['user_name'];
$stats = getCategoryStats();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SK Education</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial; }
        body { background:#f5f5f5; }
        .header { 
            background:white; 
            padding:15px 30px; 
            box-shadow:0 2px 10px rgba(0,0,0,0.1); 
            display:flex; 
            justify-content:space-between; 
            align-items:center;
        }
        .logo-container {
            display:flex;
            align-items:center;
            gap:15px;
        }
        .logo-image {
            width:50px;
            height:50px;
            border-radius:50%;
            object-fit:cover;
            border:2px solid #4361ee;
        }
        .logo-text {
            font-size:22px;
            font-weight:bold;
            color:#4361ee;
        }
        .user-info { 
            display:flex; 
            align-items:center; 
            gap:20px; 
        }
        .welcome-text {
            font-size:16px;
            color:#333;
        }
        .welcome-text span {
            color:#4361ee;
            font-weight:bold;
        }
        .logout-btn { 
            background:#f0f0f0; 
            padding:8px 20px; 
            border-radius:20px; 
            text-decoration:none; 
            color:#333;
            transition:0.3s;
        }
        .logout-btn:hover {
            background:#4361ee;
            color:white;
        }
        .container { max-width:600px; margin:0 auto; padding:20px; }
        h2 { margin-bottom:20px; color:#333; }
        .categories-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:15px; }
        .category-card { 
            background:white; 
            border-radius:15px; 
            padding:25px; 
            text-align:center; 
            text-decoration:none; 
            color:#333; 
            box-shadow:0 2px 10px rgba(0,0,0,0.05); 
            display:block; 
            transition:0.3s; 
        }
        .category-card:hover { 
            transform:translateY(-3px); 
            box-shadow:0 5px 20px rgba(67,97,238,0.2); 
        }
        .category-icon { font-size:40px; margin-bottom:10px; }
        .category-name { font-size:16px; font-weight:bold; margin-bottom:5px; }
        .category-count { font-size:12px; color:#888; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <img src="<?php echo SITE_LOGO; ?>" alt="SK Education" class="logo-image">
            <span class="logo-text">SK Education</span>
        </div>
        <div class="user-info">
            <span class="welcome-text">Welcome, <span><?php echo htmlspecialchars($userName); ?></span></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <h2>📚 Categories</h2>
        <div class="categories-grid">
            <?php foreach($CATEGORIES as $key => $name): ?>
            <a href="category.php?cat=<?php echo $key; ?>" class="category-card">
                <div class="category-icon"><?php echo getCategoryIcon($key); ?></div>
                <div class="category-name"><?php echo $name; ?></div>
                <div class="category-count"><?php echo $stats[$key] ?? 0; ?> items</div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>