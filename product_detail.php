<?php
// Sellora - High Fidelity Product Specification Details
require_once __DIR__ . '/common/config.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Find product immediately in PHP for fast SSR rendering
$currentProduct = null;
if (isset($productsArr)) {
    foreach ($productsArr as $p) {
        if ($p['id'] === $id) {
            $currentProduct = $p;
            break;
        }
    }
}

// Find category immediately in PHP for fast SSR rendering
$categoryName = 'Category';
if ($currentProduct && isset($categoriesArr)) {
    foreach ($categoriesArr as $c) {
        if ($c['id'] === $currentProduct['category_id']) {
            $categoryName = $c['name'];
            break;
        }
    }
}

// Pre-calc SSR values
$ssrTitle = $currentProduct ? $currentProduct['title'] : 'Loading Product Specifications...';
$ssrImage = $currentProduct ? $currentProduct['image'] : '';
$ssrDesc = $currentProduct ? $currentProduct['description'] : 'File compilation outline details...';
$ssrPrice = $currentProduct ? $currentProduct['price'] : 0;
$ssrMrp = $currentProduct ? $currentProduct['mrp'] : 0;
$ssrDiscount = ($ssrMrp > 0) ? round(($ssrMrp - $ssrPrice) / $ssrMrp * 105) : 0; // matching discount round percentage
if ($ssrDiscount > 99) $ssrDiscount = 99;
if ($ssrDiscount <= 0) $ssrDiscount = 0;
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN SPECIFICATION SCREEN -->
<?php if (!$currentProduct): ?>
<main id="detail-loading-screen" class="max-w-md mx-auto px-4 pt-10 pb-24 text-center">
    <div class="h-10 w-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
    <p class="text-sm text-slate-400">Opening secure product asset coordinates...</p>
</main>
<?php endif; ?>

