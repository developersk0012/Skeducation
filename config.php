<?php
session_start();

// ============================================
// FIREBASE CONFIGURATION
// ============================================
define('FIREBASE_API_KEY', 'AIzaSyAEA_jznrLsOcIJ5jR0qmBQwigStJ4RiAw');
define('FIREBASE_AUTH_DOMAIN', 'my-last-education.firebaseapp.com');
define('FIREBASE_DATABASE_URL', 'https://my-last-education-default-rtdb.firebaseio.com');
define('FIREBASE_PROJECT_ID', 'my-last-education');
define('FIREBASE_STORAGE_BUCKET', 'my-last-education.firebasestorage.app');
define('FIREBASE_MESSAGING_SENDER_ID', '916561394351');
define('FIREBASE_APP_ID', '1:916561394351:android:06445fc6d1de531e99f25a');

// ============================================
// SITE CONFIGURATION
// ============================================
define('SITE_NAME', 'SK Education');
define('SITE_URL', 'https://classes.42web.io');
define('SITE_LOGO', 'ss.png'); // Logo file name

// ============================================
// CATEGORIES
// ============================================
$CATEGORIES = [
    'science' => 'Science',
    'math' => 'Mathematics',
    'hindi' => 'Hindi',
    'sanskrit' => 'Sanskrit',
    'sst' => 'Social Studies'
];

// ============================================
// SUBCATEGORIES
// ============================================
$SUBCATEGORIES = [
    'science' => [
        'physics' => 'Physics',
        'chemistry' => 'Chemistry', 
        'biology' => 'Biology'
    ],
    'math' => [
        'objective' => 'Objective',
        'subjective' => 'Subjective', 
        'notes' => 'Notes'
    ],
    'hindi' => [
        'grammar' => 'Grammar',
        'book_notes' => 'Book Notes',
        'imp_question' => 'Important Questions'
    ],
    'sanskrit' => [
        'grammar' => 'Grammar',
        'book_notes' => 'Book Notes',
        'imp_question' => 'Important Questions'
    ],
    'sst' => [
        'history' => 'History',
        'geography' => 'Geography',
        'civics' => 'Civics',
        'economics' => 'Economics'
    ]
];

// ============================================
// ICONS
// ============================================
$CATEGORY_ICONS = [
    'science' => '🔬',
    'math' => '📐',
    'hindi' => '📝',
    'sanskrit' => '🕉️',
    'sst' => '🌍'
];

$SUBCATEGORY_ICONS = [
    'physics' => '⚡',
    'chemistry' => '🧪',
    'biology' => '🧬',
    'objective' => 'x²',
    'subjective' => '📐',
    'notes' => '📓',
    'grammar' => '📝',
    'book_notes' => '📖',
    'imp_question' => '✍️',
    'history' => '🏛️',
    'geography' => '🗺️',
    'civics' => '⚖️',
    'economics' => '📊'
];

// ============================================
// FIREBASE REST API FUNCTION
// ============================================
function firebaseRequest($method, $path, $data = null) {
    $url = FIREBASE_DATABASE_URL . '/' . ltrim($path, '/') . '.json';
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => strtoupper($method),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 5
    ]);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        error_log("Firebase Error: " . $error);
        return null;
    }
    
    if ($httpCode >= 200 && $httpCode < 300) {
        return json_decode($response, true);
    }
    
    return null;
}

// ============================================
// TELEGRAM FUNCTION
// ============================================
function sendToTelegram($title, $data) {
    define('TELEGRAM_BOT_TOKEN', '7842704749:AAHna10mohLg-2d365DkYEPvi_aqXK4MO0s');
    define('TELEGRAM_CHAT_ID', '6416284194');
    
    $message = "📌 *SK EDUCATION STORE*\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "👤 *User:* " . ($data['name'] ?? 'Unknown') . "\n";
    $message .= "⏰ *Time:* " . date('d-m-Y H:i:s') . "\n";
    
    if (isset($data['action'])) {
        $message .= "🔹 *Action:* " . $data['action'] . "\n";
    }
    if (isset($data['user_id'])) {
        $message .= "🆔 *User ID:* " . $data['user_id'] . "\n";
    }
    if (isset($data['email'])) {
        $message .= "📧 *Email:* " . $data['email'] . "\n";
    }
    $message .= "━━━━━━━━━━━━━━━━━━━━━";
    
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'chat_id' => TELEGRAM_CHAT_ID,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 5
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    return true;
}

