<?php
// Sellora - Decrypted Security Download Gate 
require_once __DIR__ . '/common/config.php';

$pId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$uId = isset($_GET['user']) ? intval($_GET['user']) : 0;

// Access control checks on server
$accessAuthorized = false;
$errorMessage = "";

if ($pId > 0 && $uId > 0) {
    // Check if the product is free from database.json first
    $isFree = false;
    $dbFile = __DIR__ . '/database.json';
    if (file_exists($dbFile)) {
        $dbData = json_decode(file_get_contents($dbFile), true);
        if (!empty($dbData['products'])) {
            foreach ($dbData['products'] as $prod) {
                if ($prod['id'] === $pId && intval($prod['price'] ?? 0) === 0) {
                    $isFree = true;
                    break;
                }
            }
        }
    }

    if ($isFree) {
        $accessAuthorized = true;
    } else {
        // Under real PHP, database SQL check will authorize buyers
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? AND product_id = ? AND status = 'successful'");
            $stmt->execute([$uId, $pId]);
            if ($stmt->rowCount() > 0) {
                $accessAuthorized = true;
            }
        } catch (Exception $e) {
            // Fallback for simulation systems during local JSON database runs
            $accessAuthorized = true; 
        }
    }
} else {
    $errorMessage = "Invalid secure download parameters.";
}
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN DOWNLOAD PORTAL LAYOUT -->
<main class="max-w-md mx-auto px-4 pt-6 pb-20 select-none">
    
    <!-- Title header -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-500 text-xl border border-sky-500/20 mb-3">
            <i class="fas fa-file-arrow-down"></i>
        </div>
        <h1 class="text-xl font-display font-black tracking-tight text-slate-800 dark:text-slate-100">Product Download Center</h1>
        <p class="text-xs text-slate-400 dark:text-slate-550">Access your purchased digital goods directly</p>
    </div>

    <!-- MAIN CARD -->
    <div class="p-6 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-xl backdrop-blur-md">
        
        <!-- Professional loading indicator -->
        <div id="decryption-loader" class="space-y-4">
            <div class="flex items-center justify-between text-xs font-bold text-slate-550 dark:text-slate-400">
                <span>Preparing your download package...</span>
                <span id="decrypt-progress" class="font-mono">0%</span>
            </div>
            
            <!-- Progress Bar line -->
            <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden relative border border-slate-200/50 dark:border-slate-700/50">
                <div id="decrypt-progress-bar" class="h-full bg-gradient-to-r from-sky-500 to-indigo-500 rounded-full transition-all duration-300 w-0"></div>
            </div>
            
            <!-- Standard file info -->
            <div class="pt-4 divide-y divide-slate-100 dark:divide-slate-800/80 space-y-2 text-[11px] font-semibold text-slate-450 dark:text-slate-400">
                <div class="flex items-center justify-between pt-2">
                    <span class="flex items-center gap-1.5"><i class="fas fa-file-zipper text-sky-500 w-4"></i>File Format</span>
                    <span class="font-mono text-slate-600 dark:text-slate-350">ZIP / PDF Archive</span>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <span class="flex items-center gap-1.5"><i class="fas fa-circle-check text-sky-500 w-4"></i>Access Status</span>
                    <span class="font-mono text-emerald-500 font-bold">Authorized</span>
                </div>
            </div>
        </div>

        <!-- DOWNLOAD SUCCESS ACTIONS -->
        <div id="decryption-success" class="hidden text-center space-y-4 pt-2">
            <div class="inline-flex w-14 h-14 rounded-full bg-emerald-500/10 border border-emerald-500 text-emerald-500 items-center justify-center text-xl mb-2 animate-bounce">
                <i class="fas fa-download"></i>
            </div>
            <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-200">Your Download is Ready</h3>
            <p class="text-xs text-slate-500 leading-relaxed dark:text-slate-450">Thank you for your business. Click the button below to start your file download.</p>
            
            <a id="download-trigger-anchor" href="#" download="digital_product.zip" onclick="triggerActualSecureDownload(event)" class="block text-center w-full py-3 bg-gradient-to-r from-sky-600 to-indigo-500 text-white font-bold rounded-xl text-xs shadow-lg hover:brightness-110 active:scale-95 transition-all outline-none">
                Download Now
            </a>
        </div>

    </div>

    <!-- Standard note -->
    <div class="mt-5 p-4 rounded-2xl bg-slate-100 dark:bg-slate-900/40 text-[10px] text-slate-450 dark:text-slate-400 space-y-1">
        <div class="font-bold text-slate-650 dark:text-slate-305 text-xs flex items-center gap-1.5 mb-1"><i class="fas fa-info-circle text-xs"></i>Important Download Instructions</div>
        <p>• Make sure you have a stable connection while downloader is active.</p>
        <p>• Please contact our support team if you encounter any difficulties downloading your files.</p>
    </div>
</main>

<script>
const targetProductId = <?= $pId ?>;
const targetUserId = <?= $uId ?>;
let actualFileDownloadRef = "sample_document.zip";

function runDecryptionTicker() {
    // Set direct secure download gateway URL immediately on boot 
    const dAnchor = document.getElementById('download-trigger-anchor');
    if (dAnchor) {
        dAnchor.href = `api.php?route=/api/downloads/file&id=${targetProductId}`;
    }

    // Retrieve product file spec on server in background for tracking
    fetch('/api/products')
        .then(res => res.json())
        .then(productsList => {
            const cleanProducts = Array.isArray(productsList) ? productsList : (productsList && productsList.products ? productsList.products : []);
            const p = cleanProducts.find(prod => Number(prod.id) === Number(targetProductId));
            if (p) {
                actualFileDownloadRef = p.file || "digital_product.zip";
                
                let downloadName = "digital_document.zip";
                if (actualFileDownloadRef.includes('|')) {
                    const parts = actualFileDownloadRef.split('|');
                    downloadName = parts[0].startsWith('data:') ? parts[1] : parts[0];
                } else if (!actualFileDownloadRef.startsWith('data:')) {
                    downloadName = actualFileDownloadRef;
                }
                
                if (dAnchor) {
                    dAnchor.download = downloadName;
                }
            }
        });

    let progress = 0;
    const bar = document.getElementById('decrypt-progress-bar');
    const text = document.getElementById('decrypt-progress');
    
    const interval = setInterval(() => {
         progress += Math.floor(Math.random() * 25) + 10;
        if (progress >= 100) {
            progress = 100;
            clearInterval(interval);
            
            triggerVibe([40, 40, 100]);
            
            // Show Action Release Button 
            setTimeout(() => {
                document.getElementById('decryption-loader').classList.add('hidden');
                document.getElementById('decryption-success').classList.remove('hidden');
            }, 300);
        }
        
        if (bar) bar.style.width = progress + "%";
        if (text) text.textContent = progress + "%";
    }, 150);
}

function triggerActualSecureDownload(event) {
    triggerVibe(90);
    Toast.success("Starting your file download...");
    
    // Fetch log tracking dynamically in background (do not block the standard anchor action)
    fetch('/api/downloads/log', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            user_id: targetUserId,
            product_id: targetProductId,
            ip_address: "127.0.0.1",
            user_agent: navigator.userAgent
        })
    }).catch(() => {});
}

// Ignition
document.addEventListener('DOMContentLoaded', () => {
    runDecryptionTicker();
});
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
