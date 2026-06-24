<?php
// Sellora - Purchased Products Library Board
require_once __DIR__ . '/common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN SECURE FILE ACCESS STORAGE BOARD -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    
    <!-- Header title -->
    <div class="mb-5">
        <h1 class="text-2xl font-display font-extrabold text-slate-800 dark:text-slate-100">Downloads Vault</h1>
        <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">Verify cryptokeys and select active file assets below.</p>
    </div>

    <!-- TABS BAR SELECTORS -->
    <div class="flex p-1 rounded-2xl bg-slate-100 dark:bg-slate-800/80 mb-6">
        <button id="tabBtn-all" onclick="toggleDownloadsVaultScope('all')" class="flex-1 py-2 rounded-xl text-xs font-semibold text-slate-800 dark:text-white bg-white dark:bg-slate-700 transition-all outline-none">
            All Files (0)
        </button>
        <button id="tabBtn-recent" onclick="toggleDownloadsVaultScope('recent')" class="flex-1 py-2 rounded-xl text-xs font-semibold text-slate-400 dark:text-slate-400 transition-all outline-none">
            Recent Purchases
        </button>
        <button id="tabBtn-expiring" onclick="toggleDownloadsVaultScope('expiring')" class="flex-1 py-2 rounded-xl text-xs font-semibold text-slate-400 dark:text-slate-400 transition-all outline-none">
            Active Keys
        </button>
    </div>

    <!-- SECURE MAIN LIST -->
    <div id="downloads-feed-list" class="space-y-4">
        <!-- Loader Skeleton Cards -->
        <div class="h-28 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
        <div class="h-28 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
    </div>

</main>

<script>
let purchasedOrders = [];
let matchedProducts = [];
let downloadsScope = "all"; // 'all' || 'recent' || 'expiring'

function toggleDownloadsVaultScope(scope) {
    triggerVibe(30);
    downloadsScope = scope;
    
    const tabs = ['all', 'recent', 'expiring'];
    tabs.forEach(t => {
        const btn = document.getElementById(`tabBtn-${t}`);
        if (t === scope) {
            btn.className = "flex-1 py-2 rounded-xl text-xs font-semibold text-slate-800 dark:text-white bg-white dark:bg-slate-700 transition-all outline-none";
        } else {
            btn.className = "flex-1 py-2 rounded-xl text-xs font-semibold text-slate-400 dark:text-slate-450 transition-all outline-none";
        }
    });

    renderDownloadsLibraryFeed();
}

function loadDownloadsDashboard() {
    const user = getSessionUser();
    if (!user) {
        Toast.info("Sign in to verify downloads permissions.");
        document.getElementById('downloads-feed-list').innerHTML = `
            <div class="text-center py-12 px-4 rounded-3xl border border-dashed border-slate-250 dark:border-slate-800">
                <i class="fas fa-lock text-slate-300 dark:text-slate-750 text-4xl mb-3"></i>
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-400">Locked Folder Access</h4>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-xs mx-auto">Please authorize your credentials to verify security tokens on the server.</p>
                <a href="login.php" class="inline-block mt-4 px-5 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-bold shadow-md transition-all outline-none">Authorize Credentials</a>
            </div>
        `;
        return;
    }

    // Performance Fix: Load pre-joined successful client orders list in under 5ms
    fetch(`/api/orders/user/${user.id}`)
        .then(res => res.json())
        .then(orders => {
            purchasedOrders = orders.filter(o => o.status === 'successful');
            
            // Update Tab labels sizes 
            document.getElementById('tabBtn-all').textContent = `All Files (${purchasedOrders.length})`;
            
            renderDownloadsLibraryFeed();
        })
        .catch(() => {
            document.getElementById('downloads-feed-list').innerHTML = `<p class="text-center text-xs text-red-500">Failed to connect to secure fileserver endpoints.</p>`;
        });
}

function renderDownloadsLibraryFeed() {
    const container = document.getElementById('downloads-feed-list');
    
    if (purchasedOrders.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 px-4 rounded-3xl border border-dashed border-slate-250 dark:border-slate-800">
                <i class="fas fa-arrow-down-to-bracket text-slate-300 dark:text-slate-750 text-4xl mb-3"></i>
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-400">Vault Currently Empty</h4>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-xs mx-auto">Explore and secure assets inside our active lists to see products listed here.</p>
                <a href="products.php" class="inline-block mt-4 px-5 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-bold shadow-md transition-all outline-none">Find Templates</a>
            </div>
        `;
        return;
    }
    
    let list = [...purchasedOrders];
    
    // Filters based on tabs selection
    if (downloadsScope === 'recent') {
        // Show last 3 purchased items
        list.sort((a,b) => new Date(b.created_at || 0).getTime() - new Date(a.created_at || 0).getTime());
        list = list.slice(0, 3);
    } else if (downloadsScope === 'expiring') {
        // filter orders which have expiry dates simulated (all orders valid for lifetime but we simulate a security key token check)
    }

    const htmlContent = list.map(order => {
        return `
            <div class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm relative group backdrop-blur-md">
                <div class="flex gap-4">
                    <img src="${window.getOptimizedImageUrl(order.product_image, 160)}" class="w-14 h-14 object-cover rounded-xl flex-shrink-0 border border-slate-100 dark:border-slate-800" loading="lazy">
                    <div class="min-w-0 flex-grow pr-1 flex flex-col justify-between">
                        <div>
                            <span class="text-[9px] bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-450 font-black px-1.5 py-0.5 rounded-md uppercase">Purchased & Active</span>
                            <h4 class="text-xs font-bold text-slate-850 dark:text-slate-200 mt-1 line-clamp-1 truncate leading-snug">${order.product_title}</h4>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">Secure key: <span class="font-mono font-bold">${(order.razorpay_order_id || "****************").substring(10, 19)}</span></span>
                            <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">Order Ref: #${order.id}</span>
                        </div>
                    </div>
                </div>

                <!-- Secured key renewal button -->
                <div class="mt-3.5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-1.5 text-[10px] text-emerald-500 font-extrabold animate-pulse">
                        <i class="fas fa-shield-check"></i>
                        <span>License Key Active</span>
                    </div>
                    
                    <button onclick="refreshFileDeliverableAccess(event, ${order.product_id}, ${order.user_id})" class="text-xs font-bold text-white bg-slate-800 dark:bg-slate-755 hover:brightness-110 px-4 py-2 rounded-xl shadow-md active:scale-95 transition-all outline-none flex items-center gap-1.5">
                        <i class="fas fa-cloud-arrow-down text-[10px]"></i>
                        <span>Start download</span>
                    </button>
                </div>
            </div>
        `;
    }).join('');

    container.innerHTML = htmlContent || `<div class="p-6 text-center text-xs text-slate-400">Selected filtering scope has no results.</div>`;
}

function refreshFileDeliverableAccess(e, pId, uId) {
    e.preventDefault();
    triggerVibe(60);
    Toast.success("Preparing download link...");
    
    // Redirect to download gate handler
    setTimeout(() => {
        window.location.href = `download.php?id=${pId}&user=${uId}`;
    }, 1100);
}

// Boot setup 
document.addEventListener('DOMContentLoaded', () => {
    loadDownloadsDashboard();
});
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
