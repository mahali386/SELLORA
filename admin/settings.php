<?php
// DigitalMohan - Admin App Configurations Preferences Settings
require_once __DIR__ . '/../common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN CONFIGURATIONS INTERFACE -->
<main class="max-w-md mx-auto px-4 pt-4 pb-24">
    
    <div class="mb-5">
        <h1 class="text-xl font-display font-black text-slate-850 dark:text-white">Site Configurations</h1>
        <p class="text-[10px] text-slate-450 dark:text-slate-500 font-bold uppercase tracking-wider">Gateway Keys & App preferences</p>
    </div>

    <!-- ADMINISTRATIVE QUICK TOOLS MATRIX -->
    <div class="grid grid-cols-3 gap-2 mb-5 text-center font-sans">
        <a href="categories.php" class="p-2 rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md hover:scale-[1.03] active:scale-95 transition-all block text-slate-800 dark:text-slate-100">
            <div class="w-6 h-6 rounded-lg bg-orange-500/10 text-orange-500 flex items-center justify-center mx-auto mb-1">
                <i class="fas fa-tags text-[10px]"></i>
            </div>
            <span class="text-[8px] font-black uppercase tracking-wider block truncate">Categories</span>
        </a>
        <a href="users.php" class="p-2 rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md hover:scale-[1.03] active:scale-95 transition-all block text-slate-800 dark:text-slate-100">
            <div class="w-6 h-6 rounded-lg bg-sky-500/10 text-sky-500 flex items-center justify-center mx-auto mb-1">
                <i class="fas fa-users text-[10px]"></i>
            </div>
            <span class="text-[8px] font-black uppercase tracking-wider block truncate">Users</span>
        </a>
        <a href="payouts.php" class="p-2 rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md hover:scale-[1.03] active:scale-95 transition-all block text-slate-800 dark:text-slate-100">
            <div class="w-6 h-6 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center mx-auto mb-1">
                <i class="fas fa-money-bill-transfer text-[10px]"></i>
            </div>
            <span class="text-[8px] font-black uppercase tracking-wider block truncate font-sans">Payouts</span>
        </a>
        <a href="banners.php" class="p-2 rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md hover:scale-[1.03] active:scale-95 transition-all block text-slate-800 dark:text-slate-100">
            <div class="w-6 h-6 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center mx-auto mb-1">
                <i class="fas fa-images text-[10px]"></i>
            </div>
            <span class="text-[8px] font-black uppercase tracking-wider block truncate font-sans">Banners</span>
        </a>
        <a href="queries.php" class="p-2 rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md hover:scale-[1.03] active:scale-95 transition-all block text-slate-800 dark:text-slate-105 font-semibold">
            <div class="w-6 h-6 rounded-lg bg-indigo-500/10 text-indigo-500 flex items-center justify-center mx-auto mb-1 animate-pulse">
                <i class="fas fa-envelope-open-text text-[10px]"></i>
            </div>
            <span class="text-[8px] font-black uppercase tracking-wider block truncate font-sans">Support</span>
        </a>
        <a href="blogs.php" class="p-2 rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md hover:scale-[1.03] active:scale-95 transition-all block text-slate-800 dark:text-slate-105 font-semibold">
            <div class="w-6 h-6 rounded-lg bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto mb-1 animate-pulse">
                <i class="fa-regular fa-newspaper text-[11px]"></i>
            </div>
            <span class="text-[8px] font-black uppercase tracking-wider block truncate font-sans">Blogs</span>
        </a>
        <a href="affiliates.php" class="p-2 rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md hover:scale-[1.03] active:scale-95 transition-all block text-slate-800 dark:text-slate-105 font-semibold">
            <div class="w-6 h-6 rounded-lg bg-amber-500/15 text-amber-500 flex items-center justify-center mx-auto mb-1">
                <i class="fas fa-handshake text-[10px]"></i>
            </div>
            <span class="text-[8px] font-black uppercase tracking-wider block truncate font-sans">Affiliates</span>
        </a>
    </div>

    <!-- MAIN CONFIGS PANEL SETTING CARD -->
    <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-5 shadow-sm backdrop-blur-md space-y-4">
        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-800 pb-2.5 mb-4">Keys & Settings</h3>
        
        <form id="settings-configs-form" onsubmit="submitAdminSettingsDetails(event)" class="space-y-4 text-xs font-semibold">
            
            <!-- App Name -->
            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Branding Platform Name</label>
                <input type="text" id="set-name" required placeholder="DigitalMohan" value="DigitalMohan" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
            </div>

            <!-- Razorpay client settings -->
            <div class="space-y-4 pt-2 border-t border-slate-100 dark:border-slate-800">
                <span class="block text-[10px] uppercase font-black text-sky-500 tracking-wider">Razorpay Gateway API credentials</span>
                
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Razorpay Public Key ID</label>
                    <input type="text" id="set-rzp-key" required placeholder="rzp_test_public_12345" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5 font-sans">Razorpay Secret HMAC Key Bundle</label>
                    <input type="password" id="set-rzp-secret" required placeholder="••••••••••••••" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>
            </div>

            <!-- Customer Contacts support options -->
            <div class="space-y-4 pt-2 border-t border-slate-100 dark:border-slate-800">
                <span class="block text-[10px] uppercase font-black text-indigo-500 tracking-wider">Customer support coordinates</span>
                
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Support Desk Email</label>
                    <input type="email" id="set-email" required placeholder="support@digitalmohan.com" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5 font-sans">Support Hotline Phone</label>
                    <input type="tel" id="set-phone" required placeholder="+91 98765 43210" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>
            </div>

            <!-- SMTP Settings block -->
            <div class="space-y-4 pt-2 border-t border-slate-100 dark:border-slate-800">
                <span class="block text-[10px] uppercase font-black text-orange-500 tracking-wider">Secure SMTP server (Receipts mail)</span>
                
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">SMTP Host Address</label>
                    <input type="text" id="set-smtp-host" placeholder="smtp.gmail.com" value="smtp.gmail.com" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">SMTP Port</label>
                        <input type="number" id="set-smtp-port" placeholder="587" value="587" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5 font-sans">SMTP Security scheme</label>
                        <select id="set-smtp-secure" class="w-full px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                            <option value="tls">TLS Enforced</option>
                            <option value="ssl">SSL Port</option>
                        </select>
                    </div>
                </div>
            </div>

             <!-- Admin Credentials changing option -->
            <div class="space-y-4 pt-2 border-t border-slate-100 dark:border-slate-800">
                <span class="block text-[10px] uppercase font-black text-rose-500 tracking-wider">Change Admin Login Access</span>
                
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Admin Username</label>
                    <input type="text" id="set-admin-username" required placeholder="admin" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5 font-sans">Admin Password Token</label>
                    <input type="password" id="set-admin-password" required placeholder="••••••••••••••" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>
            </div>

            <!-- Viral WhatsApp Ebook Popup Settings -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <span class="block text-[10px] uppercase font-black text-emerald-500 tracking-wider">Viral Ebook Popup Settings</span>
                
                <div class="flex items-center justify-between p-2 rounded-xl bg-emerald-500/5 border border-emerald-500/10">
                    <div>
                        <span class="block text-xs font-extrabold text-emerald-500">Viral Popup Enabled</span>
                        <span class="text-[9px] text-slate-450 dark:text-slate-500 font-semibold block leading-tight mt-0.5">Show dynamic WhatsApp sharing popup to get free resource</span>
                    </div>
                    
                    <input type="checkbox" id="set-viral-enabled" class="w-10 h-6 rounded-full bg-slate-200 checked:bg-emerald-550 outline-none cursor-pointer border-0 ring-0 focus:ring-0">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Free Ebook Title</label>
                    <input type="text" id="set-viral-title" required placeholder="Growth Marketing Secrets" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Ebook Value (MRP in ₹)</label>
                    <input type="number" id="set-viral-mrp" required placeholder="999" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none font-mono">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Unlocking Prompt / Rule Description</label>
                    <textarea id="set-viral-description" required rows="2" placeholder="Free Premium Ebook unlock karne ke liye ye link WhatsApp par share karein. Apna valid share ho, khud ke number par share nahi kar payenge." class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none font-semibold"></textarea>
                </div>
            </div>

            <!-- WhatsApp Group Join Popup Settings -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <span class="block text-[10px] uppercase font-black text-sky-500 tracking-wider">WhatsApp Group Join Popup Settings</span>
                
                <div class="flex items-center justify-between p-2 rounded-xl bg-sky-550/5 border border-sky-500/10">
                    <div>
                        <span class="block text-xs font-extrabold text-sky-500">WhatsApp Popup Enabled</span>
                        <span class="text-[9px] text-slate-450 dark:text-slate-500 font-semibold block leading-tight mt-0.5">Show beautiful modal to invite visitors to join your WhatsApp group</span>
                    </div>
                    
                    <input type="checkbox" id="set-wa-popup-enabled" class="w-10 h-6 rounded-full bg-slate-200 checked:bg-sky-500 outline-none cursor-pointer border-0 ring-0 focus:ring-0">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5 font-sans">WhatsApp Group Join Link</label>
                    <input type="url" id="set-wa-popup-link" required placeholder="https://chat.whatsapp.com/Gj..." class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none font-mono">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Popup Central Title Header (Attractive)</label>
                    <input type="text" id="set-wa-popup-title" required placeholder="Join Our Premium WhatsApp Community! 🚀" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Popup Detail Description / Perks info</label>
                    <textarea id="set-wa-popup-description" required rows="2" placeholder="Get instant high-quality templates, free resume tools, and direct support updates daily." class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none font-semibold"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5 font-sans">Display Delay after load (Milliseconds)</label>
                    <input type="number" id="set-wa-popup-delay" required placeholder="5000" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none font-mono">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5 font-sans">Auto Close Delay / Duration (Milliseconds, e.g. 10000. Use 0 to disable)</label>
                    <input type="number" id="set-wa-popup-autoclose" required placeholder="10000" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none font-mono">
                </div>
            </div>

            <!-- Maintenance Mode toggling options switches -->
            <div class="space-y-4 pt-2 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between p-2 rounded-xl bg-red-500/5 border border-red-500/10">
                    <div>
                        <span class="block text-xs font-extrabold text-red-500">Maintenance Mode Switch</span>
                        <span class="text-[9px] text-slate-450 dark:text-slate-500 font-semibold block leading-tight mt-0.5">Locks entire platform for consumer buyers</span>
                    </div>
                    
                    <input type="checkbox" id="set-maintenance" class="w-10 h-6 rounded-full bg-slate-200 checked:bg-sky-500 outline-none cursor-pointer border-0 ring-0 focus:ring-0">
                </div>
            </div>

            <!-- Submit action -->
            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-sky-600 to-indigo-500 text-white rounded-xl font-bold text-xs shadow-md active:scale-95 transition-all outline-none">
                Save Site Configurations
            </button>

        </form>
    </div>

    <!-- System directory summaries -->
    <div class="mt-6 text-center text-[10px] text-slate-450 dark:text-slate-550 select-none">
        <span>DigitalMohan Systems Core v1.0.3 Stable • Sandbox ready</span>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const admin = getSessionAdmin();
    if (!admin) { window.location.href="login.php"; return; }
    
    loadAdminSettingsDetails();
});

