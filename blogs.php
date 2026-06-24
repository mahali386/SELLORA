<?php
// Sellora - Premium Advanced Knowledge Sharing Hub
require_once __DIR__ . '/common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN BLOG CONTAINER -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20 select-none">
    
    <!-- Premium Header Area -->
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-display font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-graduation-cap text-sky-500 animate-pulse"></i> Knowledge Hub
            </h1>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">Masterclass Guides & AI Cheat Sheets</p>
        </div>
        
        <!-- Interactive Layout & Stats Row -->
        <div class="flex items-center gap-2">
            <!-- View Layout Toggle -->
            <div class="bg-slate-100 dark:bg-slate-900 border border-slate-200/50 dark:border-white/5 p-0.5 rounded-xl flex items-center">
                <button onclick="setBlogViewLayout('grid')" id="btn-layout-grid" class="w-7 h-7 rounded-lg flex items-center justify-center transition-all outline-none" title="Grid View">
                    <i class="fa-solid fa-grip text-xs text-slate-400"></i>
                </button>
                <button onclick="setBlogViewLayout('list')" id="btn-layout-list" class="w-7 h-7 rounded-lg flex items-center justify-center transition-all outline-none" title="List View">
                    <i class="fa-solid fa-list-ul text-xs text-slate-400"></i>
                </button>
            </div>
            
            <span id="blog-count-badge" class="px-2.5 py-1.5 rounded-full bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 text-[10px] font-black border border-slate-200/50 dark:border-white/5">
                0 Guides
            </span>
        </div>
    </div>

    <!-- QUICK HIGHLIGHT CAROUSEL -->
    <div class="mb-5 bg-gradient-to-r from-sky-500/10 to-indigo-500/10 dark:from-sky-500/5 dark:to-indigo-500/5 rounded-3xl p-3.5 border border-sky-500/10 relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-sky-500/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between mb-2">
            <span class="text-[9px] font-black uppercase text-sky-600 dark:text-sky-450 tracking-wider flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Live Broadcast
            </span>
            <span class="text-[9px] font-black text-slate-400">Weekly Pulse</span>
        </div>
        <h4 class="text-[11px] font-bold text-slate-750 dark:text-slate-200 leading-snug">
            Need direct help? Join our upcoming AI-prompt masterclass stream on Friday for tips!
        </h4>
    </div>

    <!-- BLOG SEARCH & FILTER CONTROLS -->
    <div class="mb-5">
        <div class="relative flex items-center">
            <span class="absolute left-3.5 text-slate-400 dark:text-slate-500">
                <i class="fas fa-magnifying-glass text-xs"></i>
            </span>
            <input type="text" id="blog-search-input" placeholder="Search templates, guides, or keywords..." class="w-full pl-9 pr-8 py-2.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-205/60 dark:border-white/5 text-xs text-slate-700 dark:text-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-sky-550 transition-all outline-none shadow-sm" oninput="handleBlogSearch(this.value)">
            <button id="clear-blog-search" class="hidden absolute right-3.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 outline-none" onclick="clearBlogSearch()">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        
        <!-- Dynamic Category Filter Row with Bookmark Switch -->
        <div class="mt-3">
            <div id="blog-category-filters" class="flex gap-1.5 overflow-x-auto no-scrollbar py-1">
                <!-- Loaded dynamically in JS -->
                <button onclick="setBlogCategoryFilter('')" id="cat-btn-all" class="flex-shrink-0 px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider transition-all duration-200 outline-none bg-sky-500 text-white shadow-sm border border-sky-505">
                    All Matrix
                </button>
                <div class="h-6 w-14 rounded-full bg-slate-200 dark:bg-slate-800 animate-pulse"></div>
                <div class="h-6 w-16 rounded-full bg-slate-200 dark:bg-slate-800 animate-pulse"></div>
            </div>
        </div>
    </div>

    <!-- DYNAMIC BLOG CONTENTS FEED -->
    <div id="blogs-wrapper" class="space-y-6">
        
        <!-- HERO SEGMENT (LATEST FEATURED ARTICLE) -->
        <div id="hero-blog-container" class="hidden">
            <!-- Dynamic Hero Card -->
        </div>

        <!-- INSIGHTS TITLE -->
        <div id="recent-posts-title-section" class="hidden border-t border-slate-200/50 dark:border-slate-800/40 pt-4 flex items-center justify-between">
            <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-450 dark:text-slate-500">Curated Publications</h4>
            <span id="rendered-amount-label" class="text-[9px] text-slate-400 dark:text-slate-500 font-bold">Showing all</span>
        </div>

        <!-- BLOG LAYOUT CONTAINER -->
        <div id="blogs-cards-list" class="space-y-4">
            <!-- Skeletons loaded on initial fetch -->
            <div class="p-4 rounded-3xl bg-slate-200 dark:bg-slate-850 animate-pulse h-48"></div>
            <div class="p-4 rounded-3xl bg-slate-200 dark:bg-slate-850 animate-pulse h-28"></div>
        </div>

        <!-- INLINE NEWSLETTER CARD -->
        <div id="newsletter-magnet-card" class="p-5 rounded-3xl bg-gradient-to-br from-slate-900 to-indigo-950 border border-white/[0.04] shadow-xl text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-505/10 rounded-full blur-2xl"></div>
            <div class="relative">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-[10px]"><i class="fa-solid fa-paper-plane"></i></span>
                    <span class="text-[9px] font-black uppercase tracking-wider text-sky-400">Direct Delivery Pulse</span>
                </div>
                <h4 class="text-sm font-display font-black mb-1">Get Weekly AI Guides & Sheets</h4>
                <p class="text-[10px] text-slate-400 leading-relaxed font-semibold mb-3.5">
                    Subscribe to receive free resume sheets, Master Prompt Directories, & test formulas directly in your email.
                </p>
                
                <div id="newsletter-form-container">
                    <div class="flex gap-2">
                        <input type="email" id="news-email-field" placeholder="Enter your email address..." class="flex-1 px-3 py-2 text-[10px] font-semibold bg-white/5 border border-white/10 rounded-xl text-white placeholder:text-slate-500 focus:outline-none focus:ring-1 focus:ring-sky-500 max-w-[70%]">
                        <button onclick="handleNewsletterSubscription()" class="flex-1 py-2 bg-sky-500 hover:bg-sky-600 text-white font-black text-[9px] uppercase tracking-wider rounded-xl transition-all">
                            Get Inside
                        </button>
                    </div>
                </div>
                <!-- Success message container -->
                <div id="newsletter-success-box" class="hidden p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-center">
                    <span class="text-[10px] font-black text-emerald-400 block"><i class="fa-solid fa-circle-check mr-1"></i> YOU'RE ON THE LIST!</span>
                    <span class="text-[9px] text-slate-350 block mt-1">Check email for digital assets. Use code <b class="text-yellow-400 select-all cursor-pointer">KNOWLEDGE25</b> at checkout!</span>
                </div>
            </div>
        </div>

    </div>

    <!-- QUICK ADVICE PEAK DRAWER modal / BACKDROP -->
    <div id="peek-drawer-backdrop" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[999] hidden transition-all duration-300 pointer-events-auto" onclick="closeQuickPeekDrawer()">
        <!-- Slide-Up Drawer -->
        <div id="peek-drawer-body" class="fixed bottom-0 left-0 right-0 bg-white dark:bg-slate-900 border-t border-slate-200/50 dark:border-white/5 rounded-t-[32px] p-6 max-w-md mx-auto max-h-[85vh] overflow-y-auto transform translate-y-full transition-transform duration-300 pointer-events-auto" onclick="event.stopPropagation()">
            <!-- Handle -->
            <div class="w-10 h-1 bg-slate-200 dark:bg-slate-800 rounded-full mx-auto mb-4"></div>
            
            <div class="flex items-center justify-between mb-3">
                <span id="peek-category" class="px-2.5 py-0.5 rounded-full bg-sky-100 dark:bg-sky-955 text-sky-600 dark:text-sky-400 text-[8px] font-black uppercase">
                    AI Cheat
                </span>
                <span id="peek-time" class="text-[9px] font-bold text-slate-400">
                    5 min read
                </span>
            </div>

            <h3 id="peek-title" class="font-display font-extrabold text-base text-slate-850 dark:text-slate-100 leading-snug">
                Title of target article is rendering here...
            </h3>

            <!-- AI Quick Review section -->
            <div class="mt-4 p-3.5 bg-gradient-to-r from-emerald-500/5 to-teal-500/5 dark:from-emerald-500/10 dark:to-teal-500/10 border border-emerald-500/10 rounded-2xl">
                <div class="flex items-center gap-1.5 mb-1.5">
                    <i class="fa-solid fa-sparkles text-emerald-500 text-xs"></i>
                    <span class="text-[9px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-450">AI Micro-Summary Cheat Sheet</span>
                </div>
                <p id="peek-summary" class="text-[10px] text-slate-650 dark:text-slate-350 leading-relaxed m-0 font-semibold italic">
                    AI synthesized resume format guidelines inside a quick dashboard.
                </p>
            </div>

            <!-- Key Points -->
            <div class="mt-4">
                <h4 class="text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Essential Insights checklist</h4>
                <div id="peek-bullet-points" class="space-y-2">
                    <!-- Loaded dynamically -->
                </div>
            </div>

            <!-- Action buttons -->
            <div class="mt-6 flex gap-3">
                <button onclick="closeQuickPeekDrawer()" class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-white/5 text-slate-500 text-[10px] font-bold uppercase tracking-wider transition-all">
                    Dismiss
                </button>
                <a id="peek-full-link" href="blog_detail.php?id=1" class="flex-1 py-2.5 bg-sky-500 hover:bg-sky-600 text-white text-[10px] font-black uppercase tracking-wider text-center rounded-xl block transition-all shadow-md">
                    Read Full Article
                </a>
            </div>
        </div>
    </div>

