<?php
// Sellora - High Fidelity Security Payment Panel & Coupon Codes Matrix
require_once __DIR__ . '/common/config.php';
$id_param = isset($_GET['id']) ? $_GET['id'] : '1';
$ids_arr = array_map('intval', explode(',', $id_param));
$ids_json = json_encode($ids_arr);
?>
<?php include __DIR__ . '/common/header.php'; ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<!-- PAYMENTS LOADER OVERLAY -->
<div id="payment-processing-overlay" class="hidden fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex flex-col items-center justify-center text-white p-6">
    <div class="relative w-20 h-20 mb-5">
        <!-- Circular spinner -->
        <div class="absolute inset-0 border-4 border-slate-700 rounded-full"></div>
        <div class="absolute inset-0 border-4 border-sky-400 border-t-transparent rounded-full animate-spin"></div>
    </div>
    <h3 class="text-lg font-bold font-display tracking-tight">Initiating Payment...</h3>
    <p class="text-xs text-sky-300 mt-1 max-w-[80%] text-center leading-relaxed">Preparing order transaction. Please do not close this window.</p>
</div>

<!-- PAYMENTS SUCCESS SCREEN -->
<div id="payment-success-screen" class="hidden fixed inset-0 bg-slate-50 dark:bg-slate-950 z-50 flex flex-col items-center justify-center p-6 text-center">
    <div class="w-24 h-24 rounded-full bg-emerald-500/10 border-4 border-emerald-500 text-emerald-500 flex items-center justify-center text-4xl mb-6 shadow-xl animate-bounce">
        <i class="fas fa-check"></i>
    </div>
    <h1 class="text-2xl font-display font-black tracking-tight text-slate-850 dark:text-slate-100">Access Granted Successfully!</h1>
    <p class="text-sm text-slate-500 dark:text-slate-450 mt-1 max-w-sm">Order verified successfully! Your digital items are now unlocked and ready in your library.</p>
    
    <button onclick="redirectPostBuy()" class="mt-8 px-6 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-500 text-white rounded-xl text-sm font-bold shadow-xl active:scale-98 transition-all flex items-center gap-2 outline-none">
        <span>Go To Downloads Board</span>
        <i class="fas fa-arrow-right"></i>
    </button>
</div>

<main id="buy-container" class="max-w-md mx-auto px-4 pt-4 pb-20">
    <div class="mb-5 flex items-center gap-2">
        <a href="products.php" class="text-xs font-bold text-slate-400 hover:text-slate-700 dark:hover:text-slate-350 outline-none"><i class="fas fa-arrow-left mr-1.5"></i>Checkout Page</a>
    </div>

    <!-- PRODUCT BILLING HEADER CARD -->
    <div class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 mb-5 flex gap-4 backdrop-blur-md">
        <img id="checkout-image" src="https://images.unsplash.com/photo-1677442136019-21780efad99a?w=100&q=80" class="w-16 h-16 object-cover rounded-xl flex-shrink-0">
        <div class="min-w-0 flex flex-col justify-between">
            <h3 id="checkout-title" class="text-xs font-black text-slate-800 dark:text-slate-200 line-clamp-2 leading-snug">File Title Specifying details</h3>
            <span id="checkout-category" class="text-[10px] uppercase font-bold text-sky-500 tracking-wider">Premium Access</span>
        </div>
    </div>

    <!-- COUPON MATRIX APPLIER CARD -->
    <div class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 mb-5 space-y-3 backdrop-blur-md">
        <label class="block text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Apply Campaign Coupon</label>
        <div class="flex gap-2">
            <input type="text" id="coupon-code-input" placeholder="e.g. SAVE50" class="flex-grow pl-4 pr-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-semibold uppercase text-slate-800 dark:text-slate-150 border-0 outline-none focus:ring-2 focus:ring-sky-500">
            <button onclick="applyCheckoutCouponCode()" class="px-5 py-2.5 bg-slate-800 dark:bg-slate-750 hover:brightness-110 text-white font-bold text-xs rounded-xl active:scale-95 transition-all outline-none">Apply</button>
        </div>
        <p id="coupon-feedback" class="text-[11px] font-bold hidden"></p>
        <div class="flex gap-1.5 flex-wrap pt-1.5" id="dynamic-coupons-suggest">
            <span class="text-[9px] text-slate-400 italic">Checking for promotional deals...</span>
        </div>
    </div>

    <!-- COST BREAKDOWN PANEL CARD -->
    <div class="p-5 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 mb-6 space-y-4 backdrop-blur-md shadow-sm">
        <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-800 pb-2.5">Billing Breakdowns</h4>
        
        <div class="space-y-2 text-xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span>Maximum Retail Price (MRP)</span>
                <span id="bill-mrp" class="font-mono">₹----</span>
            </div>
            
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span>Product Base Discount</span>
                <span id="bill-base-discount" class="text-emerald-500 font-bold font-mono">-₹----</span>
            </div>

            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span>Coupon Reward Code Deduction</span>
                <span id="bill-coupon-discount" class="text-emerald-500 font-bold font-mono">-₹0</span>
            </div>

            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span>Simulated GST Tax (18%)</span>
                <span id="bill-tax" class="font-mono">₹--</span>
            </div>
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">Total Payable Amount</span>
            <span id="bill-payable" class="text-xl font-display font-black text-slate-800 dark:text-indigo-400 font-mono">₹---</span>
        </div>
    </div>

    <!-- PAYMENT LAUNCHER TRIGGER -->
    <button onclick="triggerRazorpaySimulation()" class="w-full py-4 bg-gradient-to-r from-sky-600 to-indigo-500 text-white rounded-2xl font-black text-sm shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-2 outline-none">
        <i class="fas fa-lock"></i>
        <span>Trigger Secure Razorpay Gateway</span>
    </button>
    
    <div class="mt-4 flex items-center justify-center gap-1.5 text-[10px] text-slate-400">
        <i class="fas fa-shield-halved"></i>
        <span>Secured 256-bit server SSL gateway channels</span>
    </div>
