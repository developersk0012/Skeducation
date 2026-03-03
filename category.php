<?php
require_once 'config.php';
requireLogin();

$cat = $_GET['cat'] ?? '';
if (!isset($CATEGORIES[$cat])) {
    header('Location: dashboard.php');
    exit();
}

$categoryName = $CATEGORIES[$cat];
$subcats = $SUBCATEGORIES[$cat];
$subStats = getSubcategoryStats($cat);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $categoryName; ?> - SK Education</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial; }
        body { background:#f5f5f5; }
        .header { 
            background:white; 
            padding:15px 30px; 
            box-shadow:0 2px 10px rgba(0,0,0,0.1); 
            display:flex; 
            align-items:center; 
            gap:20px; 
        }
        .header-left {
            display:flex;
            align-items:center;
            gap:15px;
        }
        .logo-image {
            width:40px;
            height:40px;
            border-radius:50%;
            object-fit:cover;
            border:2px solid #4361ee;
        }
        .back-btn { 
            background:#f0f0f0; 
            padding:8px 15px; 
            border-radius:20px; 
            text-decoration:none; 
            color:#333; 
        }
        .header h1 { 
            font-size:20px; 
            color:#333; 
            margin:0;
        }
        .container { max-width:600px; margin:0 auto; padding:20px; }
        h2 { margin-bottom:20px; color:#333; }
        .sub-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:15px; }
        .sub-card { 
            background:white; 
            padding:25px; 
            border-radius:15px; 
            text-align:center; 
            text-decoration:none; 
            color:#333; 
            display:block; 
            transition:0.3s; 
        }
        .sub-card:hover { 
            transform:translateY(-3px); 
            box-shadow:0 5px 20px rgba(67,97,238,0.2); 
        }
        .sub-icon { font-size:35px; margin-bottom:10px; }
        .sub-name { font-size:16px; font-weight:bold; margin-bottom:5px; }
        .sub-count { font-size:12px; color:#888; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <img src="<?php echo SITE_LOGO; ?>" alt="SK Education" class="logo-image">
            <a href="dashboard.php" class="back-btn">← Back</a>
        </div>
        <h1><?php echo $categoryName; ?></h1>
    </div>
    
    <div class="container">
        <h2>📂 Subcategories</h2>
        <div class="sub-grid">
            <?php foreach($subcats as $key=>$name): 
                $count = $subStats[$key] ?? 0;
                $icon = getSubcategoryIcon($key);
            ?>
            <a href="subcategory.php?cat=<?php echo $cat; ?>&sub=<?php echo $key; ?>" class="sub-card">
                <div class="sub-icon"><?php echo $icon; ?></div>
                <div class="sub-name"><?php echo $name; ?></div>
                <div class="sub-count"><?php echo $count; ?> items</div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>