<?php
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit();
}

$cat = $_GET['cat'] ?? 'science';
if (!isset($CATEGORIES[$cat])) {
    $cat = 'science';
}

$message = '';

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $sub = $_POST['subcategory'] ?? '';
    $type = $_POST['type'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $fileUrl = trim($_POST['file_url'] ?? '');
    
    if ($sub && $type && $title && $fileUrl) {
        $result = addItem($cat, $sub, $type, $title, $desc, $fileUrl);
        if ($result) {
            $message = 'Item added successfully!';
        } else {
            $message = 'Failed to add item';
        }
    } else {
        $message = 'Please fill all fields';
    }
}

// Handle Delete
if (isset($_GET['delete']) && isset($_GET['sub']) && isset($_GET['item'])) {
    deleteItem($cat, $_GET['sub'], $_GET['item']);
    $message = 'Item deleted!';
    header("Location: admin_category.php?cat=$cat");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo $CATEGORIES[$cat]; ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial; }
        body { background:#f5f5f5; padding:20px; }
        .container { max-width:600px; margin:0 auto; }
        .header { 
            background:white; 
            padding:20px; 
            border-radius:10px; 
            margin-bottom:20px; 
            display:flex; 
            justify-content:space-between; 
            align-items:center;
        }
        .header-left {
            display:flex;
            align-items:center;
            gap:15px;
        }
        .header-logo {
            width:40px;
            height:40px;
            border-radius:50%;
            object-fit:cover;
            border:2px solid #4361ee;
        }
        .header h2 { 
            color:#4361ee; 
            margin:0;
        }
        .nav { 
            display:flex; 
            gap:5px; 
            margin-bottom:20px; 
            flex-wrap:wrap; 
        }
        .nav a { 
            background:white; 
            padding:10px 15px; 
            border-radius:8px; 
            text-decoration:none; 
            color:#333; 
            flex:1;
            text-align:center;
        }
        .nav a.active { 
            background:#4361ee; 
            color:white; 
        }
        .message { 
            background:#e8f5e9; 
            color:#2e7d32; 
            padding:10px; 
            border-radius:8px; 
            margin-bottom:20px; 
            text-align:center; 
        }
        .section { 
            background:white; 
            padding:20px; 
            border-radius:10px; 
            margin-bottom:20px; 
        }
        .form-group { margin-bottom:15px; }
        label { 
            display:block; 
            margin-bottom:5px; 
            color:#555; 
            font-weight:bold; 
        }
        select, input, textarea { 
            width:100%; 
            padding:12px; 
            border:2px solid #e0e0e0; 
            border-radius:8px; 
        }
        .type-selector { 
            display:flex; 
            gap:10px; 
            margin-bottom:20px; 
        }
        .type-option { 
            flex:1; 
            text-align:center; 
            padding:12px; 
            border:2px solid #e0e0e0; 
            border-radius:8px; 
            cursor:pointer; 
        }
        .type-option.selected { 
            background:#4361ee; 
            color:white; 
            border-color:#4361ee; 
        }
        .btn { 
            width:100%; 
            padding:14px; 
            background:#4361ee; 
            color:white; 
            border:none; 
            border-radius:8px; 
            cursor:pointer; 
        }
        .item { 
            background:#f8f9fa; 
            padding:15px; 
            border-radius:8px; 
            margin-bottom:10px; 
            display:flex; 
            justify-content:space-between; 
            align-items:center; 
        }
        .delete { 
            background:#ffebee; 
            color:#c62828; 
            padding:5px 10px; 
            border-radius:5px; 
            text-decoration:none; 
        }
        .sub-title { 
            font-size:16px; 
            font-weight:bold; 
            margin:15px 0 10px; 
            padding-bottom:5px; 
            border-bottom:2px solid #e0e0e0; 
        }
    </style>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <img src="<?php echo SITE_LOGO; ?>" alt="SK Education" class="header-logo">
                <h2><?php echo $CATEGORIES[$cat]; ?></h2>
            </div>
            <a href="logout.php" style="color:#4361ee;">Logout</a>
        </div>
        
        <div class="nav">
            <?php foreach($CATEGORIES as $key=>$name): ?>
            <a href="?cat=<?php echo $key; ?>" class="<?php echo $cat===$key?'active':''; ?>"><?php echo $name; ?></a>
            <?php endforeach; ?>
        </div>
        
        <?php if($message): ?>
        <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <!-- Add Form -->
        <div class="section">
            <h3>Add New Item</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Subcategory *</label>
                    <select name="subcategory" required>
                        <option value="">-- Select Subcategory --</option>
                        <?php foreach($SUBCATEGORIES[$cat] as $key=>$name): ?>
                        <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="type-selector">
                    <div class="type-option selected" onclick="selectType('pdf')" id="pdfOpt">📄 PDF</div>
                    <div class="type-option" onclick="selectType('video')" id="videoOpt">🎥 Video</div>
                </div>
                <input type="hidden" name="type" id="typeField" value="pdf">
                
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label id="urlLabel">PDF URL *</label>
                    <input type="url" name="file_url" id="urlField" placeholder="https://..." required>
                </div>
                
                <button type="submit" name="add" class="btn">Add Item</button>
            </form>
        </div>
        
        <!-- Items List -->
        <div class="section">
            <h3>Current Items</h3>
            <div id="itemsList">Loading...</div>
        </div>
    </div>

    <script>
        function selectType(type) {
            document.getElementById('pdfOpt').classList.remove('selected');
            document.getElementById('videoOpt').classList.remove('selected');
            document.getElementById(type+'Opt').classList.add('selected');
            document.getElementById('typeField').value = type;
            
            if(type === 'pdf') {
                document.getElementById('urlLabel').textContent = 'PDF URL *';
                document.getElementById('urlField').placeholder = 'https://example.com/file.pdf';
            } else {
                document.getElementById('urlLabel').textContent = 'YouTube URL *';
                document.getElementById('urlField').placeholder = 'https://youtube.com/watch?v=...';
            }
        }

        const firebaseConfig = {
            apiKey: "<?php echo FIREBASE_API_KEY; ?>",
            authDomain: "<?php echo FIREBASE_AUTH_DOMAIN; ?>",
            databaseURL: "<?php echo FIREBASE_DATABASE_URL; ?>",
            projectId: "<?php echo FIREBASE_PROJECT_ID; ?>",
            storageBucket: "<?php echo FIREBASE_STORAGE_BUCKET; ?>",
            messagingSenderId: "<?php echo FIREBASE_MESSAGING_SENDER_ID; ?>",
            appId: "<?php echo FIREBASE_APP_ID; ?>"
        };
        firebase.initializeApp(firebaseConfig);
        const db = firebase.database();
        const cat = '<?php echo $cat; ?>';

        db.ref('categories/'+cat).on('value', (snap) => {
            const data = snap.val();
            let html = '';
            
            if(data) {
                <?php foreach($SUBCATEGORIES[$cat] as $subKey=>$subName): ?>
                if(data['<?php echo $subKey; ?>'] && data['<?php echo $subKey; ?>'].items) {
                    const items = data['<?php echo $subKey; ?>'].items;
                    if(Object.keys(items).length > 0) {
                        html += '<div class="sub-title">📁 <?php echo $subName; ?></div>';
                        Object.keys(items).forEach(key => {
                            html += `
                                <div class="item">
                                    <div>
                                        <strong>${items[key].title}</strong><br>
                                        <small>${items[key].type}</small>
                                    </div>
                                    <a href="?cat=${cat}&sub=<?php echo $subKey; ?>&item=${key}&delete=1" class="delete" onclick="return confirm('Delete?')">Delete</a>
                                </div>
                            `;
                        });
                    }
                }
                <?php endforeach; ?>
            }
            
            document.getElementById('itemsList').innerHTML = html || '<p>No items found</p>';
        });
    </script>
</body>
</html>