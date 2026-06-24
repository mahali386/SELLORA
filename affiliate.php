<?php
// Sellora - Affiliate Program Portal Dashboard
require_once __DIR__ . '/common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN SECURE USER PANEL AFFILIATE -->
<main class="max-w-md mx-auto px-4 pt-4 pb-24">
    
    <!-- User brief top board -->
    <div class="flex items-center gap-4 mb-6 p-4 rounded-3xl bg-gradient-to-br from-indigo-750 via-slate-800 to-amber-700 text-white shadow-md relative overflow-hidden">
        <div class="absolute -bottom-2 -right-2 p-3 opacity-15">
            <i class="fas fa-handshake text-7xl text-amber-400"></i>
        </div>
        <div class="w-12 h-12 rounded-full bg-white/10 border border-white/20 flex items-center justify-center font-bold text-lg backdrop-blur-md">
            <span id="aff-char">A</span>
        </div>
        <div>
            <h1 class="text-base font-display font-black leading-snug">Affiliate Partner Hub</h1>
            <p id="aff-user-sub" class="text-[11px] text-amber-200 font-medium">Earn up to 20% on catalog checkout sales</p>
        </div>
    </div>

    <!-- 1. LOADING GATEWAY SCREEN -->
    <div id="affiliate-loading" class="text-center py-12">
        <div class="animate-spin inline-block w-8 h-8 border-4 border-sky-500 border-t-transparent rounded-full mb-3" role="status"></div>
        <p class="text-xs text-slate-400 font-sans">Connecting with affiliate databases...</p>
    </div>

    <!-- 2. OFFERS / JOIN REGISTRATION SCREEN -->
    <div id="affiliate-join" class="hidden rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-5 shadow-sm backdrop-blur-md mb-6">
        <div class="text-center pb-4 border-b border-slate-150/50 dark:border-slate-800 pb-5 mb-5">
            <div class="w-14 h-14 bg-amber-500/10 text-amber-550 rounded-full flex items-center justify-center mx-auto mb-3 text-xl">
                <i class="fas fa-handshake"></i>
            </div>
            <h2 class="text-sm font-black text-slate-800 dark:text-slate-100 mb-1">Become a DigitalMohan Affiliate Partner</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Turn your social shares and posts into instant passive commissions. We offer flat 20% payouts on prompt collections, resumes, notes, and designs sales!</p>
        </div>

        <div class="space-y-4 mb-6">
            <div class="flex gap-3 items-start p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-white/5">
                <div class="w-7 h-7 bg-indigo-500/10 text-indigo-500 rounded-full flex items-center justify-center text-xs flex-shrink-0 mt-0.5">
                    <span class="font-extrabold">1</span>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Register in 1-Click</h4>
                    <p class="text-[10px] text-slate-400">Activate your custom referral links immediately below.</p>
                </div>
            </div>

            <div class="flex gap-3 items-start p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-white/5">
                <div class="w-7 h-7 bg-sky-500/10 text-sky-500 rounded-full flex items-center justify-center text-xs flex-shrink-0 mt-0.5">
                    <span class="font-extrabold">2</span>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Share with Friends or Audience</h4>
                    <p class="text-[10px] text-slate-400">Post catalog links, Canva guides or quick formule sheet templates files.</p>
                </div>
            </div>

            <div class="flex gap-3 items-start p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-white/5">
                <div class="w-7 h-7 bg-emerald-500/10 text-emerald-500 rounded-full flex items-center justify-center text-xs flex-shrink-0 mt-0.5">
                    <span class="font-extrabold">3</span>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Earn Flat 20% Cash</h4>
                    <p class="text-[10px] text-slate-400">Track balance in real-time and request instant UPI transfers directly!</p>
                </div>
            </div>
        </div>

        <form id="join-form" onsubmit="submitAffiliateJoin(event)" class="space-y-4">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">UPI ID (Google Pay, PhonePe, Paytm)</label>
                <input type="text" id="join-upi" placeholder="yourusername@okaxis" required class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 text-xs font-semibold text-slate-800 dark:text-slate-150 border-0 outline-none focus:ring-2 focus:ring-amber-500">
                <span class="text-[9px] text-slate-400 mt-1 block">Your commission earnings will be credited instantly to this UPI address.</span>
            </div>

            <button type="submit" onclick="triggerVibe(30)" class="w-full py-3 bg-amber-550 hover:bg-amber-600 text-slate-900 font-black rounded-xl text-xs shadow-md active:scale-95 transition-all outline-none">
                Register & Activate Affiliate Links
            </button>
        </form>
    </div>

    <!-- 3. ACTIVE DASHBOARD SCREEN -->
    <div id="affiliate-main" class="hidden space-y-6">
        
        <!-- Stats visual grid -->
        <div class="grid grid-cols-2 gap-3.5">
            <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-emerald-500/10 dark:bg-emerald-950/20 p-4.5">
                <span class="text-[9px] font-black uppercase text-emerald-500 block mb-1">Available Cash</span>
                <span id="stat-balance" class="text-lg font-black font-mono text-emerald-600 dark:text-emerald-400">₹0</span>
                <span class="text-[8px] text-slate-400 block mt-0.5">Ready to withdraw</span>
            </div>

            <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-amber-500/10 dark:bg-amber-950/20 p-4.5">
                <span class="text-[9px] font-black uppercase text-amber-500 block mb-1">Total Earned</span>
                <span id="stat-earned" class="text-lg font-black font-mono text-amber-655 dark:text-amber-400">₹0</span>
                <span class="text-[8px] text-slate-400 block mt-0.5">All-time commissions</span>
            </div>

            <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-sky-500/10 dark:bg-sky-950/20 p-4.5">
                <span class="text-[9px] font-black uppercase text-sky-500 block mb-1">Link Clicks</span>
                <span id="stat-clicks" class="text-lg font-black font-mono text-sky-655 dark:text-sky-450">0</span>
                <span class="text-[8px] text-slate-400 block mt-0.5">Visits routed</span>
            </div>

            <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-indigo-500/10 dark:bg-indigo-950/20 p-4.5">
                <span class="text-[9px] font-black uppercase text-indigo-500 block mb-1">Total Redeemed</span>
                <span id="stat-withdrawn" class="text-lg font-black font-mono text-indigo-655 dark:text-indigo-400">₹0</span>
                <span class="text-[8px] text-slate-400 block mt-0.5">Payouts processed</span>
            </div>
        </div>

        <!-- 3A. KEY GLOBAL LINK CARD -->
        <div class="rounded-3xl border border-amber-500/20 bg-amber-500/5 p-5 shadow-sm">
            <span class="inline-block text-[9px] font-black bg-amber-500 text-slate-900 px-2 py-0.5 rounded-full mb-3 uppercase tracking-wider">Your Referral Link</span>
            <div class="flex gap-2">
                <input type="text" id="share-ref-link" readonly class="flex-1 bg-slate-100 dark:bg-slate-950 px-3.5 py-2 rounded-xl text-xs font-mono font-bold text-slate-600 dark:text-slate-300 border-0 outline-none select-all min-w-0">
                <button onclick="copyAffCodeLink()" class="px-4 bg-slate-800 dark:bg-slate-700 hover:brightness-110 active:scale-95 transition-all text-white rounded-xl text-xs font-bold outline-none">
                    <i class="fas fa-copy mr-1 text-[10px]"></i> Copy
                </button>
            </div>
            <p class="text-[9px] text-slate-400 mt-2">Share this general links or specific product codes. Any product orders completed are tracked dynamically under you for 30 days!</p>
        </div>

        <!-- 3B. PRODUCT LINK CENTER -->
        <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-5 shadow-sm backdrop-blur-md">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3.5 border-b border-slate-100 dark:border-slate-800 pb-2.5 flex items-center justify-between">
                <span>Promote Popular Templates</span>
                <span class="text-[8px] bg-sky-100 dark:bg-sky-950/40 text-sky-500 py-0.5 px-2 rounded-full font-black">20% commission</span>
            </h3>
            
            <div id="product-promote-list" class="space-y-4 max-h-72 overflow-y-auto no-scrollbar">
                <!-- Loaded dynamically -->
            </div>
        </div>

        <!-- 3C. REQUEST WITHDRAWAL BOARD -->
        <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-5 shadow-sm backdrop-blur-md">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-4 border-b border-slate-100 dark:border-slate-800 pb-2.5">Request Instant Cash Payout</h3>
            
            <form id="payout-form" onsubmit="submitPayoutRequest(event)" class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">Redeem Amount (INR)</label>
                    <input type="number" id="payout-amount" min="100" placeholder="Minimum ₹100" required class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 text-xs font-mono font-bold text-slate-800 dark:text-slate-150 border-0 outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">UPI ID (Can update)</label>
                    <input type="text" id="payout-upi" placeholder="you@upi" required class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 text-xs font-semibold text-slate-800 dark:text-slate-150 border-0 outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <button type="submit" onclick="triggerVibe(20)" class="w-full py-3 bg-emerald-550 hover:bg-emerald-600 text-slate-900 font-extrabold rounded-xl text-xs shadow-md active:scale-95 transition-all outline-none">
                    Submit Transfer Request
                </button>
            </form>
        </div>

        <!-- 3D. COMMISSIONS EARNED TIMELINE -->
        <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-5 shadow-sm backdrop-blur-md">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-4 border-b border-slate-100 dark:border-slate-800 pb-2.5">Affiliate Commissions Ledger</h3>
            <div id="commission-list" class="space-y-3 max-h-48 overflow-y-auto no-scrollbar">
                <p class="text-xs text-slate-400 italic text-center py-2">No commissions awarded yet.</p>
            </div>
        </div>

        <!-- 3E. PAYOUT HISTORY LEDGER -->
        <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-5 shadow-sm backdrop-blur-md">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-4 border-b border-slate-100 dark:border-slate-800 pb-2.5">Redemptions Status</h3>
            <div id="payouts-history-list" class="space-y-3 max-h-48 overflow-y-auto no-scrollbar">
                <p class="text-xs text-slate-400 italic text-center py-2">No payout requests completed yet.</p>
            </div>
        </div>

    </div>