function loadAdminSettingsDetails() {
    fetch('/api/settings')
        .then(res => res.json())
        .then(set => {
            document.getElementById('set-name').value = set.app_name;
            document.getElementById('set-rzp-key').value = set.razorpay_key;
            document.getElementById('set-rzp-secret').value = set.razorpay_secret;
            document.getElementById('set-email').value = set.support_email;
            document.getElementById('set-phone').value = set.support_phone;
            document.getElementById('set-maintenance').checked = (set.maintenance_mode === 1);
            document.getElementById('set-viral-enabled').checked = (set.viral_popup_enabled === undefined || set.viral_popup_enabled === 1 || set.viral_popup_enabled === "1" || set.viral_popup_enabled === true);
            document.getElementById('set-viral-title').value = set.viral_popup_title || "Growth Marketing Secrets";
            document.getElementById('set-viral-mrp').value = set.viral_popup_mrp || 999;
            document.getElementById('set-viral-description').value = set.viral_popup_description || "Free Premium Ebook unlock karne ke liye ye link WhatsApp par share karein. Apna valid share ho, khud ke number par share nahi kar payenge.";
            
            // WhatsApp Group popup settings block
            const waEnabled = set.whatsapp_group_enabled;
            document.getElementById('set-wa-popup-enabled').checked = (waEnabled === undefined || waEnabled === 1 || waEnabled === "1" || waEnabled === true);
            document.getElementById('set-wa-popup-link').value = set.whatsapp_group_link || "https://chat.whatsapp.com/GjMockGrpLnk2026Sellora";
            document.getElementById('set-wa-popup-title').value = set.whatsapp_group_title || "Join Our Premium WhatsApp Community! 🚀";
            document.getElementById('set-wa-popup-description').value = set.whatsapp_group_description || "Get instant high-quality templates, free resume tools, and direct support updates daily. Join 10,000+ members!";
            document.getElementById('set-wa-popup-delay').value = set.whatsapp_group_delay || 5000;
            document.getElementById('set-wa-popup-autoclose').value = set.whatsapp_group_autoclose !== undefined ? set.whatsapp_group_autoclose : 10000;
        });

    fetch('/api/settings/admin-info')
        .then(res => res.json())
        .then(admin => {
            document.getElementById('set-admin-username').value = admin.username;
            document.getElementById('set-admin-password').value = admin.password;
        });
}

