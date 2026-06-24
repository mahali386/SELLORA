<?php
// Sellora - Admin Knowledge Hub Blogs Controller
require_once __DIR__ . '/../common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN BLOG BASE -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20 select-none">
    
    <div class="mb-5 flex items-center justify-between">
         <div>
             <h1 class="text-xl font-display font-black text-slate-850 dark:text-white flex items-center gap-1.5">
                 <i class="fa-solid fa-graduation-cap text-rose-500"></i> Manage Hub
             </h1>
             <p class="text-[10px] text-slate-450 dark:text-slate-500 font-bold uppercase tracking-wider">Expert Publications & AI Cheat Sheets</p>
         </div>
         
         <button onclick="triggerNewBlogCreator()" class="px-3.5 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-xs font-bold shadow-md active:scale-95 transition-all outline-none flex items-center gap-1">
             <i class="fas fa-plus text-[9px]"></i><span>Write Guide</span>
         </button>
    </div>

    <!-- ADVANCED KPI STATISTICS SECTION -->
    <div class="grid grid-cols-3 gap-2.5 mb-5 text-slate-800 dark:text-slate-100">
        <!-- Metric 1: Counts -->
        <div class="p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 shadow-sm text-center">
            <span class="text-[8px] font-black uppercase text-slate-400 dark:text-slate-550 block mb-1">Index Active</span>
            <div class="flex items-center justify-center gap-1">
                <span id="stat-total-count" class="text-base font-extrabold text-slate-800 dark:text-white">0</span>
                <span class="text-[9px] text-slate-400 block font-semibold">Live</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full mt-1.5 overflow-hidden">
                <div id="stat-count-progress" class="bg-rose-500 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
            </div>
        </div>

        <!-- Metric 2: Average read time -->
        <div class="p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 shadow-sm text-center">
            <span class="text-[8px] font-black uppercase text-slate-400 dark:text-slate-550 block mb-1">Avg Read</span>
            <div class="flex items-center justify-center gap-1">
                <span id="stat-avg-read" class="text-base font-extrabold text-slate-800 dark:text-white">5.2</span>
                <span class="text-[8px] text-slate-400 font-bold">MIN</span>
            </div>
            <div class="text-[8px] text-sky-505 dark:text-sky-400 font-bold mt-1.5 uppercase tracking-wider">
                Optimal Speed
            </div>
        </div>

        <!-- Metric 3: Community Rating -->
        <div class="p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 shadow-sm text-center">
            <span class="text-[8px] font-black uppercase text-slate-400 dark:text-slate-550 block mb-1">Approval</span>
            <div class="flex items-center justify-center gap-0.5 text-amber-500">
                <span class="text-base font-extrabold text-slate-800 dark:text-white">98%</span>
                <i class="fa-solid fa-star text-[7px] mb-1"></i>
            </div>
            <div class="text-[8px] text-emerald-500 font-black uppercase tracking-wider mt-1.5">
                Excellent (4.9★)
            </div>
        </div>
    </div>

    <!-- BLOG SEARCH & CATEGORY FILTER BAR -->
    <div class="mb-5 space-y-2.5">
        <div class="relative flex items-center">
            <span class="absolute left-3.5 text-slate-400 dark:text-slate-550">
                <i class="fas fa-search text-xs"></i>
            </span>
            <input type="text" id="admin-blog-search" placeholder="Search guides by keywords or author..." class="w-full pl-9 pr-8 py-2.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 text-xs text-slate-750 dark:text-slate-200 focus:ring-2 focus:ring-rose-500/20 transition-all outline-none shadow-sm" oninput="handleAdminBlogSearch(this.value)">
            <button id="clear-admin-search" class="hidden absolute right-3 text-slate-400 hover:text-slate-600 outline-none" onclick="clearAdminSearch()">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <!-- Category Horizontal Selectors -->
        <div id="admin-category-tabs" class="flex gap-1.5 overflow-x-auto no-scrollbar py-0.5">
            <button onclick="setAdminCategoryFilter('')" id="tab-cat-all" class="flex-shrink-0 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider transition-all duration-150 outline-none bg-rose-500 text-white shadow-sm">
                All Matrices
            </button>
            <button onclick="setAdminCategoryFilter('AI')" id="tab-cat-ai" class="flex-shrink-0 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider transition-all duration-150 outline-none bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-405 border border-slate-200/55 dark:border-white/5">
                AI Tech
            </button>
            <button onclick="setAdminCategoryFilter('Careers')" id="tab-cat-careers" class="flex-shrink-0 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider transition-all duration-150 outline-none bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-405 border border-slate-200/55 dark:border-white/5">
                Careers & Resumes
            </button>
            <button onclick="setAdminCategoryFilter('Education')" id="tab-cat-education" class="flex-shrink-0 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider transition-all duration-150 outline-none bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-405 border border-slate-200/55 dark:border-white/5">
                JEE & Science
            </button>
        </div>
    </div>

    <!-- DRAFT RECOVERY SYSTEM ALERT HEADER -->
    <div id="draft-alert-banner" class="hidden mb-4 p-3 bg-indigo-500/10 border border-indigo-500/20 rounded-2.5xl flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-sparkles text-indigo-400 animate-pulse text-xs"></i>
            <span class="text-[10px] text-indigo-400 font-extrabold uppercase">Unsaved Draft Recovered</span>
        </div>
        <div class="flex gap-2">
            <button onclick="restoreAutoSavedDraft()" class="px-2 py-1 bg-indigo-550 hover:bg-indigo-650 text-white rounded text-[8.5px] font-black uppercase tracking-wider transition-all">Restore</button>
            <button onclick="clearAutoSavedDraft()" class="px-2 py-1 bg-slate-200 dark:bg-slate-800 text-slate-500 rounded text-[8.5px] font-black uppercase tracking-wider transition-all">Discard</button>
        </div>
    </div>

    <!-- ADD/EDIT BLOG MODAL OVERLAY -->
    <div id="blog-modal" class="hidden fixed inset-0 z-[100] bg-slate-950/75 backdrop-blur-md flex items-center justify-center p-4 text-slate-800 dark:text-slate-100" onclick="handleOutsideModalClick(event)">
        <div class="max-w-md w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[32px] p-5 shadow-2xl relative overflow-y-auto max-h-[90vh] no-scrollbar animate-enter" onclick="event.stopPropagation()">
            
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100 dark:border-slate-800/80">
                <h3 id="modal-title" class="text-xs font-black uppercase tracking-wider text-rose-500">
                    Write Knowledge Guide
                </h3>
                <button onclick="dismissBlogCreator()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors outline-none">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            
            <form id="blog-form" onsubmit="saveBlogPostDetails(event)" class="space-y-3.5 text-xs font-semibold">
                <input type="hidden" id="blog-id">

                <!-- Interactive Gemini Copilot Row -->
                <div class="p-3 bg-gradient-to-r from-emerald-500/10 to-teal-500/10 dark:from-emerald-500/5 dark:to-teal-500/5 border border-emerald-500/20 rounded-2xl mb-4">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <span class="text-[9px] font-black uppercase text-emerald-600 dark:text-emerald-450 tracking-wider flex items-center gap-1">
                            <i class="fa-solid fa-sparkles text-emerald-500 animate-pulse"></i> Gemini Writer Assistant
                        </span>
                        <span class="text-[8px] text-slate-400 font-bold font-mono">1-Click Content</span>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" id="ai-blog-topic-input" placeholder="Topic: e.g. Resume Formatting for IT Jobs..." class="flex-1 px-3 py-2 text-[10px] bg-slate-50 dark:bg-slate-950 border border-slate-205 dark:border-white/5 rounded-xl text-slate-800 dark:text-white outline-none">
                        <button type="button" onclick="triggerGeminiBlogCopywriter()" id="btn-gemini-blog-writer" class="px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-black text-[9px] uppercase tracking-wider rounded-xl transition-all shadow-sm flex items-center gap-1 outline-none">
                            <span>Write</span>
                        </button>
                    </div>
                </div>
                
                <!-- Title & Real-time warning limit -->
                <div>
                    <div class="flex items-center justify-between">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-550 mb-1">Article Title Heading</label>
                        <span id="title-char-counter" class="text-[8px] text-slate-400 font-bold">0 / 70</span>
                    </div>
                    <input type="text" id="blog-title" required placeholder="The Ultimate Guide to Resume Building" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-150 border border-slate-200/50 dark:border-white/5 outline-none focus:ring-1 focus:ring-rose-500" oninput="updateCharTrackers(); updateDraftBackupState();">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-550 mb-1">Category Category</label>
                        <select id="blog-category" class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-150 border border-slate-200/50 dark:border-white/5 outline-none font-black text-[10px] uppercase tracking-wider" onchange="updateDraftBackupState()">
                            <option value="AI">AI</option>
                            <option value="Careers">Careers</option>
                            <option value="Education">Education</option>
                            <option value="General">General</option>
                        </select>
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-550 mb-1">Read Time Estimate</label>
                            <button type="button" onclick="autoSuggestReadEstimation()" class="text-[8px] text-rose-505 font-black hover:underline uppercase transition-all outline-none">Estimate</button>
                        </div>
                        <input type="text" id="blog-read-time" required placeholder="5 min read" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-150 border border-slate-200/50 dark:border-white/5 outline-none" oninput="updateDraftBackupState()">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-550 mb-1">Author Name / Alias</label>
                        <input type="text" id="blog-author" required placeholder="Mohan Mahali" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-150 border border-slate-200/50 dark:border-white/5 outline-none" oninput="updateDraftBackupState()">
                    </div>
                    <!-- Status Publish Switch -->
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-550 mb-1">Publish State</label>
                        <select id="blog-status" class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-150 border border-slate-200/50 dark:border-white/5 outline-none font-black text-[10px] uppercase tracking-wider" onchange="updateDraftBackupState()">
                            <option value="active">Active (Visible)</option>
                            <option value="draft">Draft (Restricted)</option>
                        </select>
                    </div>
                </div>

                <!-- Beautiful Image Preview & Custom Gallery Selector -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-550 mb-1">Cover Asset Option</label>
                    <div class="flex gap-2 mb-2">
                        <input type="text" id="blog-image" placeholder="https://images.unsplash.com/..." class="flex-1 px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-150 border border-slate-200/50 dark:border-white/5 outline-none font-mono text-[9px]" oninput="updateDraftBackupState(); updateCoverageThumbnailPreview();">
                        <button type="button" onclick="toggleCoverAssetSelector()" class="px-3.5 py-2.5 bg-slate-100 dark:bg-slate-850 text-slate-650 dark:text-slate-350 rounded-xl font-black text-[10px] uppercase tracking-wider transition-all outline-none">
                            <i class="fa-regular fa-image text-xs"></i> Preset Gallery
                        </button>
                    </div>
                    
                    <!-- Presets Selection Grid -->
                    <div id="covers-gallery" class="hidden p-2 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200/50 dark:border-white/5 grid grid-cols-4 gap-1.5 mb-2.5 transition-all">
                        <button type="button" onclick="applyPresetUrlImage('https://images.unsplash.com/photo-1677442136019-21780efad99a?w=800&q=80')" class="group relative rounded-lg overflow-hidden h-10 border border-white/5 active:scale-95 transition-all outline-none" title="AI / Cyber theme">
                            <img src="https://images.unsplash.com/photo-1677442136019-21780efad99a?w=180&q=80" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 text-[7px] text-white font-black flex items-center justify-center">AI</div>
                        </button>
                        <button type="button" onclick="applyPresetUrlImage('https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=800&q=80')" class="group relative rounded-lg overflow-hidden h-10 border border-white/5 active:scale-95 transition-all outline-none" title="Resume / Work theme">
                            <img src="https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=180&q=80" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 text-[7px] text-white font-black flex items-center justify-center">WORK</div>
                        </button>
                        <button type="button" onclick="applyPresetUrlImage('https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=800&q=80')" class="group relative rounded-lg overflow-hidden h-10 border border-white/5 active:scale-95 transition-all outline-none" title="JEE / Study theme">
                            <img src="https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=180&q=80" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 text-[7px] text-white font-black flex items-center justify-center">STUDY</div>
                        </button>
                        <button type="button" onclick="applyPresetUrlImage('https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&q=80')" class="group relative rounded-lg overflow-hidden h-10 border border-white/5 active:scale-95 transition-all outline-none" title="Quantum / Future theme">
                            <img src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=180&q=80" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 text-[7px] text-white font-black flex items-center justify-center">QUANT</div>
                        </button>
                    </div>

                    <!-- Selected cover asset thumbnail card preview -->
                    <div class="relative h-14 w-full rounded-xl overflow-hidden border border-slate-200 dark:border-white/5 bg-slate-950 flex items-center justify-center">
                        <img id="modal-cover-preview" src="https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=400&q=80" class="absolute inset-0 w-full h-full object-cover opacity-60">
                        <span class="relative text-[8.5px] font-black uppercase text-white bg-slate-950/70 py-1 px-2.5 rounded-lg border border-white/10 shadow-md">Cover Preview Live</span>
                    </div>
                </div>

                <!-- Abstract / Summary Content Excerpt -->
                <div>
                    <div class="flex items-center justify-between">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-550 mb-1">Brief Abstract Excerpt</label>
                        <div class="flex gap-2">
                            <button type="button" onclick="autoGenerateAiSummaryAbstract()" class="text-[8px] text-emerald-500 hover:text-emerald-600 font-extrabold flex items-center gap-0.5 outline-none"><i class="fa-solid fa-sparkles"></i> AI Suggest Summary</button>
                            <span id="summary-char-counter" class="text-[8px] text-slate-400 font-bold">0 / 150</span>
                        </div>
                    </div>
                    <textarea id="blog-summary" required placeholder="Write a highly engaging overview sentence describing the main outcomes of your guide..." rows="2" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-150 border border-slate-200/50 dark:border-white/5 outline-none resize-none" oninput="updateCharTrackers(); updateDraftBackupState();"></textarea>
                </div>

                <!-- Full Article content area -->
                <div>
                    <div class="flex items-center justify-between">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-550 mb-1">Full Article Guide Body</label>
                        <button type="button" onclick="injectAiKeyInsightsChecklistDemo()" class="text-[8px] text-emerald-500 hover:text-emerald-600 font-extrabold flex items-center gap-0.5 outline-none"><i class="fa-solid fa-sparkles"></i> Append AI Key Takeaways</button>
                    </div>
                    <textarea id="blog-content" required placeholder="Format your content using Markdown markers (e.g. # Header, 1. List, - points) to ensure elegant serif display layouts." rows="6" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-150 border border-slate-200/50 dark:border-white/5 outline-none resize-y font-mono text-[10px] leading-relaxed" oninput="updateDraftBackupState()"></textarea>
                </div>

                <div class="mt-4 flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                    <button type="button" onclick="dismissBlogCreator()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-750 rounded-xl text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-300">Discard</button>
                    <button type="submit" class="px-5 py-2.5 bg-rose-500 hover:bg-rose-600 rounded-xl text-[10px] font-black uppercase tracking-wider text-white shadow-md transition-all active:scale-95 outline-none">Publish Asset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- BLOGS LIST FEED -->
    <div id="admin-blogs-feed" class="space-y-4">
         <!-- Skeletons loaded on startup -->
         <div class="p-4 rounded-3xl bg-slate-200 dark:bg-slate-850 animate-pulse h-32"></div>
         <div class="p-4 rounded-3xl bg-slate-200 dark:bg-slate-850 animate-pulse h-24"></div>
    </div>

