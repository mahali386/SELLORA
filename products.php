<?php
// Sellora - Products Listing Filter Matrix
require_once __DIR__ . '/common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN PRODUCT CATALOGUE -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    
    <!-- Title / Category Chip info header -->
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-display font-extrabold text-slate-800 dark:text-slate-100">Files Catalogue</h1>
            <p id="total-count-display" class="text-xs text-slate-400 dark:text-slate-500 font-medium">Scanning items folder...</p>
        </div>
        
        <!-- Grid/List View Toggler -->
        <button id="view-toggle-btn" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white dark:bg-slate-800/80 border border-slate-200/50 dark:border-white/5 text-slate-500 hover:text-slate-700 dark:text-slate-300 outline-none hover:scale-105 transition-all" onclick="toggleCompactViewLayout()">
            <i id="view-toggle-icon" class="fas fa-list text-md"></i>
        </button>
    </div>

    <!-- ADVANCED FILTER MATRIX FLOATER -->
    <div class="mb-6 p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 space-y-4 shadow-sm backdrop-blur-md">
        
        <!-- Filter Row 1: Categories selector -->
        <div>
            <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Filter Category</label>
            <select id="filter-category" onchange="runCatalogueFilterMatrix()" class="w-full p-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-xs font-semibold text-slate-650 dark:text-slate-300 border-0 outline-none focus:ring-2 focus:ring-sky-500">
                <option value="all">All Category Files</option>
            </select>
        </div>

        <!-- Filter Row 2: Price Filters & Sorting selection -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Price Access</label>
                <select id="filter-price" onchange="runCatalogueFilterMatrix()" class="w-full p-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-xs font-semibold text-slate-650 dark:text-slate-300 border-0 outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="all">Any Price</option>
                    <option value="free">Free Access</option>
                    <option value="paid">Premium Pro</option>
                </select>
            </div>
            
            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Sort Results</label>
                <select id="filter-sort" onchange="runCatalogueFilterMatrix()" class="w-full p-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-xs font-semibold text-slate-650 dark:text-slate-300 border-0 outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="newest">Newest First</option>
                    <option value="low-high">Price: Low to High</option>
                    <option value="high-low">Price: High to Low</option>
                    <option value="popular">Highly Popular</option>
                </select>
            </div>
        </div>
    </div>

    <!-- DYNAMIC LIST CONTAINER: GRID/LIST MODIFIABLE -->
    <div id="catalog-products-list" class="grid grid-cols-2 gap-4">
        <!-- Loader Skellie Shimmers -->
        <div class="h-52 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
        <div class="h-52 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
        <div class="h-52 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
        <div class="h-52 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
    </div>

</main>

<script>
let globalProducts = [];
let layoutViewMode = "grid"; // or 'list'

function toggleCompactViewLayout() {
    triggerVibe(30);
    const container = document.getElementById('catalog-products-list');
    const icon = document.getElementById('view-toggle-icon');
    
    if (layoutViewMode === "grid") {
        layoutViewMode = "list";
        icon.className = "fas fa-grip text-md";
        container.className = "space-y-4";
    } else {
        layoutViewMode = "grid";
        icon.className = "fas fa-list text-md";
        container.className = "grid grid-cols-2 gap-4";
    }
    
    renderMatrixResults();
}

let catalogCurrentPage = 1;
const catalogPageSize = 12;
let catalogTotalPages = 1;

function populateCategories(categories) {
    const urlParams = new URLSearchParams(window.location.search);
    const catQuery = urlParams.get('cat') || 'all';
    const selectEl = document.getElementById('filter-category');
    if (!selectEl) return;
    
    selectEl.innerHTML = `<option value="all" class="font-bold">📁 All Catalogues</option>`;
    categories.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.name;
        if (catQuery !== 'all' && parseInt(catQuery) === c.id) {
            opt.selected = true;
        }
        selectEl.appendChild(opt);
    });
}

function loadCatalogueCategoryOptions() {
    // 1. Initial Instant UI load using embedded variables
    const serverCategories = <?= $serverCategories ?>;
    const serverProducts = <?= $serverProducts ?>;

    if (serverCategories && serverCategories.length > 0) {
        populateCategories(serverCategories);
    } else {
        const cachedCategories = localStorage.getItem('sellora_cache_categories');
        if (cachedCategories) {
            try { populateCategories(JSON.parse(cachedCategories)); } catch(e) {}
        }
    }
    
    if (serverProducts && serverProducts.length > 0) {
        // Render server-side products as fast layout placeholder
        renderMatrixResults(serverProducts);
    }

    // 2. Fetch categories list
    fetch('/api/categories')
        .then(res => res.json())
        .then(categories => {
            localStorage.setItem('sellora_cache_categories', JSON.stringify(categories));
            populateCategories(categories);
        })
        .catch(() => {});

    // 3. Kick off paginated fetch
    fetchCatalogueProductsPaginated(1);
}

