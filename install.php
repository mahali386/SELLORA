<?php
/**
 * Sellora - Core Dynamic Database Installer Wizard
 * Suitable for XAMPP/WAMP (PHP + MySQL Setup)
 */

$statusLogs = [];
$errorOccurred = false;

// 1. Create upload folder directories
$dirs = [
    'uploads/products',
    'uploads/thumbnails'
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0777, true)) {
            $statusLogs[] = "Created directory: <strong>$dir</strong>";
        } else {
            $statusLogs[] = "<span class='text-red-500'>Failed creating folder directory: $dir</span>";
            $errorOccurred = true;
        }
    } else {
        $statusLogs[] = "Directory verified: <strong>$dir</strong>";
    }
}

// 2. Establish MySQL connection and tables creation
try {
    // Connect to local MySQL server
    $host = '127.0.0.1';
    $user = 'root';
    $pass = 'root';
    $dbName = 'sellify';

    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Create database sellify
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $statusLogs[] = "Database verified/created: <strong>$dbName</strong>";

    // Reconnect straight to DB
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // CREATE TABLES SYSTEM
    $tables = [
        "users" => "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `phone` VARCHAR(15) NOT NULL,
            `email` VARCHAR(100) UNIQUE NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `status` ENUM('active', 'blocked') DEFAULT 'active',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "admin" => "CREATE TABLE IF NOT EXISTS `admin` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) UNIQUE NOT NULL,
            `password` VARCHAR(255) NOT NULL
        )",
        "categories" => "CREATE TABLE IF NOT EXISTS `categories` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL
        )",
        "products" => "CREATE TABLE IF NOT EXISTS `products` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `category_id` INT NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `mrp` DECIMAL(10,2) NOT NULL,
            `price` DECIMAL(10,2) NOT NULL,
            `description` TEXT NOT NULL,
            `file` VARCHAR(255) NOT NULL,
            `image` VARCHAR(255) NOT NULL,
            `status` ENUM('active', 'inactive') DEFAULT 'active',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "orders" => "CREATE TABLE IF NOT EXISTS `orders` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `product_id` INT NOT NULL,
            `amount` DECIMAL(10,2) NOT NULL,
            `status` ENUM('pending', 'successful', 'failed') DEFAULT 'pending',
            `razorpay_order_id` VARCHAR(100) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "coupons" => "CREATE TABLE IF NOT EXISTS `coupons` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `code` VARCHAR(50) UNIQUE NOT NULL,
            `discount` INT NOT NULL,
            `expiry` DATE NOT NULL,
            `usage_limit` INT NOT NULL,
            `used_count` INT DEFAULT 0
        )",
        "settings" => "CREATE TABLE IF NOT EXISTS `settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `app_name` VARCHAR(100) NOT NULL,
            `razorpay_key` VARCHAR(100) NOT NULL,
            `razorpay_secret` VARCHAR(100) NOT NULL,
            `support_email` VARCHAR(100) NOT NULL,
            `support_phone` VARCHAR(20) NOT NULL,
            `theme_color` VARCHAR(20) NOT NULL,
            `maintenance_mode` INT DEFAULT 0
        )",
        "wishlist" => "CREATE TABLE IF NOT EXISTS `wishlist` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `product_id` INT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "reviews" => "CREATE TABLE IF NOT EXISTS `reviews` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `product_id` INT NOT NULL,
            `rating` INT NOT NULL,
            `comment` TEXT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "download_logs" => "CREATE TABLE IF NOT EXISTS `download_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `product_id` INT NOT NULL,
            `ip_address` VARCHAR(45) NOT NULL,
            `user_agent` TEXT NOT NULL,
            `downloaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "notifications" => "CREATE TABLE IF NOT EXISTS `notifications` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `message` TEXT NOT NULL,
            `is_read` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "recently_viewed" => "CREATE TABLE IF NOT EXISTS `recently_viewed` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `product_id` INT NOT NULL,
            `viewed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];

    foreach ($tables as $name => $sql) {
        $pdo->exec($sql);
        $statusLogs[] = "Verified table schemas: <strong>$name</strong>";
    }

    // 3. Seed Default Administrator account 
    $stmt = $pdo->prepare("SELECT * FROM `admin` WHERE `username` = ?");
    $stmt->execute(['admin']);
    if ($stmt->rowCount() === 0) {
        // Hashpassword standard for database protection
        $passHash = password_hash('123456', PASSWORD_BCRYPT);
        $ins = $pdo->prepare("INSERT INTO `admin` (`username`, `password`) VALUES (?, ?)");
        $ins->execute(['admin', $passHash]);
        $statusLogs[] = "Default admin seeded: <strong>admin</strong> / password: <strong>123456</strong>";
    } else {
        $statusLogs[] = "Admin credentials verified.";
    }

    // Seed default categories
    $catCheck = $pdo->query("SELECT COUNT(*) FROM `categories`")->fetchColumn();
    if ($catCheck == 0) {
        $pdo->exec("INSERT INTO `categories` (`name`) VALUES 
            ('ChatGPT Prompts'),
            ('Resume Templates'),
            ('Exam Notes'),
            ('Canva Designs')
        ");
        $statusLogs[] = "Default category categories populated.";
    }

    // Seed default settings 
    $setCheck = $pdo->query("SELECT COUNT(*) FROM `settings`")->fetchColumn();
    if ($setCheck == 0) {
        $pdo->exec("INSERT INTO `settings` (`app_name`, `razorpay_key`, `razorpay_secret`, `support_email`, `support_phone`, `theme_color`, `maintenance_mode`) VALUES 
            ('Sellora', 'rzp_test_S5bDUB1XnvePGT', 'wksVp8etGWelSTTrCzN3VMd2', 'support@sellora.com', '+91 98765 43210', '#0284C7', 0)
        ");
        $statusLogs[] = "Default site configurations seeded.";
    }

} catch (PDOException $e) {
    $statusLogs[] = "<span class='text-red-500 font-bold'>MySQL Setup Failed Exception: " . $e->getMessage() . "</span>";
    $errorOccurred = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sellora Installation Wizard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl relative overflow-hidden">
        
        <!-- Glowing background indicators -->
        <span class="absolute -top-16 -right-16 w-32 h-32 bg-sky-500/20 blur-3xl rounded-full"></span>
        <span class="absolute -bottom-16 -left-16 w-32 h-32 bg-indigo-500/20 blur-3xl rounded-full"></span>

        <!-- Logo visual header -->
        <div class="text-center mb-6">
            <div class="inline-flex w-14 h-14 bg-sky-500/10 text-sky-400 border border-sky-500/20 rounded-2xl items-center justify-center text-2xl mb-3">
                <i class="fas fa-screwdriver-wrench"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Sellora Setup Wizard</h1>
            <p class="text-xs text-slate-450 mt-1">Bootstrapping databases, tables and directories.</p>
        </div>

        <!-- Progress Log screen list -->
        <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4 max-h-64 overflow-y-auto space-y-2 text-xs">
            <?php foreach ($statusLogs as $log): ?>
                <div class="flex items-start gap-2.5">
                    <span class="text-emerald-500 mt-0.5"><i class="fas fa-circle-check"></i></span>
                    <span class="text-slate-300"><?= $log ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Action redirection button -->
        <div class="mt-6">
            <?php if (!$errorOccurred): ?>
                <a href="login.php" class="w-full py-3.5 bg-gradient-to-r from-sky-600 to-indigo-500 hover:brightness-110 text-white rounded-xl font-bold text-xs shadow-lg hover:shadow-sky-500/10 transition-all flex items-center justify-center gap-2 outline-none">
                    <span>Installation Complete - Sign In</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            <?php else: ?>
                <button onclick="window.location.reload()" class="w-full py-3.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-xs shadow-lg transition-all outline-none flex items-center justify-center gap-2">
                    <i class="fas fa-sync-alt"></i>
                    <span>Database Retry Verification</span>
                </button>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>
