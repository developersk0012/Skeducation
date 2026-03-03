-- ============================================
-- DATABASE SETUP FOR SKEDUCATION
-- ============================================

-- Step 1: Database create karo (agar nahi hai to)
CREATE DATABASE IF NOT EXISTS skeducation_db;
USE skeducation_db;

-- ============================================
-- USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    profile_pic VARCHAR(255),
    bio TEXT,
    education VARCHAR(255),
    occupation VARCHAR(100),
    location VARCHAR(100),
    website VARCHAR(255),
    
    -- Account status
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    role ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    email_verified BOOLEAN DEFAULT FALSE,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    
    -- Indexes for faster search
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SESSIONS TABLE (for login tracking)
-- ============================================
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expiry_time TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (session_token),
    INDEX idx_user (user_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- ERROR LOGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS error_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    error_code INT,
    error_message TEXT,
    error_file VARCHAR(255),
    error_line INT,
    user_id INT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    request_url TEXT,
    request_method VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_code (error_code),
    INDEX idx_created (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- ACTIVITY LOGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100),
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- LOGIN ATTEMPTS TABLE (for security)
-- ============================================
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100),
    ip_address VARCHAR(45),
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success BOOLEAN DEFAULT FALSE,
    
    INDEX idx_ip (ip_address),
    INDEX idx_email (email),
    INDEX idx_time (attempt_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- SAMPLE DATA (for testing)
-- ============================================
INSERT INTO users (username, email, password, full_name, role, email_verified) VALUES
('admin', 'admin@skeducation.com', '$2y$10$YourHashedPasswordHere', 'Administrator', 'admin', TRUE),
('testuser', 'test@skeducation.com', '$2y$10$YourHashedPasswordHere', 'Test User', 'user', TRUE);

-- Note: Replace passwords with actual hashed passwords using password_hash()

-- ============================================
-- STORED PROCEDURES
-- ============================================

-- Procedure to get user by email
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS GetUserByEmail(IN user_email VARCHAR(100))
BEGIN
    SELECT * FROM users WHERE email = user_email LIMIT 1;
END//

-- Procedure to log user activity
CREATE PROCEDURE IF NOT EXISTS LogUserActivity(
    IN p_user_id INT,
    IN p_action VARCHAR(100),
    IN p_description TEXT,
    IN p_ip VARCHAR(45),
    IN p_agent TEXT
)
BEGIN
    INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent)
    VALUES (p_user_id, p_action, p_description, p_ip, p_agent);
END//

-- Procedure to clean old sessions
CREATE PROCEDURE IF NOT EXISTS CleanOldSessions()
BEGIN
    DELETE FROM user_sessions WHERE expiry_time < NOW() OR is_active = FALSE;
END//

DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

-- Trigger to update last_login on session creation
DELIMITER //
CREATE TRIGGER IF NOT EXISTS after_session_insert
AFTER INSERT ON user_sessions
FOR EACH ROW
BEGIN
    UPDATE users SET last_login = NEW.login_time WHERE id = NEW.user_id;
END//

DELIMITER ;

-- ============================================
-- EVENTS (for automatic cleanup)
-- ============================================

-- Event to clean old sessions every day
DELIMITER //
CREATE EVENT IF NOT EXISTS clean_sessions_event
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    DELETE FROM user_sessions WHERE expiry_time < NOW() OR is_active = FALSE;
    DELETE FROM login_attempts WHERE attempt_time < NOW() - INTERVAL 7 DAY;
    DELETE FROM activity_logs WHERE created_at < NOW() - INTERVAL 30 DAY;
END//

DELIMITER ;

-- ============================================
-- INDEXES FOR PERFORMANCE
-- ============================================
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_created ON users(created_at);
CREATE INDEX idx_sessions_token ON user_sessions(session_token);
CREATE INDEX idx_sessions_expiry ON user_sessions(expiry_time);
CREATE INDEX idx_activity_user_time ON activity_logs(user_id, created_at);
CREATE INDEX idx_errors_time ON error_logs(created_at);

-- ============================================
-- VIEWS
-- ============================================

-- View for active users
CREATE OR REPLACE VIEW active_users AS
SELECT id, username, email, full_name, last_login, created_at
FROM users
WHERE status = 'active' AND email_verified = TRUE;

-- View for user statistics
CREATE OR REPLACE VIEW user_statistics AS
SELECT 
    DATE(created_at) as signup_date,
    COUNT(*) as new_users,
    SUM(CASE WHEN email_verified = TRUE THEN 1 ELSE 0 END) as verified_users
FROM users
GROUP BY DATE(created_at);

-- ============================================
-- SHOW RESULTS
-- ============================================
SELECT 'Database setup completed successfully!' as 'Status';
SELECT COUNT(*) as 'Total Tables' FROM information_schema.tables WHERE table_schema = DATABASE();
SELECT 'Tables created:' as 'Message';
SHOW TABLES;