<?php
// Sellora - PDF Invoice / Printing Receipt Page
require_once __DIR__ . '/common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    <!-- Back Header -->
    <div class="flex items-center justify-between mb-6 print:hidden">
        <div class="flex items-center gap-3">
            <a href="mydownloads.php" class="w-9 h-9 rounded-xl flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:scale-105 transition-all outline-none" onclick="triggerVibe(15)">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="font-display font-extrabold text-lg text-slate-900 dark:text-white">Tax Invoice</h1>
                <p class="text-[10px] text-slate-500 font-medium tracking-wide uppercase">Receipt PDF Center</p>
            </div>
        </div>
        <button 
            onclick="window.print()" 
            class="h-9 px-4 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs transition-all shadow-md active:scale-95 flex items-center gap-2 cursor-pointer"
        >
            <i class="fas fa-print"></i> Print PDF
        </button>
    </div>

    <!-- Loading Skeleton indicator -->
    <div id="receipt-skeleton" class="space-y-4">
        <div class="h-44 bg-slate-100 dark:bg-slate-850 rounded-3xl animate-pulse"></div>
        <div class="h-64 bg-slate-100 dark:bg-slate-850 rounded-3xl animate-pulse"></div>
    </div>

    <!-- Error container -->
    <div id="receipt-error" class="hidden bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/30 rounded-3xl p-6 text-center text-red-650 dark:text-red-400 space-y-3">
        <i class="fas fa-receipt text-3xl opacity-50"></i>
        <h3 class="font-bold text-sm">Invoice Not Found</h3>
        <p class="text-xs">We couldn't retrieve the transaction coordinates for this Receipt ID. Please ensure your payment clearance was logged successfully.</p>
        <a href="index.php" class="inline-block px-4 py-2 bg-slate-100 dark:bg-slate-805 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-300">Home Store</a>
    </div>

    <!-- Invoice Details Panel -->
    <div id="receipt-content" class="hidden space-y-6 print:space-y-6 print:block">
        
        <!-- Header brand and billing stamp card -->
        <div class="bg-white dark:bg-slate-850 border border-slate-100 dark:border-white/5 rounded-3xl p-6 shadow-sm relative overflow-hidden print:border-none print:shadow-none">
            <!-- Paid Stamp Graphic -->
            <div id="status-stamp" class="absolute -right-2 -bottom-2 w-28 h-28 border-4 border-emerald-500/30 dark:border-emerald-500/20 rounded-full flex items-center justify-center rotate-12 select-none pointer-events-none">
                <span class="font-display font-black text-rose-500 uppercase tracking-widest text">PAID</span>
            </div>

            <div class="flex justify-between items-start mb-6">
                <div>
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="w-2.5 h-2.5 bg-sky-500 rounded-full"></span>
                        <span class="font-display font-black text-base text-slate-900 dark:text-white uppercase tracking-tight">DigitalMohan Inc.</span>
                    </div>
                    <p class="text-[10px] text-slate-400 leading-normal">
                        Registered Digital Vendor<br/>
                        CIN: L72200MH2026PTC32410<br/>
                        support@digitalmohan.com
                    </p>
                </div>
                <div class="text-right">
                    <span id="invoice-id-badge" class="font-mono text-xs font-bold text-sky-600 dark:text-sky-450 bg-sky-50 dark:bg-sky-950/40 px-3 py-1.5 rounded-xl">
                        INV-#--
                    </span>
                    <p id="invoice-date" class="text-[10px] text-slate-400 mt-2 font-mono">Date: --/--/----</p>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-white/5 my-4"></div>

            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <h4 class="font-bold text-slate-400 text-[10px] uppercase tracking-wide mb-1">Billed To (Customer):</h4>
                    <p id="bill-customer-name" class="font-bold text-slate-800 dark:text-slate-200">--</p>
                    <p id="bill-customer-email" class="text-[11px] text-slate-500 font-mono">--</p>
                </div>
                <div class="text-right">
                    <h4 class="font-bold text-slate-400 text-[10px] uppercase tracking-wide mb-1">Payment Method:</h4>
                    <p class="font-bold text-slate-800 dark:text-slate-200">Razorpay Secure Gate</p>
                    <p id="bill-payment-id" class="text-[10px] text-slate-500 font-mono">ID: pay_----</p>
                </div>
            </div>
        </div>

        <!-- Bill Items Breakdown -->
        <div class="bg-white dark:bg-slate-850 border border-slate-100 dark:border-white/5 rounded-3xl p-6 shadow-sm print:border-none print:shadow-none">
            <h3 class="font-display font-extrabold text-xs text-slate-900 dark:text-white uppercase tracking-wider mb-4 border-b border-slate-100 dark:border-white/5 pb-2">Line Transactions</h3>
            
            <div id="bill-items-list" class="space-y-4">
                <!-- Rendered dynamically -->
            </div>

            <div class="border-t border-slate-100 dark:border-white/5 my-5"></div>

            <!-- Total breakdowns -->
            <div class="space-y-2 text-xs font-mono">
                <div class="flex justify-between text-slate-500">
                    <span>Taxable Subtotal:</span>
                    <span id="breakdown-subtotal">₹0.00</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>CGST (9%):</span>
                    <span id="breakdown-cgst">₹0.00</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>SGST (9%):</span>
                    <span id="breakdown-sgst">₹0.00</span>
                </div>
                <div id="promo-row" class="flex justify-between text-emerald-500 hidden">
                    <span>Coupons Applied:</span>
                    <span id="breakdown-discount">-₹0.00</span>
                </div>
                <div class="border-t border-dashed border-slate-200 dark:border-slate-800 my-2"></div>
                <div class="flex justify-between text-sm font-bold text-slate-900 dark:text-white">
                    <span>Grand Total Paid:</span>
                    <span id="breakdown-grandtotal">₹0.00</span>
                </div>
            </div>
        </div>

        <!-- Footer terms note -->
        <div class="text-center space-y-2">
            <p class="text-[10px] text-slate-400 leading-normal">
                This is a system-generated computerized Tax Invoice & PDF receipt, requiring no physical physical signature. Formats and layout sheets are licensed to single-use credentials only. Thank you for shopping format packs with DigitalMohan!
            </p>
            <div class="flex justify-center items-center gap-2 text-sky-500 font-bold text-xs print:hidden">
                <i class="fas fa-check-double text-emerald-500"></i>
                <span>Payment Clear & Verified</span>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const orderId = new URLSearchParams(window.location.search).get('id');
    const skeleton = document.getElementById('receipt-skeleton');
    const errorEl = document.getElementById('receipt-error');
    const contentEl = document.getElementById('receipt-content');

    if (!orderId) {
        skeleton.classList.add('hidden');
        errorEl.classList.remove('hidden');
        return;
    }

    try {
        // Performance Fix: Fetch single detailed record with server-joined product specs
        const res = await fetch(`/api/orders/detail/${orderId}`);
        if (!res.ok) {
            skeleton.classList.add('hidden');
            errorEl.classList.remove('hidden');
            return;
        }
        
        const data = await res.json();
        const order = data.order;
        const product = data.product;

        if (!order || !product) {
            skeleton.classList.add('hidden');
            errorEl.classList.remove('hidden');
            return;
        }

        // Populate fields
        document.getElementById('invoice-id-badge').innerText = `INV-${String(order.id).padStart(5, '0')}`;
        document.getElementById('invoice-date').innerText = `Date: ${new Date(order.created_at || Date.now()).toLocaleDateString('en-IN')}`;
        
        let clientUser = getSessionUser();
        document.getElementById('bill-customer-name').innerText = clientUser ? clientUser.name : "Registered Customer";
        document.getElementById('bill-customer-email').innerText = clientUser ? clientUser.email : order.billing_email || "user@example.com";
        document.getElementById('bill-payment-id').innerText = `ID: ${order.razorpay_order_id || 'manual_clearance'}`;

        // Build Stamp status
        const stampStrEl = document.getElementById('status-stamp');
        if (order.status === 'successful') {
            stampStrEl.innerHTML = `<span class="font-display font-black text-emerald-500 uppercase tracking-widest text-lg">PAID</span>`;
            stampStrEl.className = "absolute -right-2 -bottom-2 w-28 h-28 border-4 border-emerald-500/30 dark:border-emerald-500/20 rounded-full flex items-center justify-center rotate-12 select-none pointer-events-none";
        } else if (order.status === 'pending') {
            stampStrEl.innerHTML = `<span class="font-display font-black text-amber-500 uppercase tracking-widest text">PENDING</span>`;
            stampStrEl.className = "absolute -right-2 -bottom-2 w-28 h-28 border-4 border-amber-500/30 dark:border-amber-500/20 rounded-full flex items-center justify-center rotate-12 select-none pointer-events-none";
        } else {
            stampStrEl.innerHTML = `<span class="font-display font-black text-rose-500 uppercase tracking-widest text">FAILED</span>`;
            stampStrEl.className = "absolute -right-2 -bottom-2 w-28 h-28 border-4 border-rose-500/30 dark:border-rose-500/20 rounded-full flex items-center justify-center rotate-12 select-none pointer-events-none";
        }

        // Line Items List
        const itemsContainer = document.getElementById('bill-items-list');
        itemsContainer.innerHTML = `
            <div class="flex items-center gap-3">
                <img src="${product.image}" class="w-10 h-10 rounded-xl object-cover border border-slate-100 dark:border-white/5" />
                <div class="flex-1">
                    <h4 class="font-bold text-slate-800 dark:text-slate-200 line-clamp-1">${product.title}</h4>
                    <p class="text-[10px] text-slate-400">Single-User Personal License</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-slate-800 dark:text-slate-200">${window.formatPrice(product.price)}</p>
                    <p class="text-[9px] text-slate-400 line-through">${window.formatPrice(product.mrp)}</p>
                </div>
            </div>
        `;

        // Calculate Tax components (9% CGST + 9% SGST is included in the price in India)
        const totalPaid = Number(order.amount);
        const baseSubtotal = Number((totalPaid / 1.18).toFixed(2));
        const cgstTax = Number((baseSubtotal * 0.09).toFixed(2));
        const sgstTax = Number((baseSubtotal * 0.09).toFixed(2));

        document.getElementById('breakdown-subtotal').innerText = window.formatPrice(baseSubtotal);
        document.getElementById('breakdown-cgst').innerText = window.formatPrice(cgstTax);
        document.getElementById('breakdown-sgst').innerText = window.formatPrice(sgstTax);
        document.getElementById('breakdown-grandtotal').innerText = window.formatPrice(totalPaid);

        // Did they use a discount code?
        if (product.price > totalPaid) {
            const promoRow = document.getElementById('promo-row');
            promoRow.classList.remove('hidden');
            document.getElementById('breakdown-discount').innerText = '-' + window.formatPrice(product.price - totalPaid);
        }

        skeleton.classList.add('hidden');
        contentEl.classList.remove('hidden');

    } catch (e) {
        console.error(e);
        skeleton.classList.add('hidden');
        errorEl.classList.remove('hidden');
    }
});

function getSessionUser() {
    const activeSessionKey = localStorage.getItem("digitalmohan_current_user") ? "digitalmohan_current_user" : "sellora_current_user";
    const data = localStorage.getItem(activeSessionKey);
    return data ? JSON.parse(data) : null;
}
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