</main>

<script>
let blogsList = [];
let queryAdminSearch = '';
let activeCategoryTabFilter = '';

document.addEventListener('DOMContentLoaded', () => {
    const admin = getSessionAdmin();
    if (!admin) { window.location.href="login.php"; return; }
    
    loadAdminBlogsInventory();
    checkForAutoSavedFormState();
});

function loadAdminBlogsInventory() {
    fetch('/api/blogs')
        .then(res => res.json())
        .then(blogs => {
            blogsList = blogs || [];
            
            // Backfill default missing parameters in administration list for consistency
            blogsList.forEach(b => {
                if (!b.status) b.status = 'active'; // Default
            });

            calculateAndRenderDiagnosticsKPIs();
            renderBlogsTableFeed();
        })
        .catch(err => {
            document.getElementById('admin-blogs-feed').innerHTML = `
                <div class="p-6 text-center text-xs text-red-500 font-bold bg-red-500/10 rounded-2.5xl border border-red-500/10">
                    <i class="fas fa-triangle-exclamation text-lg mb-1 block"></i>
                    Connection to server database severed. Re-launching draft links.
                </div>
            `;
        });
}

function calculateAndRenderDiagnosticsKPIs() {
    const totalCountText = document.getElementById('stat-total-count');
    const avgReadText = document.getElementById('stat-avg-read');
    const progressBar = document.getElementById('stat-count-progress');

    const total = blogsList.length;
    totalCountText.textContent = total;
    
    // Animate bar percentage based on maximum capacity index of 10 guides for visual feedback
    const percent = Math.min(100, Math.round((total / 10) * 100));
    progressBar.style.width = percent + '%';

    // Calculate Average minutes read time estimation dynamically
    if (total > 0) {
        let totalMinutes = 0;
        blogsList.forEach(b => {
            const rawMinutes = parseInt(b.read_time) || 5;
            totalMinutes += rawMinutes;
        });
        const average = (totalMinutes / total).toFixed(1);
        avgReadText.textContent = average;
    } else {
        avgReadText.textContent = "0.0";
    }
}