</main>

<script>
let currentAffiliateUser = null;
let currentAffiliateStats = null;

document.addEventListener('DOMContentLoaded', () => {
    currentAffiliateUser = getSessionUser();
    if (!currentAffiliateUser) {
        Toast.info("Connect account to join Affiliate Network!");
        setTimeout(() => window.location.href="login.php", 1000);
        return;
    }
    
    document.getElementById('aff-char').textContent = currentAffiliateUser.name.charAt(0).toUpperCase();
    loadAffiliateDashboard();
});

function loadAffiliateDashboard() {
    fetch(`/api/affiliate/stats/${currentAffiliateUser.id}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById('affiliate-loading').classList.add('hidden');
        
        if (data.active === false) {
            // Not registered yet
            document.getElementById('affiliate-join').classList.remove('hidden');
            document.getElementById('affiliate-main').classList.add('hidden');
        } else {
            // Active partner
            currentAffiliateStats = data;
            document.getElementById('affiliate-join').classList.add('hidden');
            document.getElementById('affiliate-main').classList.remove('hidden');
            
            // Set User text
            document.getElementById('aff-user-sub').textContent = `Affiliate ID Code: ${data.affiliate.code}`;
            
            // Set Stats values
            document.getElementById('stat-balance').textContent = `₹${data.affiliate.balance || 0}`;
            document.getElementById('stat-earned').textContent = `₹${data.affiliate.total_earned || 0}`;
            document.getElementById('stat-clicks').textContent = data.affiliate.clicks || 0;
            document.getElementById('stat-withdrawn').textContent = `₹${data.affiliate.total_withdrawn || 0}`;
            
            // Set Key links
            const siteUrl = `${window.location.protocol}//${window.location.host}`;
            document.getElementById('share-ref-link').value = `${siteUrl}/?ref=${data.affiliate.code}`;
            
            // Set input defaults
            document.getElementById('payout-upi').value = data.affiliate.uupi || '';
            
            // Render checklists
            renderPromoteProducts(data.affiliate.code);
            renderCommissionsLedger(data.commissions);
            renderPayoutsLedger(data.payouts);
        }
    })
    .catch(err => {
        console.error('Error launching dashboard stats.', err);
        document.getElementById('affiliate-loading').innerHTML = `
            <p class="text-xs text-red-500 py-4"><i class="fas fa-circle-exclamation mr-1"></i> Data loading error. Review dev server console logs.</p>
        `;
    });
}

