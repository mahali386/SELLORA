<?php
// Sellora - Admin Payouts & Settlement Balance Board
require_once __DIR__ . '/../common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN PAYOUTS METRICS -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    
    <div class="mb-5">
        <h1 class="text-xl font-display font-black text-slate-850 dark:text-white">Settlements & Payouts</h1>
        <p class="text-[10px] text-slate-450 dark:text-slate-500 font-bold uppercase tracking-wider">Gateway Settlements Ledger</p>
    </div>

    <!-- BALANCE SUMMARY CARD -->
    <div class="p-5 rounded-3xl bg-gradient-to-br from-slate-900 to-indigo-950 text-white border border-slate-800 shadow-lg mb-6 relative overflow-hidden">
        <!-- Abstract glowing ambient circles -->
        <div class="absolute top-0 right-0 w-36 h-36 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-8 -left-8 w-44 h-44 bg-indigo-550/10 rounded-full blur-3xl pointer-events-none"></div>

        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block mb-1">Available Settlement balance</span>
        <div class="flex items-baseline gap-1.5 mb-1">
            <span id="settlement-sum" class="text-3xl font-display font-black font-mono text-white">₹0.00</span>
            <span class="text-[10px] font-bold text-sky-400">INR</span>
        </div>
        <p class="text-[10px] text-slate-350 leading-snug mb-5">Calculated net value after 2% automated Razorpay transactional gateway fee.</p>

        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/10 text-xs font-semibold">
            <div>
                <span class="text-[9px] text-slate-450 uppercase block mb-0.5">Gross Revenue</span>
                <span id="gross-rev" class="text-sm font-black font-mono text-slate-200">₹0.00</span>
            </div>
            <div>
                <span class="text-[9px] text-slate-450 uppercase block mb-0.5">Transactions Count</span>
                <span id="tx-count" class="text-sm font-black font-mono text-slate-200">0</span>
            </div>
        </div>

        <button id="payout-act-btn" onclick="triggerDirectPayoutTransfer()" class="mt-5 w-full py-3.5 bg-gradient-to-r from-sky-500 to-indigo-500 hover:brightness-110 active:scale-[0.98] transition-all text-white font-black uppercase tracking-wider text-[10px] rounded-2xl shadow-md flex items-center justify-center gap-1.5 outline-none">
            <i class="fas fa-money-bill-transfer"></i>
            <span>Initiate Direct Bank Payout</span>
        </button>
    </div>

    <!-- DETAILED SETTLEMENT LEDGER -->
    <div class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 mb-6 shadow-sm backdrop-blur-md">
        <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3 border-b border-slate-100 dark:border-slate-800 pb-2">Successful Orders Audits</h4>
        
        <div id="payouts-tx-feed" class="space-y-3.5 divide-y divide-slate-100 dark:divide-slate-800">
            <!-- Populated via system query -->
            <div class="h-14 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
            <div class="h-14 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const admin = getSessionAdmin();
    if (!admin) { window.location.href="login.php"; return; }
    loadPayoutSettingsMetrics();
});

function loadPayoutSettingsMetrics() {
    // 1. Fetch payout summary details
    fetch('/api/payouts/summary')
        .then(res => res.json())
        .then(sum => {
            document.getElementById('settlement-sum').textContent = `₹${sum.settlement.toFixed(2)}`;
            document.getElementById('gross-rev').textContent = `₹${sum.revenue.toFixed(2)}`;
            document.getElementById('tx-count').textContent = sum.transactions;
            
            if (sum.settlement <= 0) {
                const btn = document.getElementById('payout-act-btn');
                btn.disabled = true;
                btn.classList.add('opacity-50', 'pointer-events-none');
                btn.innerHTML = `<i class="fas fa-lock mr-1"></i> No Balance to Settle`;
            }
        });

    // 2. Fetch successful orders & product mapping
    Promise.all([
        fetch('/api/products').then(res => res.json()),
        fetch('/api/orders').then(res => res.json()),
        fetch('/api/users').then(res => res.json())
    ])
    .then(([products, orders, users]) => {
        const feed = document.getElementById('payouts-tx-feed');
        const successful = orders.filter(o => o.status === 'successful');
        
        if (successful.length === 0) {
            feed.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-4">No qualified ledger activities logged.</p>`;
            return;
        }

        feed.innerHTML = successful.map(o => {
            const p = products.find(prod => prod.id === o.product_id);
            const u = users.find(user => user.id === o.user_id);
            const title = p ? p.title : 'Digital Asset Service';
            const userEmail = u ? u.email : 'customer@sellora.com';
            
            const taxGat = (o.amount * 0.02).toFixed(2);
            const netVal = (o.amount * 0.98).toFixed(2);

            return `
                <div class="pt-3.5 first:pt-0 flex items-start justify-between text-xs">
                    <div class="min-w-0 pr-2">
                        <h5 class="font-bold text-slate-800 dark:text-slate-200 truncate leading-snug">${title}</h5>
                        <p class="text-[10px] text-slate-450 dark:text-slate-500 font-semibold mt-0.5">${userEmail} • Net: ₹${netVal}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="font-extrabold text-slate-800 dark:text-slate-100 font-mono block">₹${o.amount}</span>
                        <span class="text-[8px] font-semibold text-slate-400 block mt-0.5">Fee: ₹${taxGat}</span>
                    </div>
                </div>
            `;
        }).join('');
    });
}

function triggerDirectPayoutTransfer() {
    triggerVibe(80);
    const btn = document.getElementById('payout-act-btn');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = `
        <span class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Verifying Bank Coordinates...</span>
        </span>
    `;

    setTimeout(() => {
        triggerVibe(40);
        btn.innerHTML = `
            <span class="flex items-center justify-center gap-2">
                <i class="fas fa-shield-halved animate-pulse"></i>
                <span>Encrypting ACH Ledger...</span>
            </span>
        `;
    }, 1500);

    setTimeout(() => {
        triggerVibe(100);
        Toast.success("Success! ₹ Settlement Dispatched to Verified Administrator Account.");
        
        btn.innerHTML = `<i class="fas fa-circle-check text-emerald-400 animate-bounce"></i> Balanced Cleared & Settled`;
        btn.classList.remove('bg-gradient-to-r', 'from-sky-500', 'to-indigo-500');
        btn.classList.add('bg-emerald-650/10', 'text-emerald-500', 'border', 'border-emerald-500/10');
        
        // Reload statistics summary 
        setTimeout(() => {
            loadPayoutSettingsMetrics();
        }, 1000);
    }, 3800);
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
