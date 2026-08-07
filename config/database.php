<?php
// Database Configuration - TEMPLATE
// Copy file ini ke config/database.php dan sesuaikan dengan konfigurasi Anda

// ============================================
// LOCALHOST SETUP (XAMPP/WAMP/MAMP)
// ============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'prabasini');

// ============================================
// PRODUCTION SERVER SETUP
// ============================================
// Uncomment dan sesuaikan untuk production
/*
define('DB_HOST', 'db.example.com');      // Hostname/IP database server
define('DB_USER', 'dbuser');              // Username database
define('DB_PASS', 'password123');         // Password database
define('DB_NAME', 'prabasini');           // Nama database
*/

// ============================================
// SHARED HOSTING SETUP (cPanel)
// ============================================
// Uncomment dan sesuaikan
/*
define('DB_HOST', 'localhost');                           // Biasanya localhost
define('DB_USER', 'username_dbname');                    // Format: username_dbname
define('DB_PASS', 'password');                           // Password dari cPanel
define('DB_NAME', 'username_prabasini');                 // Format: username_dbname
*/

// ============================================
// DATABASE CONNECTION
// ============================================
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => false,
            // Additional options for better performance
            PDO::MYSQL_ATTR_FOUND_ROWS => true,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
    
    // Connection successful
    // Optional: Log connection
    // error_log("Database connection successful: " . date('Y-m-d H:i:s'));
    
} catch (PDOException $e) {
    // Connection failed
    http_response_code(503);
    
    // For debugging (disable in production)
    // die("Database Connection Error: " . $e->getMessage());
    
    // For production
    die("Database Connection Error. Please contact administrator.");
    
    // Optional: Log error
    // error_log("Database Error: " . $e->getMessage());
}

// ============================================
// OPTIONAL CONFIGURATION
// ============================================

// Enable/Disable debug mode
define('DEBUG_MODE', true);  // Set to false in production

// Upload configuration
define('UPLOAD_DIR', '../public/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);  // 5MB
define('ALLOWED_FILE_TYPES', ['xls', 'xlsx', 'csv']);

// Session configuration
define('SESSION_TIMEOUT', 3600);  // 1 hour

// Timezone
date_default_timezone_set('Asia/Jakarta');
?>