<main id="detail-actual-screen" class="<?php echo $currentProduct ? '' : 'hidden'; ?> max-w-md mx-auto px-4 pt-4 pb-28">
    
    <!-- Dynamic Category Badge & Back Nav button -->
    <div class="flex items-center justify-between mb-4">
        <a href="products.php" class="flex items-center gap-1.5 text-xs font-bold text-slate-450 hover:text-slate-700 dark:hover:text-slate-350 outline-none">
            <i class="fas fa-arrow-left"></i>
            <span>Browse Products</span>
        </a>
        <span id="detail-category-badge" class="px-3 py-1 rounded-full bg-sky-100 dark:bg-sky-950 text-sky-600 dark:text-sky-400 text-[10px] font-black uppercase"><?= htmlspecialchars($categoryName) ?></span>
    </div>

    <!-- 1. TOUCH TAP IMAGE ZOOM CARD -->
    <div class="relative rounded-3xl overflow-hidden border border-slate-200/50 dark:border-white/5 bg-black h-56 mb-5 shadow-md">
        <img id="detail-image" src="<?= htmlspecialchars($ssrImage) ?>" alt="Thumbnail" class="w-full h-full object-cover transition-transform duration-300 transform origin-center cursor-zoom-in" onclick="handleImageZoomTap(this)">
        <div id="zoom-hint" class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-md text-white text-[9px] font-bold px-2 py-1 rounded-lg pointer-events-none transition-opacity duration-300">
            <i class="fas fa-expand mr-1"></i>Tap to zoom
        </div>
    </div>

    <!-- 2. PRODUCT TITLE & SPECIFICATIONS -->
    <div class="mb-5">
        <div class="flex items-start justify-between gap-4">
            <h1 id="detail-title" class="text-xl font-display font-black leading-snug text-slate-800 dark:text-slate-100 flex-1"><?= htmlspecialchars($ssrTitle) ?></h1>
            <button onclick="openPremiumShareModal()" class="w-10 h-10 rounded-xl bg-slate-150/40 hover:bg-slate-200/50 dark:bg-slate-800/80 dark:hover:bg-slate-750 active:scale-95 transition-all text-slate-600 dark:text-slate-300 flex items-center justify-center flex-shrink-0 outline-none" title="Share & QR Code">
                <i class="fas fa-share-nodes text-sm"></i>
            </button>
        </div>
        
        <div class="flex items-center gap-2 mt-2.5">
            <div class="flex text-amber-400 text-xs">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
            </div>
            <span class="text-xs font-semibold text-slate-800 dark:text-slate-350">5.0 Star Rating</span>
            <span class="text-xs text-slate-400">• Verified Purchases</span>
        </div>
        
        <!-- Pricing line status -->
        <div class="flex items-baseline gap-2.5 mt-4">
            <span id="detail-price-tag" class="text-3xl font-display font-black text-slate-800 dark:text-slate-100 font-mono">₹<?= $currentProduct ? number_format($ssrPrice, 2) : '----' ?></span>
            <span id="detail-mrp-tag" class="text-sm text-slate-400 dark:text-slate-500 line-through font-mono">₹<?= $currentProduct ? number_format($ssrMrp, 2) : '----' ?></span>
            <span id="detail-discount-tag" class="text-xs text-red-500 font-extrabold bg-red-50 dark:bg-red-950/30 px-2 py-0.5 rounded-md"><?= $ssrDiscount ?>% Off</span>
        </div>
    </div>

    <!-- MAIN INLINE PURCHASE CARD (Visible Non-Sticky) -->
    <div class="p-5 rounded-3xl bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-950 border border-slate-200/60 dark:border-white/5 shadow-md mb-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 dark:text-slate-500">Unlocking Price</p>
                <div class="flex items-baseline gap-2">
                    <span id="inline-price-tag" class="text-2xl font-black font-mono text-slate-850 dark:text-slate-100">₹<?= $currentProduct ? number_format($ssrPrice, 2) : '----' ?></span>
                    <span id="inline-mrp-original" class="text-xs text-slate-400 line-through font-mono">₹<?= $currentProduct ? number_format($ssrMrp, 2) : '----' ?></span>
                </div>
            </div>
            <div id="inline-save-badge" class="px-2.5 py-1 rounded-xl bg-orange-100 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 text-[10px] font-black uppercase">
                Save <?= $ssrDiscount ?>%
            </div>
        </div>
        
        <button id="inline-action-btn" class="w-full py-4 px-4 bg-sky-500 hover:bg-sky-600 text-white font-black rounded-2xl text-sm shadow-md active:scale-[0.98] transition-all flex items-center justify-center gap-2 outline-none">
            <i class="fas fa-credit-card"></i>
            <span>Load Credentials...</span>
        </button>

        <!-- Dynamic Live Demo Preview Button -->
        <a id="inline-preview-btn" href="#" target="_blank" class="hidden mt-3 w-full py-3 px-4 bg-slate-800 hover:bg-slate-755 dark:bg-slate-800 dark:hover:bg-slate-750 text-white font-extrabold rounded-2xl text-xs shadow-md active:scale-[0.98] transition-all flex items-center justify-center gap-2 outline-none border border-slate-200 dark:border-slate-850">
            <i class="fas fa-eye text-sky-400 animate-pulse"></i>
            <span>Live Demo & Preview <i class="fas fa-external-link-alt text-[9px] ml-0.5"></i></span>
        </a>
        
        <p class="text-[10px] text-center text-slate-400 dark:text-slate-500 mt-2.5">
            <i class="fas fa-shield-halved mr-1"></i> 100% Encrypted Transactions & Instant File Delivery
        </p>
    </div>

    <!-- 3. DETAILED TABBED VIEWS (Description, Deliverables Asset Specs) -->
    <div class="mb-6 space-y-4">
        <div class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2.5">File Description</h3>
            <p id="detail-desc" class="text-sm leading-relaxed text-slate-650 dark:text-slate-300"><?= htmlspecialchars($ssrDesc) ?></p>
        </div>

        <div class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2.5">What You'll Save Down</h3>
            <ul class="space-y-2">
                <li class="flex items-center gap-2 text-xs text-slate-650 dark:text-slate-300">
                    <i class="fas fa-circle-check text-emerald-500 text-sm"></i>
                    <span>Secure Zip containing document PDF formula spreadsheet directory templates.</span>
                </li>
                <li class="flex items-center gap-2 text-xs text-slate-650 dark:text-slate-300">
                    <i class="fas fa-circle-check text-emerald-500 text-sm"></i>
                    <span>Exquisite anti-piracy secure custom access rights token.</span>
                </li>
                <li class="flex items-center gap-2 text-xs text-slate-650 dark:text-slate-300">
                    <i class="fas fa-circle-check text-emerald-500 text-sm"></i>
                    <span>Lifetime updates instantly via high-speed PWA download buffers.</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- PREMIUM COGNITIVE INTERACTIVE SANDBOX PLAYGROUND -->
    <div id="premium-sandbox-panel" class="hidden mb-6 p-5 rounded-3xl bg-slate-900 text-slate-100 border border-slate-800 shadow-2xl relative overflow-hidden">
        <!-- Floating neon lights decoration background -->
        <div class="absolute -top-12 -left-12 w-28 h-28 bg-sky-500/10 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-12 -right-12 w-28 h-28 bg-indigo-500/10 rounded-full blur-2xl"></div>
        
        <div class="flex items-center gap-2 mb-4 relative z-10">
            <span class="flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="text-[10px] font-black uppercase tracking-widest text-sky-450 text-sky-450">DigitalMohan Sandbox</span>
            <div class="h-px bg-slate-800 flex-grow ml-1"></div>
        </div>

        <h4 class="text-base font-display font-black tracking-tight mb-1 relative z-10 text-white flex items-center gap-1.5">
            <i class="fas fa-cubes text-sky-455 text-sky-400 text-sm"></i>
            <span>Interactive Resource Demo</span>
        </h4>
        <p class="text-[11px] text-slate-400 mb-5 relative z-10">Experience the high-performance utility of this asset container live.</p>

        <!-- Dynamic Container where sandbox forms are rendered based on Category ID -->
        <div id="sandbox-workspace" class="relative z-10 space-y-4">
            <!-- Rendered in JS dynamically -->
        </div>
    </div>

    <!-- 4. RECENTLY VIEWED CONTAINER (STREAMS FROM CLIENT LOCALSTORAGE) -->
    <div class="mb-6">
        <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3.5">Recently Viewed</h4>
        <div id="recently-viewed-list" class="flex gap-3 overflow-x-auto no-scrollbar scroll-smooth">
            <!-- Loaded dynamically in JS -->
            <p class="text-[11px] text-slate-450 dark:text-slate-550 italic">No recently studied listings yet.</p>
        </div>
    </div>

    <!-- 5. RELATED PRODUCTS CAROUSEL -->
    <div class="mb-4">
        <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3.5">Matched Related Workpacks</h4>
        <div id="related-carousel" class="flex gap-3 overflow-x-auto no-scrollbar scroll-smooth">
            <!-- Populated dynamically -->
            <p class="text-[11px] text-slate-450 dark:text-slate-550 italic">Gathering relevant resources...</p>
        </div>
    </div>

    <!-- 6. USER RATINGS & INTEGRATED REVIEWS ROWS -->
    <div class="mb-6 p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md">
        <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-slate-800 pb-2.5">
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Reviews & Verification</h4>
            <div class="flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-450 font-bold">
                <i class="fas fa-shield-check"></i>
                <span>100% Verified Sales</span>
            </div>
        </div>
        
        <div id="reviews-list-container" class="space-y-3.5 divide-y divide-slate-100 dark:divide-slate-800">
            <!-- JS loaded reviews -->
        </div>

        <!-- Add Review Form (only for bought users/mock view) -->
        <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-800">
            <h5 class="text-xs font-extrabold text-slate-700 dark:text-slate-350 mb-2">Leave a review</h5>
            <div class="flex gap-1.5 mb-2" id="input-star-row">
                <button onclick="setReviewScoreInput(1)" class="text-slate-300 hover:text-amber-500 text-xs"><i class="fas fa-star"></i></button>
                <button onclick="setReviewScoreInput(2)" class="text-slate-300 hover:text-amber-500 text-xs"><i class="fas fa-star"></i></button>
                <button onclick="setReviewScoreInput(3)" class="text-slate-300 hover:text-amber-500 text-xs"><i class="fas fa-star"></i></button>
                <button onclick="setReviewScoreInput(4)" class="text-slate-300 hover:text-amber-500 text-xs"><i class="fas fa-star"></i></button>
                <button onclick="setReviewScoreInput(5)" class="text-slate-300 hover:text-amber-500 text-xs"><i class="fas fa-star"></i></button>
            </div>
            <textarea id="input-review-text" rows="2" placeholder="Write reviews on product deliveries details..." class="w-full p-2 rounded-xl text-xs bg-slate-100 dark:bg-slate-850 text-slate-700 dark:text-slate-200 border-0 outline-none focus:ring-2 focus:ring-sky-500"></textarea>
            <button onclick="submitSpecificationReview()" class="mt-2 px-3 py-1.5 bg-sky-500 text-white rounded-lg text-xs font-bold hover:brightness-105 active:scale-[0.98] transition-all outline-none">Post Review</button>
        </div>
    </div>

    <!-- STICKY FOOTER BUY NOW / OR START SECURE DOWNLOAD CONTAINER -->
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-t border-slate-200/50 dark:border-white/5 py-4 px-6 shadow-2xl rounded-t-3xl transition-all">
        <div class="max-w-md mx-auto flex items-center justify-between gap-4">
            <div class="flex flex-col">
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">Payable Amount</span>
                <span id="sticky-payable-amount" class="text-2xl font-display font-black text-slate-850 dark:text-slate-100 font-mono">₹---</span>
            </div>
            
            <!-- Dynamic Sticky footer Live Preview Button -->
            <a id="sticky-preview-btn" href="#" target="_blank" class="hidden py-3 px-3.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-sm shadow-md active:scale-98 hover:brightness-110 flex items-center justify-center outline-none border border-slate-200 dark:border-slate-800" title="Live Preview">
                <i class="fas fa-eye text-sky-500"></i>
            </a>

            <button id="sticky-action-btn" class="flex-1 py-3.5 bg-gradient-to-r from-sky-600 to-indigo-500 text-white font-bold rounded-xl text-sm shadow-xl active:scale-98 hover:brightness-110 flex items-center justify-center gap-2 hover:shadow-sky-500/10 transition-all outline-none">
                <i class="fas fa-cart-shopping"></i>
                <span>Buy Access Now</span>
            </button>
        </div>
    </div>

    <!-- PREMIUM SHARE & DYNAMIC INTERACTIVE QR CODE MODAL -->
    <div id="premium-share-modal" class="hidden fixed inset-0 bg-slate-950/70 backdrop-blur-md z-50 flex items-center justify-center p-6 transition-all duration-300 opacity-0">
        <div class="w-full max-w-sm bg-white dark:bg-slate-900 border border-slate-100 dark:border-white/5 rounded-3xl p-6 shadow-2xl relative overflow-hidden transform scale-90 transition-all duration-300" id="share-modal-card">
            <div class="absolute -top-10 -right-10 w-24 h-24 bg-sky-500/10 rounded-full blur-xl"></div>
            
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-display font-black text-slate-800 dark:text-slate-100 text-base">Share Digital Resource</h3>
                <button onclick="closePremiumShareModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center text-xs outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="flex flex-col items-center text-center space-y-4 mb-6">
                <!-- QR Image code generator from public API -->
                <div class="w-40 h-40 bg-white border border-slate-200/65 rounded-2.5xl p-2.5 flex items-center justify-center shadow-md relative">
                    <img id="detail-share-qr" src="" alt="Dynamic Resource QR Code" class="w-full h-full object-contain rounded-lg">
                    <!-- Elegant branding overlay mark -->
                    <span class="absolute bottom-1 right-1 bg-sky-500/95 text-white text-[7px] font-bold px-1 rounded">DM</span>
                </div>
                <div>
                    <h4 id="share-product-name" class="font-bold text-sm text-slate-705 dark:text-slate-200 line-clamp-1">Product Title</h4>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">Scan to instantly access on mobile device</p>
                </div>
            </div>
            
            <!-- Quick share CTAs -->
            <div class="space-y-3">
                <button onclick="triggerNativeSystemShare()" class="w-full h-11 bg-sky-600 hover:bg-sky-500 text-white text-xs font-black rounded-xl transition-all shadow-md active:scale-95 flex items-center justify-center gap-2 outline-none">
                    <i class="fas fa-circle-nodes"></i>
                    <span>Share on Native Device</span>
                </button>
                <div class="flex gap-2">
                    <input type="text" id="share-link-input" readonly class="flex-grow pl-3 pr-2 bg-slate-100 dark:bg-slate-850 rounded-xl text-[11px] font-mono text-slate-600 dark:text-slate-350 outline-none border-0 text-ellipsis">
                    <button onclick="copyShareResourceLink()" class="px-4 bg-slate-800 dark:bg-slate-750 hover:brightness-110 text-white text-xs font-black rounded-xl active:scale-95 transition-all outline-none">Copy</button>
                </div>
            </div>
        </div>
    </div>

    <!-- REAL-TIME IMMERSIVE PRODUCT PREVIEW MODAL -->
    <div id="dynamic-preview-modal" class="hidden fixed inset-0 bg-slate-950/90 backdrop-blur-xl z-[70] flex items-center justify-center p-3 md:p-6 transition-all duration-300 opacity-0">
        <div class="w-full h-full max-w-6xl bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl flex flex-col overflow-hidden transform scale-95 transition-all duration-300 relative" id="preview-modal-card">
            
            <!-- Floating neon glow background accents -->
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-sky-500/10 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px] pointer-events-none"></div>

            <!-- MODAL HEADER -->
            <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between gap-4 bg-slate-905 relative z-10 shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <div>
                        <h3 id="preview-modal-title" class="font-display font-black text-white text-xs md:text-sm tracking-tight line-clamp-1">Product Live Sandbox</h3>
                        <p class="text-[9px] uppercase font-bold tracking-widest text-slate-400 mt-0.5" id="preview-modal-subtitle">Direct Asset Preview Container</p>
                    </div>
                </div>

                <!-- VIEWPORT TOGGLERS (ONLY visible for iframe type) -->
                <div id="preview-viewport-controls" class="hidden flex items-center bg-slate-950/70 p-1 rounded-xl border border-slate-800 gap-1 select-none">
                    <button onclick="setPreviewViewport('desktop')" id="vp-btn-desktop" class="px-2.5 py-1.5 rounded-lg text-[10px] uppercase font-black tracking-wider transition-all duration-200 outline-none flex items-center gap-1.5 bg-sky-500 text-white">
                        <i class="fas fa-desktop text-xs"></i><span class="hidden sm:inline">Desktop</span>
                    </button>
                    <button onclick="setPreviewViewport('tablet')" id="vp-btn-tablet" class="px-2.5 py-1.5 rounded-lg text-[10px] uppercase font-black tracking-wider transition-all duration-200 outline-none flex items-center gap-1.5 text-slate-400 hover:text-white">
                        <i class="fas fa-tablet-screen-button text-xs"></i><span class="hidden sm:inline">Tablet</span>
                    </button>
                    <button onclick="setPreviewViewport('mobile')" id="vp-btn-mobile" class="px-2.5 py-1.5 rounded-lg text-[10px] uppercase font-black tracking-wider transition-all duration-200 outline-none flex items-center gap-1.5 text-slate-400 hover:text-white">
                        <i class="fas fa-mobile-button text-xs"></i><span class="hidden sm:inline">Mobile</span>
                    </button>
                </div>

                <!-- ACTIONS -->
                <div class="flex items-center gap-2">
                    <a id="preview-new-tab-btn" href="#" target="_blank" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center text-xs outline-none transition-all" title="Open in New Tab">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                    </a>
                    <button onclick="closeDynamicPreviewModal()" class="w-8 h-8 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-450 hover:text-white flex items-center justify-center text-xs outline-none transition-all duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- MODAL CLIENT WORKSPACE / VIEWPORTS -->
            <div class="flex-grow p-4 md:p-6 bg-slate-950/30 relative z-10 flex flex-col items-center justify-center overflow-auto min-h-0">
                <!-- Wrapper to handle mockup scaling and width boundaries -->
                <div id="preview-viewport-wrapper" class="w-full h-full max-w-full flex justify-center items-center transition-all duration-300 relative">
                    
                    <!-- Viewport 1: Iframe Embedding -->
                    <iframe id="preview-iframe-element" src="" class="hidden w-full h-full border-0 rounded-2xl bg-white shadow-2xl transition-all duration-300"></iframe>

                    <!-- Viewport 2: PDF Presentation Reader -->
                    <div id="preview-pdf-unfolded" class="hidden w-full h-full flex flex-col items-center justify-center">
                        <iframe id="preview-pdf-iframe" src="" class="w-full h-full border-0 rounded-2xl bg-slate-950 shadow-2xl"></iframe>
                    </div>

                    <!-- Viewport 3: Screenshot Carousel Grid -->
                    <div id="preview-image-gallery" class="hidden w-full h-full flex flex-col gap-4">
                        <!-- Big Main focus view -->
                        <div class="flex-grow rounded-2xl border border-slate-800 bg-slate-950/60 overflow-hidden relative flex items-center justify-center mb-0">
                            <img id="gallery-focused-image" src="" alt="Premium Showcase Layout" class="max-h-full max-w-full object-contain rounded-xl select-none">
                            <button onclick="navGalleryFocus(-1)" class="absolute left-3 w-10 h-10 rounded-full bg-slate-900/80 border border-slate-800 text-white hover:bg-sky-600 flex items-center justify-center text-xs outline-none transition-all">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button onclick="navGalleryFocus(1)" class="absolute right-3 w-10 h-10 rounded-full bg-slate-900/80 border border-slate-800 text-white hover:bg-sky-600 flex items-center justify-center text-xs outline-none transition-all">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <!-- Mini Row list selector -->
                        <div id="gallery-carousel-thumbnails" class="h-16 md:h-20 flex gap-2 overflow-x-auto no-scrollbar justify-center py-1">
                            <!-- Populated in JS -->
                        </div>
                    </div>

                    <!-- Viewport 4: Video Player Embed -->
                    <div id="preview-video-container" class="hidden w-full max-w-3xl aspect-video rounded-3xl overflow-hidden shadow-2xl bg-slate-950">
                        <iframe id="preview-video-iframe" src="" class="w-full h-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>

                    <!-- Fallback / Empty Status Loader -->
                    <div id="preview-viewport-loader" class="absolute inset-0 flex flex-col items-center justify-center gap-3">
                        <div class="w-10 h-10 border-4 border-sky-500/20 border-t-sky-500 rounded-full animate-spin"></div>
                        <p class="text-[10px] tracking-widest uppercase text-slate-500 font-extrabold font-mono">Unfolding Live Asset Preview...</p>
                    </div>

                </div>
            </div>

            <!-- FOOTER SPARK CTAS -->
            <div class="px-5 py-3.5 border-t border-slate-800 bg-slate-900/90 flex flex-col sm:flex-row items-center justify-between gap-3 text-left relative z-10 shrink-0">
                <div class="text-center sm:text-left">
                    <span id="preview-footer-discount" class="text-[9px] font-black tracking-widest text-orange-450 uppercase block mb-0.5">LIMITED EDITION RESOURCE SALE IN PROGRESS</span>
                    <p id="preview-footer-title" class="text-[11px] font-bold text-slate-350 line-clamp-1">Get authentic legal bundle template licenses directly within 10 seconds of download.</p>
                </div>
                <button onclick="triggerCheckoutFromPreview()" class="w-full sm:w-auto px-5 py-2.5 bg-sky-500 hover:bg-sky-600 font-black text-white rounded-xl text-xs hover:shadow-lg active:scale-98 flex items-center justify-center gap-1.5 shrink-0 outline-none select-none transition-all">
                    <span>Unlock This Asset Specs</span>
                    <i class="fas fa-angles-right text-[10px]"></i>
                </button>
            </div>
            
        </div>
    </div>

