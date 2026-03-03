<?php
require_once 'config.php';
requireLogin();

$cat = $_GET['cat'] ?? '';
$sub = $_GET['sub'] ?? '';

if (!isset($CATEGORIES[$cat]) || !isset($SUBCATEGORIES[$cat][$sub])) {
    header('Location: dashboard.php');
    exit();
}

$categoryName = $CATEGORIES[$cat];
$subcategoryName = $SUBCATEGORIES[$cat][$sub];
$pdfItems = getItems($cat, $sub, 'pdf');
$videoItems = getItems($cat, $sub, 'video');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $subcategoryName; ?> - SK Education</title>
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
            font-size:18px; 
            color:#333; 
            margin:0;
        }
        .header h1 small { 
            display:block; 
            font-size:13px; 
            color:#666; 
            font-weight:normal; 
        }
        .container { max-width:600px; margin:0 auto; padding:20px; }
        .tabs { 
            display:flex; 
            gap:10px; 
            margin-bottom:20px; 
            background:white; 
            padding:5px; 
            border-radius:30px; 
        }
        .tab { 
            flex:1; 
            text-align:center; 
            padding:12px; 
            border-radius:25px; 
            cursor:pointer; 
            transition:0.3s; 
        }
        .tab.active { 
            background:#4361ee; 
            color:white; 
        }
        .item { 
            background:white; 
            padding:15px; 
            border-radius:10px; 
            margin-bottom:10px; 
            display:flex; 
            gap:15px; 
            box-shadow:0 2px 10px rgba(0,0,0,0.05); 
        }
        .item-icon { 
            width:50px; 
            height:50px; 
            background:#f0f0f0; 
            border-radius:10px; 
            display:flex; 
            align-items:center; 
            justify-content:center; 
            font-size:24px; 
        }
        .item-info { flex:1; }
        .item-title { font-weight:bold; margin-bottom:5px; }
        .item-desc { color:#666; font-size:13px; margin-bottom:8px; }
        .item-btn { 
            display:inline-block; 
            padding:8px 20px; 
            border-radius:20px; 
            text-decoration:none; 
            color:white; 
            font-size:13px; 
        }
        .pdf-btn { background:#4361ee; }
        .video-btn { background:#e74c3c; }
        .empty { 
            text-align:center; 
            padding:50px; 
            color:#999; 
            background:white; 
            border-radius:15px; 
        }
        .hidden { display:none; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <img src="<?php echo SITE_LOGO; ?>" alt="SK Education" class="logo-image">
            <a href="category.php?cat=<?php echo $cat; ?>" class="back-btn">← Back</a>
        </div>
        <h1>
            <?php echo $subcategoryName; ?>
            <small><?php echo $categoryName; ?></small>
        </h1>
    </div>
    
    <div class="container">
        <div class="tabs">
            <div class="tab active" onclick="showTab('pdf')">📄 PDFs</div>
            <div class="tab" onclick="showTab('video')">🎥 Videos</div>
        </div>
        
        <div id="pdfGrid">
            <?php if (!empty($pdfItems)): ?>
                <?php foreach ($pdfItems as $item): ?>
                <div class="item">
                    <div class="item-icon">📄</div>
                    <div class="item-info">
                        <div class="item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                        <div class="item-desc"><?php echo htmlspecialchars($item['description'] ?? ''); ?></div>
                        <a href="<?php echo htmlspecialchars($item['fileUrl']); ?>" target="_blank" class="item-btn pdf-btn">📥 Download PDF</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty">📄 No PDFs available</div>
            <?php endif; ?>
        </div>
        
        <div id="videoGrid" class="hidden">
            <?php if (!empty($videoItems)): ?>
                <?php foreach ($videoItems as $item): ?>
                <div class="item">
                    <div class="item-icon">🎥</div>
                    <div class="item-info">
                        <div class="item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                        <div class="item-desc"><?php echo htmlspecialchars($item['description'] ?? ''); ?></div>
                        <a href="<?php echo htmlspecialchars($item['fileUrl']); ?>" target="_blank" class="item-btn video-btn">▶️ Watch Video</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty">🎥 No videos available</div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function showTab(type) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            document.getElementById('pdfGrid').classList.toggle('hidden', type !== 'pdf');
            document.getElementById('videoGrid').classList.toggle('hidden', type !== 'video');
        }
    </script>
</body>
</html>