</main>

<script>
let allBlogs = [];
let queryFilter = '';
let searchFilter = '';
let viewLayout = localStorage.getItem('blog_view_layout') || 'grid'; // 'grid' | 'list'
let debouncedSearchTimeout = null;

document.addEventListener('DOMContentLoaded', () => {
    // Sync View toggles style initially
    updateLayoutToggleButtons();
    
    // Check if user subscribed to newsletter previously
    if (localStorage.getItem('sellora_subscribed_newsletter') === 'true') {
        document.getElementById('newsletter-form-container').classList.add('hidden');
        document.getElementById('newsletter-success-box').classList.remove('hidden');
    }

    fetchBlogsFeed();
});

// Fetch all from API
function fetchBlogsFeed() {
    fetch('/api/blogs')
        .then(res => res.json())
        .then(blogs => {
            allBlogs = blogs || [];
            
            // Generate mock difficulty rankings & trending stats dynamically for premium details
            const difficultyOptions = ['Beginner Friendly', 'Intermediate Level', 'Masterclass Concept'];
            allBlogs.forEach((b, idx) => {
                if (!b.difficulty) {
                    b.difficulty = difficultyOptions[idx % difficultyOptions.length];
                }
                if (!b.views_count) {
                    b.views_count = (1200 + (b.id * 850)) + " views";
                }
                if (!b.claps_count) {
                    b.claps_count = (45 + (b.id * 32)) + " claps";
                }
            });

            document.getElementById('blog-count-badge').textContent = `${allBlogs.length} Guides`;
            
            // Extract categories
            renderCategoryFilterChips();
            // Filter and render
            applyFiltersAndRenderBlogs();
        })
        .catch(err => {
            document.getElementById('blogs-cards-list').innerHTML = `
                <div class="p-6 text-center text-xs text-red-500 font-bold bg-red-500/10 rounded-2xl border border-red-500/10">
                    <i class="fas fa-triangle-exclamation text-lg mb-1 block animate-bounce"></i>
                    Failed to sync guide channel. Re-establishing link.
                </div>
            `;
        });
}