function submitAffiliateJoin(e) {
    e.preventDefault();
    const upi = document.getElementById('join-upi').value.trim();
    if (!upi) return;
    
    fetch('/api/affiliate/join', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            user_id: currentAffiliateUser.id,
            name: currentAffiliateUser.name,
            email: currentAffiliateUser.email,
            phone: currentAffiliateUser.phone || '',
            upi_id: upi
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("Partner details accepted successfully! 🎉");
            loadAffiliateDashboard();
        } else {
            Toast.error(data.error || "Execution failed. Retrying in 1s.");
        }
    });
}

function copyAffCodeLink() {
    triggerVibe(40);
    const elem = document.getElementById('share-ref-link');
    elem.select();
    elem.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(elem.value)
        .then(() => Toast.success("General referral link copied to clipboard!"))
        .catch(() => Toast.error("Failed to copy. Try selecting manually."));
}

function copyProductAffLink(pId, title) {
    triggerVibe(40);
    const elem = document.getElementById(`prod-link-${pId}`);
    elem.select();
    elem.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(elem.value)
        .then(() => Toast.success(`Promotion link for "${title.substring(0, 20)}..." in clipboard!`))
        .catch(() => Toast.error("Failed to copy."));
}

function renderPromoteProducts(affCode) {
    // Rely on list categories or products, let's fetch first 10 products
    fetch('/api/products?limit=10')
    .then(res => res.json())
    .then(prods => {
        const listContainer = document.getElementById('product-promote-list');
        const list = prods.products || prods;
        
        if (!list || list.length === 0) {
            listContainer.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-2">Catalog item is empty.</p>`;
            return;
        }
        
        const siteUrl = `${window.location.protocol}//${window.location.host}`;
        listContainer.innerHTML = list.map(p => {
            const valUrl = `${siteUrl}/buy.php?id=${p.id}&ref=${affCode}`;
            return `
                <div class="flex flex-col p-3 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-white/5 space-y-2">
                    <div class="flex gap-2.5 items-center">
                        <img src="${window.getOptimizedImageUrl(p.image, 100)}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0" referrerPolicy="no-referrer" />
                        <div class="min-w-0 flex-1">
                            <h4 class="text-[11px] font-black leading-snug text-slate-800 dark:text-slate-150 truncate">${p.title}</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5 mt-0.5">Price: ₹${p.price} • Earn 20%: <span class="text-emerald-500 font-bold">₹${Math.round(p.price * 0.2)}</span></p>
                        </div>
                    </div>
                    <div class="flex gap-1.5 pt-1">
                        <input type="text" id="prod-link-${p.id}" readonly value="${valUrl}" class="flex-1 bg-white dark:bg-slate-900 border border-slate-150/50 dark:border-white/5 px-2.5 py-1.5 rounded-lg text-[9px] font-mono leading-none select-all min-w-0">
                        <button onclick="copyProductAffLink(${p.id}, '${p.title.replace(/'/g, "\\'")}')" class="px-3 bg-amber-550 text-slate-950 text-[10px] font-black rounded-lg active:scale-95 transition-all outline-none">
                            Copy Link
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    });
}

function submitPayoutRequest(e) {
    e.preventDefault();
    const amt = parseInt(document.getElementById('payout-amount').value);
    const upi = document.getElementById('payout-upi').value.trim();
    
    if (!amt || amt < 100) {
        Toast.error("Amount must be at least ₹100.");
        return;
    }
    
    const maxBal = currentAffiliateStats?.affiliate?.balance || 0;
    if (amt > maxBal) {
        Toast.error(`Insufficient balance. Maximum requestable is ₹${maxBal}`);
        return;
    }
    
    fetch('/api/affiliate/payout/request', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            user_id: currentAffiliateUser.id,
            amount: amt,
            payment_method: 'UPI', 
            details: upi
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("Payout request successfully submitted! 💸");
            document.getElementById('payout-amount').value = '';
            loadAffiliateDashboard(); // Refresh view
        } else {
            Toast.error(data.error || "System error submitting request.");
        }
    });
}

function renderCommissionsLedger(commissions) {
    const listContainer = document.getElementById('commission-list');
    
    if (!commissions || commissions.length === 0) {
        listContainer.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-2 flex items-center justify-center gap-1.5"><i class="fas fa-face-smile text-[10px]"></i> Share links to unlock earnings timeline!</p>`;
        return;
    }
    
    listContainer.innerHTML = commissions.map(c => `
        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-white/5 flex justify-between items-center">
            <div class="min-w-0">
                <h4 class="text-[10px] font-bold text-slate-800 dark:text-slate-200 truncate leading-snug">${c.product_title}</h4>
                <p class="text-[8px] text-slate-400 mt-0.5 font-mono">Order #${c.order_id} • ${new Date(c.created_at).toLocaleDateString()}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <span class="text-xs font-black text-emerald-500 font-mono">+₹${c.amount}</span>
                <span class="text-[7px] text-slate-400 block mt-0.5">20% commission</span>
            </div>
        </div>
    `).reverse().join('');
}

function renderPayoutsLedger(payouts) {
    const listContainer = document.getElementById('payouts-history-list');
    
    if (!payouts || payouts.length === 0) {
        listContainer.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-2">No withdrawal activities registered.</p>`;
        return;
    }
    
    listContainer.innerHTML = payouts.map(p => {
        let badgeStyle = "bg-amber-100 text-amber-655 dark:bg-amber-950/40 dark:text-amber-450";
        if (p.status === 'completed') badgeStyle = "bg-emerald-100 text-emerald-655 dark:bg-emerald-990/40 dark:text-emerald-450";
        if (p.status === 'rejected') badgeStyle = "bg-red-100 text-red-655 dark:bg-red-950/40 dark:text-red-450";
        
        return `
            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-white/5 flex justify-between items-center">
                <div>
                    <h4 class="text-[10px] font-bold text-slate-800 dark:text-slate-200">Redeem Transfer</h4>
                    <p class="text-[8px] text-slate-400 mt-0.5">UPI: ${p.details} • ${new Date(p.created_at).toLocaleDateString()}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-xs font-black text-slate-800 dark:text-slate-200 font-mono">-₹${p.amount}</span>
                    <span class="text-[7px] font-black px-1.5 py-0.5 rounded-full ${badgeStyle} block mt-1 scale-90">${p.status}</span>
                </div>
            </div>
        `;
    }).reverse().join('');
}
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