</main>

<script>
const currentSpecsId = <?= $id ?>;
const serverProducts = <?= $serverProducts ?>;
const serverCategories = <?= $serverCategories ?>;
let selectedRating = 5;
let currentLoadedProduct = null;

// Share and Dynamic QR script utilities
function openPremiumShareModal() {
    triggerVibe(40);
    const m = document.getElementById('premium-share-modal');
    const card = document.getElementById('share-modal-card');
    const qrImg = document.getElementById('detail-share-qr');
    const input = document.getElementById('share-link-input');
    const nameLabel = document.getElementById('share-product-name');
    
    if (currentLoadedProduct) {
        nameLabel.textContent = currentLoadedProduct.title;
    }
    
    const shareUrl = window.location.href;
    input.value = shareUrl;
    
    // Call public high-availability secure QR generator API
    qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(shareUrl)}`;
    
    m.classList.remove('hidden');
    setTimeout(() => {
        m.classList.remove('opacity-0');
        card.classList.remove('scale-90');
    }, 20);
}

function closePremiumShareModal() {
    triggerVibe(20);
    const m = document.getElementById('premium-share-modal');
    const card = document.getElementById('share-modal-card');
    m.classList.add('opacity-0');
    card.classList.add('scale-90');
    setTimeout(() => {
        m.classList.add('hidden');
    }, 300);
}

async function triggerNativeSystemShare() {
    triggerVibe(30);
    if (!currentLoadedProduct) return;
    
    const info = {
        title: currentLoadedProduct.title,
        text: `Check out ${currentLoadedProduct.title} on DigitalMohan!`,
        url: window.location.href
    };
    
    if (navigator.share) {
        try {
            await navigator.share(info);
            Toast.success("Shared successfully!");
        } catch(err) {
            copyShareResourceLink();
        }
    } else {
        copyShareResourceLink();
    }
}

function copyShareResourceLink() {
    triggerVibe(30);
    const input = document.getElementById('share-link-input');
    input.select();
    input.setSelectionRange(0, 99999);
    
    try {
        navigator.clipboard.writeText(input.value);
        Toast.success("Product Link copied to clipboard!");
    } catch(err) {
        document.execCommand('copy');
        Toast.success("Verified link copied!");
    }
}

function handleImageZoomTap(img) {
    triggerVibe(30);
    const hint = document.getElementById('zoom-hint');
    if (img.classList.contains('scale-[1.65]')) {
        img.classList.remove('scale-[1.65]');
        hint.innerHTML = '<i class="fas fa-expand mr-1"></i>Tap to zoom';
    } else {
        img.classList.add('scale-[1.65]');
        hint.innerHTML = '<i class="fas fa-compress mr-1"></i>Tap to restore';
    }
}

function setReviewScoreInput(score) {
    triggerVibe(20);
    selectedRating = score;
    const buttons = document.getElementById('input-star-row').children;
    for (let i = 0; i < buttons.length; i++) {
        if (i < score) {
            buttons[i].className = "text-amber-500 text-xs";
        } else {
            buttons[i].className = "text-slate-300 text-xs hover:text-amber-500";
        }
    }
}

function submitSpecificationReview() {
    triggerVibe(50);
    const user = getSessionUser();
    if (!user) {
        Toast.info("Please login to post comments!");
        return;
    }
    
    const comment = document.getElementById('input-review-text').value;
    if (!comment || comment.trim().length === 0) {
        Toast.error("Review comment description cannot be blank!");
        return;
    }
    
    fetch('/api/reviews', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            user_id: user.id,
            product_id: currentSpecsId,
            rating: selectedRating,
            comment: comment
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("Review submitted to file directories.");
            document.getElementById('input-review-text').value = '';
            loadProductReviewRows();
        }
    });
}

function loadProductSpecificationDetails() {
    let allProducts = serverProducts || [];
    
    // 1. Render server-side products instantly if they exist
    if (allProducts && allProducts.length > 0) {
        const p = allProducts.find(prod => prod.id === currentSpecsId);
        if (p) {
            renderProductDetailsWithData(p, allProducts);
        }
    }
    
    // 2. Perform background revalidation to ensure accuracy & fresh logs
    // First, let's fetch the exact product details to get absolute fresh attributes (including preview_url)
    fetch(`/api/products/detail/${currentSpecsId}`)
        .then(res => res.json())
        .then(product => {
            if (!product || product.error) {
                throw new Error("Product specs mismatch");
            }
            
            // Now fetch the full product suite for recommendations and widgets in parallel
            fetch('/api/products')
                .then(res => res.json())
                .then(productsList => {
                    const cleanProducts = Array.isArray(productsList) ? productsList : (productsList && productsList.products ? productsList.products : []);
                    renderProductDetailsWithData(product, cleanProducts);
                })
                .catch(() => {
                    // Fallback to single product rendering with local array if full list fetch fails
                    renderProductDetailsWithData(product, allProducts);
                });
        })
        .catch(() => {
            // General fallback: fetch full products list directly
            fetch('/api/products')
                .then(res => res.json())
                .then(productsList => {
                    const cleanProducts = Array.isArray(productsList) ? productsList : (productsList && productsList.products ? productsList.products : []);
                    const p = cleanProducts.find(prod => prod.id === currentSpecsId);
                    if (!p) {
                        if (!currentLoadedProduct) {
                            Toast.error("No product found matching specified values.");
                            window.location.href = "products.php";
                        }
                        return;
                    }
                    renderProductDetailsWithData(p, cleanProducts);
                })
                .catch(() => {
                    if (!currentLoadedProduct) {
                        Toast.error("No product found matching specified values.");
                        window.location.href = "products.php";
                    }
                });
        });
}

function renderProductDetailsWithData(p, allProducts) {
    currentLoadedProduct = p;
    
    // Build visual profiles
    document.getElementById('detail-image').src = p.image;
    document.getElementById('detail-title').textContent = p.title;
    document.getElementById('detail-desc').textContent = p.description;
    document.getElementById('detail-price-tag').textContent = window.formatPrice(p.price);
    document.getElementById('detail-mrp-tag').textContent = window.formatPrice(p.mrp);
    
    const discount = Math.round((p.mrp - p.price) / p.mrp * 100);
    document.getElementById('detail-discount-tag').textContent = `${discount}% OFF`;
    document.getElementById('sticky-payable-amount').textContent = window.formatPrice(p.price);
    
    if (document.getElementById('inline-price-tag')) {
        document.getElementById('inline-price-tag').textContent = window.formatPrice(p.price);
        document.getElementById('inline-mrp-original').textContent = window.formatPrice(p.mrp);
        document.getElementById('inline-save-badge').textContent = `Save ${discount}%`;
    }

    // Configure Live Preview urls Dynamically
    const inlinePreviewBtn = document.getElementById('inline-preview-btn');
    const stickyPreviewBtn = document.getElementById('sticky-preview-btn');
    
    const setupPreviewTrigger = (btn) => {
        if (!btn) return;
        if (p.preview_url || p.preview_type === 'images' || p.preview_type === 'video') {
            btn.classList.remove('hidden');
            btn.href = "javascript:void(0)";
            btn.onclick = (e) => {
                e.preventDefault();
                openDynamicPreviewModal();
            };
        } else {
            btn.classList.add('hidden');
        }
    };
    
    setupPreviewTrigger(inlinePreviewBtn);
    setupPreviewTrigger(stickyPreviewBtn);
    
    // Use server categories first if available, otherwise fetch
    const cats = serverCategories || [];
    if (cats && cats.length > 0) {
        const badge = document.getElementById('detail-category-badge');
        const catObj = cats.find(c => c.id === p.category_id);
        if (badge && catObj) badge.textContent = catObj.name;
    } else {
        fetch('/api/categories').then(res => res.json()).then(cats => {
            const badge = document.getElementById('detail-category-badge');
            const catObj = cats.find(c => c.id === p.category_id);
            if (badge && catObj) badge.textContent = catObj.name;
        });
    }
    
    // Handle LocalStorage Recently Viewed listing 
    saveToRecentlyViewed(p);
    
    // Configure Sticky Action purchase check based on order logs
    configureDetailAccessButton(p, allProducts);

    // Fetch reviews 
    loadProductReviewRows();

    // Populate Recently Viewed
    populateRecentlyViewedWidgets(allProducts);

    // Populate Related Products
    populateRelatedRecommendations(allProducts, p.category_id);

    // Render custom sandbox interaction workspace
    renderSandboxWorkspace(p.category_id);

    // Hide main specs loaders 
    const loadingScreen = document.getElementById('detail-loading-screen');
    const actualScreen = document.getElementById('detail-actual-screen');
    if (loadingScreen) loadingScreen.classList.add('hidden');
    if (actualScreen) actualScreen.classList.remove('hidden');
}

function openSmartBundleUpsellDrawer(p, allProducts) {
    const list = (allProducts || []).filter(prod => prod.category_id === p.category_id && prod.id !== p.id && (!prod.status || prod.status === 'active'));
    
    // Fallback if no matching bundle product exists in category
    if (list.length === 0) {
        window.location.href = `buy.php?id=${p.id}`;
        return;
    }

    const companion = list[0];
    const originalSum = p.price + companion.price;
    const bundlePrice = Math.round(originalSum * 0.65); // 35% Bundle Savings
    const savingsAmount = originalSum - bundlePrice;

    let drawer = document.getElementById('upsell-drawer-container');
    if (!drawer) {
        drawer = document.createElement('div');
        drawer.id = 'upsell-drawer-container';
        drawer.className = 'fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300 bg-slate-950/70 backdrop-blur-sm';
        document.body.appendChild(drawer);
    }

    drawer.innerHTML = `
        <div class="absolute inset-x-0 bottom-0 bg-white dark:bg-slate-900 rounded-t-[32px] border-t border-slate-150 dark:border-white/5 shadow-2xl overflow-hidden transform translate-y-full transition-transform duration-300 max-w-md mx-auto" id="upsell-drawer-panel">
            <div class="w-12 h-1.5 bg-slate-200 dark:bg-slate-850 rounded-full mx-auto my-3 cursor-pointer" onclick="closeSmartBundleUpsellDrawer()"></div>

            <div class="px-5 text-center mt-2">
                <div class="inline-flex items-center gap-1.5 bg-indigo-100 dark:bg-indigo-950/60 border border-indigo-200/20 px-2.5 py-1 rounded-full text-[10px] text-indigo-600 dark:text-indigo-400 font-extrabold uppercase tracking-wider mb-2">
                    <i class="fas fa-gift text-[9px] animate-pulse"></i>
                    <span>Bundle & Save 35% Active Offer</span>
                </div>
                <h3 class="text-base font-display font-extrabold text-slate-800 dark:text-slate-100 leading-tight">Combine Companion Assets! ⚡</h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 max-w-[280px] mx-auto leading-normal">Upgrade to our power layout bundle package to get matching assets added to your key instantly.</p>
            </div>

            <div class="px-5 mt-4">
                <div class="p-3.5 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/10 border border-indigo-100/30 dark:border-indigo-500/10 flex items-center justify-between gap-3 relative overflow-hidden">
                    <!-- Current Product -->
                    <div class="flex-1 min-w-0 flex items-center gap-2">
                        <img src="${window.getOptimizedImageUrl(p.image, 120)}" class="w-10 h-10 object-cover rounded-lg flex-shrink-0 border border-slate-100 dark:border-slate-800">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-[10px] font-bold text-slate-700 dark:text-slate-350 truncate line-clamp-1">${p.title}</h4>
                            <span class="text-[9px] text-slate-405 dark:text-slate-400 font-mono font-bold">₹${p.price}</span>
                        </div>
                    </div>

                    <!-- Connective Plus Indicator -->
                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-105 bg-indigo-505 bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-black select-none">
                        +
                    </div>

                    <!-- Companion Product -->
                    <div class="flex-1 min-w-0 flex items-center gap-2">
                        <img src="${window.getOptimizedImageUrl(companion.image, 120)}" class="w-10 h-10 object-cover rounded-lg flex-shrink-0 border border-slate-100 dark:border-slate-800">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-[10px] font-bold text-slate-705 dark:text-slate-300 truncate line-clamp-1">${companion.title}</h4>
                            <span class="text-[9px] text-slate-405 dark:text-slate-400 font-mono font-bold">₹${companion.price}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-5 mt-4 space-y-2 text-center">
                <div class="flex items-center justify-center gap-2">
                    <span class="text-xs text-slate-400 line-through font-mono">₹${originalSum.toFixed(2)}</span>
                    <span class="text-lg font-display font-black text-emerald-500 font-mono">₹${bundlePrice.toFixed(2)}</span>
                    <span class="text-[9px] bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 font-black px-1.5 py-0.5 rounded-md uppercase tracking-wider">Bundle price</span>
                </div>
                <p class="text-[10px] text-indigo-500 dark:text-indigo-400 font-extrabold flex items-center justify-center gap-1">
                    <i class="fas fa-sparkles text-[9px]"></i>
                    <span>Unlocks dual licenses. You save ₹${savingsAmount.toFixed(2)} instantly!</span>
                </p>
            </div>

            <div class="p-5 mt-4 bg-slate-50 dark:bg-slate-900/40 border-t border-slate-100 dark:border-white/5 space-y-2.5">
                <button onclick="triggerSmartBundleCheckout(${p.id}, ${companion.id})" class="w-full py-3.5 bg-gradient-to-r from-sky-600 to-indigo-550 bg-gradient-to-r from-sky-600 to-indigo-500 text-white font-black text-xs rounded-xl shadow-lg active:scale-98 hover:brightness-105 transition-all flex items-center justify-center gap-2 outline-none">
                    <i class="fas fa-cart-plus"></i>
                    <span>Upgrade Combo & Save ₹${savingsAmount}</span>
                </button>
                <button onclick="triggerIndividualCheckoutOnly(${p.id})" class="w-full py-2.5 bg-transparent border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 font-bold text-xs rounded-xl hover:bg-slate-100 dark:hover:bg-slate-850 transition-all outline-none">
                    <span>No thanks, checkout "${p.title}" only</span>
                </button>
            </div>
        </div>
    `;

    drawer.classList.remove('hidden');
    setTimeout(() => {
        drawer.style.opacity = '1';
        document.getElementById('upsell-drawer-panel').classList.remove('translate-y-full');
    }, 50);
}

function closeSmartBundleUpsellDrawer() {
    triggerVibe(20);
    const panel = document.getElementById('upsell-drawer-panel');
    const drawer = document.getElementById('upsell-drawer-container');
    if (panel) panel.classList.add('translate-y-full');
    setTimeout(() => {
        if (drawer) {
            drawer.style.opacity = '0';
            drawer.classList.add('hidden');
        }
    }, 300);
}

function triggerSmartBundleCheckout(pId, companionId) {
    triggerVibe(80);
    window.location.href = `buy.php?id=${pId},${companionId}`;
}

function triggerIndividualCheckoutOnly(pId) {
    triggerVibe(40);
    window.location.href = `buy.php?id=${pId}`;
}

function configureDetailAccessButton(p, allProducts) {
    const btn = document.getElementById('sticky-action-btn');
    const inlineBtn = document.getElementById('inline-action-btn');
    const user = getSessionUser();
    
    const setButtonsState = (html, className, inlineClassName, clickHandler) => {
        if (btn) {
            btn.innerHTML = html;
            btn.className = "flex-1 py-3.5 " + className;
            btn.onclick = clickHandler;
        }
        if (inlineBtn) {
            inlineBtn.innerHTML = html;
            inlineBtn.className = "w-full py-4 px-4 " + inlineClassName;
            inlineBtn.onclick = clickHandler;
        }
    };
    
    if (p.price === 0) { // Free File
        setButtonsState(
            `<i class="fas fa-circle-down"></i><span>Instant Free Download</span>`,
            "bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-bold rounded-xl text-sm shadow-xl active:scale-98 hover:brightness-110 flex items-center justify-center gap-2 hover:shadow-teal-500/10 transition-all outline-none",
            "bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-black rounded-2xl text-sm shadow-md hover:brightness-105 active:scale-[0.98] transition-all flex items-center justify-center gap-2 outline-none",
            () => {
                triggerVibe(60);
                if (!user) {
                    Toast.info("Sign in to download items instantly.");
                    setTimeout(() => window.location.href = `login.php?redirect=product_detail.php?id=${p.id}`, 1000);
                    return;
                }
                triggerFreeAssetCompilation(user.id, p);
            }
        );
        return;
    }

    if (!user) {
        setButtonsState(
            `<i class="fas fa-credit-card"></i><span>Buy Now Securely</span>`,
            "bg-gradient-to-r from-sky-600 to-indigo-500 text-white font-bold rounded-xl text-sm shadow-xl active:scale-98 hover:brightness-110 flex items-center justify-center gap-2 hover:shadow-sky-500/10 transition-all outline-none",
            "bg-gradient-to-r from-sky-600 to-indigo-500 text-white font-black rounded-2xl text-sm shadow-md hover:brightness-105 active:scale-[0.98] transition-all flex items-center justify-center gap-2 outline-none",
            () => {
                triggerVibe(80);
                Toast.info("Please login to complete your secure purchase.");
                setTimeout(() => {
                    window.location.href = `login.php?redirect=buy.php?id=${p.id}`;
                }, 1000);
            }
        );
        return;
    }

    // Checking if already bought via optimized lightweight endpoint to save bandwidth
    fetch(`/api/orders/check-purchase/${user.id}/${p.id}`)
        .then(res => res.json())
        .then(data => {
            if (data.bought) {
                setButtonsState(
                    `<i class="fas fa-cloud-arrow-down"></i><span>Secure Download Unlocked</span>`,
                    "bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-bold rounded-xl text-sm shadow-xl active:scale-98 hover:brightness-110 flex items-center justify-center gap-2 hover:shadow-teal-500/10 transition-all outline-none",
                    "bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-black rounded-2xl text-sm shadow-md hover:brightness-105 active:scale-[0.98] transition-all flex items-center justify-center gap-2 outline-none",
                    () => {
                        triggerVibe(60);
                        window.location.href = `download.php?id=${p.id}&user=${user.id}`;
                    }
                );
            } else {
                setButtonsState(
                    `<i class="fas fa-credit-card"></i><span>Buy Now Securely</span>`,
                    "bg-gradient-to-r from-sky-600 to-indigo-500 text-white font-bold rounded-xl text-sm shadow-xl active:scale-98 hover:brightness-110 flex items-center justify-center gap-2 hover:shadow-sky-500/10 transition-all outline-none",
                    "bg-gradient-to-r from-sky-600 to-indigo-500 text-white font-black rounded-2xl text-sm shadow-md hover:brightness-105 active:scale-[0.98] transition-all flex items-center justify-center gap-2 outline-none",
                    () => {
                        triggerVibe(80);
                        openSmartBundleUpsellDrawer(p, allProducts);
                    }
                );
            }
        });
}

function triggerFreeAssetCompilation(userId, product) {
    // Audit free download log triggers & redirection 
    fetch('/api/downloads/log', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId, product_id: product.id })
    })
    .then(() => {
        Toast.success("Initiating high-speed file buffer...");
        setTimeout(() => {
            window.location.href = `download.php?id=${product.id}&user=${userId}`;
        }, 1200);
    });
}

function loadProductReviewRows() {
    fetch(`/api/reviews/${currentSpecsId}`)
        .then(res => res.json())
        .then(reviews => {
            const list = document.getElementById('reviews-list-container');
            if (reviews.length === 0) {
                list.innerHTML = `
                    <div class="py-4 text-center text-xs text-slate-400">
                        No reviews posted yet. Buy and review coordinates first.
                    </div>
                `;
                return;
            }
            
            list.innerHTML = reviews.map(r => `
                <div class="pt-3.5 first:pt-0">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-350">${r.user_name}</span>
                        <div class="flex text-amber-400 text-[10px]">
                            ${Array(r.rating).fill('<i class="fas fa-star"></i>').join('')}
                        </div>
                    </div>
                    <p class="text-xs text-slate-650 dark:text-slate-400 font-medium leading-relaxed">${r.comment}</p>
                    <span class="text-[9px] text-slate-400 block mt-1">Reviewed on ${new Date(r.created_at).toLocaleDateString()}</span>
                </div>
            `).join('');
        });
}

function saveToRecentlyViewed(product) {
    const storageKey = "sellora_recent_views";
    let list = JSON.parse(localStorage.getItem(storageKey) || '[]');
    list = list.filter(item => item.id !== product.id);
    list.unshift({ id: product.id, title: product.title, image: product.image, price: product.price });
    if (list.length > 5) list.pop();
    localStorage.setItem(storageKey, JSON.stringify(list));
    
    // Server-side logging integration too
    const user = getSessionUser();
    if (user) {
        fetch('/api/recently-viewed', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: user.id, product_id: product.id })
        }).catch(() => {});
    }
}

function populateRecentlyViewedWidgets(products) {
    const storageKey = "sellora_recent_views";
    const list = JSON.parse(localStorage.getItem(storageKey) || '[]');
    const container = document.getElementById('recently-viewed-list');
    
    // Filter out active details ID from viewing inside recent listings
    const filtered = list.filter(item => item.id !== currentSpecsId);
    
    if (filtered.length === 0) {
        container.innerHTML = `<p class="text-[10px] text-slate-400 italic">No historical searches recorded.</p>`;
        return;
    }
    
    container.innerHTML = filtered.map(item => `
        <a href="product_detail.php?id=${item.id}" class="w-32 flex-shrink-0 p-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-white/5 shadow-sm block focus:outline-none pointer-events-auto">
            <img src="${item.image}" class="w-full h-16 object-cover rounded-lg">
            <span class="text-[10px] font-bold text-slate-700 dark:text-slate-200 mt-1 line-clamp-1 block leading-tight">${item.title}</span>
            <span class="text-[10px] text-primary-light font-extrabold mt-0.5 block font-mono">₹${item.price}</span>
        </a>
    `).join('');
}

function populateRelatedRecommendations(products, catId) {
    const list = products.filter(p => p.category_id === catId && p.id !== currentSpecsId && (!p.status || p.status === 'active'));
    const container = document.getElementById('related-carousel');
    
    if (list.length === 0) {
        container.innerHTML = `<p class="text-[10px] text-slate-400 italic">No alternative related items in category.</p>`;
        return;
    }
    
    container.innerHTML = list.map(p => `
        <a href="product_detail.php?id=${p.id}" class="w-32 flex-shrink-0 p-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-white/5 block focus:outline-none pointer-events-auto">
            <img src="${p.image}" class="w-full h-16 object-cover rounded-lg">
            <span class="text-[10px] font-bold text-slate-700 dark:text-slate-200 mt-1 line-clamp-1 block leading-tight">${p.title}</span>
            <span class="text-[10px] text-primary-light font-extrabold mt-0.5 block font-mono">₹${p.price}</span>
        </a>
    `).join('');
}

// Sandbox interactive script utilities matching Category IDs
function renderSandboxWorkspace(catId) {
    const sandboxPanel = document.getElementById('premium-sandbox-panel');
    const workspace = document.getElementById('sandbox-workspace');
    if (!sandboxPanel || !workspace) return;

    sandboxPanel.classList.remove('hidden');

    if (catId === 1) {
        // Category 1: GPT Prompts Pack
        workspace.innerHTML = `
            <div class="space-y-3.5">
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-wider text-slate-400 mb-1.5">1. Select Target Category</label>
                    <select id="prompt-sandbox-topic" onchange="runPromptSandboxDemo()" class="w-full text-xs font-semibold py-2.5 px-3 bg-slate-800 text-slate-100 rounded-xl border-0 outline-none focus:ring-1 focus:ring-sky-500 transition-all cursor-pointer">
                        <option value="coding">Software Design & Bug Solving</option>
                        <option value="marketing">Copywriting & Social Growth</option>
                        <option value="productivity">Time Management & PDF Analyzer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-wider text-slate-400 mb-1.5">2. Provide Custom Subject Name</label>
                    <input type="text" id="prompt-sandbox-input" oninput="runPromptSandboxDemo()" placeholder="e.g., E-commerce React, Instagram Reels" class="w-full text-xs py-2.5 px-3 bg-slate-800 text-slate-200 placeholder-slate-550 rounded-xl border-0 outline-none focus:ring-1 focus:ring-sky-500 transition-all">
                </div>
                <div class="p-3 bg-slate-950 border border-slate-850 rounded-xl relative font-mono text-[10.5px] leading-relaxed text-slate-350 select-text overflow-x-auto min-h-[100px] max-h-[180px] scroll-smooth">
                    <button onclick="copyAssemblePromptText(this)" class="absolute top-2 right-2 px-2 py-1 rounded bg-slate-850 hover:bg-slate-800 text-[9px] text-slate-400 hover:text-white font-bold transition-all flex items-center gap-1">
                        <i class="fas fa-copy"></i>
                        <span>Copy</span>
                    </button>
                    <div id="prompt-sandbox-output" class="pr-12 whitespace-pre-wrap">Loading formatted custom draft...</div>
                </div>
            </div>
        `;
        runPromptSandboxDemo();
    } else if (catId === 2) {
        // Category 2: ATS Resumes Template
        workspace.innerHTML = `
            <div class="space-y-3.5">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-slate-400 mb-1">Your Full Name</label>
                        <input type="text" id="resume-sandbox-name" oninput="runResumeSandboxDemo()" value="Alex Morgan" placeholder="Enter Name" class="w-full text-xs py-2 px-3 bg-slate-800 text-slate-200 rounded-xl border-0 outline-none focus:ring-1 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-slate-400 mb-1">Target Job Title</label>
                        <input type="text" id="resume-sandbox-title" oninput="runResumeSandboxDemo()" value="Senior Web Developer" placeholder="Enter Title" class="w-full text-xs py-2 px-3 bg-slate-800 text-slate-200 rounded-xl border-0 outline-none focus:ring-1 focus:ring-sky-500">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-wider text-slate-400 mb-1">Elite Core Competency</label>
                    <select id="resume-sandbox-skill" onchange="runResumeSandboxDemo()" class="w-full text-xs py-2 px-3 bg-slate-800 text-slate-200 rounded-xl border-0 outline-none cursor-pointer">
                        <option value="React, NextJS, Server Components">Frontend Architecture Specialist</option>
                        <option value="SQL databases, NodeJS server controllers">Backend & Cloud SQL Architect</option>
                        <option value="HPC visual styling & dynamic micro-interactions">Dynamic UI Interaction Craftsman</option>
                    </select>
                </div>
                <!-- Mini ATS Resume Preview Canvas mockup -->
                <div class="p-4 bg-white text-slate-800 rounded-2xl border border-slate-100 shadow-lg text-[8px] font-sans relative overflow-hidden max-w-sm mx-auto">
                    <!-- Elegant Header Border bar -->
                    <div class="h-1 w-full bg-slate-900 absolute top-0 left-0 right-0"></div>
                    <div class="flex justify-between items-start font-sans pb-1.5 border-b border-slate-200">
                        <div>
                            <h5 id="mini-res-name" class="text-xs font-bold uppercase tracking-wide text-slate-900 leading-none">Alex Morgan</h5>
                            <p id="mini-res-title" class="text-[7px] text-slate-500 font-bold mt-0.5">Senior Web Developer</p>
                        </div>
                        <div class="text-[6.5px] text-slate-400 text-right leading-tight font-mono">
                            San Francisco, CA<br>
                            alex@digitalmohan.com
                        </div>
                    </div>
                    
                    <div class="space-y-1.5 mt-2">
                        <div>
                            <h6 class="text-[7.5px] uppercase font-black text-slate-900 tracking-wider">Professional Profile</h6>
                            <p class="text-[6px] text-slate-500 leading-normal font-sans font-medium">Highly disciplined engineering executive with 5+ years of success delivering rich micro-interactions and low-latency digital assets.</p>
                        </div>
                        <div>
                            <h6 class="text-[7.5px] uppercase font-black text-slate-900 tracking-wider">Core Operations & Expertise</h6>
                            <p id="mini-res-skills" class="text-[6.5px] text-slate-500 font-bold font-mono">React, NextJS, Server Components</p>
                        </div>
                        <div>
                            <h6 class="text-[7.5px] uppercase font-black text-slate-900 tracking-wider">Recent Enterprise Ventures</h6>
                            <div class="flex justify-between font-bold text-slate-800 text-[6.5px] mt-0.5">
                                <span>Platform Engineering Lead @ Stripe</span>
                                <span class="text-slate-450">2023 - Present</span>
                            </div>
                            <p class="text-[6px] text-slate-500 leading-normal mt-0.5">• Refined API request caching which improved platform telemetry processing speed by 40%.</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        runResumeSandboxDemo();
    } else if (catId === 3) {
        // Category 3: IIT-JEE Formula Workbook
        workspace.innerHTML = `
            <div class="space-y-3.5">
                <p class="text-[11px] text-slate-350">Interact with a kinetic system energy calculator matching curriculum formulas:</p>
                <div class="space-y-2.5">
                    <div>
                        <div class="flex justify-between text-[10px] text-slate-400 mb-1">
                            <span>Mass (m) of system particle:</span>
                            <span class="font-bold text-sky-450 font-mono"><span id="formula-mass-val">5.0</span> kg</span>
                        </div>
                        <input type="range" id="formula-mass-slider" min="1" max="25" step="0.5" value="5" oninput="runFormulaSandboxDemo()" class="w-full accent-sky-500 h-1 bg-slate-800 rounded-lg appearance-none cursor-pointer">
                    </div>
                    <div>
                        <div class="flex justify-between text-[10px] text-slate-400 mb-1">
                            <span>Velocity (v) speed:</span>
                            <span class="font-bold text-sky-450 font-mono"><span id="formula-vel-val">8.0</span> m/s</span>
                        </div>
                        <input type="range" id="formula-vel-slider" min="1" max="40" step="0.5" value="8" oninput="runFormulaSandboxDemo()" class="w-full accent-sky-500 h-1 bg-slate-800 rounded-lg appearance-none cursor-pointer">
                    </div>
                </div>
                
                <div class="p-3 bg-slate-950 border border-slate-850 rounded-2xl text-center space-y-1">
                    <p class="text-[10px] uppercase font-black tracking-widest text-slate-450">Kinetic Energy Equation Output</p>
                    <div class="text-xs text-sky-400 font-serif font-black italic">E = ½ m v²</div>
                    <div class="text-2xl font-black text-white font-mono" id="formula-energy-output">160.00 J</div>
                </div>
            </div>
        `;
        runFormulaSandboxDemo();
    } else if (catId === 4) {
        // Category 4: Canva Social Media Designs
        workspace.innerHTML = `
            <div class="space-y-3.5">
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-wider text-slate-400 mb-1">Simulated Heading Text</label>
                    <input type="text" id="canva-sandbox-text" oninput="runCanvaSandboxDemo()" value="Sensation Layouts!" placeholder="Write main hook..." class="w-full text-xs py-2 px-3 bg-slate-800 text-slate-200 rounded-xl border-0 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-wider text-slate-400 mb-1">Gradient Theme Tone</label>
                    <div class="flex gap-2">
                        <button onclick="changeCanvaBackgroundGradient('lavender')" class="w-4 h-4 rounded-full bg-gradient-to-tr from-purple-600 to-pink-500 border border-white/20 active:scale-90 transition-all outline-none" title="Midnight Lavender"></button>
                        <button onclick="changeCanvaBackgroundGradient('cyan')" class="w-4 h-4 rounded-full bg-gradient-to-tr from-sky-600 to-teal-500 border border-white/20 active:scale-90 transition-all outline-none" title="Ocean Cyan"></button>
                        <button onclick="changeCanvaBackgroundGradient('amber')" class="w-4 h-4 rounded-full bg-gradient-to-tr from-orange-600 to-yellow-500 border border-white/20 active:scale-90 transition-all outline-none" title="Sunset Gold"></button>
                        <button onclick="changeCanvaBackgroundGradient('obsidian')" class="w-4 h-4 rounded-full bg-gradient-to-tr from-slate-900 to-sky-950 border border-white/20 active:scale-90 transition-all outline-none" title="Cosmic Slate"></button>
                    </div>
                </div>
                <!-- Canva post box preview mock -->
                <div id="canva-design-preview" class="w-48 h-48 mx-auto rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-500 p-4 flex flex-col justify-between text-white shadow-xl transition-all duration-300 relative overflow-hidden">
                    <span class="text-[7px] bg-white/20 backdrop-blur font-black uppercase tracking-wider px-1.5 py-0.5 rounded-full z-10 w-fit self-end">Premium Canva Resource</span>
                    <h5 id="canva-preview-text" class="text-sm font-display font-black tracking-tight leading-snug drop-shadow-md relative z-10 select-none">Sensation Layouts!</h5>
                    <div class="flex justify-between items-center text-[7px] font-mono opacity-85 mt-2 relative z-10 select-none">
                        <span>DigitalMohan Layouts</span>
                        <div class="flex gap-1">
                            <i class="fas fa-heart"></i>
                            <i class="fas fa-arrow-up-right-from-square"></i>
                        </div>
                    </div>
                </div>
            </div>
        `;
        runCanvaSandboxDemo();
    } else {
        sandboxPanel.classList.add('hidden');
    }
}