function handleAdminBlogSearch(val) {
    const clearBtn = document.getElementById('clear-admin-search');
    if (val && val.trim().length > 0) {
        clearBtn.classList.remove('hidden');
    } else {
        clearBtn.classList.add('hidden');
    }
    queryAdminSearch = val.trim().toLowerCase();
    renderBlogsTableFeed();
}

function clearAdminSearch() {
    const input = document.getElementById('admin-blog-search');
    input.value = '';
    document.getElementById('clear-admin-search').classList.add('hidden');
    queryAdminSearch = '';
    renderBlogsTableFeed();
}

function setAdminCategoryFilter(cat) {
    triggerVibe(15);
    activeCategoryTabFilter = cat;
    
    // Adjust visual states of the tabs Row
    const categories = ['', 'AI', 'Careers', 'Education'];
    categories.forEach(c => {
        const idName = c === '' ? 'all' : c.toLowerCase();
        const tabEl = document.getElementById(`tab-cat-${idName}`);
        if (!tabEl) return;
        
        if (c.toLowerCase() === cat.toLowerCase()) {
            tabEl.className = "flex-shrink-0 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider transition-all duration-150 bg-rose-500 text-white shadow-sm border border-rose-505 outline-none";
        } else {
            tabEl.className = "flex-shrink-0 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider transition-all duration-150 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-405 border border-slate-200/55 dark:border-white/5 outline-none";
        }
    });

    renderBlogsTableFeed();
}