</main>

<script>
const urlParams = new URLSearchParams(window.location.search);
const idParam = urlParams.get('id') || '1';
const currentSpecsIds = idParam.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));
const currentSpecsId = currentSpecsIds[0] || 1;
let itemsList = [];
let itemPrice = 0;
let itemMrp = 0;
let activeCouponDeductionPercent = 0;
let activeCouponCode = "";
let payableBillTotal = 0;
let appSettings = null;

function fillCouponCode(val) {
    triggerVibe(20);
    document.getElementById('coupon-code-input').value = val;
}

function applyCheckoutCouponCode() {
    triggerVibe(30);
    const code = document.getElementById('coupon-code-input').value;
    const fb = document.getElementById('coupon-feedback');
    
    if (!code || code.trim().length === 0) {
        Toast.error("Please insert a valid promotional voucher code.");
        return;
    }
    
    fetch('/api/coupons/validate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code })
    })
    .then(async res => {
        if (!res.ok) {
            const err = await res.json();
            throw new Error(err.error || "Failed key verification.");
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            activeCouponDeductionPercent = data.discount;
            activeCouponCode = code.toUpperCase();
            
            fb.className = "text-[11px] font-bold text-emerald-500 block";
            fb.textContent = `Promo code applied successfully of ${data.discount}% discount!`;
            
            // Recalculate bill
            renderCheckoutCostBreakdown();
            Toast.success("Coupon code successfully processed!");
        }
    })
    .catch(err => {
        activeCouponDeductionPercent = 0;
        activeCouponCode = "";
        fb.className = "text-[11px] font-bold text-red-500 block";
        fb.textContent = err.message;
        renderCheckoutCostBreakdown();
        Toast.error("Coupon failed: " + err.message);
    });
}