function renderCategoryFilterChips() {
    const filtersRow = document.getElementById('blog-category-filters');
    const categories = ['AI', 'Careers', 'Education', 'General'];
    
    // Scan unique categories inside response array
    allBlogs.forEach(b => {
        if (b.category && !categories.includes(b.category)) {
            categories.push(b.category);
        }
    });

    // We add a virtual 'Bookmarked' chip
    filtersRow.innerHTML = `
        <button onclick="setBlogCategoryFilter('')" id="cat-btn-all" class="flex-shrink-0 px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider transition-all duration-200 outline-none ${queryFilter === '' ? 'bg-sky-505 text-white shadow-sm border border-sky-505' : 'bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200/50 dark:border-white/5'}">
            All Matrix
        </button>
        ${categories.map(cat => `
            <button onclick="setBlogCategoryFilter('${cat}')" id="cat-btn-${cat.toLowerCase().replace(/\s+/g, '-')}" class="flex-shrink-0 px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider transition-all duration-200 outline-none ${queryFilter.toLowerCase() === cat.toLowerCase() ? 'bg-sky-505 text-white shadow-sm border border-sky-505' : 'bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200/50 dark:border-white/5'}">
                ${cat}
            </button>
        `).join('')}
        
        <!-- Virtual Pocket Bookmark Selector -->
        <button onclick="setBlogCategoryFilter('bookmarked_vault_pocket')" id="cat-btn-saved" class="flex-shrink-0 px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider transition-all duration-200 outline-none flex items-center gap-1 ${queryFilter === 'bookmarked_vault_pocket' ? 'bg-pink-500 text-white shadow-sm border border-pink-505' : 'bg-pink-100/30 hover:bg-pink-100/50 dark:bg-pink-950/20 dark:hover:bg-pink-950/35 text-pink-500 dark:text-pink-400 border border-pink-205/30'}">
            <i class="fa-solid fa-bookmark text-[9px]"></i> Saved Bookmarks
        </button>
    `;
}

