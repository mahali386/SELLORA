<!-- Admin bottom nav bar for mobile devices control shells -->
<div class="fixed bottom-0 left-0 right-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg border-t border-slate-200/50 dark:border-white/5 pb-safe rounded-t-3xl shadow-[0_-8px_30px_rgb(0,0,0,0.04)]">
    <div class="max-w-md mx-auto px-6 h-16 flex items-center justify-between relative">
        
        <!-- Dashboard Home -->
        <a href="index.php" id="adminNav-home" class="flex flex-col items-center justify-center flex-1 h-full text-slate-400 dark:text-slate-500 hover:text-sky-500 font-bold transition-all outline-none" onclick="triggerVibe(20)">
            <i class="fas fa-chart-line text-lg mb-1"></i>
            <span class="text-[9px]">Metrics</span>
        </a>
        
        <!-- Products catalogue list -->
        <a href="products.php" id="adminNav-products" class="flex flex-col items-center justify-center flex-1 h-full text-slate-400 dark:text-slate-500 hover:text-sky-500 font-bold transition-all outline-none" onclick="triggerVibe(20)">
            <i class="fas fa-file-shield text-lg mb-1"></i>
            <span class="text-[9px]">Files</span>
        </a>
        
        <!-- Orders list log -->
        <a href="orders.php" id="adminNav-orders" class="flex flex-col items-center justify-center flex-1 h-full text-slate-400 dark:text-slate-500 hover:text-sky-500 font-bold transition-all outline-none" onclick="triggerVibe(20)">
            <i class="fas fa-receipt text-lg mb-1"></i>
            <span class="text-[9px]">Orders</span>
        </a>

        <!-- Coupons list log -->
        <a href="coupons.php" id="adminNav-coupons" class="flex flex-col items-center justify-center flex-1 h-full text-slate-400 dark:text-slate-500 hover:text-sky-500 font-bold transition-all outline-none" onclick="triggerVibe(20)">
            <i class="fas fa-ticket-simple text-lg mb-1"></i>
            <span class="text-[9px]">Promo</span>
        </a>
        
        <!-- Settings list log -->
        <a href="settings.php" id="adminNav-settings" class="flex flex-col items-center justify-center flex-1 h-full text-slate-400 dark:text-slate-500 hover:text-sky-500 font-bold transition-all outline-none" onclick="triggerVibe(20)">
            <i class="fas fa-sliders-h text-lg mb-1"></i>
            <span class="text-[9px]">Configs</span>
        </a>
        
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const p = window.location.pathname.split('/').pop() || 'index.php';
    let idValue = 'adminNav-home';
    
    if (p.includes('products.php')) {
        idValue = 'adminNav-products';
    } else if (p.includes('orders.php')) {
        idValue = 'adminNav-orders';
    } else if (p.includes('coupons.php')) {
        idValue = 'adminNav-coupons';
    } else if (p.includes('settings.php') || p.includes('users.php') || p.includes('categories.php') || p.includes('payouts.php')) {
        idValue = 'adminNav-settings';
    }
    
    const activeBtn = document.getElementById(idValue);
    if (activeBtn) {
        activeBtn.classList.remove('text-slate-400', 'dark:text-slate-500');
        activeBtn.classList.add('text-sky-500', 'scale-105');
    }
});
</script>
</body>
</html>
