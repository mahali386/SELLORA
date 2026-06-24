<?php
// Sellora - Profile Account Settings & Past Orders History
require_once __DIR__ . '/common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN SECURE USER PANEL PROFILE -->
<main class="max-w-md mx-auto px-4 pt-4 pb-24">
    
    <!-- User brief top board -->
    <div class="flex items-center gap-4 mb-6 p-4 rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 text-white shadow-md relative overflow-hidden">
        <div class="w-14 h-14 rounded-full bg-white/25 flex items-center justify-center font-bold text-xl backdrop-blur-md">
            <span id="prof-char">G</span>
        </div>
        <div>
            <h1 id="prof-title-name" class="text-lg font-display font-black leading-snug">Guest Buyer</h1>
            <p id="prof-title-email" class="text-xs text-sky-100">Sign in to sync database records</p>
        </div>
    </div>

    <!-- AFFILIATE DISCOVERY BANNER CARD -->
    <div class="rounded-3xl border border-amber-200/60 dark:border-amber-500/10 bg-amber-500/10 dark:bg-amber-950/20 p-5 shadow-sm backdrop-blur-md mb-6 relative overflow-hidden">
        <div class="absolute -top-1 -right-1 p-3 opacity-15">
            <i class="fas fa-handshake text-6xl text-amber-500 dark:text-amber-400"></i>
        </div>
        <div class="relative z-10">
            <span class="inline-block text-[9px] font-black uppercase tracking-wider bg-amber-500 text-slate-900 px-2 py-0.5 rounded-full mb-3">Affiliate Program</span>
            <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 mb-1 leading-snug">Earn 20% on Every Successful Referral 💰</h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">Promote any templates, revision booklets, or Canva bundle resources and earn direct cash commission on every buy order sales!</p>
            <a href="affiliate.php" onclick="triggerVibe(10)" class="inline-flex items-center justify-center px-4.5 py-2.5 bg-slate-800 dark:bg-slate-700 hover:brightness-110 text-white font-bold text-[10px] rounded-xl shadow-sm hover:translate-x-0.5 active:scale-95 transition-all outline-none">
                Visit Affiliate Dashboard <i class="fas fa-arrow-right text-[9px] ml-1.5"></i>
            </a>
        </div>
    </div>

    <!-- PROFILE EDIT SETTINGS CARD -->
    <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-5 shadow-sm backdrop-blur-md mb-6">
        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-4 border-b border-slate-100 dark:border-slate-800 pb-2.5">Edit Personal Credentials</h3>
        
        <form id="form-edit-account" onsubmit="submitAccountEditForm(event)" class="space-y-4">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">Full Legal Name</label>
                <input type="text" id="prof-input-name" placeholder="Elon Musk" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 text-xs font-semibold text-slate-800 dark:text-slate-150 border-0 outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">Mobile Contact Phone</label>
                <input type="tel" id="prof-input-phone" placeholder="9876543210" pattern="[0-9]{10}" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 text-xs font-semibold text-slate-800 dark:text-slate-150 border-0 outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">Change Password Code</label>
                <input type="password" id="prof-input-pass" placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 text-xs font-semibold text-slate-800 dark:text-slate-150 border-0 outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <button type="submit" class="w-full py-3 bg-slate-800 dark:bg-slate-750 hover:brightness-110 text-white rounded-xl font-bold text-xs shadow-md active:scale-95 transition-all outline-none">
                Save Account Changes
            </button>
        </form>
    </div>

    <!-- ALERTS / NOTIFICATIONS FEED LIST -->
    <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-5 shadow-sm backdrop-blur-md mb-6">
        <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800 pb-2.5">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Personal Notification inbox</h3>
            <button onclick="markAllNotificationsAsRead()" class="text-[10px] font-bold text-sky-500 hover:underline outline-none">Dismiss All</button>
        </div>
        
        <div id="notifications-feed" class="space-y-3 max-h-48 overflow-y-auto no-scrollbar">
            <!-- Populated via AJAX -->
            <p class="text-xs text-slate-400 italic text-center py-2">Inbox inbox empty.</p>
        </div>
    </div>

    <!-- PAST PURCHASES HISTORY TIMELINE CARD -->
    <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-5 shadow-sm backdrop-blur-md mb-6">
        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-4 border-b border-slate-100 dark:border-slate-800 pb-2.5">Past Purchase History Transactions</h3>
        
        <div id="past-orders-list" class="space-y-3.5">
            <!-- Dynamic billing cards -->
            <p class="text-xs text-slate-400 italic text-center py-2">No past order receipts identified.</p>
        </div>
    </div>

    <!-- DANGER BLOCKOUT DESTRUCTIVE ACTIONS -->
    <div class="flex gap-3">
        <a href="help.php" class="flex-1 py-3 border border-slate-200/50 dark:border-white/5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-350 rounded-xl font-bold text-xs text-center hover:bg-slate-200 active:scale-95 transition-all">
            <i class="fas fa-circle-question mr-1.5 text-xs"></i>Support Center FAQs
        </a>
        <button onclick="handleUserLogout()" class="flex-1 py-3 bg-red-650/10 text-red-600 hover:bg-red-650 hover:text-white rounded-xl font-bold text-xs shadow-sm active:scale-95 transition-all outline-none">
            <i class="fas fa-power-off mr-1.5 text-xs"></i>Exit Log Out
        </button>
    </div>