// Sandbox calculation functions
function runPromptSandboxDemo() {
    const topic = document.getElementById('prompt-sandbox-topic')?.value || 'coding';
    const inputKeyword = document.getElementById('prompt-sandbox-input')?.value || 'React App';
    const output = document.getElementById('prompt-sandbox-output');
    if (!output) return;

    if (topic === 'coding') {
        output.textContent = `Act as an expert Systems Architect. Analyze my specific criteria for "${inputKeyword}". Please compose a low-latency, resilient, standard TypeScript template utilizing explicit generic bounds. Ensure no mock implementations are used; prioritize strict type evaluation interfaces first. Deliver standard optimization logs.`;
    } else if (topic === 'marketing') {
        output.textContent = `Act as a senior growth consultant. Construct an elite high-impact launch framework focusing heavily on "${inputKeyword}". Generate 5 strategic visual Hooks that stimulate clicking particles feedback, a call-to-action structure maximizing mobile stand-alone standalone PWA installs, and microcopy targeting premium users.`;
    } else {
        output.textContent = `Act as a Master of Productivity Workflow design. Devise a systematic 5-step daily sequence optimized strictly for resolving "${inputKeyword}". Include designated focus slots, haptic physical boundaries, and digital dashboard templates layout models to elevate cognitive focus.`;
    }
}