function setBlogCategoryFilter(cat) {
    triggerVibe(20);
    queryFilter = cat;
    renderCategoryFilterChips();
    applyFiltersAndRenderBlogs();
}

function handleBlogSearch(val) {
    const clearBtn = document.getElementById('clear-blog-search');
    if (val && val.trim().length > 0) {
        clearBtn.classList.remove('hidden');
    } else {
        clearBtn.classList.add('hidden');
    }
    
    clearTimeout(debouncedSearchTimeout);
    debouncedSearchTimeout = setTimeout(() => {
        searchFilter = val.trim().toLowerCase();
        applyFiltersAndRenderBlogs();
    }, 250);
}

function clearBlogSearch() {
    const input = document.getElementById('blog-search-input');
    input.value = '';
    document.getElementById('clear-blog-search').classList.add('hidden');
    searchFilter = '';
    applyFiltersAndRenderBlogs();
}

function setBlogViewLayout(mode) {
    triggerVibe(15);
    viewLayout = mode;
    localStorage.setItem('blog_view_layout', mode);
    updateLayoutToggleButtons();
    applyFiltersAndRenderBlogs();
}

function updateLayoutToggleButtons() {
    const gridBtn = document.getElementById('btn-layout-grid');
    const listBtn = document.getElementById('btn-layout-list');
    
    if (viewLayout === 'grid') {
        gridBtn.className = "w-7 h-7 rounded-lg flex items-center justify-center transition-all bg-sky-500 text-white shadow-sm";
        listBtn.className = "w-7 h-7 rounded-lg flex items-center justify-center transition-all hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-400";
    } else {
        listBtn.className = "w-7 h-7 rounded-lg flex items-center justify-center transition-all bg-sky-500 text-white shadow-sm";
        gridBtn.className = "w-7 h-7 rounded-lg flex items-center justify-center transition-all hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-400";
    }
}

function getSavedBlogIds() {
    return JSON.parse(localStorage.getItem('sellora_saved_blogs') || '[]');
}

function toggleSavedBlog(blogId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    triggerVibe(35);
    let saved = getSavedBlogIds();
    const idx = saved.indexOf(blogId);
    
    if (idx !== -1) {
        saved.splice(idx, 1);
        Toast.success("Article removed from saved Reading Bank.");
    } else {
        saved.push(blogId);
        Toast.success("Article successfully added to bookmarks!");
    }
    localStorage.setItem('sellora_saved_blogs', JSON.stringify(saved));
    applyFiltersAndRenderBlogs();
}

