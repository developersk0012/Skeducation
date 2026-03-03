<?php
// db_config.php - Database configuration file
// Is file ko include karo jahan database chahiye

// ============================================
// DATABASE CONFIGURATION
// ============================================
define('DB_HOST', 'sqlXXX.infinityfree.com'); // InfinityFree ka host
define('DB_USER', 'if0_41114819'); // InfinityFree username
define('DB_PASS', 'your_password_here'); // InfinityFree password
define('DB_NAME', 'if0_41114819_skeducation'); // Database name

// ============================================
// DATABASE CONNECTION CLASS WITH AUTO-RETRY
// ============================================
class Database {
    private static $instance = null;
    private $connection;
    private $connected = false;
    
    private function __construct() {
        $this->connect();
    }
    
    // Singleton pattern - ek hi connection reuse karo
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    // Connect to database with retry
    private function connect() {
        $max_attempts = 3;
        $attempt = 1;
        
        while ($attempt <= $max_attempts) {
            try {
                $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                
                if ($this->connection->connect_error) {
                    throw new Exception("Connection failed: " . $this->connection->connect_error);
                }
                
                $this->connection->set_charset("utf8mb4");
                $this->connected = true;
                
                // Log successful connection
                error_log("Database connected successfully on attempt $attempt");
                break;
                
            } catch (Exception $e) {
                error_log("Database connection attempt $attempt failed: " . $e->getMessage());
                $attempt++;
                
                if ($attempt > $max_attempts) {
                    error_log("All database connection attempts failed");
                    $this->connected = false;
                    $this->connection = null;
                } else {
                    // Wait before retrying
                    sleep(1);
                }
            }
        }
    }
    
    // Get connection
    public function getConnection() {
        if (!$this->connected || !$this->connection) {
            $this->connect();
        }
        return $this->connection;
    }
    
    // Check if connected
    public function isConnected() {
        return $this->connected && $this->connection && !$this->connection->connect_error;
    }
    
    // Execute query with error handling
    public function query($sql) {
        if (!$this->isConnected()) {
            throw new Exception("No database connection");
        }
        
        $result = $this->connection->query($sql);
        
        if (!$result) {
            throw new Exception("Query failed: " . $this->connection->error);
        }
        
        return $result;
    }
    
    // Prepare statement
    public function prepare($sql) {
        if (!$this->isConnected()) {
            throw new Exception("No database connection");
        }
        
        return $this->connection->prepare($sql);
    }
    
    // Insert and get last ID
    public function insert($sql) {
        $this->query($sql);
        return $this->connection->insert_id;
    }
    
    // Escape string
    public function escape($str) {
        return $this->connection->real_escape_string($str);
    }
    
    // Close connection
    public function close() {
        if ($this->connection) {
            $this->connection->close();
            $this->connected = false;
        }
    }
    
    // Destructor
    public function __destruct() {
        $this->close();
    }
}

// ============================================
// USER FUNCTIONS
// ============================================

// Create new user
function createUser($username, $email, $password, $full_name = '') {
    $db = Database::getInstance();
    
    // Check if user exists
    $check = $db->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        return ['success' => false, 'message' => 'User already exists'];
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $insert = $db->prepare("INSERT INTO users (username, email, password, full_name, created_at) VALUES (?, ?, ?, ?, NOW())");
    $insert->bind_param("ssss", $username, $email, $hashed_password, $full_name);
    
    if ($insert->execute()) {
        $user_id = $insert->insert_id;
        return [
            'success' => true, 
            'message' => 'User created successfully',
            'user_id' => $user_id
        ];
    } else {
        return ['success' => false, 'message' => 'Failed to create user'];
    }
}

// Get user by email
function getUserByEmail($email) {
    $db = Database::getInstance();
    
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Get user by ID
function getUserById($id) {
    $db = Database::getInstance();
    
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Update last login
function updateLastLogin($user_id, $ip, $user_agent) {
    $db = Database::getInstance();
    
    // Update user last login
    $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // Create session
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    $session = $db->prepare("INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expiry_time) VALUES (?, ?, ?, ?, ?)");
    $session->bind_param("issss", $user_id, $token, $ip, $user_agent, $expiry);
    $session->execute();
    
    return $token;
}

// Log error
function logError($code, $message, $file, $line, $user_id = null) {
    $db = Database::getInstance();
    
    $stmt = $db->prepare("INSERT INTO error_logs (error_code, error_message, error_file, error_line, user_id, ip_address, user_agent, request_url, request_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $url = $_SERVER['REQUEST_URI'] ?? null;
    $method = $_SERVER['REQUEST_METHOD'] ?? null;
    
    $stmt->bind_param("ississsss", $code, $message, $file, $line, $user_id, $ip, $agent, $url, $method);
    $stmt->execute();
}

// Log activity
function logActivity($user_id, $action, $description) {
    $db = Database::getInstance();
    
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    $stmt->bind_param("issss", $user_id, $action, $description, $ip, $agent);
    $stmt->execute();
}

// Log login attempt
function logLoginAttempt($email, $success) {
    $db = Database::getInstance();
    
    $stmt = $db->prepare("INSERT INTO login_attempts (email, ip_address, success) VALUES (?, ?, ?)");
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    
    $stmt->bind_param("ssi", $email, $ip, $success);
    $stmt->execute();
}

// Check if user is logged in
function isLoggedIn() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
        return false;
    }
    
    $db = Database::getInstance();
    
    $stmt = $db->prepare("SELECT * FROM user_sessions WHERE user_id = ? AND session_token = ? AND is_active = 1 AND expiry_time > NOW()");
    $stmt->bind_param("is", $_SESSION['user_id'], $_SESSION['session_token']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

// ============================================
// TEST DATABASE CONNECTION
// ============================================
function testDatabaseConnection() {
    $db = Database::getInstance();
    
    if ($db->isConnected()) {
        echo "✅ Database connected successfully<br>";
        
        // Test query
        $result = $db->query("SELECT COUNT(*) as total FROM users");
        $row = $result->fetch_assoc();
        echo "✅ Total users: " . $row['total'] . "<br>";
        
        return true;
    } else {
        echo "❌ Database connection failed<br>";
        return false;
    }
}
?>