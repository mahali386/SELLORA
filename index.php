<?php
// Sellora - Home Landing Space
require_once __DIR__ . '/common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN HOME CONTEXT -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    
    <!-- 1. AUTO-SLIDING HERO BANNER CAROUSEL (Swipeable, Pure JS) -->
    <div class="relative rounded-3xl overflow-hidden shadow-lg h-44 mb-6 group">
        <div id="carousel-track" class="flex w-full h-full transition-transform duration-500 ease-out" style="transform: translateX(0%);">
            <div class="w-full h-full flex-shrink-0 bg-slate-200 dark:bg-slate-850 animate-pulse flex items-center justify-center">
                <i class="fas fa-spinner animate-spin text-slate-400"></i>
            </div>
        </div>
        
        <!-- Controls Indicator Dots -->
        <div id="carousel-dots" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
        </div>
    </div>

    <!-- 2. CATEGORY CHIPS SCROLLER -->
    <div class="mb-6">
        <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3.5">Assigned Categories</h4>
        <div id="category-scroller" class="flex gap-2 overflow-x-auto no-scrollbar scroll-smooth">
            <!-- Loader Skellie Chips inside scroller -->
            <div class="h-9 w-20 rounded-full bg-slate-200 dark:bg-slate-800 animate-pulse"></div>
            <div class="h-9 w-28 rounded-full bg-slate-200 dark:bg-slate-800 animate-pulse"></div>
            <div class="h-9 w-24 rounded-full bg-slate-200 dark:bg-slate-800 animate-pulse"></div>
            <div class="h-9 w-20 rounded-full bg-slate-200 dark:bg-slate-800 animate-pulse"></div>
        </div>
    </div>

    <!-- 3. LIMITED OFFER COUNTDOWN TIMER BAR CARD -->
    <div id="homepage-flash-sale-banner" class="hidden mb-6 p-4 rounded-3xl bg-gradient-to-r from-red-500/10 to-transparent border border-red-500/10 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <span class="w-9 h-9 rounded-xl bg-red-500/20 text-red-500 flex items-center justify-center text-sm animate-pulse-slow">
                <i class="fas fa-fire-flame-curved"></i>
            </span>
            <div>
                <h5 class="text-xs font-black uppercase tracking-wider text-red-500">Flash Sale Ending</h5>
                <p class="text-[10px] text-slate-450 dark:text-slate-500">Claim special savings with code <span class="font-bold text-sky-500 dark:text-sky-400 font-mono tracking-wider" id="homepage-promo-placeholder">SAVE50</span></p>
            </div>
        </div>
        
        <!-- Interactive ticking countdown tracker -->
        <div class="flex gap-1.5 text-center font-mono">
            <div class="bg-red-500/15 text-red-500 text-xs font-bold px-2 py-1 rounded-lg">
                <span id="timer-hr">02</span><span class="text-[9px] block font-sans">hr</span>
            </div>
            <div class="bg-red-500/15 text-red-500 text-xs font-bold px-2 py-1 rounded-lg">
                <span id="timer-min">49</span><span class="text-[9px] block font-sans">min</span>
            </div>
            <div class="bg-red-500/15 text-red-500 text-xs font-bold px-2 py-1 rounded-lg">
                <span id="timer-sec">12</span><span class="text-[9px] block font-sans">sec</span>
            </div>
        </div>
    </div>

    <!-- 4. FEATURED PRODUCTS (HORIZONTAL SCROLL) -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3.5">
            <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Trending Now</h4>
            <a href="products.php" class="text-xs font-bold text-sky-500 hover:text-sky-600">View All</a>
        </div>
        
        <div id="featured-products-container" class="flex gap-4 overflow-x-auto no-scrollbar scroll-smooth">
            <!-- Shimmer Loaders for layout -->
            <div class="w-48 flex-shrink-0 rounded-2xl bg-slate-200 dark:bg-slate-850 h-56 animate-pulse"></div>
            <div class="w-48 flex-shrink-0 rounded-2xl bg-slate-200 dark:bg-slate-850 h-56 animate-pulse"></div>
        </div>
    </div>

    <!-- 5. BEST SELLING PRODUCTS (GRID LAYOUT) -->
    <div class="mb-10">
        <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3.5">Best Sellers</h4>
        
        <div id="bestseller-grid" class="grid grid-cols-2 gap-4">
            <!-- Shimmer Loaders -->
            <div class="rounded-2xl bg-slate-200 dark:bg-slate-850 h-52 animate-pulse"></div>
            <div class="rounded-2xl bg-slate-200 dark:bg-slate-850 h-52 animate-pulse"></div>
        </div>
    </div>