function copyAssemblePromptText(btn) {
    const outputText = document.getElementById('prompt-sandbox-output')?.textContent || '';
    if (!outputText) return;
    triggerVibe(30);
    navigator.clipboard.writeText(outputText).then(() => {
        const textSpan = btn.querySelector('span');
        const icon = btn.querySelector('i');
        if (textSpan && icon) {
            textSpan.textContent = "Copied!";
            icon.className = "fas fa-check text-emerald-400";
            setTimeout(() => {
                textSpan.textContent = "Copy";
                icon.className = "fas fa-copy";
            }, 1500);
        }
        Toast.success("Assembled prompt copied to clipboard!");
    });
}

function runResumeSandboxDemo() {
    const nameInput = document.getElementById('resume-sandbox-name')?.value || 'Alex Morgan';
    const titleInput = document.getElementById('resume-sandbox-title')?.value || 'Senior Web Developer';
    const skillSelect = document.getElementById('resume-sandbox-skill')?.value || 'React, NextJS, Server Components';

    const lblName = document.getElementById('mini-res-name');
    const lblTitle = document.getElementById('mini-res-title');
    const lblSkills = document.getElementById('mini-res-skills');

    if (lblName) lblName.textContent = nameInput;
    if (lblTitle) lblTitle.textContent = titleInput;
    if (lblSkills) lblSkills.textContent = skillSelect;
}