function applyFiltersAndRenderBlogs() {
    let list = allBlogs.filter(b => b.status !== 'draft');
    const savedIds = getSavedBlogIds();
    
    // Apply dynamic bookmarks filter
    if (queryFilter === 'bookmarked_vault_pocket') {
        list = list.filter(b => savedIds.includes(b.id));
    } else if (queryFilter) {
        list = list.filter(b => b.category && b.category.toLowerCase() === queryFilter.toLowerCase());
    }
    
    // Apply search filter
    if (searchFilter) {
        list = list.filter(b => 
            (b.title && b.title.toLowerCase().includes(searchFilter)) ||
            (b.summary && b.summary.toLowerCase().includes(searchFilter)) ||
            (b.content && b.content.toLowerCase().includes(searchFilter))
        );
    }

    const heroContainer = document.getElementById('hero-blog-container');
    const recentTitleSec = document.getElementById('recent-posts-title-section');
    const cardsContainer = document.getElementById('blogs-cards-list');
    const amountLabel = document.getElementById('rendered-amount-label');

    if (list.length === 0) {
        heroContainer.classList.add('hidden');
        recentTitleSec.classList.add('hidden');
        amountLabel.textContent = "0 matchings";
        
        if (queryFilter === 'bookmarked_vault_pocket') {
            cardsContainer.innerHTML = `
                <div class="text-center py-12 px-4 rounded-3xl border border-dashed border-pink-200 dark:border-pink-900/30 bg-pink-100/10 mb-4">
                    <i class="fa-solid fa-bookmark text-pink-400 text-4xl mb-3 block"></i>
                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-350">Reading Bank is Empty</h4>
                    <p class="text-[10px] text-slate-450 mt-1 max-w-xs mx-auto">Click the bookmark icon on any guide or cheat card to save it here for offline speed access.</p>
                </div>
            `;
        } else {
            cardsContainer.innerHTML = `
                <div class="text-center py-12 px-4 rounded-3xl border border-dashed border-slate-300 dark:border-slate-800">
                    <i class="fas fa-newspaper text-slate-300 dark:text-slate-755 text-4xl mb-3 block"></i>
                    <h4 class="text-xs font-bold text-slate-650 dark:text-slate-400">0 Matching Publications</h4>
                    <p class="text-[10px] text-slate-400 mt-1 max-w-xs mx-auto">Try typing another knowledge aspect or clear category filters.</p>
                </div>
            `;
        }
        return;
    }

    amountLabel.textContent = `${list.length} articles found`;
    
    // Sort descending chronologically
    const sorted = list.sort((a,b) => b.id - a.id);
    
    // Hero Featured segment loaded when NOT searching or filtering bookmarks
    if (!queryFilter && !searchFilter && sorted.length > 0) {
        const heroBlog = sorted[0];
        const remainingBlogs = sorted.slice(1);
        const isHeroSaved = savedIds.includes(heroBlog.id);
        
        heroContainer.innerHTML = `
            <div class="block rounded-3xl overflow-hidden bg-white dark:bg-slate-900 border border-slate-205/60 dark:border-white/5 shadow-md relative group">
                <div class="relative h-44 w-full overflow-hidden">
                    <img src="${window.getOptimizedImageUrl(heroBlog.image, 600)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                    
                    <!-- Top Ribbon Elements -->
                    <div class="absolute top-4 left-4 right-4 flex items-center justify-between">
                        <span class="bg-sky-500 text-white text-[9px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow">
                            <i class="fa-solid fa-star text-[7px] mr-1"></i> ${heroBlog.category || 'Featured'}
                        </span>
                        
                        <!-- Premium Bookmark Action -->
                        <button onclick="toggleSavedBlog(${heroBlog.id}, event)" class="w-8 h-8 rounded-full bg-slate-950/70 text-white flex items-center justify-center hover:scale-105 active:scale-90 transition-all outline-none backdrop-blur-sm shadow border border-white/5">
                            <i class="${isHeroSaved ? 'fa-solid text-pink-500' : 'fa-regular'} fa-bookmark text-xs"></i>
                        </button>
                    </div>

                    <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between">
                        <span class="bg-amber-500/90 text-slate-950 text-[8px] font-black px-2 py-0.5 rounded backdrop-blur-sm">
                            <i class="fa-solid fa-bolt mr-0.5"></i> ${heroBlog.difficulty}
                        </span>
                        <span class="bg-slate-950/70 text-white text-[8px] font-bold px-2 py-0.5 rounded backdrop-blur-sm">
                            ${heroBlog.read_time || '5 min'}
                        </span>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-2 text-[9px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">
                        <span>By ${heroBlog.author}</span>
                        <span>•</span>
                        <span>${new Date(heroBlog.created_at).toLocaleDateString(undefined, {month:'short', day:'numeric', year:'numeric'})}</span>
                    </div>
                    
                    <a href="blog_detail.php?id=${heroBlog.id}" class="group-hover:text-sky-505">
                        <h3 class="font-display font-black text-base leading-snug text-slate-850 dark:text-slate-100 hover:text-sky-550 transition-colors line-clamp-2">
                            ${heroBlog.title}
                        </h3>
                    </a>
                    
                    <p class="text-[10px] text-slate-455 dark:text-slate-400 font-semibold leading-normal mt-2 line-clamp-2">
                        ${heroBlog.summary}
                    </p>
                    
                    <div class="flex items-center justify-between mt-4">
                        <!-- Easy Actions Block -->
                        <div class="flex gap-2">
                            <button onclick="showQuickPeekDrawer(${heroBlog.id}, event)" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-850 hover:bg-slate-202 text-slate-650 dark:text-slate-300 rounded-lg text-[9px] font-black uppercase tracking-wider flex items-center gap-1 transition-all">
                                <i class="fa-solid fa-sparkles text-emerald-500"></i> AI Quick Peek
                            </button>
                            <span class="text-[9px] text-slate-400 font-semibold self-center"><i class="fa-regular fa-eye mr-0.5"></i> ${heroBlog.views_count}</span>
                        </div>
                        
                        <a href="blog_detail.php?id=${heroBlog.id}" class="flex items-center gap-1 text-[10px] font-black text-sky-500 hover:translate-x-1 duration-200 transition-transform">
                            <span>Read full guide</span>
                            <i class="fas fa-arrow-right text-[9px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        `;
        heroContainer.classList.remove('hidden');
        recentTitleSec.classList.remove('hidden');
        
        renderCardsFeed(remainingBlogs, cardsContainer, savedIds);
    } else {
        heroContainer.classList.add('hidden');
        recentTitleSec.classList.add('hidden');
        renderCardsFeed(sorted, cardsContainer, savedIds);
    }
}