</main>

<script>
// Slider Carousel Engine Controls 
let currentSlide = 0;
let totalSlides = document.querySelectorAll('#carousel-track > div').length;
let slideInterval;

function startCarouselTimer() {
    clearInterval(slideInterval);
    if (totalSlides <= 1) return;
    slideInterval = setInterval(() => {
        goToSlide((currentSlide + 1) % totalSlides);
    }, 4500);
}

function goToSlide(index) {
    if (totalSlides === 0) return;
    currentSlide = index;
    const track = document.getElementById('carousel-track');
    if (track) {
        track.style.transform = `translateX(-${currentSlide * 100}%)`;
    }
    // Update active dot visual colors
    for (let i = 0; i < totalSlides; i++) {
        const dot = document.getElementById(`dot-${i}`);
        if (dot) {
            if (i === currentSlide) {
                dot.className = "w-3 h-1.5 rounded-full bg-white transition-all";
            } else {
                dot.className = "w-1.5 h-1.5 rounded-full bg-white/40 transition-all";
            }
        }
    }
    // trigger vibe on custom slide select 
    triggerVibe(10);
}

// Carousel Swipe Handlers
let carouselX = 0;
const trackEl = document.getElementById('carousel-track');
if (trackEl) {
    trackEl.addEventListener('touchstart', e => {
        carouselX = e.touches[0].clientX;
    });
    trackEl.addEventListener('touchend', e => {
        if (totalSlides === 0) return;
        const diff = carouselX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) {
            if (diff > 0) {
                goToSlide((currentSlide + 1) % totalSlides);
            } else {
                goToSlide((currentSlide - 1 + totalSlides) % totalSlides);
            }
            startCarouselTimer();
        }
    });
}

// Flash Sale Countdown Timer Ticker 
let countdownInterval = null;

function runCountdownSaleTimer(expiryTimeString) {
    if (countdownInterval) {
        clearInterval(countdownInterval);
        countdownInterval = null;
    }
    
    const minSpan = document.getElementById('timer-min');
    const secSpan = document.getElementById('timer-sec');
    const hrSpan = document.getElementById('timer-hr');
    const banner = document.getElementById('homepage-flash-sale-banner');
    
    if (!expiryTimeString) {
        if (banner) banner.classList.add('hidden');
        return;
    }
    
    const expiryTime = new Date(expiryTimeString).getTime();
    
    function tick() {
        const now = Date.now();
        const diff = expiryTime - now;
        
        if (diff <= 0) {
            clearInterval(countdownInterval);
            countdownInterval = null;
            if (minSpan) minSpan.textContent = '00';
            if (secSpan) secSpan.textContent = '00';
            if (hrSpan) hrSpan.textContent = '00';
            if (banner) banner.classList.add('hidden');
            return;
        }
        
        const totalSecs = Math.floor(diff / 1000);
        const secs = totalSecs % 60;
        const totalMins = Math.floor(totalSecs / 60);
        const mins = totalMins % 60;
        const hrs = Math.floor(totalMins / 60);
        
        if (secSpan) secSpan.textContent = String(secs).padStart(2, '0');
        if (minSpan) minSpan.textContent = String(mins).padStart(2, '0');
        if (hrSpan) hrSpan.textContent = String(hrs).padStart(2, '0');
    }
    
    tick();
    countdownInterval = setInterval(tick, 1000);
}