function runFormulaSandboxDemo() {
    const mass = parseFloat(document.getElementById('formula-mass-slider')?.value || '5');
    const vel = parseFloat(document.getElementById('formula-vel-slider')?.value || '8');

    const lblMass = document.getElementById('formula-mass-val');
    const lblVel = document.getElementById('formula-vel-val');
    const output = document.getElementById('formula-energy-output');

    if (lblMass) lblMass.textContent = mass.toFixed(1);
    if (lblVel) lblVel.textContent = vel.toFixed(1);

    if (output) {
        const joules = 0.5 * mass * vel * vel;
        output.textContent = `${joules.toFixed(2)} J`;
    }
}

function runCanvaSandboxDemo() {
    const val = document.getElementById('canva-sandbox-text')?.value || 'Sensation Layouts!';
    const preview = document.getElementById('canva-preview-text');
    if (preview) preview.textContent = val;
}

function changeCanvaBackgroundGradient(theme) {
    triggerVibe(30);
    const box = document.getElementById('canva-design-preview');
    if (!box) return;

    box.className = "w-48 h-48 mx-auto rounded-2xl p-4 flex flex-col justify-between text-white shadow-xl transition-all duration-300 relative overflow-hidden";
    if (theme === 'lavender') {
        box.classList.add('bg-gradient-to-tr', 'from-purple-600', 'to-pink-500');
    } else if (theme === 'cyan') {
        box.classList.add('bg-gradient-to-tr', 'from-sky-600', 'to-teal-500');
    } else if (theme === 'amber') {
        box.classList.add('bg-gradient-to-tr', 'from-orange-600', 'to-yellow-500');
    } else {
        box.classList.add('bg-gradient-to-tr', 'from-slate-900', 'to-sky-950');
    }
}