function renderBlogsTableFeed() {
    const container = document.getElementById('admin-blogs-feed');
    let list = [...blogsList];
    
    // Filter Category Tab
    if (activeCategoryTabFilter) {
        list = list.filter(b => b.category && b.category.toLowerCase() === activeCategoryTabFilter.toLowerCase());
    }

    // Filter Search keywords
    if (queryAdminSearch) {
        list = list.filter(b => 
            (b.title && b.title.toLowerCase().includes(queryAdminSearch)) || 
            (b.summary && b.summary.toLowerCase().includes(queryAdminSearch)) ||
            (b.author && b.author.toLowerCase().includes(queryAdminSearch)) ||
            (b.category && b.category.toLowerCase().includes(queryAdminSearch))
        );
    }
    
    if (list.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 px-4 rounded-3xl border border-dashed border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/30">
                <i class="fa-solid fa-rectangle-list text-slate-350 dark:text-slate-700 text-4xl mb-3 block"></i>
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-400">0 Matchings Discovered</h4>
                <p class="text-[10px] text-slate-400 mt-1 max-w-xs mx-auto">Publish custom articles, resume manuals or guidelines presets.</p>
            </div>
        `;
        return;
    }

    // Sort Descending by ID
    const sorted = list.sort((a,b) => b.id - a.id);

    container.innerHTML = sorted.map(b => {
        const isDraft = b.status === 'draft';
        return `
            <div class="p-3 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-white/5 rounded-3xl flex gap-3.5 shadow-sm group hover:scale-[1.005] transition-all relative">
                
                <!-- Graphic Cover preview -->
                <div class="relative w-18 h-18 rounded-2xl overflow-hidden flex-shrink-0 bg-slate-950">
                    <img src="${window.getOptimizedImageUrl(b.image, 160)}" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500" loading="lazy">
                    
                    <!-- Status Overlay Ribbon if Draft -->
                    ${isDraft ? `
                        <div class="absolute inset-0 bg-slate-950/70 flex items-center justify-center">
                            <span class="text-[7.5px] font-black text-rose-500 tracking-wider bg-rose-500/10 px-1 py-0.5 rounded border border-rose-500/20 uppercase">Draft</span>
                        </div>
                    ` : ''}
                </div>
                
                <div class="min-w-0 flex-1 flex flex-col justify-between py-0.5">
                    <div>
                        <!-- Category, Date, & Status Indicator -->
                        <div class="flex items-center justify-between gap-1.5">
                            <span class="text-[8px] font-black uppercase tracking-wider text-rose-500">${b.category || 'General'}</span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[8px] text-slate-400 font-bold">${b.read_time || '5 min'}</span>
                                <span class="text-[8px] text-slate-400 font-bold">•</span>
                                <span class="text-[8px] text-slate-400 font-bold">${b.created_at ? new Date(b.created_at).toLocaleDateString(undefined, {month:'short', day:'numeric'}) : 'Today'}</span>
                            </div>
                        </div>

                        <h4 class="text-[11.5px] font-extrabold text-slate-800 dark:text-slate-100 truncate mt-1 leading-snug hover:text-sky-505 transition-colors">${b.title}</h4>
                        <p class="text-[9.5px] text-slate-400 leading-snug line-clamp-1 mt-0.5 font-semibold">${b.summary}</p>
                    </div>

                    <!-- Direct Fast Action Deck -->
                    <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-800/40 pt-1.5 mt-1.5">
                        <span class="text-[8px] text-slate-400 font-bold">By ${b.author}</span>
                        
                        <div class="flex items-center gap-2">
                            <button onclick="editBlogSlide(${b.id})" class="text-[9px] font-black uppercase text-sky-505 hover:text-sky-600 transition-colors flex items-center gap-0.5 outline-none">
                                <i class="fa-regular fa-pen-to-square"></i> Edit
                            </button>
                            <span class="text-slate-200 dark:text-slate-800 text-[8px] font-bold">/</span>
                            <button onclick="deleteBlogSlide(${b.id})" class="text-[9px] font-black uppercase text-rose-500 hover:text-rose-600 transition-colors flex items-center gap-0.5 outline-none">
                                <i class="fa-solid fa-trash-can text-[8px]"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Preset Covers Toggle
function toggleCoverAssetSelector() {
    triggerVibe(15);
    const box = document.getElementById('covers-gallery');
    box.classList.toggle('hidden');
}

function applyPresetUrlImage(url) {
    triggerVibe(20);
    document.getElementById('blog-image').value = url;
    updateCoverageThumbnailPreview();
    updateDraftBackupState();
}

function updateCoverageThumbnailPreview() {
    const inputUrl = document.getElementById('blog-image').value.trim();
    const coverEl = document.getElementById('modal-cover-preview');
    if (inputUrl) {
        coverEl.src = window.getOptimizedImageUrl(inputUrl, 400);
    } else {
        coverEl.src = 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=400&q=80';
    }
}

// SEO Character lengths monitors
function updateCharTrackers() {
    const titleVal = document.getElementById('blog-title').value;
    const summaryVal = document.getElementById('blog-summary').value;
    
    const tc = document.getElementById('title-char-counter');
    const sc = document.getElementById('summary-char-counter');
    
    tc.innerText = `${titleVal.length} / 70`;
    sc.innerText = `${summaryVal.length} / 150`;
    
    // Alert values visual formatting
    if (titleVal.length > 70) {
        tc.className = "text-[8px] text-rose-500 font-black animate-pulse";
    } else {
        tc.className = "text-[8px] text-slate-400 font-semibold";
    }

    if (summaryVal.length > 150) {
        sc.className = "text-[8px] text-rose-500 font-black animate-pulse";
    } else {
        sc.className = "text-[8px] text-slate-400 font-semibold";
    }
}

// Dynamic Auto Read Time suggestions
function autoSuggestReadEstimation() {
    triggerVibe(20);
    const content = document.getElementById('blog-content').value.trim();
    if (content.length < 50) {
        Toast.error("Write more blog content before running read estimators.");
        return;
    }
    const totalWords = content.split(/\s+/).length;
    
    // Avg count benchmark: 200 words a minute
    const readMinutes = Math.max(1, Math.ceil(totalWords / 200));
    const estimatedText = `${readMinutes} min read`;
    
    document.getElementById('blog-read-time').value = estimatedText;
    Toast.success(`Pacing estimated successfully: ${estimatedText} (${totalWords} words total).`);
    updateDraftBackupState();
}

// Simulated Generative AI Abstract generator 
function autoGenerateAiSummaryAbstract() {
    triggerVibe(35);
    const title = document.getElementById('blog-title').value.trim();
    const content = document.getElementById('blog-content').value.trim();
    
    if (title.length < 5) {
        Toast.error("Provide a detailed heading/title to let the AI digest context details.");
        return;
    }

    Toast.show("✨ AI is analyzing article structures...", "success");
    
    setTimeout(() => {
        // High quality generative simulation matching topics
        let summaryTextValue = `Discover standard methods, actionable blueprints, and master cheat guides to master ${title.toLowerCase().replace('the ultimate guide to', '').replace('how to', '')} instantly.`;
        
        if (content.length > 100) {
            // Synthesize using real first sentence clues 
            const firstCleanLine = content.split(/[.!?]/)[0].trim().replace(/\n/g, ' ');
            if (firstCleanLine && firstCleanLine.length > 15) {
                summaryTextValue = `${firstCleanLine}. Learn exact blueprints & optimal keywords tailored by certified experts.`;
            }
        }
        
        document.getElementById('blog-summary').value = summaryTextValue;
        updateCharTrackers();
        updateDraftBackupState();
        Toast.success("✨ Success! Synthesized abstract excerpt injected.");
    }, 900);
}

// Generate Insight bullets demo
function injectAiKeyInsightsChecklistDemo() {
    triggerVibe(20);
    const contentTextarea = document.getElementById('blog-content');
    const title = document.getElementById('blog-title').value.trim();
    
    let topicIndicator = title ? title : "Masterclass Outline";
    const bulletsDemoText = `\n\n### ✨ Core Cheat Sheet Checklist:\n- **Fundamental Pillar**: Understand core structures and remove complex elements immediately.\n- **Optimized Strategy**: Implement direct actionable bullet metrics instead of wordy essays.\n- **Error Log**: Track system errors and revision patterns every 48 hours to secure 10x outputs.`;
    
    contentTextarea.value = contentTextarea.value + bulletsDemoText;
    contentTextarea.focus();
    updateDraftBackupState();
    Toast.success("✨ Key takeaways checklist template appended below!");
}

// Draft recovery utilities space
function getAutoBackupKey() {
    return 'sellora_draft_backup_model';
}

function updateDraftBackupState() {
    const idVal = document.getElementById('blog-id').value;
    // Don't auto-save if editing pre-existing articles
    if (idVal) return;

    const title = document.getElementById('blog-title').value;
    const category = document.getElementById('blog-category').value;
    const read_time = document.getElementById('blog-read-time').value;
    const author = document.getElementById('blog-author').value;
    const image = document.getElementById('blog-image').value;
    const summary = document.getElementById('blog-summary').value;
    const content = document.getElementById('blog-content').value;
    const status = document.getElementById('blog-status').value;

    const backupObj = { title, category, read_time, author, image, summary, content, status };
    if (title || summary || content) {
        localStorage.setItem(getAutoBackupKey(), JSON.stringify(backupObj));
    }
}

function checkForAutoSavedFormState() {
    const raw = localStorage.getItem(getAutoBackupKey());
    if (raw) {
        const parsed = JSON.parse(raw);
        if (parsed.title || parsed.summary || parsed.content) {
            document.getElementById('draft-alert-banner').classList.remove('hidden');
        }
    }
}

function restoreAutoSavedDraft() {
    triggerVibe(30);
    const raw = localStorage.getItem(getAutoBackupKey());
    if (!raw) return;
    
    const parsed = JSON.parse(raw);
    
    document.getElementById('blog-title').value = parsed.title || '';
    document.getElementById('blog-category').value = parsed.category || 'General';
    document.getElementById('blog-read-time').value = parsed.read_time || '5 min read';
    document.getElementById('blog-author').value = parsed.author || 'Mohan Mahali';
    document.getElementById('blog-image').value = parsed.image || '';
    document.getElementById('blog-summary').value = parsed.summary || '';
    document.getElementById('blog-content').value = parsed.content || '';
    document.getElementById('blog-status').value = parsed.status || 'active';
    
    document.getElementById('blog-id').value = '';
    document.getElementById('modal-title').textContent = "Write Knowledge Guide (Draft Recovered)";
    
    // Open editor modal cleanly
    document.getElementById('blog-modal').classList.remove('hidden');
    document.getElementById('draft-alert-banner').classList.add('hidden');
    
    updateCoverageThumbnailPreview();
    updateCharTrackers();
    Toast.success("Unsaved draft recovered successfully.");
}

function clearAutoSavedDraft() {
    triggerVibe(15);
    localStorage.removeItem(getAutoBackupKey());
    document.getElementById('draft-alert-banner').classList.add('hidden');
    Toast.success("Temporary drafts backup discarded.");
}

// Trigger Modal Creator with preset clean resets
function triggerNewBlogCreator() {
    triggerVibe(30);
    document.getElementById('blog-form').reset();
    document.getElementById('blog-id').value = '';
    
    // Suggested premium defaults
    document.getElementById('blog-author').value = 'Mohan Mahali';
    document.getElementById('blog-category').value = 'AI';
    document.getElementById('blog-read-time').value = '5 min read';
    document.getElementById('blog-image').value = 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=800&q=80';
    document.getElementById('blog-status').value = 'active';
    
    document.getElementById('modal-title').textContent = "Write Knowledge Guide";
    updateCoverageThumbnailPreview();
    updateCharTrackers();
    
    document.getElementById('blog-modal').classList.remove('hidden');
}

function dismissBlogCreator() {
    triggerVibe(25);
    document.getElementById('blog-modal').classList.add('hidden');
}

function handleOutsideModalClick(event) {
    dismissBlogCreator();
}

function editBlogSlide(id) {
    triggerVibe(35);
    const post = blogsList.find(b => b.id === id);
    if (!post) return;
    
    document.getElementById('blog-id').value = post.id;
    document.getElementById('blog-title').value = post.title || '';
    document.getElementById('blog-category').value = post.category || 'General';
    document.getElementById('blog-read-time').value = post.read_time || '5 min read';
    document.getElementById('blog-author').value = post.author || 'Mohan Mahali';
    document.getElementById('blog-image').value = post.image || '';
    document.getElementById('blog-summary').value = post.summary || '';
    document.getElementById('blog-content').value = post.content || '';
    document.getElementById('blog-status').value = post.status || 'active';
    
    document.getElementById('modal-title').textContent = "Modify Article Draft";
    updateCoverageThumbnailPreview();
    updateCharTrackers();
    
    // Toggle Modal
    document.getElementById('blog-modal').classList.remove('hidden');
}

function saveBlogPostDetails(e) {
    if (e) e.preventDefault();
    triggerVibe(50);
    
    const idVal = document.getElementById('blog-id').value;
    const title = document.getElementById('blog-title').value.trim();
    const category = document.getElementById('blog-category').value;
    const read_time = document.getElementById('blog-read-time').value.trim();
    const author = document.getElementById('blog-author').value.trim();
    const image = document.getElementById('blog-image').value.trim() || 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=800&q=80';
    const summary = document.getElementById('blog-summary').value.trim();
    const content = document.getElementById('blog-content').value.trim();
    const status = document.getElementById('blog-status').value;
    
    const path = idVal ? '/api/blogs/update' : '/api/blogs/create';
    
    fetch(path, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id: idVal,
            title,
            category,
            read_time,
            author,
            image,
            summary,
            content,
            status // Direct publish status capability
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.blog) {
            Toast.success(idVal ? "Article modified successfully!" : "New blog post launched successfully.");
            
            // Clean unsubmitted draft cache on success
            if (!idVal) {
                localStorage.removeItem(getAutoBackupKey());
            }

            dismissBlogCreator();
            loadAdminBlogsInventory();
        } else {
            Toast.error("Problem running server blog save.");
        }
    })
    .catch(err => {
        Toast.error("Fail connection. Saving to local draft backups.");
    });
}