// Render Banners function
function renderBanners(banners) {
    const track = document.getElementById('carousel-track');
    const dots = document.getElementById('carousel-dots');
    if (!track) return;
    
    if (!banners || banners.length === 0) {
        banners = [{
            id: 1,
            badge: "WELCOME",
            title: "Welcome to DigitalMohan!",
            subtitle: "Browse premium digital prompt packs and ATS resume formats instantly.",
            link_url: "products.php",
            bg_gradient: "from-indigo-900 to-sky-900"
        }];
    }
    
    totalSlides = banners.length;
    
    track.innerHTML = banners.map(b => `
        <div class="w-full h-full flex-shrink-0 relative bg-gradient-to-br ${b.bg_gradient} text-white p-6 flex flex-col justify-center cursor-pointer" onclick="window.location.href='${b.link_url}'">
            <span class="absolute top-4 right-4 bg-red-500 text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full z-10">${b.badge}</span>
            <h3 class="text-xl font-display font-black max-w-[85%] leading-snug tracking-tight">${b.title}</h3>
            <p class="text-xs text-white/80 mt-1 max-w-[85%] line-clamp-2 leading-relaxed font-semibold">${b.subtitle}</p>
            <div class="mt-3.5 flex items-center gap-1.5 text-xs font-black text-sky-400">
                <span>Get Started</span>
                <i class="fas fa-arrow-right text-[10px]"></i>
            </div>
        </div>
    `).join('');
    
    if (dots) {
        dots.innerHTML = banners.map((b, i) => `
            <button id="dot-${i}" onclick="goToSlide(${i})" class="${i === 0 ? 'w-3' : 'w-1.5'} h-1.5 rounded-full bg-white transition-all"></button>
        `).join('');
    }
    
    currentSlide = 0;
    goToSlide(0);
}

// Render Categories function
function renderCategories(categories) {
    const container = document.getElementById('category-scroller');
    if (!container) return;
    const chips = categories.map(c => `
        <button onclick="filterCategoryHome(${c.id})" class="flex-shrink-0 px-4 py-2 rounded-full border border-slate-150 dark:border-white/5 bg-white dark:bg-slate-800 hover:bg-sky-500 hover:text-white dark:hover:bg-sky-500 text-xs font-semibold text-slate-600 dark:text-slate-300 transition-colors focus:outline-none">
            ${c.name}
        </button>
    `).join('');
    
    container.innerHTML = `
        <button onclick="filterCategoryHome('all')" class="flex-shrink-0 px-4 py-2 rounded-full bg-sky-500 text-white text-xs font-semibold hover:brightness-105 transition-colors focus:outline-none">
            All Items
        </button>
        ${chips}
    `;
}