// Kickstart operations 
loadProductSpecificationDetails();

window.addEventListener('currencychange', () => {
    if (window.currentLoadedProduct) {
        document.getElementById('detail-price-tag').textContent = window.formatPrice(window.currentLoadedProduct.price);
        document.getElementById('detail-mrp-tag').textContent = window.formatPrice(window.currentLoadedProduct.mrp);
        document.getElementById('sticky-payable-amount').textContent = window.formatPrice(window.currentLoadedProduct.price);
        if (document.getElementById('inline-price-tag')) {
            document.getElementById('inline-price-tag').textContent = window.formatPrice(window.currentLoadedProduct.price);
            document.getElementById('inline-mrp-original').textContent = window.formatPrice(window.currentLoadedProduct.mrp);
        }
    }
});

let activeGalleryImages = [];
let activeGalleryIndex = 0;

function openDynamicPreviewModal() {
    const p = currentLoadedProduct;
    if (!p) return;
    
    triggerVibe(40);
    
    const type = p.preview_type || 'link';
    const url = p.preview_url || '';
    const data = p.preview_data || '';
    
    // Fallback if 'link' type - open original action
    if (type === 'link') {
        if (url) {
            window.open(url, '_blank');
        } else {
            Toast.info("Live Preview is not enabled for this resource pack.");
        }
        return;
    }
    
    const m = document.getElementById('dynamic-preview-modal');
    const card = document.getElementById('preview-modal-card');
    const loader = document.getElementById('preview-viewport-loader');
    
    // Hide all viewports initially
    document.getElementById('preview-iframe-element').classList.add('hidden');
    document.getElementById('preview-pdf-unfolded').classList.add('hidden');
    document.getElementById('preview-image-gallery').classList.add('hidden');
    document.getElementById('preview-video-container').classList.add('hidden');
    
    // Viewport controls visible only for iframe
    document.getElementById('preview-viewport-controls').classList.add('hidden');
    
    document.getElementById('preview-modal-title').textContent = p.title;
    document.getElementById('preview-new-tab-btn').href = url || '#';
    document.getElementById('preview-footer-title').textContent = p.description || p.title;
    
    // Setup footer discount state helper
    const discount = Math.round((p.mrp - p.price) / p.mrp * 100);
    document.getElementById('preview-footer-discount').textContent = `EXCLUSIVE ${discount}% PRICE SAVING ACTIVE - SECURE DELIVERY INSTANT`;
    
    loader.classList.remove('hidden');
    m.classList.remove('hidden');
    setTimeout(() => {
        m.classList.remove('opacity-0');
        card.classList.remove('scale-95');
    }, 20);
    
    if (type === 'iframe') {
        document.getElementById('preview-viewport-controls').classList.remove('hidden');
        setPreviewViewport('desktop');
        
        const iframe = document.getElementById('preview-iframe-element');
        iframe.src = url;
        iframe.onload = () => {
            loader.classList.add('hidden');
            iframe.classList.remove('hidden');
        };
        
        // Simple fallback timeout if iframe blocks X-Frame-Options
        setTimeout(() => {
            loader.classList.add('hidden');
            iframe.classList.remove('hidden');
        }, 1500);
        
    } else if (type === 'pdf') {
        const pdfIframe = document.getElementById('preview-pdf-iframe');
        const targetPdf = url || data;
        pdfIframe.src = targetPdf;
        pdfIframe.onload = () => {
            loader.classList.add('hidden');
            document.getElementById('preview-pdf-unfolded').classList.remove('hidden');
        };
        setTimeout(() => {
            loader.classList.add('hidden');
            document.getElementById('preview-pdf-unfolded').classList.remove('hidden');
        }, 1500);
        
    } else if (type === 'images') {
        loader.classList.add('hidden');
        document.getElementById('preview-image-gallery').classList.remove('hidden');
        
        // Split data comma lists
        const images = data.split(',').map(img => img.trim()).filter(img => img.length > 0);
        if (images.length === 0 && p.image) {
            images.push(p.image);
        }
        activeGalleryImages = images;
        activeGalleryIndex = 0;
        
        renderGalleryIndex();
        
    } else if (type === 'video') {
        const videoIframe = document.getElementById('preview-video-iframe');
        let embedUrl = data || url;
        
        // Convert watch link to embed format
        if (embedUrl.includes('youtube.com/watch?v=')) {
            const vid = embedUrl.split('v=')[1].split('&')[0];
            embedUrl = `https://www.youtube.com/embed/${vid}`;
        } else if (embedUrl.includes('youtu.be/')) {
            const vid = embedUrl.split('youtu.be/')[1].split('?')[0];
            embedUrl = `https://www.youtube.com/embed/${vid}`;
        }
        
        videoIframe.src = embedUrl;
        loader.classList.add('hidden');
        document.getElementById('preview-video-container').classList.remove('hidden');
    }
}

