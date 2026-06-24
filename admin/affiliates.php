<?php
// Sellora - Admin Affiliate & Payout Management Panel
require_once __DIR__ . '/../common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN SECURE ADMIN PANEL AFFILIATE -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    
    <div class="mb-5 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-display font-black text-slate-850 dark:text-white">Affiliate Partner Network</h1>
            <p class="text-[10px] text-slate-450 dark:text-slate-500 font-bold uppercase tracking-wider">Promotional Ledger Payouts</p>
        </div>
        <a href="settings.php" class="text-xs text-sky-500 hover:underline font-bold"><i class="fas fa-arrow-left mr-1"></i> Settings</a>
    </div>

    <!-- 1. KEY METRICS STATS SUMMARY -->
    <div class="grid grid-cols-2 gap-3.5 mb-6">
        <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-4.5 shadow-sm">
            <span class="text-[9px] font-black uppercase text-slate-400 block mb-1">Total Affiliates</span>
            <span id="metric-total-aff" class="text-xl font-black font-mono text-slate-800 dark:text-slate-100">0</span>
            <span class="text-[8px] text-slate-400 block mt-0.5">Approved publishers</span>
        </div>

        <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-4.5 shadow-sm">
            <span class="text-[9px] font-black uppercase text-slate-400 block mb-1">Outstanding Redemptions</span>
            <span id="metric-outstanding-payouts" class="text-xl font-black font-mono text-amber-600 dark:text-amber-400">0</span>
            <span class="text-[8px] text-slate-400 block mt-0.5">In pending queue</span>
        </div>
    </div>

    <!-- 2. PENDING WITHDRAWALS SECTION -->
    <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-5 shadow-sm backdrop-blur-md mb-6">
        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-4 border-b border-slate-100 dark:border-slate-800 pb-2.5 flex justify-between items-center">
            <span>Redeem Requests Waiting Approval</span>
            <span id="pending-count-tag" class="text-[9px] bg-amber-500 text-slate-900 font-black px-2 py-0.5 rounded-full">0 Pending</span>
        </h3>
        
        <div id="admin-pending-payouts" class="space-y-3.5">
            <!-- Dynamic list -->
            <p class="text-xs text-slate-400 italic text-center py-4">No outstanding payout requests in the queue.</p>
        </div>
    </div>

    <!-- 3. ACTIVE AFFILIATE ROSTER -->
    <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-5 shadow-sm backdrop-blur-md">
        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-4 border-b border-slate-100 dark:border-slate-800 pb-2.5">Registered Partners Directory</h3>
        
        <div id="admin-partners-list" class="space-y-4">
            <!-- Dynamic partner cards -->
            <p class="text-xs text-slate-400 italic text-center py-4">No registered affiliate partners located.</p>
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const admin = getSessionAdmin();
    if (!admin) {
        window.location.href = "login.php";
        return;
    }
    loadAdminAffiliateOverview();
});

