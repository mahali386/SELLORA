<?php
// Sellora - Admin Discount Coupon Matrices
require_once __DIR__ . '/../common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN COUPONS BASE -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-display font-black text-slate-850 dark:text-white">Promo Coupons</h1>
            <p class="text-[10px] text-slate-450 dark:text-slate-500 font-bold uppercase tracking-wider">Vouchers matrix and metrics</p>
        </div>
        
        <button onclick="triggerNewCouponCreator()" class="px-3.5 py-1.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-bold shadow-md active:scale-95 transition-all outline-none flex items-center gap-1">
            <i class="fas fa-plus text-[10px]"></i><span>Create</span>
        </button>
    </div>

    <!-- ADD COUPON modal OVERLAY -->
    <div id="coupon-modal" class="hidden fixed inset-0 z-50 bg-slate-950/75 backdrop-blur-md flex items-center justify-center p-6 text-slate-800 dark:text-slate-100">
        <div class="max-w-sm w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-2xl relative">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-550 dark:text-slate-400 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">New Promo Code Setup</h3>
            
            <form id="coupon-form" onsubmit="saveCouponVoucherCode(event)" class="space-y-4 text-xs font-semibold">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Coupon Code Label</label>
                    <div class="flex gap-2">
                        <input type="text" id="c-code" required placeholder="SAVE50" class="flex-grow px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 uppercase border-0 outline-none">
                        <button type="button" onclick="generateRandomCouponCodeLabel()" class="px-3 py-2 bg-slate-850 hover:bg-slate-800 dark:bg-slate-800 dark:text-sky-400 text-white font-bold rounded-xl active:scale-95 transition-all text-[11px] outline-none">Generate</button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Deduction (%)</label>
                        <input type="number" id="c-discount" min="1" max="100" required placeholder="50" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5 font-sans">Usage Limit Count</label>
                        <input type="number" id="c-limit" min="1" required placeholder="100" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Promotion Expiry Date & Time</label>
                    <input type="datetime-local" id="c-expiry" required class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>

                <div class="mt-6 flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="dismissCouponCreator()" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-650 dark:text-slate-300">Discard</button>
                    <button type="submit" class="px-5 py-2 bg-sky-500 hover:bg-sky-600 rounded-xl text-xs font-bold text-white shadow-md">Create Promo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ACTIVE COUPONS MATRIX LIST -->
    <div id="coupons-feed" class="space-y-4">
        <!-- Loader Skeleton -->
        <div class="h-24 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
        <div class="h-24 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const admin = getSessionAdmin();
    if (!admin) { window.location.href="login.php"; return; }
    
    // Set default standard date & time for coupon selectors
    const d = new Date();
    d.setDate(d.getDate() + 30);
    const pad = (n) => String(n).padStart(2, '0');
    const formatted = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    document.getElementById('c-expiry').value = formatted;

    loadAdminCouponsInventory();
});

function loadAdminCouponsInventory() {
    fetch('/api/coupons')
        .then(res => res.json())
        .then(coupons => {
            const container = document.getElementById('coupons-feed');
            
            if (coupons.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-10 rounded-3xl border border-dashed border-slate-250 dark:border-slate-800">
                        <p class="text-xs text-slate-400">No voucher matrices defined. Setup promotional codes above.</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = coupons.map(c => {
                const isExpired = new Date(c.expiry) < new Date();
                const progressPercent = Math.min(100, Math.round(c.used_count / c.usage_limit * 100));
                
                return `
                    <div class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md relative group">
                        
                        <div class="absolute top-4 right-4 z-10 flex gap-1.5">
                            <button onclick="discardCouponVoucherRecord(${c.id})" class="text-slate-400 hover:text-red-500 transition-all outline-none">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-500 flex items-center justify-center text-xs">
                                <i class="fas fa-ticket-simple"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-850 dark:text-slate-200 font-mono tracking-wide uppercase">${c.code}</h4>
                                <span class="text-[10px] text-slate-450 dark:text-slate-500">Deduction option: <span class="text-orange-500 font-bold">${c.discount}%</span></span>
                            </div>
                        </div>

                        <!-- Progress analysis meter -->
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center justify-between text-[10px] font-bold text-slate-450 dark:text-slate-400 mb-1.5">
                                <span>Total uses limit analysis:</span>
                                <span class="font-mono">${c.used_count} / ${c.usage_limit} uses (${progressPercent}%)</span>
                            </div>
                            <!-- Bar line -->
                            <div class="w-full h-1.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden relative">
                                <div class="h-full bg-orange-400 rounded-full w-0 transition-all duration-300" style="width: ${progressPercent}%"></div>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between text-[9px] text-slate-400 font-semibold">
                            <span>Ends: ${new Date(c.expiry).toLocaleString()}</span>
                            <span class="${isExpired ? 'text-red-500 font-bold' : 'text-emerald-500 font-bold'}">${isExpired ? 'Expired' : 'Active promotion'}</span>
                        </div>
                    </div>
                `;
            }).join('');
        });
}

function triggerNewCouponCreator() {
    triggerVibe(30);
    document.getElementById('coupon-modal').classList.remove('hidden');
}

function dismissCouponCreator() {
    triggerVibe(20);
    document.getElementById('coupon-modal').classList.add('hidden');
}

function generateRandomCouponCodeLabel() {
    triggerVibe(20);
    const words = ["OFF", "DECOR", "SALE", "COUP", "PROMO", "SAVE"];
    const textStr = words[Math.floor(Math.random() * words.length)];
    const num = Math.floor(Math.random() * 89) + 10;
    document.getElementById('c-code').value = textStr + num;
}

function saveCouponVoucherCode(e) {
    if (e) e.preventDefault();
    triggerVibe(50);
    
    const code = document.getElementById('c-code').value.toUpperCase();
    const discount = parseInt(document.getElementById('c-discount').value);
    const expiry = document.getElementById('c-expiry').value;
    const usage_limit = parseInt(document.getElementById('c-limit').value);
    
    fetch('/api/coupons/create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code, discount, expiry, usage_limit })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("Voucher promo matrix established successfully!");
            dismissCouponCreator();
            loadAdminCouponsInventory();
        } else {
            Toast.error(data.error || "Save error occurred.");
        }
    });
}

function discardCouponVoucherRecord(id) {
    if (!confirm("Are you sure you want to permanently discard this promo code voucher limit?")) {
        return;
    }
    
    triggerVibe(70);
    fetch('/api/coupons/delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("Voucher code deleted.");
            loadAdminCouponsInventory();
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
