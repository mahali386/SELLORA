<!-- Modern Slide-out Sidebar Drawer -->
<div id="sidebar-drawer" class="fixed inset-y-0 left-0 w-80 max-w-[85vw] bg-white dark:bg-slate-950 shadow-2xl z-50 transform -translate-x-full transition-transform duration-300 ease-out flex flex-col justify-between overflow-y-auto">
    
    <!-- Sidebar Main content area -->
    <div class="px-5 py-6">
        
        <!-- Header area with logo & close cross button -->
        <div class="flex items-center justify-between pb-6 border-b border-slate-100 dark:border-white/5">
            <div class="flex items-center gap-2">
                <span class="w-3.5 h-3.5 rounded-full bg-sky-500"></span>
                <span class="font-display font-extrabold text-xl bg-gradient-to-r from-sky-600 to-indigo-500 bg-clip-text text-transparent app-hub-name-display">DigitalMohan Hub</span>
            </div>
            <button class="w-9 h-9 rounded-xl flex items-center justify-center bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-slate-500 hover:text-slate-700 dark:text-slate-400 outline-none" onclick="toggleSidebar()">
                <i class="fas fa-arrow-left"></i>
            </button>
        </div>
        
        <!-- User dynamic profile display card -->
        <div class="mt-6 p-4 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/50 dark:from-slate-900/60 dark:to-slate-900/10 border border-slate-150 dark:border-white/5">
            <div id="side-user-avatar" class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-sky-100 dark:bg-sky-950 flex items-center justify-center text-sky-600 dark:text-sky-400 font-bold text-lg">
                    <span id="side-user-initial">G</span>
                </div>
                <div class="min-w-0">
                    <h4 id="side-user-name" class="font-bold text-sm text-slate-800 dark:text-slate-200 truncate">Guest Buyer</h4>
                    <p id="side-user-email" class="text-xs text-slate-450 dark:text-slate-400 truncate">Sign-in to buy products</p>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Category-Based Link navigation list -->
        <nav class="mt-8 flex flex-col gap-1.5">
            <a href="index.php" class="flex items-center gap-3.5 p-3 rounded-xl text-slate-650 hover:bg-slate-50 dark:hover:bg-slate-900/80 hover:text-sky-500 font-medium transition-all text-sm outline-none" onclick="toggleSidebar()">
                <i class="fas fa-house-chimney text-slate-400 w-5"></i>
                <span>Explore Home</span>
            </a>
            
            <a href="products.php" class="flex items-center gap-3.5 p-3 rounded-xl text-slate-650 hover:bg-slate-50 dark:hover:bg-slate-900/80 hover:text-sky-500 font-medium transition-all text-sm outline-none" onclick="toggleSidebar()">
                <i class="fas fa-boxes-stacked text-slate-400 w-5"></i>
                <span>All Products</span>
            </a>
            
            <a href="blogs.php" class="flex items-center gap-3.5 p-3 rounded-xl text-slate-650 hover:bg-slate-50 dark:hover:bg-slate-900/80 hover:text-sky-500 font-medium transition-all text-sm outline-none" onclick="toggleSidebar()">
                <i class="fa-regular fa-newspaper text-slate-400 w-5"></i>
                <span>Knowledge Hub</span>
            </a>
            
            <a href="mydownloads.php" class="flex items-center gap-3.5 p-3 rounded-xl text-slate-650 hover:bg-slate-50 dark:hover:bg-slate-900/80 hover:text-sky-500 font-medium transition-all text-sm outline-none" onclick="toggleSidebar()">
                <i class="fas fa-circle-down text-slate-400 w-5"></i>
                <span>My Saved Orders</span>
            </a>
            
            <a href="wishlist.php" class="flex items-center gap-3.5 p-3 rounded-xl text-slate-650 hover:bg-slate-50 dark:hover:bg-slate-900/80 hover:text-sky-500 font-medium transition-all text-sm outline-none" onclick="toggleSidebar()">
                <i class="fas fa-heart text-slate-400 w-5"></i>
                <span>My Wishlist</span>
            </a>
            
            <a href="profile.php" class="flex items-center gap-3.5 p-3 rounded-xl text-slate-650 hover:bg-slate-50 dark:hover:bg-slate-900/80 hover:text-sky-500 font-medium transition-all text-sm outline-none" onclick="toggleSidebar()">
                <i class="fas fa-user-gear text-slate-400 w-5"></i>
                <span>Account Profile</span>
            </a>
            
            <a href="help.php" class="flex items-center gap-3.5 p-3 rounded-xl text-slate-650 hover:bg-slate-50 dark:hover:bg-slate-900/80 hover:text-sky-500 font-medium transition-all text-sm outline-none" onclick="toggleSidebar()">
                <i class="fas fa-circle-question text-slate-400 w-5"></i>
                <span>Help & FAQs</span>
            </a>
        </nav>
    </div>
    
    <!-- Footer / Logout logout row inside sidebar -->
    <div class="p-5 border-t border-slate-100 dark:border-white/5 flex flex-col gap-3">
        
        <!-- Inline Custom Currency Toggler -->
        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50">
            <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Display Currency</span>
            <select id="sidebar-currency-switch" onchange="window.setCurrentCurrency(this.value);" class="bg-white dark:bg-slate-800 text-xs font-black py-1 px-2.5 rounded-lg border-0 outline-none text-slate-750 dark:text-slate-200 cursor-pointer focus:ring-1 focus:ring-sky-500">
                <option value="INR">INR (₹)</option>
                <option value="USD">USD ($)</option>
                <option value="EUR">EUR (€)</option>
            </select>
        </div>

        <!-- Inline Theme Switcher Slider -->
        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50">
            <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Night mode ambient</span>
            <input type="checkbox" id="sidebar-dark-switch" class="sr-only peer" onchange="toggleThemeMode(); syncSidebarToggle()">
            <label for="sidebar-dark-switch" class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500 cursor-pointer"></label>
        </div>
        
        <!-- Auth Actions line -->
        <div id="side-auth-block">
            <a href="login.php" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-sky-500/10 text-sky-500 hover:bg-sky-500 hover:text-white transition-all text-sm font-semibold outline-none" onclick="triggerVibe(30)">
                <i class="fas fa-arrow-right-to-bracket"></i>
                <span>Sign In Account</span>
            </a>
        </div>
    </div>