function loadCheckoutItemSpecs() {
    const fetchPromises = currentSpecsIds.map(id => 
        fetch(`/api/products/detail/${id}`).then(res => {
            if (!res.ok) throw new Error("Unavailable product");
            return res.json();
        })
    );

    Promise.all(fetchPromises)
        .then(products => {
            itemsList = products;
            itemPrice = products.reduce((sum, p) => sum + p.price, 0);
            itemMrp = products.reduce((sum, p) => sum + p.mrp, 0);

            const isBundle = products.length > 1;
            const headerContainer = document.getElementById('checkout-image').parentElement;
            
            if (isBundle) {
                headerContainer.className = "p-4 rounded-3xl bg-indigo-50/70 dark:bg-indigo-950/20 border border-indigo-200/50 dark:border-indigo-500/10 mb-5 flex flex-col gap-3 backdrop-blur-md";
                headerContainer.innerHTML = `
                    <div class="w-full">
                        <span class="text-[9px] bg-indigo-100 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-extrabold px-2 py-0.5 rounded-md uppercase tracking-wide block mb-3 w-fit">⚡ Active Power Bundle Pack</span>
                        <div class="space-y-3">
                            ${products.map(p => `
                                <div class="flex items-center gap-3 border-b border-light-divider dark:border-white/5 pb-2.5 last:border-b-0 last:pb-0">
                                    <img src="${window.getOptimizedImageUrl(p.image, 120)}" class="w-10 h-10 object-cover rounded-lg flex-shrink-0 border border-slate-100 dark:border-slate-800">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-[11px] font-bold text-slate-800 dark:text-slate-200 line-clamp-1 truncate">${p.title}</h4>
                                        <p class="text-[9px] text-slate-450 dark:text-slate-400 font-mono mt-0.5">₹${p.price.toFixed(2)}</p>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            } else {
                const p = products[0];
                document.getElementById('checkout-image').src = window.getOptimizedImageUrl(p.image, 160);
                document.getElementById('checkout-title').textContent = p.title;
                
                fetch('/api/categories').then(res => res.json()).then(cats => {
                    const badge = document.getElementById('checkout-category');
                    const catObj = cats.find(c => c.id === p.category_id);
                    if (badge && catObj) badge.textContent = catObj.name + " • Premium File";
                });
            }
            
            renderCheckoutCostBreakdown();
        })
        .catch(() => {
            Toast.error("Catalogue record missing.");
            window.location.href = "products.php";
        });
}

function renderCheckoutCostBreakdown() {
    const isBundle = itemsList.length > 1;
    document.getElementById('bill-mrp').textContent = window.formatPrice(itemMrp);
    
    const baseDiscount = itemMrp - itemPrice;
    document.getElementById('bill-base-discount').textContent = baseDiscount === 0 ? window.formatPrice(0) : '-' + window.formatPrice(baseDiscount);
    
    let bundleDiscountAmount = 0;
    if (isBundle) {
        bundleDiscountAmount = Math.round(itemPrice * 0.35); // Apply 35% bundle discount
    }

    const couponRow = document.getElementById('bill-coupon-discount').parentElement;
    let bundleRow = document.getElementById('bill-bundle-row');
    if (isBundle) {
        if (!bundleRow) {
            bundleRow = document.createElement('div');
            bundleRow.id = 'bill-bundle-row';
            bundleRow.className = 'flex items-center justify-between text-indigo-500 hover:text-indigo-600 font-bold';
            bundleRow.innerHTML = `
                <span>Power Bundle Savings (35%)</span>
                <span class="font-mono">-₹----</span>
            `;
            couponRow.parentNode.insertBefore(bundleRow, couponRow);
        }
        bundleRow.querySelector('span:last-child').textContent = '-' + window.formatPrice(bundleDiscountAmount);
    } else if (bundleRow) {
        bundleRow.remove();
    }
    
    const currentPricePostBundle = itemPrice - bundleDiscountAmount;
    const couponDeduction = Math.round(currentPricePostBundle * activeCouponDeductionPercent / 100);
    document.getElementById('bill-coupon-discount').textContent = couponDeduction === 0 ? window.formatPrice(0) : '-' + window.formatPrice(couponDeduction);
    
    const basePostPromo = currentPricePostBundle - couponDeduction;
    const tax = Math.round(basePostPromo * 0.18);
    document.getElementById('bill-tax').textContent = window.formatPrice(tax);
    
    const finalBill = Math.max(0, basePostPromo + tax);
    document.getElementById('bill-payable').textContent = window.formatPrice(finalBill);
    payableBillTotal = finalBill;
}