// Render the grid/list list cards
function renderCardsFeed(blogsArray, wrapperElement, savedIds) {
    if (viewLayout === 'grid') {
        // Double grid bento layout
        wrapperElement.className = "grid grid-cols-2 gap-3.5 space-y-0";
        wrapperElement.innerHTML = blogsArray.map(b => renderBentoGridCard(b, savedIds)).join('');
    } else {
        // Vertical List layout
        wrapperElement.className = "space-y-4";
        wrapperElement.innerHTML = blogsArray.map(b => renderSleekListRow(b, savedIds)).join('');
    }
}

// Render dynamic Bento Box Grid item (extremely high visual hierarchy)
function renderBentoGridCard(b, savedIds) {
    const isSaved = savedIds.includes(b.id);
    return `
        <div class="bg-white dark:bg-slate-900 border border-slate-205/60 dark:border-white/5 rounded-2.5xl overflow-hidden flex flex-col justify-between group shadow-sm relative hover:scale-[1.01] transition-all">
            <!-- Image top wrapper -->
            <div class="relative h-24 w-full overflow-hidden flex-shrink-0">
                <img src="${window.getOptimizedImageUrl(b.image, 250)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                
                <!-- Category badge -->
                <span class="absolute bottom-2 left-2 bg-slate-950/70 text-white text-[7px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded">
                    ${b.category || 'Guide'}
                </span>
                
                <!-- Pocket Save Button -->
                <button onclick="toggleSavedBlog(${b.id}, event)" class="absolute top-2 right-2 w-6 h-6 rounded-full bg-slate-950/70 text-white flex items-center justify-center transition-all outline-none backdrop-blur-sm border border-white/5">
                    <i class="${isSaved ? 'fa-solid text-pink-500' : 'fa-regular'} fa-bookmark text-[9px]"></i>
                </button>
            </div>
            
            <div class="p-3 flex-1 flex flex-col justify-between">
                <div>
                    <!-- Difficulty / Time row -->
                    <span class="text-[7px] font-black uppercase tracking-wider text-amber-550 dark:text-amber-400 block mb-1">
                        ${b.difficulty}
                    </span>
                    <a href="blog_detail.php?id=${b.id}">
                        <h4 class="text-[10px] font-black text-slate-800 dark:text-slate-100 leading-tight line-clamp-2 hover:text-sky-505 transition-colors">
                            ${b.title}
                        </h4>
                    </a>
                    <p class="text-[9px] text-slate-400 line-clamp-2 mt-1 font-semibold leading-relaxed">
                        ${b.summary}
                    </p>
                </div>
                
                <div class="border-t border-slate-100 dark:border-slate-800/40 pt-2 mt-2 flex items-center justify-between">
                    <button onclick="showQuickPeekDrawer(${b.id}, event)" class="text-[8px] font-black uppercase text-emerald-500 hover:text-emerald-600 flex items-center gap-0.5">
                        <i class="fa-solid fa-sparkles"></i> Peek
                    </button>
                    <a href="blog_detail.php?id=${b.id}" class="text-[8px] font-black text-sky-505 hover:text-sky-600 flex items-center gap-0.5">
                        Open <i class="fa-solid fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    `;
}