function submitAdminSettingsDetails(e) {
    if (e) e.preventDefault();
    triggerVibe(50);
    
    const app_name = document.getElementById('set-name').value;
    const razorpay_key = document.getElementById('set-rzp-key').value;
    const razorpay_secret = document.getElementById('set-rzp-secret').value;
    const support_email = document.getElementById('set-email').value;
    const support_phone = document.getElementById('set-phone').value;
    const maintenance_mode = document.getElementById('set-maintenance').checked ? 1 : 0;
    
    const viral_popup_enabled = document.getElementById('set-viral-enabled').checked ? 1 : 0;
    const viral_popup_title = document.getElementById('set-viral-title').value;
    const viral_popup_mrp = document.getElementById('set-viral-mrp').value;
    const viral_popup_description = document.getElementById('set-viral-description').value;

    const whatsapp_group_enabled = document.getElementById('set-wa-popup-enabled').checked ? 1 : 0;
    const whatsapp_group_link = document.getElementById('set-wa-popup-link').value;
    const whatsapp_group_title = document.getElementById('set-wa-popup-title').value;
    const whatsapp_group_description = document.getElementById('set-wa-popup-description').value;
    const whatsapp_group_delay = parseInt(document.getElementById('set-wa-popup-delay').value) || 5000;
    const whatsapp_group_autoclose = parseInt(document.getElementById('set-wa-popup-autoclose').value) !== undefined ? (parseInt(document.getElementById('set-wa-popup-autoclose').value) || 0) : 10000;

    const admin_username = document.getElementById('set-admin-username').value;
    const admin_password = document.getElementById('set-admin-password').value;
    
    fetch('/api/settings/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            app_name,
            razorpay_key,
            razorpay_secret,
            support_email,
            support_phone,
            theme_color: '#0284C7',
            maintenance_mode,
            viral_popup_enabled,
            viral_popup_title,
            viral_popup_mrp,
            viral_popup_description,
            whatsapp_group_enabled,
            whatsapp_group_link,
            whatsapp_group_title,
            whatsapp_group_description,
            whatsapp_group_delay,
            whatsapp_group_autoclose,
            admin_username,
            admin_password
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("Dynamic site arrangements locks saved successfully!");
            loadAdminSettingsDetails();
        } else {
            Toast.error("Failed server settings upgrade.");
        }
    });
}
</script>

<!-- Custom Toast -->
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none max-w-sm w-full px-4 sm:px-0"></div>
<script>
const Toast = {
    show: function(m, type='success') {
        const container = document.getElementById('toast-container');
        const t = document.createElement('div');
        t.className = `p-4 rounded-xl shadow-lg border border-white/5 backdrop-blur-md text-xs font-bold ${type === 'error' ? 'bg-red-500' : 'bg-emerald-500'} text-white transition-all transform duration-300`;
        t.textContent = m;
        container.appendChild(t);
        setTimeout(() => t.remove(), 2200);
    },
    success: function(m) { this.show(m, 'success'); },
    error: function(m) { this.show(m, 'error'); }
};
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