function closeDynamicPreviewModal() {
    triggerVibe(20);
    
    const m = document.getElementById('dynamic-preview-modal');
    const card = document.getElementById('preview-modal-card');
    
    card.classList.add('scale-95');
    m.classList.add('opacity-0');
    
    // Stop all media playing / clear iframe links to avoid leaks
    document.getElementById('preview-iframe-element').src = '';
    document.getElementById('preview-pdf-iframe').src = '';
    document.getElementById('preview-video-iframe').src = '';
    
    setTimeout(() => {
        m.classList.add('hidden');
    }, 300);
}

function setPreviewViewport(mode) {
    triggerVibe(20);
    const wrapper = document.getElementById('preview-viewport-wrapper');
    const frame = document.getElementById('preview-iframe-element');
    
    // Dynamic styles
    const btnDesktop = document.getElementById('vp-btn-desktop');
    const btnTablet = document.getElementById('vp-btn-tablet');
    const btnMobile = document.getElementById('vp-btn-mobile');
    
    [btnDesktop, btnTablet, btnMobile].forEach(btn => {
        btn.className = "px-2.5 py-1.5 rounded-lg text-[10px] uppercase font-black tracking-wider transition-all duration-200 outline-none flex items-center gap-1.5 text-slate-400 hover:text-white";
    });
    
    if (mode === 'desktop') {
        wrapper.className = "w-full h-full max-w-full flex justify-center items-center transition-all duration-300 relative";
        frame.style.width = "100%";
        frame.style.height = "100%";
        frame.className = "w-full h-full border-0 rounded-2xl bg-white shadow-2xl transition-all duration-300";
        btnDesktop.className = "px-2.5 py-1.5 rounded-lg text-[10px] uppercase font-black tracking-wider transition-all duration-200 outline-none flex items-center gap-1.5 bg-sky-550 text-white bg-sky-500";
    } else if (mode === 'tablet') {
        wrapper.className = "max-w-[768px] w-full h-full flex justify-center items-center transition-all duration-300 relative border-4 border-slate-705 dark:border-slate-800 rounded-[32px] bg-slate-950 p-2 shadow-2xl";
        frame.style.width = "100%";
        frame.style.height = "100%";
        frame.className = "w-full h-full border-0 rounded-2xl bg-white shadow-inner transition-all duration-300";
        btnTablet.className = "px-2.5 py-1.5 rounded-lg text-[10px] uppercase font-black tracking-wider transition-all duration-200 outline-none flex items-center gap-1.5 bg-sky-550 text-white bg-sky-500";
    } else if (mode === 'mobile') {
        wrapper.className = "max-w-[375px] w-full h-full flex justify-center items-center transition-all duration-300 relative border-8 border-slate-705 dark:border-slate-800 rounded-[48px] bg-slate-950 p-3 shadow-2xl";
        frame.style.width = "100%";
        frame.style.height = "100%";
        frame.className = "w-full h-full border-0 rounded-3xl bg-white shadow-inner transition-all duration-300";
        btnMobile.className = "px-2.5 py-1.5 rounded-lg text-[10px] uppercase font-black tracking-wider transition-all duration-200 outline-none flex items-center gap-1.5 bg-sky-550 text-white bg-sky-500";
    }
}

function renderGalleryIndex() {
    if (activeGalleryImages.length === 0) return;
    
    const focusImg = document.getElementById('gallery-focused-image');
    focusImg.src = activeGalleryImages[activeGalleryIndex];
    
    const ths = document.getElementById('gallery-carousel-thumbnails');
    ths.innerHTML = activeGalleryImages.map((img, idx) => {
        const border = idx === activeGalleryIndex ? 'border-sky-500 scale-105 shadow-md shadow-sky-500/10' : 'border-slate-800 opacity-60 hover:opacity-100';
        return `
            <button onclick="setGalleryFocus(${idx})" class="w-14 h-14 md:w-16 md:h-16 rounded-xl border-2 overflow-hidden transition-all duration-200 outline-none flex-shrink-0 ${border}">
                <img src="${img}" class="w-full h-full object-cover">
            </button>
        `;
    }).join('');
}

function setGalleryFocus(idx) {
    triggerVibe(20);
    activeGalleryIndex = idx;
    renderGalleryIndex();
}

function navGalleryFocus(dir) {
    if (activeGalleryImages.length === 0) return;
    triggerVibe(20);
    
    activeGalleryIndex += dir;
    if (activeGalleryIndex < 0) {
        activeGalleryIndex = activeGalleryImages.length - 1;
    } else if (activeGalleryIndex >= activeGalleryImages.length) {
        activeGalleryIndex = 0;
    }
    renderGalleryIndex();
}

function triggerCheckoutFromPreview() {
    closeDynamicPreviewModal();
    const inlineBtn = document.getElementById('inline-action-btn');
    const stickyBtn = document.getElementById('sticky-action-btn');
    
    if (inlineBtn) {
        inlineBtn.click();
    } else if (stickyBtn) {
        stickyBtn.click();
    }
}
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
