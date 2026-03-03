<?php
require_once 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Firebase Database Setup</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; text-align:center; }
        .logo-image { width:80px; height:80px; border-radius:50%; object-fit:cover; border:3px solid #4361ee; margin-bottom:20px; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; text-align:left; }
        .btn { display: inline-block; background: #4361ee; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class='container'>
        <img src='" . SITE_LOGO . "' alt='SK Education' class='logo-image' onerror='this.style.display=\"none\";'>
        <h1>🔥 Firebase Database Setup</h1>";

// Test connection
if (!testFirebaseConnection()) {
    echo "<p class='error'>❌ Cannot connect to Firebase. Please check:</p>";
    echo "<ul style='text-align:left;'>";
    echo "<li>Database URL: " . FIREBASE_DATABASE_URL . "</li>";
    echo "<li>Firebase Rules are set to allow read/write</li>";
    echo "</ul>";
    echo "<p>Temporary rules for setup:</p>";
    echo "<pre>{
  \"rules\": {
    \".read\": true,
    \".write\": true
  }
}</pre>";
    echo "<a href='' class='btn'>Retry</a>";
    exit();
}

echo "<p class='success'>✅ Connected to Firebase successfully!</p>";

// Check if already setup
$existing = firebaseRequest('GET', 'categories');
if ($existing) {
    echo "<p class='error'>⚠️ Database already has data. Clearing old data...</p>";
    firebaseRequest('DELETE', 'categories');
    firebaseRequest('DELETE', 'users');
}

// Create categories structure
echo "<h3>Creating categories...</h3>";
foreach ($CATEGORIES as $catKey => $catName) {
    foreach ($SUBCATEGORIES[$catKey] as $subKey => $subName) {
        $path = "categories/{$catKey}/{$subKey}";
        firebaseRequest('PUT', $path . '/info', [
            'name' => $subName,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        echo "✓ Created: {$catKey} → {$subName}<br>";
    }
}

// Create test user
echo "<h3>Creating test user...</h3>";
$testUser = [
    'user_id' => 'testuser',
    'email' => 'test@example.com',
    'password' => password_hash('test123', PASSWORD_DEFAULT),
    'name' => 'Test User',
    'created_at' => date('Y-m-d H:i:s')
];
$result = firebaseRequest('POST', 'users', $testUser);
if ($result) {
    echo "✓ Test user created<br>";
    echo "User ID: testuser<br>";
    echo "Password: test123<br>";
}

// Add sample items
echo "<h3>Adding sample items...</h3>";

$samples = [
    ['science', 'physics', 'pdf', 'Physics Chapter 1 - Motion', 'Complete notes on motion', 'https://example.com/physics-motion.pdf'],
    ['science', 'chemistry', 'pdf', 'Chemistry - Periodic Table', 'Complete periodic table guide', 'https://example.com/periodic-table.pdf'],
    ['science', 'biology', 'pdf', 'Biology - Cell Structure', 'Detailed notes on cells', 'https://example.com/cell-biology.pdf']
];

foreach ($samples as $sample) {
    $itemData = [
        'type' => $sample[2],
        'title' => $sample[3],
        'description' => $sample[4],
        'fileUrl' => $sample[5],
        'created_at' => date('Y-m-d H:i:s')
    ];
    $path = "categories/{$sample[0]}/{$sample[1]}/items";
    firebaseRequest('POST', $path, $itemData);
    echo "✓ Added: {$sample[3]}<br>";
}

echo "<h3 class='success'>✅ Setup Complete!</h3>";
echo "<a href='index.php' class='btn'>Go to Login Page</a>";
echo "</div></body></html>";
?>