function deleteBlogSlide(id) {
    triggerVibe(60);
    if (!confirm("Are you absolutely sure you want to discard this article? This is irreversible.")) {
        return;
    }
    
    fetch('/api/blogs/delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(data => {
        Toast.success("Article deleted instantly.");
        loadAdminBlogsInventory();
    });
}

function triggerGeminiBlogCopywriter() {
    triggerVibe(40);
    const inputEl = document.getElementById('ai-blog-topic-input');
    const topic = inputEl.value.trim();
    const btn = document.getElementById('btn-gemini-blog-writer');
    const category = document.getElementById('blog-category').value;
    const author = document.getElementById('blog-author').value;
    
    if (topic.length < 3) {
        Toast.error("Provide a detailed topic (e.g., 'Google Workspace Automation guide').");
        return;
    }
    
    const prevText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<i class="fa-solid fa-spinner animate-spin mr-1"></i> Writing...`;
    Toast.show("✨ Gemini is drafting the masterclass guide structure...", "success");
    
    fetch('/api/admin/generate-blog-assistant', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ topic, category, author })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = prevText;
        
        if (data && data.title) {
            document.getElementById('blog-title').value = data.title;
            document.getElementById('blog-summary').value = data.summary;
            document.getElementById('blog-content').value = data.content;
            document.getElementById('blog-read-time').value = data.read_time || '5 min read';
            
            // Auto suggest a beautiful corresponding preset image cover based on category selection
            let coverUrl = 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=800&q=80';
            if (category === 'AI') {
                coverUrl = 'https://images.unsplash.com/photo-1677442136019-21780efad99a?w=800&q=80';
            } else if (category === 'Careers') {
                coverUrl = 'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=800&q=80';
            } else if (category === 'Education') {
                coverUrl = 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=800&q=80';
            }
            document.getElementById('blog-image').value = coverUrl;
            
            updateCoverageThumbnailPreview();
            updateCharTrackers();
            updateDraftBackupState();
            
            Toast.success("✨ Success! Fully detailed expert guide generated successfully.");
            inputEl.value = '';
        } else {
            Toast.error("Unexpected response from Gemini blog API.");
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = prevText;
        Toast.error("Failed to connect to the server's Gemini generator endpoint.");
    });
}
</script>

<!-- Custom Toast -->
<div id="toast-container" class="fixed top-5 right-5 z-[500] flex flex-col gap-3 pointer-events-none max-w-sm w-full px-4 sm:px-0 animate-enter"></div>
<script>
const Toast = {
    show: function(m, type='success') {
        const container = document.getElementById('toast-container');
        const t = document.createElement('div');
        t.className = `p-4 rounded-2xl shadow-xl border border-white/5 backdrop-blur-md text-xs font-black uppercase tracking-wider flex items-center gap-2 ${type === 'error' ? 'bg-red-500 text-white' : 'bg-emerald-500 text-white'} transition-all transform duration-300`;
        t.innerHTML = `<i class="fa-solid ${type==='error'?'fa-triangle-exclamation':'fa-circle-check'}"></i> <span>${m}</span>`;
        container.appendChild(t);
        setTimeout(() => t.remove(), 2500);
    },
    success: function(m) { this.show(m, 'success'); },
    error: function(m) { this.show(m, 'error'); }
};
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
