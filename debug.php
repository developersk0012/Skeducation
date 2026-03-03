<?php
require_once 'config.php';

echo "<h1>SK Education - Debug Info</h1>";

echo "<h2>1. Firebase Configuration:</h2>";
echo "Database URL: " . FIREBASE_DATABASE_URL . "<br>";
echo "Project ID: " . FIREBASE_PROJECT_ID . "<br><br>";

echo "<h2>2. Connection Test:</h2>";
$connected = testFirebaseConnection();
if ($connected) {
    echo "<span style='color:green'>✓ Connected Successfully</span><br>";
} else {
    echo "<span style='color:red'>✗ Connection Failed</span><br>";
}

echo "<h2>3. Categories:</h2>";
echo "<pre>";
print_r($CATEGORIES);
echo "</pre>";

echo "<h2>4. Subcategories (Science):</h2>";
echo "<pre>";
print_r($SUBCATEGORIES['science']);
echo "</pre>";

echo "<h2>5. Server Time:</h2>";
echo date('Y-m-d H:i:s') . "<br>";
?>