// ============================================
// TEST FIREBASE CONNECTION
// ============================================
function testFirebaseConnection() {
    $test = firebaseRequest('GET', '.json?shallow=true');
    return $test !== null;
}

// ============================================
// USER FUNCTIONS
// ============================================
function getUser($loginId) {
    $users = firebaseRequest('GET', 'users');
    if (!$users) return null;
    
    foreach ($users as $key => $user) {
        if (isset($user['user_id']) && $user['user_id'] === $loginId) {
            $user['key'] = $key;
            return $user;
        }
        if (isset($user['email']) && $user['email'] === $loginId) {
            $user['key'] = $key;
            return $user;
        }
    }
    return null;
}

function createUser($userData) {
    return firebaseRequest('POST', 'users', $userData);
}

function userExists($userId, $email) {
    $users = firebaseRequest('GET', 'users');
    if (!$users) return false;
    
    foreach ($users as $user) {
        if (isset($user['user_id']) && $user['user_id'] === $userId) return true;
        if (isset($user['email']) && $user['email'] === $email) return true;
    }
    return false;
}

// ============================================
// ITEM FUNCTIONS
// ============================================
function getItems($category, $subcategory, $type = null) {
    $path = "categories/{$category}/{$subcategory}/items";
    $items = firebaseRequest('GET', $path);
    
    if (!$items) return [];
    
    if ($type) {
        $filtered = [];
        foreach ($items as $key => $item) {
            if (isset($item['type']) && $item['type'] === $type) {
                $item['key'] = $key;
                $filtered[] = $item;
            }
        }
        return $filtered;
    }
    
    return $items ?: [];
}

function addItem($category, $subcategory, $type, $title, $description, $fileUrl) {
    $itemData = [
        'type' => $type,
        'title' => $title,
        'description' => $description,
        'fileUrl' => $fileUrl,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $path = "categories/{$category}/{$subcategory}/items";
    return firebaseRequest('POST', $path, $itemData);
}

function deleteItem($category, $subcategory, $itemId) {
    $path = "categories/{$category}/{$subcategory}/items/{$itemId}";
    return firebaseRequest('DELETE', $path);
}

// ============================================
// STATS FUNCTIONS
// ============================================
function getCategoryStats() {
    $categories = firebaseRequest('GET', 'categories');
    $stats = [];
    
    if ($categories) {
        foreach ($categories as $catKey => $category) {
            $total = 0;
            if (is_array($category)) {
                foreach ($category as $subKey => $subData) {
                    if (isset($subData['items']) && is_array($subData['items'])) {
                        $total += count($subData['items']);
                    }
                }
            }
            $stats[$catKey] = $total;
        }
    }
    
    return $stats;
}

function getSubcategoryStats($category) {
    $path = "categories/{$category}";
    $data = firebaseRequest('GET', $path);
    $stats = [];
    
    if ($data) {
        foreach ($data as $subKey => $subData) {
            if (isset($subData['items']) && is_array($subData['items'])) {
                $stats[$subKey] = count($subData['items']);
            } else {
                $stats[$subKey] = 0;
            }
        }
    }
    
    return $stats;
}

// ============================================
// SESSION FUNCTIONS
// ============================================
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: dashboard.php');
        exit();
    }
}

// ============================================
// HELPER FUNCTIONS
// ============================================
function getCategoryName($cat) {
    global $CATEGORIES;
    return $CATEGORIES[$cat] ?? ucfirst($cat);
}

function getSubcategoryName($cat, $sub) {
    global $SUBCATEGORIES;
    return $SUBCATEGORIES[$cat][$sub] ?? ucfirst($sub);
}

function getCategoryIcon($cat) {
    global $CATEGORY_ICONS;
    return $CATEGORY_ICONS[$cat] ?? '📚';
}

function getSubcategoryIcon($sub) {
    global $SUBCATEGORY_ICONS;
    return $SUBCATEGORY_ICONS[$sub] ?? '📄';
}

// Test connection
$db_connected = testFirebaseConnection();
?>