// Render Products function
function renderProducts(products) {
    const activeProds = products.filter(p => !p.status || p.status === 'active');
    
    // Render Featured Products Horizontal list (First 3)
    const featuredContainer = document.getElementById('featured-products-container');
    if (featuredContainer) {
        const featuredItems = activeProds.slice(0, 3).map(p => {
            const discount = Math.round((p.mrp - p.price) / p.mrp * 100);
            return `
                <div class="w-48 flex-shrink-0 rounded-2xl bg-white dark:bg-slate-800/65 border border-slate-200/50 dark:border-white/5 overflow-hidden shadow-sm relative group hover:scale-[1.01] transition-transform pointer-events-auto">
                    <span class="absolute top-2.5 left-2.5 z-10 bg-gradient-to-r from-teal-500 to-sky-500 text-white text-[9px] font-black uppercase px-2 py-0.5 rounded-full shadow-sm">
                        <i class="fas fa-bolt mr-0.5"></i>Trending
                    </span>
                    
                    <button class="absolute top-2.5 right-2.5 z-10 w-7 h-7 rounded-lg bg-white/70 backdrop-blur-md flex items-center justify-center text-slate-455 hover:text-red-500 hover:scale-110 active:scale-95 transition-all outline-none" onclick="toggleLocalWishlist(event, ${p.id}, this)">
                        <i class="fas fa-heart text-xs"></i>
                    </button>
                    
                    <a href="product_detail.php?id=${p.id}" class="block focus:outline-none">
                        <img src="${window.getOptimizedImageUrl(p.image, 250)}" class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                        <div class="p-3">
                            <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200 line-clamp-2 min-h-[32px]">${p.title}</h5>
                            <div class="flex items-center gap-1.5 mt-1.5">
                                <div class="flex text-amber-400 text-[10px]">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-[9px] text-slate-455 dark:text-slate-500">(5.0)</span>
                            </div>
                            <div class="flex items-end justify-between mt-2.5">
                                <div class="flex flex-col">
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 line-through">${window.formatPrice(p.mrp)}</span>
                                    <span class="text-sm font-black text-slate-800 dark:text-slate-200">${window.formatPrice(p.price)}</span>
                                </div>
                                <span class="text-[10px] bg-red-100 dark:bg-red-950/40 text-red-600 dark:text-red-400 font-extrabold px-1.5 py-0.5 rounded-md">${discount}% OFF</span>
                            </div>
                        </div>
                    </a>
                </div>
            `;
        }).join('');
        featuredContainer.innerHTML = featuredItems || `<div class="p-4 text-center text-xs text-slate-400">No active trending sheets.</div>`;
    }

    // Render bestseller grid list - Performance Fix: Limit display size to maximum 8 items to protect mobile rendering speed and DOM node counts
    const bestsellerGrid = document.getElementById('bestseller-grid');
    if (bestsellerGrid) {
        const gridItems = activeProds.slice(0, 8).map(p => {
            const discount = Math.round((p.mrp - p.price) / p.mrp * 100);
            return `
                <div class="rounded-2xl bg-white dark:bg-slate-800/65 border border-slate-200/50 dark:border-white/5 overflow-hidden shadow-sm relative group hover:scale-[1.01] transition-transform pointer-events-auto">
                    <button class="absolute top-2.5 right-2.5 z-10 w-7 h-7 rounded-lg bg-white/70 backdrop-blur-md flex items-center justify-center text-slate-440 hover:text-red-500 hover:scale-110 active:scale-95 transition-all outline-none" onclick="toggleLocalWishlist(event, ${p.id}, this)">
                        <i class="fas fa-heart text-xs"></i>
                    </button>
                    
                    <a href="product_detail.php?id=${p.id}" class="block focus:outline-none">
                        <img src="${window.getOptimizedImageUrl(p.image, 250)}" class="w-full h-24 object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                        <div class="p-3">
                            <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200 line-clamp-2 min-h-[32px]">${p.title}</h5>
                            <div class="flex items-end justify-between mt-3">
                                <div class="flex flex-col">
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 line-through font-mono">${window.formatPrice(p.mrp)}</span>
                                    <span class="text-sm font-black text-slate-800 dark:text-slate-200 font-mono">${window.formatPrice(p.price)}</span>
                                </div>
                                <span class="text-[10px] bg-red-150/50 dark:bg-red-950/40 text-red-600 dark:text-red-400 font-extrabold px-1.5 py-0.5 rounded-md">${discount}% OFF</span>
                            </div>
                        </div>
                    </a>
                </div>
            `;
        }).join('');
        bestsellerGrid.innerHTML = gridItems || `<div class="p-4 text-center text-xs text-slate-400">No active templates.</div>`;
    }
}

