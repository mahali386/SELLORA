<?php
// Sellora - Unified PHP API Routing Controller
// Allows Sellora to run natively on standard PHP local servers (XAMPP/WAMP) or standard hosting

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$dbFile = __DIR__ . '/database.json';

function loadDB() {
    global $dbFile;
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $now = time();
    if (isset($_SESSION['sellora_db_cache']) && isset($_SESSION['sellora_db_cache_time']) && ($now - $_SESSION['sellora_db_cache_time'] < 10)) {
        return $_SESSION['sellora_db_cache'];
    }

    $initial = [
        "users" => [],
        "admin" => [["id" => 1, "username" => "admin", "password" => "password123"]],
        "categories" => [],
        "products" => [],
        "orders" => [],
        "coupons" => [],
        "settings" => [
            "id" => 1,
            "app_name" => "DigitalMohan",
            "razorpay_key" => "rzp_test_S5bDUB1XnvePGT",
            "razorpay_secret" => "wksVp8etGWelSTTrCzN3VMd2",
            "support_email" => "support@digitalmohan.com",
            "support_phone" => "+91 98765 43210",
            "theme_color" => "#0284C7",
            "maintenance_mode" => 0
        ],
        "wishlist" => [],
        "reviews" => [],
        "download_logs" => [],
        "notifications" => [],
        "recently_viewed" => [],
        "queries" => [],
        "subscribers" => [],
        "campaigns" => [],
        "sent_emails" => [],
        "banners" => [
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
            ],
            [
                "id" => 3,
                "badge" => "FREE RESOURCE",
                "title" => "JEE Physics Formulas",
                "subtitle" => "Comprehensive micro revisions sheet. Authored by top IITian mentors.",
                "link_url" => "products.php?cat=3",
                "bg_gradient" => "from-indigo-950 to-purple-900"
            ]
        ],
        "blogs" => []
    ];

    if (!file_exists($dbFile)) {
        file_put_contents($dbFile, json_encode($initial, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $initial;
    }

    $raw = file_get_contents($dbFile);
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        file_put_contents($dbFile, json_encode($initial, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $initial;
    }

    // Repair missing/corrupt keys to avoid PHP 8 TypeError with count(null) or foreach(null)
    foreach ($initial as $key => $val) {
        if (!isset($data[$key]) || (!is_array($data[$key]) && $key !== "settings")) {
            if ($key === "settings") {
                if (!isset($data[$key]) || $data[$key] === null) {
                    $data[$key] = $initial["settings"];
                }
            } else {
                $data[$key] = $initial[$key];
            }
        }
    }

    // Secondary deep check for banners to maintain defaults
    if (empty($data['banners'])) {
        $data['banners'] = $initial['banners'];
        file_put_contents($dbFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // Secondary deep check for admin structure
    if (empty($data['admin'])) {
        $data['admin'] = $initial['admin'];
        file_put_contents($dbFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['sellora_db_cache'] = $data;
    $_SESSION['sellora_db_cache_time'] = time();

    return $data;
}

function saveDB($db) {
    global $dbFile;
    file_put_contents($dbFile, json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    unset($_SESSION['sellora_db_cache']);
    unset($_SESSION['sellora_db_cache_time']);
}

function handleBase64Upload($fieldValue, $prefix) {
    if (!$fieldValue || strpos($fieldValue, 'data:') !== 0) {
        return $fieldValue;
    }

    try {
        $parts = explode(';base64,', $fieldValue);
        if (count($parts) < 2) return $fieldValue;

        $mimePart = $parts[0];
        $mime = substr($mimePart, 5);
        
        $base64Data = $parts[1];
        $originalName = "";
        if (strpos($base64Data, '|') !== false) {
            $bParts = explode('|', $base64Data);
            $base64Data = $bParts[0];
            $originalName = $bParts[1];
        }

        $data = base64_decode($base64Data);
        if ($data === false) return $fieldValue;

        $ext = 'bin';
        if (strpos($mime, 'jpeg') !== false || strpos($mime, 'jpg') !== false) $ext = 'jpg';
        elseif (strpos($mime, 'png') !== false) $ext = 'png';
        elseif (strpos($mime, 'gif') !== false) $ext = 'gif';
        elseif (strpos($mime, 'webp') !== false) $ext = 'webp';
        elseif (strpos($mime, 'pdf') !== false) $ext = 'pdf';
        elseif (strpos($mime, 'zip') !== false) $ext = 'zip';
        elseif (strpos($mime, 'vnd.openxmlformats-officedocument.wordprocessingml.document') !== false) $ext = 'docx';
        elseif (strpos($mime, 'word') !== false) $ext = 'doc';

        if (!empty($originalName)) {
            $pathInfo = pathinfo($originalName);
            if (isset($pathInfo['extension'])) {
                $ext = strtolower($pathInfo['extension']);
            }
        }

        $uploadDir = __DIR__ . '/uploads';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uniqueId = time() . '_' . rand(10000, 99999);
        $fileName = $prefix . '_' . $uniqueId . '.' . $ext;
        $filePath = $uploadDir . '/' . $fileName;

        file_put_contents($filePath, $data);
        return '/uploads/' . $fileName; // return the root path /uploads/file_name so that it is universally fetchable on standard hosting
    } catch (Exception $e) {
        return $fieldValue;
    }
}

// Parse request path
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Handle subdirectory routing if hosted in a folder (e.g. /sellora/api/products)
if (isset($_GET['route'])) {
    $route = $_GET['route'];
} else {
    $apiSegmentPos = strpos($requestPath, '/api/');
    if ($apiSegmentPos !== false) {
        $route = substr($requestPath, $apiSegmentPos);
    } else {
        $route = '/api/' . ltrim($requestPath, '/');
    }
}
$route = rtrim(explode('?', $route)[0], '/');

$input = json_decode(file_get_contents("php://input"), true) ?? [];
$method = $_SERVER['REQUEST_METHOD'];

function sendJSON($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function sendError($message, $statusCode = 400) {
    sendJSON(["error" => $message, "success" => false], $statusCode);
}

$db = loadDB();

// ---------------------- ROUTING CONTROLLER ----------------------

// 1. Auth & Users
if ($route === '/api/auth/register' && $method === 'POST') {
    $name = $input['name'] ?? '';
    $phone = $input['phone'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    foreach ($db['users'] as $u) {
        if ($u['email'] === $email) {
            sendError("Email already registered");
        }
    }
    
    $newId = count($db['users']) > 0 ? max(array_column($db['users'], 'id')) + 1 : 1;
    $newUser = [
        "id" => $newId,
        "name" => $name,
        "phone" => $phone,
        "email" => $email,
        "password" => $password,
        "status" => "active",
        "created_at" => date(DATE_ATOM)
    ];
    $db['users'][] = $newUser;
    saveDB($db);
    sendJSON(["success" => true, "user" => ["id" => $newUser['id'], "name" => $newUser['name'], "email" => $newUser['email'], "phone" => $newUser['phone']]]);
}

if ($route === '/api/auth/login' && $method === 'POST') {
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    foreach ($db['users'] as $user) {
        if ($user['email'] === $email && $user['password'] === $password) {
            if ($user['status'] === 'blocked') {
                sendError("Your account is suspended.", 403);
            }
            sendJSON(["success" => true, "user" => ["id" => $user['id'], "name" => $user['name'], "email" => $user['email'], "phone" => $user['phone']]]);
        }
    }
    sendError("Invalid email or password");
}

if ($route === '/api/auth/admin-login' && $method === 'POST') {
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    
    foreach ($db['admin'] as $admin) {
        if ($admin['username'] === $username && $admin['password'] === $password) {
            sendJSON(["success" => true, "admin" => ["id" => $admin['id'], "username" => $admin['username']]]);
        }
    }
    sendError("Invalid credentials");
}

if ($route === '/api/users' && $method === 'GET') {
    sendJSON($db['users']);
}

if ($route === '/api/users/toggle' && $method === 'POST') {
    $userId = intval($input['userId'] ?? 0);
    foreach ($db['users'] as &$user) {
        if ($user['id'] === $userId) {
            $user['status'] = $user['status'] === 'active' ? 'blocked' : 'active';
            saveDB($db);
            sendJSON(["success" => true, "status" => $user['status']]);
        }
    }
    sendError("User not found", 404);
}

if ($route === '/api/users/update' && $method === 'POST') {
    $id = intval($input['id'] ?? 0);
    foreach ($db['users'] as &$user) {
        if ($user['id'] === $id) {
            if (isset($input['name'])) $user['name'] = $input['name'];
            if (isset($input['phone'])) $user['phone'] = $input['phone'];
            if (isset($input['password'])) $user['password'] = $input['password'];
            saveDB($db);
            sendJSON(["success" => true, "user" => ["id" => $user['id'], "name" => $user['name'], "email" => $user['email'], "phone" => $user['phone']]]);
        }
    }
    sendError("User not found", 404);
}

// 2. Categories
if ($route === '/api/categories' && $method === 'GET') {
    sendJSON($db['categories']);
}

if (($route === '/api/categories' || $route === '/api/categories/create') && $method === 'POST') {
    $name = $input['name'] ?? '';
    $newId = count($db['categories']) > 0 ? max(array_column($db['categories'], 'id')) + 1 : 1;
    $newCat = ["id" => $newId, "name" => $name];
    $db['categories'][] = $newCat;
    saveDB($db);
    sendJSON(["success" => true, "category" => $newCat]);
}

if ($route === '/api/categories/update' && $method === 'POST') {
    $id = intval($input['id'] ?? 0);
    $name = $input['name'] ?? '';
    foreach ($db['categories'] as &$cat) {
        if ($cat['id'] === $id) {
            $cat['name'] = $name;
            saveDB($db);
            sendJSON(["success" => true, "category" => $cat]);
        }
    }
    sendError("Category not found", 404);
}

if ($route === '/api/categories/delete' && $method === 'POST') {
    $id = intval($input['id'] ?? 0);
    $db['categories'] = array_values(array_filter($db['categories'], function($c) use ($id) { return $c['id'] !== $id; }));
    saveDB($db);
    sendJSON(["success" => true]);
}

if (preg_match('#^/api/categories/([0-9]+)$#', $route, $m) && $method === 'DELETE') {
    $id = intval($m[1]);
    $db['categories'] = array_values(array_filter($db['categories'], function($c) use ($id) { return $c['id'] !== $id; }));
    saveDB($db);
    sendJSON(["success" => true]);
}

// 3. Products
if ($route === '/api/products' && $method === 'GET') {
    $page = isset($_GET['page']) ? intval($_GET['page']) : null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;
    $search = isset($_GET['search']) ? trim(strtolower($_GET['search'])) : null;
    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
    $price = isset($_GET['price']) ? $_GET['price'] : null; // 'free', 'paid'
    $sort = isset($_GET['sort']) ? $_GET['sort'] : null; // 'newest', 'low-high', 'high-low', 'popular'
    $admin = isset($_GET['admin']) && ($_GET['admin'] === 'true' || $_GET['admin'] == '1');

    $products = $db['products'] ?? [];

    // Admin gets all; public default to active
    if (!$admin) {
        $products = array_filter($products, function($p) {
            return !isset($p['status']) || $p['status'] === 'active';
        });
    }

    // Filter category
    if ($category_id !== null && $category_id > 0) {
        $products = array_filter($products, function($p) use ($category_id) {
            return intval($p['category_id'] ?? 0) === $category_id;
        });
    }

    // Filter price
    if ($price === 'free') {
        $products = array_filter($products, function($p) {
            return floatval($p['price'] ?? 0) == 0;
        });
    } else if ($price === 'paid') {
        $products = array_filter($products, function($p) {
            return floatval($p['price'] ?? 0) > 0;
        });
    }

    // Filter search keyword
    if (!empty($search)) {
        $products = array_filter($products, function($p) use ($search) {
            $t = isset($p['title']) ? strtolower($p['title']) : '';
            $d = isset($p['description']) ? strtolower($p['description']) : '';
            return (stripos($t, $search) !== false) || (stripos($d, $search) !== false);
        });
    }

    // Re-index array keys after filtering
    $products = array_values($products);

    // Sort results
    if ($sort === 'low-high') {
        usort($products, function($a, $b) {
            $pa = floatval($a['price'] ?? 0);
            $pb = floatval($b['price'] ?? 0);
            return $pa <=> $pb;
        });
    } else if ($sort === 'high-low') {
        usort($products, function($a, $b) {
            $pa = floatval($a['price'] ?? 0);
            $pb = floatval($b['price'] ?? 0);
            return $pb <=> $pa;
        });
    } else if ($sort === 'popular') {
        usort($products, function($a, $b) {
            $ma = floatval($a['mrp'] ?? 0);
            $mb = floatval($b['mrp'] ?? 0);
            return $mb <=> $ma;
        });
    } else {
        // Default: newest first
        usort($products, function($a, $b) {
            $timeA = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
            $timeB = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
            return $timeB <=> $timeA;
        });
    }

    // Check if limit is requested
    if ($limit !== null && $limit > 0) {
        $activePage = $page !== null && $page > 0 ? $page : 1;
        $startIndex = ($activePage - 1) * $limit;
        $paginated = array_slice($products, $startIndex, $limit);
        sendJSON([
            "products" => $paginated,
            "total" => count($products),
            "page" => $activePage,
            "limit" => $limit,
            "totalPages" => intval(ceil(count($products) / $limit))
        ]);
    } else {
        sendJSON($products);
    }
}

if (preg_match('#^/api/products/detail/([0-9]+)$#', $route, $m) && $method === 'GET') {
    $productId = intval($m[1]);
    $product = null;
    foreach ($db['products'] as $p) {
        if (intval($p['id']) === $productId) {
            $product = $p;
            break;
        }
    }
    if ($product) {
        sendJSON($product);
    } else {
        sendError("Product not found", 404);
    }
}

if (($route === '/api/products' || $route === '/api/products/create') && $method === 'POST') {
    $savedFile = isset($input['file']) ? handleBase64Upload($input['file'], 'product_file') : "default_file.zip";
    $savedImage = isset($input['image']) ? handleBase64Upload($input['image'], 'product_image') : "https://images.unsplash.com/photo-1541963463532-d68292c34b19?w=600&q=80";

    $newProd = [
        "id" => count($db['products']) > 0 ? max(array_column($db['products'], 'id')) + 1 : 1,
        "category_id" => intval($input['category_id'] ?? 0),
        "title" => $input['title'] ?? '',
        "mrp" => floatval($input['mrp'] ?? 0),
        "price" => floatval($input['price'] ?? 0),
        "description" => $input['description'] ?? '',
        "file" => $savedFile,
        "image" => $savedImage,
        "status" => $input['status'] ?? "active",
        "preview_url" => $input['preview_url'] ?? "",
        "preview_type" => $input['preview_type'] ?? "link",
        "preview_data" => $input['preview_data'] ?? "",
        "created_at" => date(DATE_ATOM)
    ];
    $db['products'][] = $newProd;
    saveDB($db);
    sendJSON(["success" => true, "product" => $newProd]);
}

if ($route === '/api/products/update' && $method === 'POST') {
    $id = intval($input['id'] ?? 0);
    foreach ($db['products'] as &$p) {
        if ($p['id'] === $id) {
            if (isset($input['category_id'])) $p['category_id'] = intval($input['category_id']);
            if (isset($input['title'])) $p['title'] = $input['title'];
            if (isset($input['mrp'])) $p['mrp'] = floatval($input['mrp']);
            if (isset($input['price'])) $p['price'] = floatval($input['price']);
            if (isset($input['description'])) $p['description'] = $input['description'];
            if (isset($input['file'])) $p['file'] = handleBase64Upload($input['file'], 'product_file');
            if (isset($input['image'])) $p['image'] = handleBase64Upload($input['image'], 'product_image');
            if (isset($input['status'])) $p['status'] = $input['status'];
            if (isset($input['preview_url'])) $p['preview_url'] = $input['preview_url'];
            if (isset($input['preview_type'])) $p['preview_type'] = $input['preview_type'];
            if (isset($input['preview_data'])) $p['preview_data'] = $input['preview_data'];
            saveDB($db);
            sendJSON(["success" => true, "product" => $p]);
        }
    }
    sendError("Product not found", 404);
}

if ($route === '/api/products/delete' && $method === 'POST') {
    $id = intval($input['id'] ?? 0);
    $db['products'] = array_values(array_filter($db['products'], function($p) use ($id) { return $p['id'] !== $id; }));
    saveDB($db);
    sendJSON(["success" => true]);
}

if ($route === '/api/products/bulk-toggle' && $method === 'POST') {
    $ids = $input['ids'] ?? [];
    $status = $input['status'] ?? 'active';
    foreach ($db['products'] as &$p) {
        if (in_array($p['id'], $ids)) {
            $p['status'] = $status;
        }
    }
    saveDB($db);
    sendJSON(["success" => true]);
}

if (preg_match('#^/api/products/([0-9]+)$#', $route, $m)) {
    $id = intval($m[1]);
    if ($method === 'PUT') {
        foreach ($db['products'] as &$p) {
            if ($p['id'] === $id) {
                if (isset($input['category_id'])) $p['category_id'] = intval($input['category_id']);
                if (isset($input['title'])) $p['title'] = $input['title'];
                if (isset($input['mrp'])) $p['mrp'] = floatval($input['mrp']);
                if (isset($input['price'])) $p['price'] = floatval($input['price']);
                if (isset($input['description'])) $p['description'] = $input['description'];
                if (isset($input['file'])) $p['file'] = handleBase64Upload($input['file'], 'product_file');
                if (isset($input['image'])) $p['image'] = handleBase64Upload($input['image'], 'product_image');
                if (isset($input['status'])) $p['status'] = $input['status'];
                if (isset($input['preview_url'])) $p['preview_url'] = $input['preview_url'];
                if (isset($input['preview_type'])) $p['preview_type'] = $input['preview_type'];
                if (isset($input['preview_data'])) $p['preview_data'] = $input['preview_data'];
                saveDB($db);
                sendJSON(["success" => true, "product" => $p]);
            }
        }
        sendError("Product not found", 404);
    } elseif ($method === 'DELETE') {
        $db['products'] = array_values(array_filter($db['products'], function($p) use ($id) { return $p['id'] !== $id; }));
        saveDB($db);
        sendJSON(["success" => true]);
    }
}

// 4. Coupons
if ($route === '/api/coupons' && $method === 'GET') {
    sendJSON($db['coupons']);
}

if (($route === '/api/coupons' || $route === '/api/coupons/create') && $method === 'POST') {
    $newCoupon = [
        "id" => count($db['coupons']) > 0 ? max(array_column($db['coupons'], 'id')) + 1 : 1,
        "code" => strtoupper($input['code'] ?? ''),
        "discount" => floatval($input['discount'] ?? 0),
        "expiry" => $input['expiry'] ?? '',
        "usage_limit" => intval($input['usage_limit'] ?? 0),
        "used_count" => 0
    ];
    $db['coupons'][] = $newCoupon;
    saveDB($db);
    sendJSON(["success" => true, "coupon" => $newCoupon]);
}

if ($route === '/api/coupons/delete' && $method === 'POST') {
    $id = intval($input['id'] ?? 0);
    $db['coupons'] = array_values(array_filter($db['coupons'], function($c) use ($id) { return $c['id'] !== $id; }));
    saveDB($db);
    sendJSON(["success" => true]);
}

if (preg_match('#^/api/coupons/([0-9]+)$#', $route, $m) && $method === 'DELETE') {
    $id = intval($m[1]);
    $db['coupons'] = array_values(array_filter($db['coupons'], function($c) use ($id) { return $c['id'] !== $id; }));
    saveDB($db);
    sendJSON(["success" => true]);
}

if ($route === '/api/coupons/validate' && $method === 'POST') {
    $code = strtoupper($input['code'] ?? '');
    foreach ($db['coupons'] as $coupon) {
        if (strtoupper($coupon['code']) === $code) {
            if (strtotime($coupon['expiry']) < time()) {
                sendError("Coupon code has expired");
            }
            if ($coupon['used_count'] >= $coupon['usage_limit']) {
                sendError("Coupon usage limit reached");
            }
            sendJSON(["success" => true, "discount" => $coupon['discount']]);
        }
    }
    sendError("Invalid coupon code", 404);
}

// 5. Orders & Purchases
if ($route === '/api/orders' && $method === 'GET') {
    sendJSON($db['orders']);
}

if (preg_match('#^/api/orders/user/([0-9]+)$#', $route, $m) && $method === 'GET') {
    $userId = intval($m[1]);
    $userOrders = array_values(array_filter($db['orders'], function($o) use ($userId) {
        return intval($o['user_id'] ?? 0) === $userId;
    }));
    
    // Joint lookup for products
    $productMap = [];
    foreach ($db['products'] as $p) {
        $productMap[intval($p['id'])] = $p;
    }
    
    $joinedOrders = [];
    foreach ($userOrders as $o) {
        $p = isset($productMap[intval($o['product_id'])]) ? $productMap[intval($o['product_id'])] : [];
        $joinedOrders[] = array_merge($o, [
            "product_title" => isset($p['title']) ? $p['title'] : "Deleted Document Product Specs",
            "product_image" => isset($p['image']) ? $p['image'] : "",
            "product_price" => isset($p['price']) ? floatval($p['price']) : 0.0
        ]);
    }
    sendJSON($joinedOrders);
}

if (preg_match('#^/api/orders/detail/([0-9]+)$#', $route, $m) && $method === 'GET') {
    $orderId = intval($m[1]);
    $order = null;
    foreach ($db['orders'] as $o) {
        if (intval($o['id']) === $orderId) {
            $order = $o;
            break;
        }
    }
    if ($order) {
        $product = null;
        foreach ($db['products'] as $p) {
            if (intval($p['id']) === intval($order['product_id'])) {
                $product = $p;
                break;
            }
        }
        $joinedOrder = array_merge($order, [
            "product_title" => $product ? $product['title'] : "Deleted Product",
            "product_image" => $product ? $product['image'] : "",
            "product_price" => $product ? floatval($product['price']) : 0.0,
            "product_description" => $product ? $product['description'] : ""
        ]);
        sendJSON($joinedOrder);
    } else {
        sendError("Order details not found.", 404);
    }
}

if ($route === '/api/orders/create' && $method === 'POST') {
    $userId = intval($input['user_id'] ?? 0);
    $productIdStr = $input['product_id'] ?? '0';
    $discountCode = $input['discountCode'] ?? '';
    
    if (is_array($productIdStr)) {
        $productIds = array_map('intval', $productIdStr);
    } else {
        $productIds = array_map('intval', explode(',', $productIdStr));
    }

    $products = [];
    foreach ($db['products'] as $p) {
        if (in_array($p['id'], $productIds)) {
            $products[] = $p;
        }
    }
    if (count($products) === 0) sendError("Product not found", 404);
    
    $isBundle = count($products) > 1;
    $rzpId = "order_rzp_mock_" . substr(md5(uniqid()), 0, 9);
    $createdOrders = [];
    $nextId = count($db['orders']) > 0 ? max(array_column($db['orders'], 'id')) + 1 : 10001;

    foreach ($products as $product) {
        $finalPrice = $product['price'];
        if ($isBundle) {
            $finalPrice = round($finalPrice * 0.65);
        }

        if ($discountCode) {
            foreach ($db['coupons'] as $coupon) {
                if (strtoupper($coupon['code']) === strtoupper($discountCode)) {
                    $finalPrice = max(0, $finalPrice - ($finalPrice * $coupon['discount'] / 100));
                    break;
                }
            }
        }

        $newOrder = [
            "id" => $nextId++,
            "user_id" => $userId,
            "product_id" => $product['id'],
            "amount" => $finalPrice,
            "status" => "pending",
            "razorpay_order_id" => $rzpId,
            "created_at" => date(DATE_ATOM)
        ];
        $db['orders'][] = $newOrder;
        $createdOrders[] = $newOrder;
    }
    
    saveDB($db);
    sendJSON(["success" => true, "order" => $createdOrders[0], "orders" => $createdOrders]);
}

if ($route === '/api/orders/verify' && $method === 'POST') {
    $rzpOrderId = $input['razorpay_order_id'] ?? '';
    $success = $input['success'] ?? false;
    
    $foundAny = false;
    $matchingOrders = [];
    foreach ($db['orders'] as &$order) {
        if ($order['razorpay_order_id'] === $rzpOrderId) {
            $foundAny = true;
            if ($success) {
                $order['status'] = "successful";
                
                $pTitle = "Digital Asset";
                foreach ($db['products'] as $p) {
                    if ($p['id'] === $order['product_id']) {
                        $pTitle = $p['title'];
                        break;
                    }
                }
                $newNotifyId = count($db['notifications']) > 0 ? max(array_column($db['notifications'], 'id')) + 1 : 1;
                $db['notifications'][] = [
                    "id" => $newNotifyId,
                    "user_id" => $order['user_id'],
                    "title" => "Purchase Successful! 🎉",
                    "message" => "You successfully unlocked \"{$pTitle}\". Secure download is available now.",
                    "is_read" => 0,
                    "created_at" => date(DATE_ATOM)
                ];
            } else {
                $order['status'] = "failed";
            }
            $matchingOrders[] = $order;
        }
    }
    unset($order);

    if ($foundAny) {
        saveDB($db);
        sendJSON(["success" => true, "order" => $matchingOrders[0], "orders" => $matchingOrders]);
    } else {
        sendError("Order session not found", 404);
    }
}

// 6. Settings
if ($route === '/api/settings' && $method === 'GET') {
    sendJSON($db['settings']);
}

if ($route === '/api/settings/admin-info' && $method === 'GET') {
    if (!empty($db['admin'][0])) {
        sendJSON(["username" => $db['admin'][0]['username'], "password" => $db['admin'][0]['password']]);
    } else {
        sendJSON(["username" => "admin", "password" => "password123"]);
    }
}

if (($route === '/api/settings' || $route === '/api/settings/update') && $method === 'POST') {
    $db['settings'] = [
        "id" => 1,
        "app_name" => $input['app_name'] ?? $db['settings']['app_name'] ?? 'DigitalMohan',
        "razorpay_key" => $input['razorpay_key'] ?? $db['settings']['razorpay_key'] ?? '',
        "razorpay_secret" => $input['razorpay_secret'] ?? $db['settings']['razorpay_secret'] ?? '',
        "support_email" => $input['support_email'] ?? $db['settings']['support_email'] ?? '',
        "support_phone" => $input['support_phone'] ?? $db['settings']['support_phone'] ?? '',
        "theme_color" => $input['theme_color'] ?? $db['settings']['theme_color'] ?? '#0284C7',
        "maintenance_mode" => intval($input['maintenance_mode'] ?? $db['settings']['maintenance_mode'] ?? 0),
        "viral_popup_enabled" => intval($input['viral_popup_enabled'] ?? $db['settings']['viral_popup_enabled'] ?? 1),
        "viral_popup_title" => $input['viral_popup_title'] ?? $db['settings']['viral_popup_title'] ?? 'Growth Marketing Secrets',
        "viral_popup_mrp" => intval($input['viral_popup_mrp'] ?? $db['settings']['viral_popup_mrp'] ?? 999),
        "viral_popup_description" => $input['viral_popup_description'] ?? $db['settings']['viral_popup_description'] ?? 'Free Premium Ebook unlock karne ke liye ye link WhatsApp par share karein.',
        "whatsapp_group_enabled" => intval($input['whatsapp_group_enabled'] ?? $db['settings']['whatsapp_group_enabled'] ?? 1),
        "whatsapp_group_title" => $input['whatsapp_group_title'] ?? $db['settings']['whatsapp_group_title'] ?? 'Join Our Premium WhatsApp Community! 🚀',
        "whatsapp_group_link" => $input['whatsapp_group_link'] ?? $db['settings']['whatsapp_group_link'] ?? 'https://chat.whatsapp.com/GjMockGrpLnk2026Sellora',
        "whatsapp_group_description" => $input['whatsapp_group_description'] ?? $db['settings']['whatsapp_group_description'] ?? 'Get instant high-quality templates, free resume tools, and direct support updates daily. Join 10,000+ members!',
        "whatsapp_group_delay" => intval($input['whatsapp_group_delay'] ?? $db['settings']['whatsapp_group_delay'] ?? 5000),
        "whatsapp_group_autoclose" => intval($input['whatsapp_group_autoclose'] ?? $db['settings']['whatsapp_group_autoclose'] ?? 10000)
    ];
    
    // Support admin username/password updates
    if (!empty($input['admin_username'])) {
        if (!isset($db['admin'])) $db['admin'] = [];
        if (count($db['admin']) === 0) {
            $db['admin'][] = ["id" => 1, "username" => "admin", "password" => "password123"];
        }
        $db['admin'][0]['username'] = $input['admin_username'];
    }
    if (!empty($input['admin_password'])) {
        if (!isset($db['admin'])) $db['admin'] = [];
        if (count($db['admin']) === 0) {
            $db['admin'][] = ["id" => 1, "username" => "admin", "password" => "password123"];
        }
        $db['admin'][0]['password'] = $input['admin_password'];
    }
    
    saveDB($db);
    sendJSON(["success" => true, "settings" => $db['settings']]);
}

// 6.2 Blogs API Endpoints
if ($route === '/api/blogs' && $method === 'GET') {
    if (!isset($db['blogs'])) {
        $db['blogs'] = [];
    }
    $search = isset($_GET['search']) ? trim(strtolower($_GET['search'])) : null;
    $category = isset($_GET['category']) ? trim(strtolower($_GET['category'])) : null;
    
    $blogs = $db['blogs'];
    
    if ($category !== null && $category !== '') {
        $filtered = [];
        foreach ($blogs as $b) {
            if (isset($b['category']) && strtolower($b['category']) === $category) {
                $filtered[] = $b;
            }
        }
        $blogs = $filtered;
    }
    
    if ($search !== null && $search !== '') {
        $filtered = [];
        foreach ($blogs as $b) {
            $titleMatch = isset($b['title']) && strpos(strtolower($b['title']), $search) !== false;
            $summaryMatch = isset($b['summary']) && strpos(strtolower($b['summary']), $search) !== false;
            $contentMatch = isset($b['content']) && strpos(strtolower($b['content']), $search) !== false;
            if ($titleMatch || $summaryMatch || $contentMatch) {
                $filtered[] = $b;
            }
        }
        $blogs = $filtered;
    }
    
    sendJSON($blogs);
}

if (strpos($route, '/api/blogs/detail/') === 0 && $method === 'GET') {
    $id = intval(substr($route, strlen('/api/blogs/detail/')));
    if (!isset($db['blogs'])) {
        $db['blogs'] = [];
    }
    foreach ($db['blogs'] as $b) {
        if (intval($b['id']) === $id) {
            sendJSON($b);
            exit;
        }
    }
    sendError("Blog not found", 404);
}

if (($route === '/api/blogs/create') && $method === 'POST') {
    if (!isset($db['blogs'])) {
        $db['blogs'] = [];
    }
    $maxId = 0;
    foreach ($db['blogs'] as $b) {
        if (intval($b['id']) > $maxId) {
            $maxId = intval($b['id']);
        }
    }
    $newId = $maxId + 1;
    $savedImage = isset($input['image']) ? handleBase64Upload($input['image'], 'blog_image') : 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=800&q=80';
    $newBlog = [
        "id" => $newId,
        "title" => $input['title'] ?? '',
        "summary" => $input['summary'] ?? '',
        "content" => $input['content'] ?? '',
        "author" => $input['author'] ?? 'Mohan Mahali',
        "image" => $savedImage,
        "category" => $input['category'] ?? 'General',
        "read_time" => $input['read_time'] ?? '5 min read',
        "status" => $input['status'] ?? 'active',
        "created_at" => date('c')
    ];
    $db['blogs'][] = $newBlog;
    saveDB($db);
    sendJSON(["success" => true, "blog" => $newBlog]);
}

if (($route === '/api/blogs/update') && $method === 'POST') {
    $id = intval($input['id'] ?? 0);
    if (!isset($db['db_source'])) { // or plain blogs
        // let's stick to matching original
    }
    if (!isset($db['blogs'])) {
        $db['blogs'] = [];
    }
    $foundIndex = -1;
    for ($i = 0; $i < count($db['blogs']); $i++) {
        if (intval($db['blogs'][$i]['id']) === $id) {
            $foundIndex = $i;
            break;
        }
    }
    
    if ($foundIndex === -1) {
        sendError("Blog not found", 404);
    }
    
    $db['blogs'][$foundIndex]['title'] = $input['title'] ?? $db['blogs'][$foundIndex]['title'] ?? '';
    $db['blogs'][$foundIndex]['summary'] = $input['summary'] ?? $db['blogs'][$foundIndex]['summary'] ?? '';
    $db['blogs'][$foundIndex]['content'] = $input['content'] ?? $db['blogs'][$foundIndex]['content'] ?? '';
    $db['blogs'][$foundIndex]['author'] = $input['author'] ?? $db['blogs'][$foundIndex]['author'] ?? 'Mohan Mahali';
    if (isset($input['image'])) {
        $db['blogs'][$foundIndex]['image'] = handleBase64Upload($input['image'], 'blog_image');
    }
    $db['blogs'][$foundIndex]['category'] = $input['category'] ?? $db['blogs'][$foundIndex]['category'] ?? 'General';
    $db['blogs'][$foundIndex]['read_time'] = $input['read_time'] ?? $db['blogs'][$foundIndex]['read_time'] ?? '5 min read';
    $db['blogs'][$foundIndex]['status'] = $input['status'] ?? $db['blogs'][$foundIndex]['status'] ?? 'active';
    
    saveDB($db);
    sendJSON(["success" => true, "blog" => $db['blogs'][$foundIndex]]);
}

if (($route === '/api/blogs/delete') && $method === 'POST') {
    $id = intval($input['id'] ?? 0);
    if (!isset($db['blogs'])) {
        $db['blogs'] = [];
    }
    $newBlogs = [];
    foreach ($db['blogs'] as $b) {
        if (intval($b['id']) !== $id) {
            $newBlogs[] = $b;
        }
    }
    $db['blogs'] = $newBlogs;
    saveDB($db);
    sendJSON(["success" => true]);
}

// 6.5 Banners Management
if ($route === '/api/banners' && $method === 'GET') {
    if (!isset($db['banners'])) $db['banners'] = [];
    sendJSON($db['banners']);
}

if (($route === '/api/banners' || $route === '/api/banners/create') && $method === 'POST') {
    if (!isset($db['banners'])) $db['banners'] = [];
    $newId = count($db['banners']) > 0 ? max(array_column($db['banners'], 'id')) + 1 : 1;
    $newB = [
        "id" => $newId,
        "badge" => $input['badge'] ?? "PROMO",
        "title" => $input['title'] ?? "",
        "subtitle" => $input['subtitle'] ?? "",
        "link_url" => $input['link_url'] ?? "products.php",
        "bg_gradient" => $input['bg_gradient'] ?? "from-slate-900 to-indigo-900"
    ];
    $db['banners'][] = $newB;
    saveDB($db);
    sendJSON(["success" => true, "banner" => $newB]);
}

if ($route === '/api/banners/update' && $method === 'POST') {
    if (!isset($db['banners'])) $db['banners'] = [];
    $id = intval($input['id'] ?? 0);
    foreach ($db['banners'] as &$b) {
        if ($b['id'] === $id) {
            if (isset($input['badge'])) $b['badge'] = $input['badge'];
            if (isset($input['title'])) $b['title'] = $input['title'];
            if (isset($input['subtitle'])) $b['subtitle'] = $input['subtitle'];
            if (isset($input['link_url'])) $b['link_url'] = $input['link_url'];
            if (isset($input['bg_gradient'])) $b['bg_gradient'] = $input['bg_gradient'];
            saveDB($db);
            sendJSON(["success" => true, "banner" => $b]);
            exit;
        }
    }
    sendError("Banner not found", 404);
}

if ($route === '/api/banners/delete' && $method === 'POST') {
    if (!isset($db['banners'])) $db['banners'] = [];
    $id = intval($input['id'] ?? 0);
    $db['banners'] = array_values(array_filter($db['banners'], function($b) use ($id) { return $b['id'] !== $id; }));
    saveDB($db);
    sendJSON(["success" => true]);
}

// 7. Wishlist
if (preg_match('#^/api/wishlist/([0-9]+)$#', $route, $m) && $method === 'GET') {
    $userId = intval($m[1]);
    $list = array_values(array_filter($db['wishlist'], function($w) use ($userId) { return $w['user_id'] === $userId; }));
    sendJSON($list);
}

if ($route === '/api/wishlist/toggle' && $method === 'POST') {
    $userId = intval($input['user_id'] ?? 0);
    $productId = intval($input['product_id'] ?? 0);
    
    $idx = -1;
    foreach ($db['wishlist'] as $i => $w) {
        if ($w['user_id'] === $userId && $w['product_id'] === $productId) {
            $idx = $i;
            break;
        }
    }
    
    if ($idx !== -1) {
        unset($db['wishlist'][$idx]);
        $db['wishlist'] = array_values($db['wishlist']);
        saveDB($db);
        sendJSON(["success" => true, "state" => "removed"]);
    } else {
        $newId = count($db['wishlist']) > 0 ? max(array_column($db['wishlist'], 'id')) + 1 : 1;
        $db['wishlist'][] = [
            "id" => $newId,
            "user_id" => $userId,
            "product_id" => $productId,
            "created_at" => date(DATE_ATOM)
        ];
        saveDB($db);
        sendJSON(["success" => true, "state" => "added"]);
    }
}

// 8. Reviews
if (preg_match('#^/api/reviews/([0-9]+)$#', $route, $m) && $method === 'GET') {
    $productId = intval($m[1]);
    $filtered = [];
    foreach ($db['reviews'] as $r) {
        if ($r['product_id'] === $productId) {
            $uname = "Anonymous Buyer";
            foreach ($db['users'] as $u) {
                if ($u['id'] === $r['user_id']) {
                    $uname = $u['name'];
                    break;
                }
            }
            $r['user_name'] = $uname;
            $filtered[] = $r;
        }
    }
    sendJSON($filtered);
}

if ($route === '/api/reviews' && $method === 'POST') {
    $newReview = [
        "id" => count($db['reviews']) > 0 ? max(array_column($db['reviews'], 'id')) + 1 : 1,
        "user_id" => intval($input['user_id'] ?? 0),
        "product_id" => intval($input['product_id'] ?? 0),
        "rating" => intval($input['rating'] ?? 5),
        "comment" => $input['comment'] ?? '',
        "created_at" => date(DATE_ATOM)
    ];
    $db['reviews'][] = $newReview;
    saveDB($db);
    sendJSON(["success" => true, "review" => $newReview]);
}

// 9. Downloads logs
if ($route === '/api/downloads/log' && $method === 'POST') {
    $newId = count($db['download_logs']) > 0 ? max(array_column($db['download_logs'], 'id')) + 1 : 1;
    $db['download_logs'][] = [
        "id" => $newId,
        "user_id" => intval($input['user_id'] ?? 0),
        "product_id" => intval($input['product_id'] ?? 0),
        "ip_address" => $input['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        "user_agent" => $input['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? 'Browser',
        "downloaded_at" => date(DATE_ATOM)
    ];
    saveDB($db);
    sendJSON(["success" => true]);
}

if ($route === '/api/downloads/logs' && $method === 'GET') {
    sendJSON($db['download_logs']);
}

if ($route === '/api/downloads/file' && $method === 'GET') {
    $pId = isset($_GET['id']) ? intval($_GET['id']) : (isset($_GET['productId']) ? intval($_GET['productId']) : 0);
    $product = null;
    if ($pId === 999) {
        $product = [
            'id' => 999,
            'category_id' => 1,
            'title' => $db['settings']['viral_popup_title'] ?? "Free Premium Ebook Secrets",
            'mrp' => intval($db['settings']['viral_popup_mrp'] ?? 999),
            'price' => 0,
            'description' => $db['settings']['viral_popup_description'] ?? "Free Premium Ebook Secrets Unlock successfully!",
            'file' => "Free_Premium_Ebook_Secrets.pdf",
            'image' => "",
            'status' => "active"
        ];
    } else {
        foreach ($db['products'] as $p) {
            if (intval($p['id']) === $pId) {
                $product = $p;
                break;
            }
        }
    }
    if (!$product) {
        http_response_code(404);
        echo "Product not found";
        exit;
    }

    $filename = !empty($product['file']) ? $product['file'] : "digital_product.zip";
    
    // Check if filename contains multiple fields separated by "|"
    $downloadName = "digital_product.zip";
    $downloadUrl = "";
    if (strpos($filename, '|') !== false) {
        $parts = explode('|', $filename);
        if (strpos($parts[0], 'data:') === 0) {
            $downloadUrl = $parts[0];
            $downloadName = $parts[1];
        } else if (strpos($parts[1], 'data:') === 0) {
            $downloadUrl = $parts[1];
            $downloadName = $parts[0];
        } else {
            $downloadName = $parts[1];
        }
    } else if (strpos($filename, 'data:') === 0) {
        $downloadUrl = $filename;
        $downloadName = "digital_product.zip";
    } else {
        $downloadName = $filename;
    }

    $parts = explode('.', $downloadName);
    $ext = strtolower(end($parts));

    $contentType = 'application/octet-stream';
    if ($ext === 'pdf') {
        $contentType = 'application/pdf';
    } else if ($ext === 'xlsx') {
        $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    } else if ($ext === 'docx') {
        $contentType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    } else if ($ext === 'zip') {
        $contentType = 'application/zip';
    }

    // Determine the base64 content
    $base64Data = "";
    if (strpos($downloadUrl, 'data:') === 0) {
        $commaPos = strpos($downloadUrl, ',');
        if ($commaPos !== false) {
            $base64Data = substr($downloadUrl, $commaPos + 1);
        }
    }

    if (empty($base64Data)) {
        // Fallback or default content
        $base64Data = "UEsFBgAAAAAAAAAAAAAAAAAAAAAAAA=="; // default empty zip
        if ($ext === 'pdf') {
            $base64Data = "JVBERi0xLjEKMSAwIG9iajw8L1R5cGUvQ2F0YWxvZy9QYWdlcyAyIDAgUj4+ZW5kb2JqMiAwIG9iajw8L1R5cGUvUGFnZXMvS2lkc1szIDAgUl0vQ291bnQgMT4+ZW5kb2JqMyAwIG9iajw8L1R5cGUvUGFnZS9QYXJlbnQgMiAwIFIvTWVkaWFCb3hbMCAwIDU5NSA4NDJdL0NvbnRlbnRzIDQgMCBSPj5lbmRvYmo0IDAgb2JqPDwvTGVuZ3RoIDU5Pj5zdHJlYW0KQlQgL0YxIDEyIFRmIDcwIDcwMCBUZCAoRGlnaXRhbE1vaGFuIERvY3VtZW50IERvd25sb2FkKSBUaiBFVAplbmRzdHJlYW0lJUVPRg==";
        }
    }

    $binaryData = base64_decode($base64Data);

    // Clean any active output buffers to prevent stray characters or headers corrupting the download stream
    if (ob_get_length()) {
        ob_clean();
    }

    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . $downloadName . '"');
    header('Content-Length: ' . strlen($binaryData));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    echo $binaryData;
    exit;
}

if ($route === '/api/payouts/summary' && $method === 'GET') {
    $suc = array_filter($db['orders'], function($o) { return $o['status'] === 'successful'; });
    $rev = array_reduce($suc, function($sum, $o) { return $sum + $o['amount']; }, 0);
    sendJSON([
        "revenue" => $rev,
        "settlement" => $rev * 0.98,
        "transactions" => count($suc)
    ]);
}

// 10. Recently viewed
if (preg_match('#^/api/recently-viewed/([0-9]+)$#', $route, $m) && $method === 'GET') {
    $userId = intval($m[1]);
    $list = array_values(array_filter($db['recently_viewed'], function($rv) use ($userId) { return $rv['user_id'] === $userId; }));
    sendJSON($list);
}

if ($route === '/api/recently-viewed' && $method === 'POST') {
    $userId = intval($input['user_id'] ?? 0);
    $productId = intval($input['product_id'] ?? 0);
    
    $db['recently_viewed'] = array_values(array_filter($db['recently_viewed'], function($rv) use ($userId, $productId) {
        return !($rv['user_id'] === $userId && $rv['product_id'] === $productId);
    }));
    
    $newId = count($db['recently_viewed']) > 0 ? max(array_column($db['recently_viewed'], 'id')) + 1 : 1;
    array_unshift($db['recently_viewed'], [
        "id" => $newId,
        "user_id" => $userId,
        "product_id" => $productId,
        "viewed_at" => date(DATE_ATOM)
    ]);
    if (count($db['recently_viewed']) > 20) {
        array_pop($db['recently_viewed']);
    }
    saveDB($db);
    sendJSON(["success" => true]);
}

// 11. Notifications
if (preg_match('#^/api/notifications/([0-9]+)$#', $route, $m) && $method === 'GET') {
    $userId = intval($m[1]);
    $list = array_values(array_filter($db['notifications'], function($n) use ($userId) { 
        return $n['user_id'] === $userId || $n['user_id'] === 0; 
    }));
    sendJSON($list);
}

if ($route === '/api/notifications/read' && $method === 'POST') {
    $userId = intval($input['userId'] ?? 0);
    foreach ($db['notifications'] as &$n) {
        if ($n['user_id'] === $userId || $n['user_id'] === 0) {
            $n['is_read'] = 1;
        }
    }
    saveDB($db);
    sendJSON(["success" => true]);
}

if ($route === '/api/notifications/bulk' && $method === 'POST') {
    $userIdRaw = $input['user_id'] ?? 'all';
    $title = $input['title'] ?? '';
    $message = $input['message'] ?? '';
    
    $targets = [];
    if ($userIdRaw === 'all') {
        foreach ($db['users'] as $u) $targets[] = $u['id'];
        $targets[] = 0; // Add global broadcast target ID!
    } else {
        $targets[] = intval($userIdRaw);
    }
    
    foreach ($targets as $tid) {
        $newId = count($db['notifications']) > 0 ? max(array_column($db['notifications'], 'id')) + 1 : 1;
        $db['notifications'][] = [
            "id" => $newId,
            "user_id" => $tid,
            "title" => $title,
            "message" => $message,
            "is_read" => 0,
            "created_at" => date(DATE_ATOM)
        ];
    }
    saveDB($db);
    sendJSON(["success" => true]);
}

if (($route === '/api/notifications/create' || $route === '/api/notifications') && $method === 'POST') {
    $userId = intval($input['userId'] ?? $input['user_id'] ?? 0);
    $title = $input['title'] ?? '';
    $message = $input['message'] ?? '';
    
    $newId = count($db['notifications']) > 0 ? max(array_column($db['notifications'], 'id')) + 1 : 1;
    $db['notifications'][] = [
        "id" => $newId,
        "user_id" => $userId,
        "title" => $title,
        "message" => $message,
        "is_read" => 0,
        "created_at" => date(DATE_ATOM)
    ];
    saveDB($db);
    sendJSON(["success" => true]);
}

// 12. Support Queries / Customer Message Board / Ticket Service
if ($route === '/api/support/query' && $method === 'POST') {
    $name = $input['name'] ?? '';
    $email = $input['email'] ?? '';
    $subject = $input['subject'] ?? '';
    $message = $input['message'] ?? '';
    
    if (empty($db['queries'])) {
        $db['queries'] = [];
    }
    
    $newId = count($db['queries']) > 0 ? max(array_column($db['queries'], 'id')) + 1 : 1;
    $newQuery = [
        "id" => $newId,
        "name" => $name,
        "email" => $email,
        "subject" => $subject,
        "message" => $message,
        "reply_text" => "",
        "is_responded" => false,
        "created_at" => date(DATE_ATOM)
    ];
    $db['queries'][] = $newQuery;
    saveDB($db);
    sendJSON(["success" => true, "query" => $newQuery]);
}

if ($route === '/api/support/queries' && $method === 'GET') {
    if (empty($db['queries'])) {
        $db['queries'] = [];
    }
    sendJSON($db['queries']);
}

if ($route === '/api/support/reply' && $method === 'POST') {
    $id = intval($input['id'] ?? 0);
    $reply_text = $input['reply_text'] ?? '';
    
    if (empty($db['queries'])) {
        $db['queries'] = [];
    }
    
    $foundIndex = -1;
    foreach ($db['queries'] as $idx => $q) {
        if ($q['id'] === $id) {
            $foundIndex = $idx;
            break;
        }
    }
    
    if ($foundIndex !== -1) {
        $db['queries'][$foundIndex]['reply_text'] = $reply_text;
        $db['queries'][$foundIndex]['is_responded'] = true;
        
        $queryObj = $db['queries'][$foundIndex];
        
        if (empty($db['sent_emails'])) {
            $db['sent_emails'] = [];
        }
        
        $emailId = count($db['sent_emails']) > 0 ? max(array_column($db['sent_emails'], 'id')) + 1 : 1;
        $db['sent_emails'][] = [
            "id" => $emailId,
            "user_id" => 0,
            "email" => $queryObj['email'],
            "subject" => "RE: " . $queryObj['subject'] . " | DigitalMohan Help Desk",
            "body" => "Hi " . $queryObj['name'] . ",\n\nOur support team has reviewed your query:\n\"" . $queryObj['message'] . "\"\n\nResponse:\n" . $reply_text . "\n\nHope this helps! Let us know if you have further concerns.\n\nWarm regards,\nDigitalMohan Customer Relations",
            "created_at" => date(DATE_ATOM)
        ];
        
        saveDB($db);
        sendJSON(["success" => true, "query" => $queryObj]);
    } else {
        sendError("Query not found", 404);
    }
}

// 13. Email Newsletters & Subscribers
if ($route === '/api/newsletter/subscribe' && $method === 'POST') {
    $email = $input['email'] ?? '';
    if (empty($db['subscribers'])) {
        $db['subscribers'] = [];
    }
    
    $exists = false;
    foreach ($db['subscribers'] as $s) {
        if (strtolower($s['email']) === strtolower($email)) {
            $exists = true;
            break;
        }
    }
    
    if ($exists) {
        sendJSON(["success" => true, "message" => "You are already a newsletter subscriber!"]);
    }
    
    $newSubId = count($db['subscribers']) > 0 ? max(array_column($db['subscribers'], 'id')) + 1 : 1;
    $newSub = [
        "id" => $newSubId,
        "email" => $email,
        "subscribed_at" => date(DATE_ATOM)
    ];
    $db['subscribers'][] = $newSub;
    
    if (empty($db['sent_emails'])) {
        $db['sent_emails'] = [];
    }
    
    $emailId = count($db['sent_emails']) > 0 ? max(array_column($db['sent_emails'], 'id')) + 1 : 1;
    $db['sent_emails'][] = [
        "id" => $emailId,
        "user_id" => 0,
        "email" => $email,
        "subject" => "Welcome to DigitalMohan Newsletters! 🎁",
        "body" => "Hi there,\n\nWe're thrilled to have you subscribe to our launches & promotional deals center!\n\nYou'll be the first to know when a new Canva design, prompt directory, or revising JEE formulae is added.\n\nAs a welcome offer, use code SAVE50 to get 50% discount on any purchase!\n\nDigitalMohan Marketing Team",
        "created_at" => date(DATE_ATOM)
    ];
    
    saveDB($db);
    sendJSON(["success" => true, "message" => "Subscribed successfully! Check email logs for welcome offer."]);
}

if ($route === '/api/newsletter/subscribers' && $method === 'GET') {
    if (empty($db['subscribers'])) {
        $db['subscribers'] = [];
    }
    sendJSON($db['subscribers']);
}

if ($route === '/api/newsletter/send-campaign' && $method === 'POST') {
    $subject = $input['subject'] ?? '';
    $body = $input['body'] ?? '';
    
    if (empty($db['subscribers'])) $db['subscribers'] = [];
    if (empty($db['campaigns'])) $db['campaigns'] = [];
    if (empty($db['sent_emails'])) $db['sent_emails'] = [];
    if (empty($db['notifications'])) $db['notifications'] = [];
    
    $campId = count($db['campaigns']) > 0 ? max(array_column($db['campaigns'], 'id')) + 1 : 1;
    $db['campaigns'][] = [
        "id" => $campId,
        "subject" => $subject,
        "body" => $body,
        "sent_at" => date(DATE_ATOM)
    ];
    
    foreach ($db['subscribers'] as $sub) {
        $emailId = count($db['sent_emails']) > 0 ? max(array_column($db['sent_emails'], 'id')) + 1 : 1;
        $db['sent_emails'][] = [
            "id" => $emailId,
            "user_id" => 0,
            "email" => $sub['email'],
            "subject" => $subject,
            "body" => $body,
            "created_at" => date(DATE_ATOM)
        ];
    }
    
    foreach ($db['users'] as $u) {
        $notifyId = count($db['notifications']) > 0 ? max(array_column($db['notifications'], 'id')) + 1 : 1;
        $db['notifications'][] = [
            "id" => $notifyId,
            "user_id" => $u['id'],
            "title" => "📢 New Campaign: " . $subject,
            "message" => "We've dispatched an email newsletter with exclusive offers! Check your subscription logs.",
            "is_read" => 0,
            "created_at" => date(DATE_ATOM)
        ];
    }
    
    saveDB($db);
    sendJSON(["success" => true, "sent_count" => count($db['subscribers'])]);
}

// 14. Support Chatbot with Gemini / Fallback Support
if ($route === '/api/support-chat' && $method === 'POST') {
    $message = $input['message'] ?? '';
    $history = $input['history'] ?? [];
    
    // Check key in env
    $apiKey = getenv('GEMINI_API_KEY');
    if (empty($apiKey) && isset($_ENV['GEMINI_API_KEY'])) {
        $apiKey = $_ENV['GEMINI_API_KEY'];
    }
    if (empty($apiKey) && isset($_SERVER['GEMINI_API_KEY'])) {
        $apiKey = $_SERVER['GEMINI_API_KEY'];
    }
    
    if (empty($apiKey)) {
        // Fallback or simulation responses
        $reply = "AI Support Chat is running in simulation mode. How can we help you today with your DigitalMohan products, receipts, or refund regulations? (Tip: Set GEMINI_API_KEY in Secrets for live AI responses!)";
        
        $lower = strtolower($message);
        if (strpos($lower, 'refund') !== false || strpos($lower, 'return') !== false) {
            $reply = "Our policy includes a 7-day refund guarantee if your digital assets have download/corrupt file issues! Refund request will be handled in less than 4 hours via our ticket dispatcher.";
        } else if (strpos($lower, 'receipt') !== false || strpos($lower, 'invoice') !== false) {
            $reply = "You can download full PDF invoices & receipts anytime in your Profile section! Tap invoice PDF beside your successful transactions.";
        } else if (strpos($lower, 'download') !== false || strpos($lower, 'format') !== false || strpos($lower, 'template') !== false) {
            $reply = "All products (Canva carousels, prompt folders, physics sheets) are instantly available under 'My Downloads' once your payment is successfully completed. Check your user dashboard!";
        } else if (strpos($lower, 'hi') !== false || strpos($lower, 'hello') !== false) {
            $reply = "Hello! I am your DigitalMohan Support Agent. Ask me anything about template links, orders, PDF receipt logs, or refund rules! ⚡";
        }
        
        sendJSON(["response" => $reply]);
    } else {
        // Build JSON contents payload
        $contents = [];
        if (is_array($history)) {
            foreach ($history as $item) {
                $role = ($item['role'] === 'user') ? 'user' : 'model';
                $contents[] = [
                    "role" => $role,
                    "parts" => [["text" => $item['text']]]
                ];
            }
        }
        $contents[] = [
            "role" => "user",
            "parts" => [["text" => $message]]
        ];
        
        $payload = [
            "contents" => $contents,
            "systemInstruction" => [
                "parts" => [[
                    "text" => "You are \"DigitalMohan Support Agent AI\", a smart customer support assistant for DigitalMohan. " .
                             "DigitalMohan is a premium digital product hub selling ChatGPT Prompt directories, Resume templates, study notes, and Canva layouts. " .
                             "Answer customer questions with clarity: " .
                             "- Order access: Files are unlocked instantly upon payment. View PDF receipt or find downloads in Profile > Saved Library. " .
                             "- Refund Policy: 7-day refund guarantee if files fail to download or are corrupted. Replacement is instant. " .
                             "- Payments: Secure simulated Razorpay gate channel. " .
                             "Be helpful and extremely concise. Respond in simple formatted markdown (lists/bold text are fine, keep spacing tight)."
                ]]
            ],
            "generationConfig" => [
                "temperature" => 0.6
            ]
        ];
        
        $ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . urlencode($apiKey));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "User-Agent: aistudio-build"]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        
        $resp = curl_exec($ch);
        curl_close($ch);
        
        $responseText = null;
        if ($resp) {
            $apiData = json_decode($resp, true);
            $responseText = $apiData['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }
        
        if ($responseText) {
            sendJSON(["response" => $responseText]);
        } else {
            sendJSON(["response" => "Hello! Our live chat lines are experiencing high volume. You can submit a query ticket above or use instant WhatsApp connect!"]);
        }
    }
}

// 15. Abandoned Wishlist/Cart Automated Trigger 
if (preg_match('#^/api/wishlist/check-abandoned/([0-9]+)$#', $route, $m) && $method === 'GET') {
    $userId = intval($m[1]);
    if (!$userId) {
        sendJSON(["has_abandoned" => false]);
    }
    
    $user = null;
    foreach ($db['users'] as $u) {
        if ($u['id'] === $userId) {
            $user = $u;
            break;
        }
    }
    if (!$user) {
        sendJSON(["has_abandoned" => false]);
    }
    
    $userWish = [];
    foreach ($db['wishlist'] as $w) {
        if ($w['user_id'] === $userId) {
            $userWish[] = $w;
        }
    }
    
    if (empty($userWish)) {
        sendJSON(["has_abandoned" => false]);
    }
    
    $orderedProductIds = [];
    foreach ($db['orders'] as $o) {
        if ($o['user_id'] === $userId && $o['status'] === 'successful') {
            $orderedProductIds[] = $o['product_id'];
        }
    }
    
    $abandonedWish = [];
    foreach ($userWish as $w) {
        if (!in_array($w['product_id'], $orderedProductIds)) {
            $abandonedWish[] = $w;
        }
    }
    
    if (empty($abandonedWish)) {
        sendJSON(["has_abandoned" => false]);
    }
    
    $recentAbandon = $abandonedWish[count($abandonedWish) - 1];
    $prod = null;
    foreach ($db['products'] as $p) {
        if ($p['id'] === $recentAbandon['product_id']) {
            $prod = $p;
            break;
        }
    }
    
    if (!$prod) {
        sendJSON(["has_abandoned" => false]);
    }
    
    if (empty($db['sent_emails'])) $db['sent_emails'] = [];
    if (empty($db['notifications'])) $db['notifications'] = [];
    
    $emailExists = false;
    foreach ($db['sent_emails'] as $e) {
        if ($e['user_id'] === $userId && strpos($e['subject'], 'wishlist') !== false) {
            $emailExists = true;
            break;
        }
    }
    
    if (!$emailExists) {
        $notifyId = count($db['notifications']) > 0 ? max(array_column($db['notifications'], 'id')) + 1 : 1;
        $db['notifications'][] = [
            "id" => $notifyId,
            "user_id" => $userId,
            "title" => "🛒 Items waiting in Cart!",
            "message" => "Don't forget! \"" . $prod['title'] . "\" is in your wishlist. Order today with code SAVE50 to get 50% discount!",
            "is_read" => 0,
            "created_at" => date(DATE_ATOM)
        ];
        
        $emailId = count($db['sent_emails']) > 0 ? max(array_column($db['sent_emails'], 'id')) + 1 : 1;
        $db['sent_emails'][] = [
            "id" => $emailId,
            "user_id" => $userId,
            "email" => $user['email'],
            "subject" => "You left something special in your DigitalMohan wishlist! 🛍️",
            "body" => "Hi " . $user['name'] . ",\n\nWe noticed you left \"" . $prod['title'] . "\" in your secure wishlist.\n\nDon't let it slip away! We have active promotions on formula sheets, resume packs, and Canva layouts today.\n\nApply code SAVE50 on the secure checkout link to claim an instant 50% Discount:\nhttps://" . ($_SERVER['HTTP_HOST'] ?? 'digitalmohan.com') . "/buy.php?id=" . $prod['id'] . "\n\nDigitalMohan Support",
            "created_at" => date(DATE_ATOM)
        ];
        
        saveDB($db);
    }
    
    sendJSON([
        "has_abandoned" => true,
        "product" => [
            "id" => $prod['id'],
            "title" => $prod['title'],
            "price" => $prod['price'],
            "image" => $prod['image']
        ]
    ]);
}

// 16. Inbox emails viewing
if (preg_match('#^/api/emails/([^/]+)$#', $route, $m) && $method === 'GET') {
    $email = urldecode($m[1]);
    if (empty($db['sent_emails'])) {
        $db['sent_emails'] = [];
    }
    
    $filtered = [];
    foreach ($db['sent_emails'] as $e) {
        if (strtolower($e['email']) === strtolower($email)) {
            $filtered[] = $e;
        }
    }
    sendJSON($filtered);
}

// --- AFFILIATE SYSTEM ENDPOINTS ---

if ($route === '/api/affiliate/click' && $method === 'POST') {
    $ref = $input['ref'] ?? '';
    if (empty($ref)) {
        sendError("Referral code required", 400);
    }
    
    if (empty($db['affiliates'])) $db['affiliates'] = [];
    
    $foundIdx = -1;
    $refInt = intval($ref);
    foreach ($db['affiliates'] as $idx => $a) {
        if ($a['user_id'] === $refInt || $a['code'] === $ref) {
            $foundIdx = $idx;
            break;
        }
    }
    
    if ($foundIdx === -1) {
        foreach ($db['users'] as $u) {
            $parts = explode('@', $u['email']);
            if (strtolower($parts[0]) === strtolower($ref)) {
                foreach ($db['affiliates'] as $idx => $a) {
                    if ($a['user_id'] === $u['id']) {
                        $foundIdx = $idx;
                        break 2;
                    }
                }
            }
        }
    }
    
    if ($foundIdx !== -1) {
        $db['affiliates'][$foundIdx]['clicks'] = intval($db['affiliates'][$foundIdx]['clicks'] ?? 0) + 1;
        saveDB($db);
        sendJSON(["success" => true, "clicks" => $db['affiliates'][$foundIdx]['clicks']]);
    }
    
    sendJSON(["success" => false, "message" => "No active affiliate linked under this code"]);
}

if ($route === '/api/affiliate/join' && $method === 'POST') {
    $userId = intval($input['user_id'] ?? 0);
    $upiId = $input['upi_id'] ?? '';
    if (!$userId) {
        sendError("User ID required", 400);
    }
    
    if (empty($db['users'])) $db['users'] = [];
    if (empty($db['affiliates'])) $db['affiliates'] = [];
    
    $user = null;
    foreach ($db['users'] as $u) {
        if ($u['id'] === $userId) {
            $user = $u;
            break;
        }
    }
    
    if (!$user && isset($input['email'])) {
        $email = $input['email'];
        foreach ($db['users'] as $u) {
            if (strtolower($u['email']) === strtolower($email)) {
                $user = $u;
                break;
            }
        }
    }
    
    if (!$user && isset($input['email'])) {
        $user = [
            "id" => $userId,
            "name" => $input['name'] ?? 'Affiliate Partner',
            "email" => $input['email'],
            "phone" => $input['phone'] ?? '',
            "password" => "password123",
            "status" => "active",
            "created_at" => date(DATE_ATOM)
        ];
        $db['users'][] = $user;
        saveDB($db);
    }
    
    $affiliate = null;
    foreach ($db['affiliates'] as &$a) {
        if ($a['user_id'] === $userId) {
            if (!empty($upiId)) {
                $a['uupi'] = $upiId;
                saveDB($db);
            }
            sendJSON(["success" => true, "message" => "Already an affiliate partner", "affiliate" => $a]);
        }
    }
    
    if (!$user) {
        sendError("User not found", 404);
    }
    
    $parts = explode('@', $user['email']);
    $affCode = strtolower($parts[0]) . "_" . $userId;
    
    $newId = count($db['affiliates']) > 0 ? max(array_column($db['affiliates'], 'id')) + 1 : 1;
    $newAff = [
        "id" => $newId,
        "user_id" => $userId,
        "code" => $affCode,
        "balance" => 0,
        "total_earned" => 0,
        "total_withdrawn" => 0,
        "clicks" => 1,
        "uupi" => $upiId,
        "status" => "active",
        "created_at" => date(DATE_ATOM)
    ];
    
    $db['affiliates'][] = $newAff;
    
    if (empty($db['notifications'])) $db['notifications'] = [];
    $notifyId = count($db['notifications']) > 0 ? max(array_column($db['notifications'], 'id')) + 1 : 1;
    $db['notifications'][] = [
        "id" => $notifyId,
        "user_id" => $userId,
        "title" => "Affiliate Activated! 🤝",
        "message" => "Congratulations! Your brand affiliate dashboard is ready. Share links to earn 20% commission on every successful template lock order sale!",
        "is_read" => 0,
        "created_at" => date(DATE_ATOM)
    ];
    
    saveDB($db);
    sendJSON(["success" => true, "affiliate" => $newAff]);
}

if (preg_match('#^/api/affiliate/stats/([0-9]+)$#', $route, $m) && $method === 'GET') {
    $userId = intval($m[1]);
    
    if (empty($db['affiliates'])) $db['affiliates'] = [];
    if (empty($db['affiliate_commissions'])) $db['affiliate_commissions'] = [];
    if (empty($db['affiliate_payouts'])) $db['affiliate_payouts'] = [];
    
    $affiliate = null;
    foreach ($db['affiliates'] as $a) {
        if ($a['user_id'] === $userId) {
            $affiliate = $a;
            break;
        }
    }
    
    if (!$affiliate) {
        sendJSON(["active" => false]);
    }
    
    $commissions = [];
    foreach ($db['affiliate_commissions'] as $c) {
        if ($c['affiliate_id'] === $userId) {
            $commissions[] = $c;
        }
    }
    
    $payouts = [];
    foreach ($db['affiliate_payouts'] as $p) {
        if ($p['affiliate_id'] === $userId) {
            $payouts[] = $p;
        }
    }
    
    sendJSON([
        "active" => true,
        "affiliate" => $affiliate,
        "commissions" => $commissions,
        "payouts" => $payouts
    ]);
}

if ($route === '/api/affiliate/payout/request' && $method === 'POST') {
    $userId = intval($input['user_id'] ?? 0);
    $amount = intval($input['amount'] ?? 0);
    $method_pay = $input['payment_method'] ?? 'UPI';
    $details = $input['details'] ?? '';
    
    if (!$userId || !$amount) {
        sendError("User ID and amount required", 400);
    }
    
    if (empty($db['affiliates'])) $db['affiliates'] = [];
    if (empty($db['affiliate_payouts'])) $db['affiliate_payouts'] = [];
    
    $foundIdx = -1;
    foreach ($db['affiliates'] as $idx => $a) {
        if ($a['user_id'] === $userId) {
            $foundIdx = $idx;
            break;
        }
    }
    
    if ($foundIdx === -1) {
        sendError("Affiliate account not activated", 404);
    }
    
    $affiliate = &$db['affiliates'][$foundIdx];
    if ($amount <= 0) {
        sendError("Invalid payout amount", 400);
    }
    if ($affiliate['balance'] < $amount) {
        sendError("Insufficient available balance", 400);
    }
    
    $newPayoutId = count($db['affiliate_payouts']) > 0 ? max(array_column($db['affiliate_payouts'], 'id')) + 1 : 1;
    $newPayout = [
        "id" => $newPayoutId,
        "affiliate_id" => $userId,
        "amount" => $amount,
        "payment_method" => $method_pay,
        "details" => !empty($details) ? $details : ($affiliate['uupi'] ?? ''),
        "status" => "pending",
        "created_at" => date(DATE_ATOM)
    ];
    
    $db['affiliate_payouts'][] = $newPayout;
    $affiliate['balance'] -= $amount;
    
    saveDB($db);
    sendJSON(["success" => true, "payout" => $newPayout, "current_balance" => $affiliate['balance']]);
}

if ($route === '/api/admin/affiliates/all' && $method === 'GET') {
    if (empty($db['affiliates'])) $db['affiliates'] = [];
    
    $list = [];
    foreach ($db['affiliates'] as $a) {
        $user_name = "Unknown Partner";
        $user_email = "deleted@user.com";
        foreach ($db['users'] as $u) {
            if ($u['id'] === $a['user_id']) {
                $user_name = $u['name'];
                $user_email = $u['email'];
                break;
            }
        }
        $a['user_name'] = $user_name;
        $a['user_email'] = $user_email;
        $list[] = $a;
    }
    sendJSON($list);
}

if ($route === '/api/admin/affiliates/payouts' && $method === 'GET') {
    if (empty($db['affiliate_payouts'])) $db['affiliate_payouts'] = [];
    
    $list = [];
    foreach ($db['affiliate_payouts'] as $p) {
        $user_name = "Unknown Partner";
        $user_email = "deleted@user.com";
        foreach ($db['users'] as $u) {
            if ($u['id'] === $p['affiliate_id']) {
                $user_name = $u['name'];
                $user_email = $u['email'];
                break;
            }
        }
        $p['user_name'] = $user_name;
        $p['user_email'] = $user_email;
        $list[] = $p;
    }
    sendJSON($list);
}

if ($route === '/api/admin/affiliates/payouts/process' && $method === 'POST') {
    $payoutId = intval($input['payout_id'] ?? 0);
    $status = $input['status'] ?? ''; // completed or rejected
    
    if (!$payoutId || empty($status)) {
        sendError("Payout ID and status required", 400);
    }
    
    if (empty($db['affiliate_payouts'])) $db['affiliate_payouts'] = [];
    if (empty($db['affiliates'])) $db['affiliates'] = [];
    if (empty($db['notifications'])) $db['notifications'] = [];
    
    $pIndex = -1;
    foreach ($db['affiliate_payouts'] as $idx => $p) {
        if ($p['id'] === $payoutId) {
            $pIndex = $idx;
            break;
        }
    }
    
    if ($pIndex === -1) {
        sendError("Payout request not found", 404);
    }
    
    $payout = &$db['affiliate_payouts'][$pIndex];
    if ($payout['status'] !== 'pending') {
        sendError("Payout has already been processed", 400);
    }
    
    $aIndex = -1;
    foreach ($db['affiliates'] as $idx => $a) {
        if ($a['user_id'] === $payout['affiliate_id']) {
            $aIndex = $idx;
            break;
        }
    }
    
    if ($status === 'completed') {
        $payout['status'] = 'completed';
        if ($aIndex !== -1) {
            $db['affiliates'][$aIndex]['total_withdrawn'] = intval($db['affiliates'][$aIndex]['total_withdrawn'] ?? 0) + $payout['amount'];
        }
        
        $notifyId = count($db['notifications']) > 0 ? max(array_column($db['notifications'], 'id')) + 1 : 1;
        $db['notifications'][] = [
            "id" => $notifyId,
            "user_id" => $payout['affiliate_id'],
            "title" => "Payout Disbursed! 💳",
            "message" => "Your requested payout of ₹" . $payout['amount'] . " has been successfully processed and transferred to your bank coordinates.",
            "is_read" => 0,
            "created_at" => date(DATE_ATOM)
        ];
    } else if ($status === 'rejected') {
        $payout['status'] = 'rejected';
        if ($aIndex !== -1) {
            $db['affiliates'][$aIndex]['balance'] = intval($db['affiliates'][$aIndex]['balance'] ?? 0) + $payout['amount'];
        }
        
        $notifyId = count($db['notifications']) > 0 ? max(array_column($db['notifications'], 'id')) + 1 : 1;
        $db['notifications'][] = [
            "id" => $notifyId,
            "user_id" => $payout['affiliate_id'],
            "title" => "Payout Request Declined ❌",
            "message" => "Your requested payout of ₹" . $payout['amount'] . " was returned to your balance. Please check your UPI / banking details.",
            "is_read" => 0,
            "created_at" => date(DATE_ATOM)
        ];
    }
    
    saveDB($db);
    sendJSON(["success" => true, "payout" => $payout]);
}

sendError("Path '{$route}' not found by API processor", 404);