</main>

<script>
function loadUserProfileOverview() {
    const user = getSessionUser();
    if (!user) {
        Toast.info("Please login to verify profile configuration metrics.");
        setTimeout(() => window.location.href="login.php", 800);
        return;
    }

    // Bind data values 
    document.getElementById('prof-char').textContent = user.name.charAt(0).toUpperCase();
    document.getElementById('prof-title-name').textContent = user.name;
    document.getElementById('prof-title-email').textContent = user.email;
    
    // Bind input defaults
    document.getElementById('prof-input-name').value = user.name;
    document.getElementById('prof-input-phone').value = user.phone;

    // Performance Fix: Load pre-joined custom user-specific transactions in 5ms
    fetch(`/api/orders/user/${user.id}`)
    .then(res => res.json())
    .then(list => {
        const container = document.getElementById('past-orders-list');
        
        if (!list || list.length === 0) {
            container.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-2">No database receipts recorded.</p>`;
            return;
        }

        container.innerHTML = list.map(o => {
            let badgeClass = "bg-amber-100 text-amber-655 dark:bg-amber-950/40 dark:text-amber-450";
            if (o.status === 'successful') {
                badgeClass = "bg-emerald-100 text-emerald-655 dark:bg-emerald-950/40 dark:text-emerald-450";
            } else if (o.status === 'failed') {
                badgeClass = "bg-red-100 text-red-655 dark:bg-red-950/40 dark:text-red-450";
            }
            
            return `
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-white/5">
                    <div class="min-w-0 pr-2">
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate leading-snug">${o.product_title}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[9px] text-slate-400">Purchased on ${new Date(o.created_at).toLocaleDateString()}</span>
                            ${o.status === 'successful' ? `
                                <span class="text-[9px] text-slate-300 dark:text-slate-650">•</span>
                                <a href="receipt.php?id=${o.id}" class="text-[9px] text-sky-500 font-extrabold hover:underline flex items-center gap-0.5">
                                    <i class="fas fa-file-invoice text-[8px]"></i> Invoice PDF
                                </a>
                            ` : ''}
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200 font-mono block">${window.formatPrice(o.amount)}</span>
                        <span class="text-[8px] font-black uppercase px-2 py-0.5 rounded-full ${badgeClass} inline-block scale-90 mt-1">${o.status}</span>
                    </div>
                </div>
            `;
        }).join('');
    });

    // Load alerts notifications feed logs
    loadUserNotificationsList(user.id);
}

function loadUserNotificationsList(userId) {
    fetch(`/api/notifications/${userId}`)
        .then(res => res.json())
        .then(notifications => {
            const feed = document.getElementById('notifications-feed');
            if (notifications.length === 0) {
                feed.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-2">Inbox is tidy.</p>`;
                return;
            }
            
            feed.innerHTML = notifications.map(n => `
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-white/5 relative ${n.is_read === 0 ? 'border-l-4 border-l-sky-500' : ''}">
                    <h5 class="text-xs font-bold text-slate-800 dark:text-slate-250 leading-snug">${n.title}</h5>
                    <p class="text-[10px] text-slate-550 dark:text-slate-400 mt-0.5 leading-relaxed font-semibold">${n.message}</p>
                    <span class="text-[8px] text-slate-400 block mt-1">${new Date(n.created_at).toLocaleTimeString()}</span>
                </div>
            `).join('');
        });
}

function markAllNotificationsAsRead() {
    triggerVibe(30);
    const user = getSessionUser();
    if (!user) return;
    
    fetch('/api/notifications/read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ userId: user.id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("Notifications dismissed.");
            loadUserNotificationsList(user.id);
            // Hide parent counter bells badges if existing
            const b = document.getElementById('unread-count');
            if (b) b.classList.add('hidden');
        }
    });
}

function submitAccountEditForm(event) {
    event.preventDefault();
    triggerVibe(50);
    const user = getSessionUser();
    
    const name = document.getElementById('prof-input-name').value;
    const phone = document.getElementById('prof-input-phone').value;
    const password = document.getElementById('prof-input-pass').value;
    
    fetch('/api/users/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id: user.id,
            name,
            phone,
            password: password || undefined
        })
    })
    .then(async res => {
        if (!res.ok) {
            const err = await res.json();
            throw new Error(err.error || 'Server rejected changes');
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            localStorage.setItem(activeSessionKey, JSON.stringify(data.user));
            Toast.success("Personal specifications updated successfully!");
            loadUserProfileOverview();
        }
    })
    .catch(err => {
        Toast.error(err.message);
    });
}

// Ignition
document.addEventListener('DOMContentLoaded', () => {
    loadUserProfileOverview();
});

window.addEventListener('currencychange', () => {
    loadUserProfileOverview();
});
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