// Fetch lists and categories dynamically using Stale-While-Revalidate caching (AJAX) 
function loadDashboardItems() {
    // 1. STALE server-side or local cache instant rendering
    const serverBanners = <?= $serverBanners ?>;
    const serverCategories = <?= $serverCategories ?>;
    const serverProducts = <?= $serverProducts ?>;
    
    if (serverBanners && serverBanners.length > 0) {
        renderBanners(serverBanners);
    } else {
        const cachedBanners = localStorage.getItem('sellora_cache_banners');
        if (cachedBanners) {
            try { renderBanners(JSON.parse(cachedBanners)); } catch(e) {}
        }
    }
    
    if (serverCategories && serverCategories.length > 0) {
        renderCategories(serverCategories);
    } else {
        const cachedCategories = localStorage.getItem('sellora_cache_categories');
        if (cachedCategories) {
            try { renderCategories(JSON.parse(cachedCategories)); } catch(e) {}
        }
    }
    
    if (serverProducts && serverProducts.length > 0) {
        renderProducts(serverProducts);
    } else {
        const cachedProducts = localStorage.getItem('sellora_cache_products');
        if (cachedProducts) {
            try { renderProducts(JSON.parse(cachedProducts)); } catch(e) {}
        }
    }
    
    // 2. REVALIDATE fresh background queries
    // Load promo sliding banners
    fetch('/api/banners')
        .then(res => res.json())
        .then(banners => {
            localStorage.setItem('sellora_cache_banners', JSON.stringify(banners));
            renderBanners(banners);
            startCarouselTimer();
        })
        .catch(() => {});

    // Update highest active coupon shown on homepage alert bar with dynamic live countdown clock
    fetch('/api/coupons')
        .then(res => res.json())
        .then(coupons => {
            const label = document.getElementById('homepage-promo-placeholder');
            const banner = document.getElementById('homepage-flash-sale-banner');
            if (coupons && coupons.length > 0) {
                const active = coupons.filter(c => new Date(c.expiry).getTime() >= Date.now() && c.used_count < c.usage_limit);
                if (active.length > 0) {
                    active.sort((a, b) => b.discount - a.discount);
                    const highestCoupon = active[0];
                    if (label) {
                        label.textContent = `${highestCoupon.code} (${highestCoupon.discount}% Off)`;
                    }
                    if (banner) banner.classList.remove('hidden');
                    runCountdownSaleTimer(highestCoupon.expiry);
                } else {
                    if (label) label.textContent = "NONE";
                    if (banner) banner.classList.add('hidden');
                    runCountdownSaleTimer(null);
                }
            } else {
                if (label) label.textContent = "NONE";
                if (banner) banner.classList.add('hidden');
                runCountdownSaleTimer(null);
            }
        })
        .catch(() => {
            const banner = document.getElementById('homepage-flash-sale-banner');
            if (banner) banner.classList.add('hidden');
            runCountdownSaleTimer(null);
        });

    // Load categorychips
    fetch('/api/categories')
        .then(res => res.json())
        .then(data => {
            localStorage.setItem('sellora_cache_categories', JSON.stringify(data));
            renderCategories(data);
        })
        .catch(() => {});
        
    // Load products - Limited to 12 items for under 50ms rapid load
    fetch('/api/products?limit=12')
        .then(res => res.json())
        .then(data => {
            const productsList = data.products || data || [];
            localStorage.setItem('sellora_cache_products', JSON.stringify(productsList));
            renderProducts(productsList);
        })
        .catch(() => {});
}

function filterCategoryHome(catId) {
    triggerVibe(30);
    // Redirect to products filtering panel
    window.location.href = `products.php?cat=${catId}`;
}

function toggleLocalWishlist(e, pId, button) {
    e.preventDefault();
    e.stopPropagation();
    triggerVibe(40);
    
    const user = getSessionUser();
    if (!user) {
        Toast.info("Please login to wishlist products!");
        return;
    }
    
    fetch('/api/wishlist/toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: user.id, product_id: pId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.state === 'added') {
            button.classList.add('text-red-500');
            button.classList.remove('text-slate-400');
            Toast.success("Added to secure wishlist!");
        } else {
            button.classList.add('text-slate-400');
            button.classList.remove('text-red-500');
            Toast.info("Removed from wishlist.");
        }
    });
}

// Startup execution lists
loadDashboardItems();

window.addEventListener('currencychange', () => {
    const cachedProducts = localStorage.getItem('sellora_cache_products');
    if (cachedProducts) {
        try { renderProducts(JSON.parse(cachedProducts)); } catch(e) {}
    }
});

document.addEventListener('DOMContentLoaded', () => {
    startCarouselTimer();
});
</script>

<?php include __DIR__ . '/common/footer.php'; ?>
<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