function triggerRazorpaySimulation() {
    triggerVibe(80);
    const user = getSessionUser();
    if (!user) {
        Toast.info("Sign in to make product lock acquisitions.");
        setTimeout(() => window.location.href="login.php", 1000);
        return;
    }
    
    const overlay = document.getElementById('payment-processing-overlay');
    overlay.classList.remove('hidden');
    
    // Create checkout order on server
    fetch('/api/orders/create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            user_id: user.id,
            product_id: currentSpecsIds.join(','),
            discountCode: activeCouponCode,
            referral: localStorage.getItem('affiliate_referral') || ''
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const oid = data.order.razorpay_order_id;
            
            // If the payable bill is 0 (FREE discount), bypass payment dialog entirely!
            if (payableBillTotal === 0) {
                fetch('/api/orders/verify', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        razorpay_order_id: oid,
                        success: true
                    })
                })
                .then(res => res.json())
                .then(verifyData => {
                    overlay.classList.add('hidden');
                    if (verifyData.success) {
                        triggerVibe([100, 50, 100]);
                        document.getElementById('payment-success-screen').classList.remove('hidden');
                        document.getElementById('buy-container').classList.add('hidden');
                    } else {
                        Toast.error("Failed to unlock free digital product.");
                    }
                });
                return;
            }

            // Real Razorpay implementation
            const rzpKey = (appSettings && appSettings.razorpay_key) ? appSettings.razorpay_key : "rzp_test_S5bDUB1XnvePGT";
            const appName = (appSettings && appSettings.app_name) ? appSettings.app_name : "DigitalMohan";
            const themeColor = (appSettings && appSettings.theme_color) ? appSettings.theme_color : "#0284C7";
            const prodTitle = document.getElementById('checkout-title').textContent || "Digital Asset";

            const options = {
                "key": rzpKey,
                "amount": payableBillTotal * 100, // Amount is in subunits (INR Paisa)
                "currency": "INR",
                "name": appName,
                "description": prodTitle.substring(0, 255),
                "image": "https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=120&q=80",
                "prefill": {
                    "name": user.name || "",
                    "email": user.email || "",
                    "contact": user.phone || ""
                },
                "theme": {
                    "color": themeColor
                },
                "handler": function (response) {
                    // Send transaction verification to server
                    fetch('/api/orders/verify', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            razorpay_order_id: oid,
                            razorpay_payment_id: response.razorpay_payment_id,
                            success: true
                        })
                    })
                    .then(res => res.json())
                    .then(verifyData => {
                        overlay.classList.add('hidden');
                        if (verifyData.success) {
                            triggerVibe([100, 50, 100]);
                            document.getElementById('payment-success-screen').classList.remove('hidden');
                            document.getElementById('buy-container').classList.add('hidden');
                        } else {
                            Toast.error("Security verify error on Razorpay live feedback.");
                        }
                    });
                },
                "modal": {
                    "ondismiss": function() {
                        overlay.classList.add('hidden');
                        Toast.info("Payment interface closed.");
                    }
                }
            };

            const rzp = new Razorpay(options);
            rzp.on('payment.failed', function (response){
                overlay.classList.add('hidden');
                Toast.error("Transaction failed: " + response.error.description);
            });
            rzp.open();

        } else {
            overlay.classList.add('hidden');
            Toast.error("Order creation aborted. Verify gateway rules.");
        }
    })
    .catch(() => {
        overlay.classList.add('hidden');
        Toast.error("Razorpay Checkout connection failed.");
    });
}

function redirectPostBuy() {
    triggerVibe(30);
    window.location.href = "mydownloads.php";
}

function loadDynamicCouponSuggestions() {
    fetch('/api/coupons')
        .then(res => res.json())
        .then(coupons => {
            const container = document.getElementById('dynamic-coupons-suggest');
            if (!container) return;
            const active = coupons.filter(c => new Date(c.expiry).getTime() >= Date.now() && c.used_count < c.usage_limit);
            if (active.length === 0) {
                container.innerHTML = `<span class="text-[10px] text-slate-400 italic">No offer active today.</span>`;
                return;
            }
            container.innerHTML = active.map(c => `
                <button onclick="fillCouponCode('${c.code}')" class="text-[10px] font-bold text-sky-500 border border-sky-500/20 px-2 py-0.5 rounded-md bg-sky-500/5 hover:bg-sky-500/10 transition-all">
                    ${c.code} (${c.discount}% Off)
                </button>
            `).join('');
        })
        .catch(() => {
            const container = document.getElementById('dynamic-coupons-suggest');
            if (container) {
                container.innerHTML = `
                    <button onclick="fillCouponCode('SAVE50')" class="text-[10px] font-bold text-sky-500 border border-sky-500/20 px-2 py-0.5 rounded-md bg-sky-500/5 hover:bg-sky-500/10">SAVE50 (50% Off)</button>
                    <button onclick="fillCouponCode('FREE100')" class="text-[10px] font-bold text-emerald-500 border border-emerald-500/20 px-2 py-0.5 rounded-md bg-emerald-500/5 hover:bg-emerald-500/10">FREE100 (100% Off)</button>
                `;
            }
        });
}

// Action triggers 
document.addEventListener('DOMContentLoaded', () => {
    loadCheckoutItemSpecs();
    loadDynamicCouponSuggestions();
    
    // Preload portal configuration
    fetch('/api/settings')
        .then(res => res.json())
        .then(set => {
            appSettings = set;
        })
        .catch(() => {});
});

window.addEventListener('currencychange', () => {
    renderCheckoutCostBreakdown();
});
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