</div>

<!-- Backdrop shadow click blocker for drawer -->
<div id="sidebar-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0" onclick="toggleSidebar()"></div>

<script>
function toggleSidebar() {
    triggerVibe(40);
    const sidebar = document.getElementById('sidebar-drawer');
    const backdrop = document.getElementById('sidebar-backdrop');
    
    const isOpen = !sidebar.classList.contains('-translate-x-full');
    
    if (isOpen) {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('opacity-0');
        setTimeout(() => backdrop.classList.add('hidden'), 300);
    } else {
        backdrop.classList.remove('hidden');
        setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
        sidebar.classList.remove('-translate-x-full');
    }
}

function syncSidebarToggle() {
    // Sync the status of side toggle with current dark class flag
    const isDark = document.documentElement.classList.contains('dark');
    document.getElementById('sidebar-dark-switch').checked = isDark;
}

// Populate user information in real-time
document.addEventListener('DOMContentLoaded', () => {
    syncSidebarToggle();
    
    const currentCurr = window.getCurrentCurrency();
    const currSelect = document.getElementById('sidebar-currency-switch');
    if (currSelect) currSelect.value = currentCurr;

    const user = getSessionUser();
    const initialEl = document.getElementById('side-user-initial');
    const nameEl = document.getElementById('side-user-name');
    const emailEl = document.getElementById('side-user-email');
    const authBlock = document.getElementById('side-auth-block');
    
    if (user) {
        if (initialEl) initialEl.innerText = user.name.charAt(0).toUpperCase();
        if (nameEl) nameEl.innerText = user.name;
        if (emailEl) emailEl.innerText = user.email;
        
        if (authBlock) {
            authBlock.innerHTML = `
                <button onclick="handleUserLogout()" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-red-600/10 text-red-600 hover:bg-red-600 hover:text-white transition-all text-sm font-semibold outline-none">
                    <i class="fas fa-power-off"></i>
                    <span>Log Out</span>
                </button>
            `;
        }
    }
});

function handleUserLogout() {
    triggerVibe(50);
    localStorage.removeItem(activeSessionKey);
    localStorage.removeItem("sellora_wishlist");
    localStorage.removeItem("digitalmohan_wishlist");
    Toast.info("Logged out successfully");
    setTimeout(() => {
        window.location.href = "login.php";
    }, 800);
}
</script>
