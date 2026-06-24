<?php
/**
 * DigitalMohan - Core Configuration & Security Helpers
 * Suitable for XAMPP/WAMP (PHP + MySQL)
 */

// Host config
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'sellify');

// Start secure session with session security settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secures', 0); // set to 1 in production with HTTPS
    session_start();
}

// Ensure session ID is regenerated to prevent session fixation on login
function regenerateUserSession($user) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_phone'] = $user['phone'];
    $_SESSION['user_status'] = $user['status'];
    $_SESSION['logged_in'] = true;
}

// DB connection helper using mysqli or PDO (PDO is preferred for security)
function getDBConnection() {
    static $conn = null;
    if ($conn === null) {
        try {
            $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new Exception("Database Connection Failed: " . $e->getMessage());
        }
    }
    return $conn;
}

// Auth checks
function isUserLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// CSRF Protection
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// Secure HMAC Download Token Maker (IP + Expiry + User-Agent bound)
function generateSecureDownloadToken($userId, $productId, $expiryHrs = 3) {
    $secretKey = "sellora_secure_download_gate_salt_key_123456";
    $expiryTime = time() + ($expiryHrs * 3600);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'browser';
    
    // Hash containing identity details to prevent link sharing
    $signature = hash_hmac('sha256', "$userId|$productId|$expiryTime|$ip|$ua", $secretKey);
    return [
        'token' => $signature,
        'expires' => $expiryTime
    ];
}

function verifySecureDownloadToken($token, $userId, $productId, $expires) {
    if (time() > $expires) {
        return false; // Token expired
    }
    $secretKey = "sellora_secure_download_gate_salt_key_123456";
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'browser';
    
    $expectedSignature = hash_hmac('sha256', "$userId|$productId|$expires|$ip|$ua", $secretKey);
    return hash_equals($expectedSignature, $token);
}

// Load database JSON data to provide initial state to client pages with a fast 10-second session cache to prevent disk I/O bottlenecks
$dbPath = dirname(__DIR__) . '/database.json';
$bannersArr = [];
$categoriesArr = [];
$productsArr = [];

$now = time();
if (isset($_SESSION['sellora_db_cache']) && isset($_SESSION['sellora_db_cache_time']) && ($now - $_SESSION['sellora_db_cache_time'] < 10)) {
    $dbData = $_SESSION['sellora_db_cache'];
} else {
    $dbData = null;
    if (file_exists($dbPath)) {
        try {
            $raw = file_get_contents($dbPath);
            $dbData = json_decode($raw, true);
            if ($dbData) {
                $_SESSION['sellora_db_cache'] = $dbData;
                $_SESSION['sellora_db_cache_time'] = $now;
            }
        } catch (Exception $e) {
            $dbData = null;
        }
    }
}

if ($dbData) {
    $bannersArr = isset($dbData['banners']) ? $dbData['banners'] : [];
    $categoriesArr = isset($dbData['categories']) ? $dbData['categories'] : [];
    $productsArr = isset($dbData['products']) ? $dbData['products'] : [];
    $blogsArr = isset($dbData['blogs']) ? $dbData['blogs'] : [];
}

// Fallback to default banners if empty
if (empty($bannersArr)) {
    $bannersArr = [
        [
            "id" => 1,
            "badge" => "HOT SALE",
            "title" => "All Prompt Packs of Chat GPT",
            "subtitle" => "Boost production by 10x instantly. 100% Tested copy-paste directories.",
            "link_url" => "products.php?cat=1",
            "bg_gradient" => "from-indigo-900 to-sky-900"
        ],
        [
            "id" => 2,
            "badge" => "NEW RELEASE",
            "title" => "ATS-Friendly Resumes",
            "subtitle" => "Pass screening tests. Recruiter-approved formatting sheets.",
            "link_url" => "products.php?cat=2",
            "bg_gradient" => "from-emerald-950 to-teal-850"
        ]
    ];
}

$serverBanners = json_encode($bannersArr, JSON_UNESCAPED_UNICODE);
$serverCategories = json_encode($categoriesArr, JSON_UNESCAPED_UNICODE);

// Performance Optimization: Strip heavy data fields (description, preview_data) and limit to top 8 items
// to prevent massive payload sizes inside PHP page-loads as of 10,000+ admin additions.
$lightProducts = [];
$cnt = 0;
foreach ($productsArr as $p) {
    if ((!isset($p['status']) || $p['status'] === 'active') && $cnt < 8) {
        $pLight = $p;
        unset($pLight['description']);
        unset($pLight['preview_data']);
        $lightProducts[] = $pLight;
        $cnt++;
    }
}
$serverProducts = json_encode($lightProducts, JSON_UNESCAPED_UNICODE);
?>