function fetchCatalogueProductsPaginated(pageNum = 1) {
    catalogCurrentPage = pageNum;
    
    const categoryVal = document.getElementById('filter-category') ? document.getElementById('filter-category').value : 'all';
    const priceVal = document.getElementById('filter-price') ? document.getElementById('filter-price').value : 'all';
    const sortVal = document.getElementById('filter-sort') ? document.getElementById('filter-sort').value : 'newest';
    
    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = urlParams.get('search') || '';
    
    // Construct query filters
    const params = new URLSearchParams();
    params.set('page', catalogCurrentPage.toString());
    params.set('limit', catalogPageSize.toString());
    if (categoryVal !== 'all') params.set('category_id', categoryVal);
    if (priceVal !== 'all') params.set('price', priceVal);
    params.set('sort', sortVal);
    if (searchQuery) params.set('search', searchQuery);
    
    const container = document.getElementById('catalog-products-list');
    container.innerHTML = `
        <div class="h-44 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
        <div class="h-44 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
    `;
    
    fetch(`/api/products?${params.toString()}`)
        .then(res => res.json())
        .then(result => {
            // Check if server returned paginated object or flat array
            const products = result.products || result || [];
            catalogTotalPages = result.totalPages || 1;
            
            document.getElementById('total-count-display').textContent = `${result.total || products.length} high-quality document products located`;
            
            globalProducts = products; // Store current page products
            renderMatrixResults(products);
            renderPaginationControls();
        })
        .catch(() => {
            container.innerHTML = `
                <div class="col-span-2 text-center py-12 px-4 rounded-3xl border border-dashed border-red-200 dark:border-red-900">
                    <p class="text-xs text-red-500">Failed to load catalogue database. Please retry.</p>
                </div>
            `;
        });
}

function renderPaginationControls() {
    let paginationBar = document.getElementById('catalog-pagination-bar');
    if (!paginationBar) {
        const container = document.getElementById('catalog-products-list');
        paginationBar = document.createElement('div');
        paginationBar.id = 'catalog-pagination-bar';
        paginationBar.className = 'col-span-2 flex items-center justify-between mt-6 bg-slate-50 dark:bg-slate-850 p-3 rounded-2xl border border-slate-150/80 dark:border-white/5';
        container.insertAdjacentElement('afterend', paginationBar);
    }
    
    if (catalogTotalPages <= 1) {
        paginationBar.classList.add('hidden');
        return;
    } else {
        paginationBar.classList.remove('hidden');
    }
    
    paginationBar.innerHTML = `
        <button onclick="goToCatalogPage(${catalogCurrentPage - 1})" ${catalogCurrentPage === 1 ? 'disabled class="opacity-40 cursor-not-allowed text-xs font-black text-slate-400 dark:text-slate-500 p-2"' : 'class="text-xs font-black text-sky-500 hover:text-sky-600 dark:text-sky-400 p-2 hover:bg-sky-50 dark:hover:bg-sky-950/25 rounded-xl transition-all cursor-pointer"'}>
            <i class="fas fa-arrow-left mr-1"></i> Prev
        </button>
        <span class="text-xs font-bold text-slate-650 dark:text-slate-350 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl">
            Page ${catalogCurrentPage} of ${catalogTotalPages}
        </span>
        <button onclick="goToCatalogPage(${catalogCurrentPage + 1})" ${catalogCurrentPage === catalogTotalPages ? 'disabled class="opacity-40 cursor-not-allowed text-xs font-black text-slate-400 dark:text-slate-500 p-2"' : 'class="text-xs font-black text-sky-500 hover:text-sky-600 dark:text-sky-400 p-2 hover:bg-sky-50 dark:hover:bg-sky-950/25 rounded-xl transition-all cursor-pointer"'}>
            Next <i class="fas fa-arrow-right ml-1"></i>
        </button>
    `;
}

