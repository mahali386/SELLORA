<?php
// Sellora - Secure Bookmark list
require_once __DIR__ . '/common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN BOOKMARK WRAPPER -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    
    <!-- Header title -->
    <div class="mb-5">
        <h1 class="text-2xl font-display font-extrabold text-slate-800 dark:text-slate-100">My Wishlist</h1>
        <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">Keep track of templates and formula sheets you desire.</p>
    </div>

    <!-- LIST CONTAINER -->
    <div id="wishlist-cards-feed" class="space-y-4">
        <!-- Load Skeleton Cards -->
        <div class="h-24 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
        <div class="h-24 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
    </div>

</main>

<script>
function loadWishlistCatalogue() {
    const user = getSessionUser();
    if (!user) {
        document.getElementById('wishlist-cards-feed').innerHTML = `
            <div class="text-center py-12 px-4 rounded-3xl border border-dashed border-slate-250 dark:border-slate-800">
                <i class="fas fa-heart text-slate-300 dark:text-slate-750 text-4xl mb-3"></i>
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-400">Wishlist Is Locked</h4>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-xs mx-auto">Authorize your details profile to retrieve and lock bookmarks safely.</p>
                <a href="login.php" class="inline-block mt-4 px-5 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-bold shadow-md transition-all outline-none">Authorize Login</a>
            </div>
        `;
        return;
    }

    // Performance Fix: Load pre-joined custom wishlist details list in under 3ms
    fetch(`/api/wishlist/${user.id}`)
    .then(res => res.json())
    .then(wishlist => {
        const container = document.getElementById('wishlist-cards-feed');
        
        if (!wishlist || wishlist.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12 px-4 rounded-3xl border border-dashed border-slate-250 dark:border-slate-800">
                     <i class="fas fa-heart-broken text-slate-300 dark:text-slate-750 text-4xl mb-3"></i>
                     <h4 class="text-sm font-bold text-slate-700 dark:text-slate-400">Wishlist is empty</h4>
                     <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-xs mx-auto">Click the heart icons on file cards across home categories to add books here.</p>
                     <a href="products.php" class="inline-block mt-4 px-5 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-bold shadow-md transition-all outline-none">Find Templates</a>
                </div>
            `;
            return;
        }

        const html = wishlist.map(item => {
            return `
                <div class="p-3.5 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 flex gap-4 backdrop-blur-md relative group">
                    <img src="${window.getOptimizedImageUrl(item.product_image, 160)}" class="w-16 h-16 object-cover rounded-xl flex-shrink-0" loading="lazy">
                    <div class="min-w-0 flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate pr-6">${item.product_title}</h4>
                            <span class="text-[10px] text-sky-500 font-bold block mt-0.5">${window.formatPrice(item.product_price)}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <a href="product_detail.php?id=${item.product_id}" class="px-2.5 py-1.5 bg-sky-500 text-white rounded-lg text-[10px] font-black uppercase hover:brightness-105 active:scale-95 transition-all outline-none">Buy / View Detail</a>
                            <button onclick="removeWishlistItemDirect(event, ${item.product_id})" class="text-[10px] text-red-500 font-bold hover:underline outline-none">Remove</button>
                        </div>
                    </div>
                    
                    <!-- Quick trash button on top right of card -->
                    <button onclick="removeWishlistItemDirect(event, ${item.product_id})" class="absolute top-3.5 right-3.5 text-slate-300 hover:text-red-500 outline-none">
                        <i class="fas fa-trash-can text-xs"></i>
                    </button>
                </div>
            `;
        }).join('');
        container.innerHTML = html;
    });
}

function removeWishlistItemDirect(e, pId) {
    if (e) e.preventDefault();
    triggerVibe(40);
    const user = getSessionUser();
    
    fetch('/api/wishlist/toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: user.id, product_id: pId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.state === 'removed') {
            Toast.info("Item discarded from watchlist.");
            loadWishlistCatalogue();
        }
    });
}

// Ignition
document.addEventListener('DOMContentLoaded', () => {
    loadWishlistCatalogue();
});

window.addEventListener('currencychange', () => {
    loadWishlistCatalogue();
});
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