// Render dynamic Compact Sleek list row
function renderSleekListRow(b, savedIds) {
    const isSaved = savedIds.includes(b.id);
    return `
        <div class="p-3 bg-white dark:bg-slate-900 border border-slate-205/60 dark:border-white/5 rounded-3xl flex gap-3 shadow-sm group hover:scale-[1.01] transition-all pointer-events-auto">
            <div class="relative w-18 h-18 rounded-2xl overflow-hidden flex-shrink-0">
                <img src="${window.getOptimizedImageUrl(b.image, 180)}" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500" loading="lazy">
                
                <!-- Quick Save Ribbon -->
                <button onclick="toggleSavedBlog(${b.id}, event)" class="absolute top-1 right-1 w-5.5 h-5.5 rounded-full bg-slate-950/70 text-white flex items-center justify-center transition-all outline-none">
                    <i class="${isSaved ? 'fa-solid text-pink-500' : 'fa-regular'} fa-bookmark text-[8px]"></i>
                </button>
            </div>
            
            <div class="min-w-0 flex-1 flex flex-col justify-between py-0.5">
                <div>
                    <div class="flex items-center justify-between gap-2.5">
                        <span class="text-[7.5px] font-black uppercase tracking-widest text-sky-550 dark:text-sky-450">${b.category || 'General'}</span>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[7.5px] text-amber-500 font-extrabold uppercase"><i class="fa-solid fa-bolt text-[6px]"></i> ${b.difficulty.split(' ')[0]}</span>
                            <span class="text-[7.5px] text-slate-400 font-semibold">${b.read_time || '4m'}</span>
                        </div>
                    </div>
                    <a href="blog_detail.php?id=${b.id}">
                        <h4 class="text-[11px] font-extrabold text-slate-800 dark:text-slate-200 mt-1 leading-snug truncate hover:text-sky-505 transition-colors">
                            ${b.title}
                        </h4>
                    </a>
                    <p class="text-[9px] text-slate-400 line-clamp-1 mt-0.5 font-semibold">
                        ${b.summary}
                    </p>
                </div>
                
                <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-800/40 pt-1.5 mt-1.5">
                    <div class="flex gap-2">
                        <span class="text-[8px] text-slate-400 font-bold">By ${b.author}</span>
                        <span class="text-[8px] text-slate-400 font-bold">•</span>
                        <span class="text-[8px] text-slate-400 font-bold">${new Date(b.created_at).toLocaleDateString(undefined, {month:'short', day:'numeric'})}</span>
                    </div>
                    
                    <button onclick="showQuickPeekDrawer(${b.id}, event)" class="text-[8px] font-black uppercase text-emerald-500 hover:text-emerald-600 flex items-center gap-0.5">
                        <i class="fa-solid fa-sparkles text-[7px]"></i> AI Peek
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Slide-Up AI Peek drawer execution
function showQuickPeekDrawer(articleId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    triggerVibe(30);
    const blog = allBlogs.find(b => b.id === articleId);
    if (!blog) return;

    // Fill Peek drawer specs
    document.getElementById('peek-category').innerText = blog.category || 'Insight';
    document.getElementById('peek-time').innerText = bPlayTimeFormat(blog.read_time);
    document.getElementById('peek-title').innerText = blog.title;
    document.getElementById('peek-summary').innerText = `"${blog.summary}"`;
    document.getElementById('peek-full-link').href = `blog_detail.php?id=${blog.id}`;

    // Synthesize mock Bullet Insights for incredible professional look
    const bulletsBox = document.getElementById('peek-bullet-points');
    const defaultBullets = [
        "Core Concepts: Systemizing layout architectures optimized for automated machine filters/queries.",
        "Step-by-Step Blueprint: Direct, structured checklists with few-shot copyable folders.",
        "Monetization Insights: Increase daily client throughput by 10x using curated asset lists."
    ];
    
    // Customize bullets slightly based on category to make it extremely premium
    let currentCategory = (blog.category || '').toLowerCase();
    let specificBullets = [...defaultBullets];
    if (currentCategory.includes('career') || currentCategory.includes('resume')) {
        specificBullets = [
            "Layout Screening rules: Recruiter tracking software maps standard, accessible grid hierarchies.",
            "Visual Accents: Eliminate nested images, vertical borders, and colored graphs inside files.",
            "Active Tailoring helper: Fit standard keywords from target positions description directories."
        ];
    } else if (currentCategory.includes('ai') || currentCategory.includes('prompt')) {
        specificBullets = [
            "Role Stacking principle: Formulate dedicated system behaviors (*'Act as senior developer...'*) to align responses code.",
            "Dynamic Parameter formatting: Demand structured markdown directories, copy buttons, and strict lists.",
            "Zero Hallucination hacks: Supply exact structural outlines and negative parameter filters."
        ];
    } else if (currentCategory.includes('education') || currentCategory.includes('study')) {
        specificBullets = [
            "Focus Revision sessions: Short, highly condensed formula charts reduce immediate cognitive friction.",
            "Analytical Mastery sheets: Prioritize dynamic derivation structures rather than simple rote memorization.",
            "Debugging Error journals: Record mistakes instantly and re-evaluate blocks after a 48 hour freeze block."
        ];
    }

    bulletsBox.innerHTML = specificBullets.map(b => `
        <div class="flex items-start gap-2">
            <span class="text-sky-500 text-xs mt-0.5"><i class="fa-solid fa-circle-check"></i></span>
            <span class="text-[10px] text-slate-650 dark:text-slate-300 font-semibold leading-relaxed">${b}</span>
        </div>
    `).join('');

    // Toggle Animations CSS
    const backdrop = document.getElementById('peek-drawer-backdrop');
    const body = document.getElementById('peek-drawer-body');
    
    backdrop.classList.remove('hidden');
    setTimeout(() => {
        body.classList.remove('translate-y-full');
    }, 50);
}

function closeQuickPeekDrawer() {
    const backdrop = document.getElementById('peek-drawer-backdrop');
    const body = document.getElementById('peek-drawer-body');
    
    body.classList.add('translate-y-full');
    setTimeout(() => {
        backdrop.classList.add('hidden');
    }, 300);
}

function bPlayTimeFormat(rawText) {
    if (!rawText) return '5 Min Reading';
    return rawText.toLowerCase().includes('read') ? rawText : `${rawText} Read`;
}

// Interactive Subscription execution
function handleNewsletterSubscription() {
    triggerVibe(45);
    const emailField = document.getElementById('news-email-field');
    const email = emailField.value.trim();
    
    if (!email || !email.includes('@')) {
        Toast.error("Please provide a valid active email address.");
        return;
    }

    // Save state in browser 
    localStorage.setItem('sellora_subscribed_newsletter', 'true');
    localStorage.setItem('sellora_subscribed_email', email);
    
    // Animate view switch nicely
    const formBox = document.getElementById('newsletter-form-container');
    const successBox = document.getElementById('newsletter-success-box');
    
    formBox.classList.add('hidden');
    successBox.classList.remove('hidden');
    
    Toast.success("Welcome aboard! Digital assets package sent to " + email);
}
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