function goToCatalogPage(p) {
    if (p < 1 || p > catalogTotalPages) return;
    triggerVibe(30);
    fetchCatalogueProductsPaginated(p);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function runCatalogueFilterMatrix() {
    fetchCatalogueProductsPaginated(1);
}

function renderMatrixResults(list = []) {
    const container = document.getElementById('catalog-products-list');
    
    if (list.length === 0) {
        container.innerHTML = `
            <div class="col-span-2 text-center py-12 px-4 rounded-3xl border border-dashed border-slate-200 dark:border-slate-800 animate-fade">
                <i class="fas fa-folder-open text-slate-300 dark:text-slate-700 text-3xl mb-3"></i>
                <h4 class="text-sm font-bold text-slate-600 dark:text-slate-400">No Match Products Found</h4>
                <p class="text-xs text-slate-450 dark:text-slate-500 mt-1">Try relaxing filters or search other active keywords.</p>
            </div>
        `;
        return;
    }

    if (layoutViewMode === "grid") {
        container.innerHTML = list.map(p => {
            const discount = Math.round((p.mrp - p.price) / p.mrp * 100);
            return `
                <div class="rounded-2xl bg-white dark:bg-slate-800/65 border border-slate-200/50 dark:border-white/5 overflow-hidden shadow-sm relative group hover:scale-[1.01] transition-transform pointer-events-auto">
                    <button class="absolute top-2.5 right-2.5 z-10 w-7 h-7 rounded-lg bg-white/70 backdrop-blur-md flex items-center justify-center text-slate-450 hover:text-red-500 hover:scale-110 active:scale-95 transition-all outline-none" onclick="toggleLocalWishlist(event, ${p.id}, this)">
                        <i class="fas fa-heart text-xs"></i>
                    </button>
                    
                    <a href="product_detail.php?id=${p.id}" class="block focus:outline-none">
                        <img src="${window.getOptimizedImageUrl(p.image, 320)}" class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                        <div class="p-3">
                            <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200 line-clamp-2 min-h-[32px]">${p.title}</h5>
                            <div class="flex items-end justify-between mt-3">
                                <div class="flex flex-col">
                                    <span class="text-[9px] text-slate-455 dark:text-slate-500 line-through font-mono">${window.formatPrice(p.mrp)}</span>
                                    <span class="text-sm font-black text-slate-800 dark:text-slate-100 font-mono">${window.formatPrice(p.price)}</span>
                                </div>
                                <span class="text-[10px] bg-red-100 dark:bg-red-950/40 text-red-600 dark:text-red-400 font-extrabold px-1.5 py-0.5 rounded-md">${discount}% OFF</span>
                            </div>
                        </div>
                    </a>
                </div>
            `;
        }).join('');
    } else { // List Compact style layout
        container.innerHTML = list.map(p => {
            const discount = Math.round((p.mrp - p.price) / p.mrp * 100);
            return `
                <div class="rounded-2xl bg-white dark:bg-slate-800/65 border border-slate-200/50 dark:border-white/5 overflow-hidden shadow-sm relative group flex hover:scale-[1.005] transition-transform p-2.5 gap-4.5 pointer-events-auto">
                    
                    <a href="product_detail.php?id=${p.id}" class="w-24 h-20 flex-shrink-0 rounded-xl overflow-hidden focus:outline-none relative">
                        <img src="${window.getOptimizedImageUrl(p.image, 200)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                        <span class="absolute top-1.5 left-1.5 bg-red-500 text-[8px] font-black text-white px-1.5 py-0.5 rounded-md">${discount}% OFF</span>
                    </a>
                    
                    <div class="flex-grow min-w-0 flex flex-col justify-between pr-2">
                        <a href="product_detail.php?id=${p.id}" class="focus:outline-none">
                            <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200 line-clamp-2">${p.title}</h5>
                        </a>
                        <div class="flex items-center justify-between mt-2">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-sm font-black text-slate-800 dark:text-slate-100 font-mono">${window.formatPrice(p.price)}</span>
                                <span class="text-[9px] text-slate-455 dark:text-slate-500 line-through font-mono">${window.formatPrice(p.mrp)}</span>
                            </div>
                            
                            <button class="w-7 h-7 rounded-md bg-slate-100/60 dark:bg-slate-700/60 flex items-center justify-center text-slate-455 hover:text-red-500 transition-all outline-none" onclick="toggleLocalWishlist(event, ${p.id}, this)">
                                <i class="fas fa-heart text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }
}

window.addEventListener('currencychange', () => {
    fetchCatalogueProductsPaginated(catalogCurrentPage);
});

// Startup activation hooks
loadCatalogueCategoryOptions();
</script>

<?php include __DIR__ . '/common/footer.php'; ?>
<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