function loadAdminAffiliateOverview() {
    // 1. Fetch Registered Partners
    fetch('/api/admin/affiliates/all')
    .then(res => res.json())
    .then(partners => {
        document.getElementById('metric-total-aff').textContent = partners.length;
        
        const rosterContainer = document.getElementById('admin-partners-list');
        if (!partners || partners.length === 0) {
            rosterContainer.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-4">No registered partners found.</p>`;
            return;
        }
        
        rosterContainer.innerHTML = partners.map(p => `
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-white/5 space-y-2.5">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="text-xs font-black text-slate-800 dark:text-slate-200 leading-tight">${p.user_name}</h4>
                        <p class="text-[9px] text-slate-400 font-medium font-mono">${p.user_email}</p>
                    </div>
                    <span class="text-[8px] font-black uppercase bg-emerald-100 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400 px-2 py-0.5 rounded-full scale-90">${p.status}</span>
                </div>
                
                <div class="grid grid-cols-4 gap-1 pt-2 border-t border-slate-200/50 dark:border-white/5 text-center font-mono">
                    <div>
                        <span class="text-[7px] text-slate-450 dark:text-slate-500 uppercase block">Code</span>
                        <span class="text-[10px] font-extrabold text-slate-700 dark:text-slate-350 truncate block">${p.code}</span>
                    </div>
                    <div>
                        <span class="text-[7px] text-slate-450 dark:text-slate-500 uppercase block">Balance</span>
                        <span class="text-[10px] font-extrabold text-emerald-500 block">₹${p.balance || 0}</span>
                    </div>
                    <div>
                        <span class="text-[7px] text-slate-450 dark:text-slate-500 uppercase block">Clicks</span>
                        <span class="text-[10px] font-extrabold text-sky-500 block">${p.clicks || 0}</span>
                    </div>
                    <div>
                        <span class="text-[7px] text-slate-450 dark:text-slate-500 uppercase block">Earned</span>
                        <span class="text-[10px] font-extrabold text-amber-550 block">₹${p.total_earned || 0}</span>
                    </div>
                </div>
            </div>
        `).reverse().join('');
    });

    // 2. Fetch Payout Requests
    fetch('/api/admin/affiliates/payouts')
    .then(res => res.json())
    .then(payouts => {
        const pendingQueue = payouts.filter(p => p.status === 'pending');
        document.getElementById('metric-outstanding-payouts').textContent = pendingQueue.length;
        document.getElementById('pending-count-tag').textContent = `${pendingQueue.length} Pending`;
        
        const queueContainer = document.getElementById('admin-pending-payouts');
        if (!pendingQueue || pendingQueue.length === 0) {
            queueContainer.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-4 flex items-center justify-center gap-1.5 text-slate-400 dark:text-slate-600"><i class="fas fa-circle-check text-green-500"></i> Withdrawal queue completely settled!</p>`;
            return;
        }
        
        queueContainer.innerHTML = pendingQueue.map(p => `
            <div id="payout-card-${p.id}" class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-white/5 space-y-3">
                <div class="flex justify-between items-center pb-2 border-b border-dashed border-slate-250/50 dark:border-slate-800">
                    <div>
                        <h4 class="text-xs font-black text-slate-800 dark:text-slate-200">${p.user_name}</h4>
                        <p class="text-[9px] text-slate-400 font-medium font-mono">UPI ID: ${p.details}</p>
                    </div>
                    <span class="text-sm font-black text-rose-500 font-mono">₹${p.amount}</span>
                </div>
                
                <div class="flex gap-2">
                    <button onclick="processPayoutUpdate(${p.id}, 'completed')" class="flex-1 py-1.5 bg-emerald-550 active:scale-95 transition-all text-slate-900 font-extrabold text-[9px] uppercase tracking-wider rounded-xl shadow-sm outline-none">
                        <i class="fas fa-check-circle mr-0.5"></i> Approve Transfer
                    </button>
                    <button onclick="processPayoutUpdate(${p.id}, 'rejected')" class="flex-1 py-1.5 bg-rose-500/10 hover:bg-rose-500 hover:text-white text-rose-500 active:scale-95 transition-all font-extrabold text-[9px] uppercase tracking-wider rounded-xl outline-none">
                        <i class="fas fa-ban mr-0.5"></i> Decline Request
                    </button>
                </div>
            </div>
        `).reverse().join('');
    });
}

function processPayoutUpdate(payoutId, status) {
    triggerVibe(30);
    
    // Disable buttons instantly
    const card = document.getElementById(`payout-card-${payoutId}`);
    if (card) {
        card.style.opacity = '0.5';
        card.style.pointerEvents = 'none';
    }
    
    fetch('/api/admin/affiliates/payouts/process', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            payout_id: payoutId,
            status: status
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success(`Payout successfully marked as ${status}!`);
            loadAdminAffiliateOverview(); // Reload view metrics
        } else {
            Toast.error(data.error || "Execution failed. Retrying...");
            if (card) {
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
            }
        }
    })
    .catch(() => {
        Toast.error("Request offline. Check network.");
        if (card) {
            card.style.opacity = '1';
            card.style.pointerEvents = 'auto';
        }
    });
}
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
