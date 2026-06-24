<?php
// Sellora - Premium Advanced Blog Narrative Spec Detail
require_once __DIR__ . '/common/config.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Find blog immediately in PHP for fast SSR rendering
$currentBlog = null;
if (isset($blogsArr)) {
    foreach ($blogsArr as $b) {
        if (intval($b['id']) === $id) {
            $currentBlog = $b;
            break;
        }
    }
}

// Fallback if not found in cache
if (!$currentBlog) {
    $dbPath = __DIR__ . '/database.json';
    if (file_exists($dbPath)) {
        $data = json_decode(file_get_contents($dbPath), true);
        if (isset($data['blogs'])) {
            foreach ($data['blogs'] as $b) {
                if (intval($b['id']) === $id) {
                    $currentBlog = $b;
                    break;
                }
            }
        }
    }
}

// Pre-define details or fallbacks
$ssrTitle = $currentBlog ? $currentBlog['title'] : 'Advanced Guide Specs...';
$ssrImage = $currentBlog ? $currentBlog['image'] : 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=800&q=80';
$ssrSummary = $currentBlog ? $currentBlog['summary'] : '';
$ssrContent = $currentBlog ? $currentBlog['content'] : '';
$ssrCategory = $currentBlog ? $currentBlog['category'] : 'General';
$ssrAuthor = $currentBlog ? $currentBlog['author'] : 'Mohan Mahali';
$ssrReadTime = $currentBlog ? $currentBlog['read_time'] : '5 min read';
$ssrDate = $currentBlog ? date('M d, Y', strtotime($currentBlog['created_at'])) : date('M d, Y');

// Custom default metadata for dynamic elements
$ssrDifficulty = 'Intermediate';
if (strpos(strtolower($ssrTitle), 'ultimate') !== false) $ssrDifficulty = 'Masterclass';
if (strpos(strtolower($ssrTitle), 'hacks') !== false) $ssrDifficulty = 'Beginner Friendly';

// Generate dynamic Call to Action parameters matching current article 
$ctaProductLink = "products.php";
$ctaHeadline = "Unlock Premium Resources";
$ctaText = "Get top-tier templates, prompt directories, and research sheets designed by industry experts.";
$ctaButtonText = "Browse Asset Store";

if ($currentBlog) {
    $catLower = strtolower($ssrCategory);
    if (strpos($catLower, 'career') !== false || strpos($catLower, 'resume') !== false) {
        $ctaProductLink = "product_detail.php?id=2"; 
        $ctaHeadline = "Premium ATS-Friendly Resume Template";
        $ctaText = "Score 3x more recruiters response clicks. Download our clean resume layouts that pass modern automated scanner filters instantly.";
        $ctaButtonText = "Download Template (₹99)";
    } elseif (strpos($catLower, 'ai') !== false || strpos($catLower, 'prompt') !== false) {
        $ctaProductLink = "product_detail.php?id=1"; 
        $ctaHeadline = "ChatGPT Mega Prompt Pack (10,000+ Master Prompts)";
        $ctaText = "Unlock our certified system templates. Effortlessly automate writing, marketing campaigns, and python engineering streams.";
        $ctaButtonText = "Unlock Master Pack (₹299)";
    } elseif (strpos($catLower, 'education') !== false || strpos($catLower, 'jee') !== false || strpos($catLower, 'notes') !== false) {
        $ctaProductLink = "product_detail.php?id=3"; 
        $ctaHeadline = "IIT-JEE Physics Revision Formulas Guide";
        $ctaText = "Quick-retention guides designed by top IITian mentors to maximize performance under exam stress.";
        $ctaButtonText = "Download revision sheets FREE";
    }
}

// Related articles
$relatedBlogs = [];
if (isset($blogsArr)) {
    foreach ($blogsArr as $b) {
        if (intval($b['id']) !== $id && isset($b['category']) && strtolower($b['category']) === strtolower($ssrCategory)) {
            $relatedBlogs[] = $b;
            if (count($relatedBlogs) >= 2) break;
        }
    }
}
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- Custom pulse scale indicator for TTS Audio wave -->
<style>
.audio-bar-node {
    animation: bar-pulse 1.2s ease-in-out infinite alternate;
}
.audio-bar-node:nth-child(2) { animation-delay: 0.15s; }
.audio-bar-node:nth-child(3) { animation-delay: 0.3s; }
.audio-bar-node:nth-child(4) { animation-delay: 0.45s; }
.audio-bar-node:nth-child(5) { animation-delay: 0.1s; }

@keyframes bar-pulse {
    0% { height: 4px; }
    100% { height: 18px; }
}
</style>

<!-- MAIN ARTICLE OUTLET -->
<?php if (!$currentBlog): ?>
<main id="blog-loading-screen" class="max-w-md mx-auto px-4 pt-10 pb-24 text-center select-none">
    <div class="h-10 w-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
    <p class="text-sm text-slate-400">Locating publication specifics...</p>
</main>
<?php endif; ?>

<main id="blog-detail-screen" class="<?php echo $currentBlog ? '' : 'hidden'; ?> max-w-md mx-auto px-4 pt-4 pb-28">
    
    <!-- Top back navigation row -->
    <div class="flex items-center justify-between mb-4 select-none">
        <a href="blogs.php" class="flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 outline-none">
            <i class="fas fa-arrow-left"></i>
            <span>Browse Knowledge Hub</span>
        </a>
        
        <!-- Live Layout Customization (Typography scale & Bookmark) -->
        <div class="flex items-center gap-2">
            <!-- Typo Scale Controls -->
            <div class="flex bg-slate-100 dark:bg-slate-900 border border-slate-200/50 dark:border-white/5 rounded-xl p-0.5">
                <button onclick="changeFontSizeMultiplier('minus')" class="w-6 h-6 text-[9.5px] font-black text-slate-500 flex items-center justify-center hover:bg-white dark:hover:bg-slate-800 rounded-lg outline-none" title="Decrease size">Aa-</button>
                <button onclick="changeFontSizeMultiplier('plus')" class="w-6 h-6 text-[9.5px] font-black text-slate-500 flex items-center justify-center hover:bg-white dark:hover:bg-slate-800 rounded-lg outline-none" title="Increase size">Aa+</button>
            </div>
            
            <button id="detail-bookmark-btn" onclick="toggleDetailBookmark()" class="w-7.5 h-7.5 rounded-xl bg-slate-100 dark:bg-slate-900 text-slate-550 border border-slate-200/50 dark:border-white/5 flex items-center justify-center hover:scale-105 active:scale-90 transition-all outline-none">
                <i class="fa-regular fa-bookmark text-xs" id="detail-bookmark-icon"></i>
            </button>
        </div>
    </div>

    <!-- READING PROGRESS BAR (STICKY DYNAMIC GRID) -->
    <div class="fixed top-[110px] left-0 right-0 h-1.5 bg-slate-200/40 dark:bg-slate-800/40 z-30 max-w-md mx-auto pointer-events-none">
        <div id="reading-bar" class="h-full bg-gradient-to-r from-sky-500 via-indigo-500 to-sky-500 transition-all duration-75 relative" style="width: 0%;">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-indigo-455 shadow-md"></div>
        </div>
    </div>

    <!-- MAIN ARTICLE GRAPHIC HEADER HERO -->
    <div class="relative rounded-3xl overflow-hidden border border-slate-205/60 dark:border-white/5 bg-slate-950 h-48 mb-5 shadow-sm select-none">
        <img id="article-cover" src="<?= htmlspecialchars($ssrImage) ?>" alt="Cover Image" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-black/30"></div>
        
        <!-- Tags on Graphic -->
        <div class="absolute top-4 left-4 bg-sky-500 text-white text-[8px] font-black uppercase px-2.5 py-1 rounded-md shadow-sm">
            <?= htmlspecialchars($ssrCategory) ?>
        </div>
        
        <div class="absolute bottom-4 left-4 right-4 text-white flex items-center justify-between">
            <span class="text-[9px] font-black text-amber-400 bg-slate-950/70 p-1.5 rounded-lg backdrop-blur-sm">
                <i class="fa-solid fa-bolt mr-0.5"></i> <?= $ssrDifficulty ?> Class
            </span>
            <span class="text-[9px] font-bold text-slate-350 bg-slate-950/70 py-1 px-2 rounded-lg backdrop-blur-sm">
                <i class="fa-regular fa-clock mr-1"></i> <?= htmlspecialchars($ssrReadTime) ?>
            </span>
        </div>
    </div>

    <!-- AUTHOR CARD & META INFO -->
    <div class="flex items-center justify-between mb-5 p-3 rounded-2.5xl bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-white/5 shadow-sm select-none">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-505 text-white flex items-center justify-center font-black text-sm">
                <?= strtoupper(substr($ssrAuthor, 0, 1)) ?>
            </div>
            <div>
                <h5 class="text-xs font-black text-slate-800 dark:text-slate-100 flex items-center gap-1">
                    <?= htmlspecialchars($ssrAuthor) ?> <i class="fa-solid fa-circle-check text-sky-505 text-[10px]"></i>
                </h5>
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider"><?= $ssrDate ?> • Certified Expert Publisher</p>
            </div>
        </div>
        
        <!-- Share/Utility drawer actions -->
        <button onclick="shareActiveArticle()" class="w-8.5 h-8.5 rounded-xl bg-slate-100 dark:bg-slate-850 hover:bg-slate-200 text-slate-500 dark:text-slate-300 flex items-center justify-center hover:scale-[1.03] active:scale-95 transition-all outline-none">
            <i class="fas fa-share-nodes text-xs"></i>
        </button>
    </div>

    <!-- AUDIO PODCAST PLAYER (HIGHLY ADVANCED MEDIA SIMULATION MODULE) -->
    <div class="mb-5 p-4 rounded-3xl bg-slate-50 dark:bg-slate-950 border border-slate-150 dark:border-white/5 select-none transition-all">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Guide Audio Companion</span>
            </div>
            
            <!-- Pitch Speed Control multiplier -->
            <div class="flex items-center gap-1">
                <span class="text-[8px] text-slate-400 font-bold uppercase uppercase">Speed:</span>
                <select id="audio-speed-selector" onchange="changeTtsSpeed()" class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-white/5 rounded px-1 text-[8px] font-black text-sky-500 outline-none cursor-pointer">
                    <option value="1">1.0x</option>
                    <option value="1.25" selected>1.25x</option>
                    <option value="1.5">1.5x</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- Play CTA Toggle icon -->
            <button id="tts-play-btn" onclick="toggleTtsNarrator()" class="w-10 h-10 rounded-full bg-sky-500 hover:bg-sky-600 text-white flex items-center justify-center shadow-lg active:scale-90 transition-all outline-none flex-shrink-0">
                <i class="fa-solid fa-play ml-0.5 text-sm" id="tts-icon"></i>
            </button>
            
            <div class="flex-1">
                <!-- Soundwave Visualizer Bars -->
                <div class="flex items-end gap-[3px] h-6 mb-1.5" id="soundwave-bars-container">
                    <!-- Inactive Visualizers -->
                    <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-2"></div>
                    <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-3"></div>
                    <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-4"></div>
                    <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-2"></div>
                    <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-4"></div>
                    <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-1.5"></div>
                    <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-3"></div>
                    <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-2"></div>
                    <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-4"></div>
                    <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-2"></div>
                    <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-3"></div>
                </div>

                <div class="flex items-center justify-between text-[8px] text-slate-400 font-bold font-mono">
                    <span id="tts-curr-timer">0:00</span>
                    <span id="tts-audio-headline">Simulated Narrator - Tapped Ready</span>
                    <span>3:45</span>
                </div>
            </div>
        </div>
    </div>

    <!-- CORE ARTICLE ESSAY BODY -->
    <article class="prose dark:prose-invert max-w-none text-slate-800 dark:text-slate-100 select-text">
        <h1 class="text-xl font-display font-black leading-snug tracking-tight mb-4 text-slate-900 dark:text-white">
            <?= htmlspecialchars($ssrTitle) ?>
        </h1>

        <!-- Executive Excerpt Capsule -->
        <div class="p-4 rounded-3xl bg-slate-50 dark:bg-slate-950/40 border-l-4 border-l-sky-500 border border-slate-200/50 dark:border-white/5 mb-5 select-text">
            <p class="text-[10.5px] text-slate-550 dark:text-slate-400 font-semibold italic leading-relaxed m-0">
                &ldquo; <?= htmlspecialchars($ssrSummary) ?> &rdquo;
            </p>
        </div>

        <!-- Narrative Content Area (Allows Font Sizes multiplication values) -->
        <div id="narrative-content-body" class="text-xs leading-relaxed space-y-4 font-serif tracking-wide text-slate-705 dark:text-slate-300 transition-all duration-300">
            <!-- PHP Content Output pre-formatted -->
            <?= nl2br(htmlspecialchars($ssrContent)) ?>
        </div>
    </article>

    <!-- AUTOMATIC CODE BOX INJECT / CHEAT SHEET TRIGGER (Only if category matches AI / Career / Education) -->
    <?php if (strpos(strtolower($ssrTitle), 'chatgpt') !== false || strpos(strtolower($ssrCategory), 'ai') !== false): ?>
    <div class="mt-6 p-4 rounded-3xl bg-slate-900 text-slate-100 border border-white/5 shadow-lg select-none">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[9px] font-black uppercase text-amber-400 tracking-wider flex items-center gap-1"><i class="fa-solid fa-code text-xs"></i> Prompt Code Cheat Sheet</span>
            <button onclick="copySnippetTextBlock('prompt-code-tag')" class="px-2 py-1 bg-white/10 hover:bg-white/15 text-[8.5px] font-bold text-slate-300 rounded flex items-center gap-1 active:scale-95 transition-all outline-none">
                <i class="fa-regular fa-copy"></i> <span id="copy-text-indicator">Copy Snippet</span>
            </button>
        </div>
        <pre class="font-mono text-[9px] text-sky-300 leading-normal overflow-x-auto whitespace-pre no-scrollbar bg-slate-950 p-3 rounded-xl m-0 select-all" id="prompt-code-tag">
Act as high-yield Applicant Screening parser. 
Analyze the following user profile metrics. 
Compare keywords with target technical description catalog.
Format corrections output exactly in simple JSON:
{ "score": "/100", "gaps": [], "substitute": [] }</pre>
    </div>
    <?php endif; ?>

    <!-- INTERACTIVE VOTING SYSTEM ("Was this Helpful?") -->
    <div class="mt-8 p-4 rounded-3xl bg-slate-50 dark:bg-slate-950 border border-slate-150 dark:border-white/5 text-center select-none" id="voting-module-box">
        <div id="voting-initial-state">
            <span class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-550 block mb-2">Did this Masterclass help you?</span>
            <div class="flex justify-center gap-4">
                <button onclick="submitHelpfulnessVote(true)" class="px-4 py-2 bg-white dark:bg-slate-900 hover:bg-slate-100 border border-slate-200 dark:border-white/5 rounded-xl text-[10px] font-black text-slate-700 dark:text-slate-300 flex items-center gap-1.5 active:scale-95 transition-all outline-none">
                    👍 Yes, valuable
                </button>
                <button onclick="submitHelpfulnessVote(false)" class="px-4 py-2 bg-white dark:bg-slate-900 hover:bg-slate-100 border border-slate-200 dark:border-white/5 rounded-xl text-[10px] font-black text-slate-700 dark:text-slate-300 flex items-center gap-1.5 active:scale-95 transition-all outline-none">
                    👎 Needs upgrades
                </button>
            </div>
        </div>
        <div id="voting-thanks-state" class="hidden py-1 text-center">
            <span class="text-[10px] font-black text-emerald-500 block"><i class="fa-solid fa-circle-check"></i> FEEDBACK REGISTERED SUCCESSFULLY</span>
            <p class="text-[9px] text-slate-400 dark:text-slate-500 font-semibold mt-1">Thank you for supporting our community knowledge hub upgrades!</p>
        </div>
    </div>

    <!-- DYNAMIC READER REACTION EMOTION DECK -->
    <div class="mt-6 pt-5 border-t border-slate-150 dark:border-slate-800/40 select-none">
        <h4 class="text-[9.5px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-550 mb-3 flex items-center gap-1">
            <i class="fa-solid fa-face-smile text-sky-505"></i> Tap to React
        </h4>
        
        <div class="grid grid-cols-4 gap-2">
            <button onclick="incrementEmojiReaction('thumbsup')" class="p-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 flex flex-col items-center justify-center hover:scale-[1.03] active:scale-90 transition-all outline-none">
                <span class="text-base">👍</span>
                <span id="react-thumbsup-count" class="text-[9.5px] font-bold text-slate-550 dark:text-slate-405 mt-1">24</span>
            </button>
            <button onclick="incrementEmojiReaction('claps')" class="p-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 flex flex-col items-center justify-center hover:scale-[1.03] active:scale-90 transition-all outline-none">
                <span class="text-base">🔥</span>
                <span id="react-claps-count" class="text-[9.5px] font-bold text-slate-550 dark:text-slate-405 mt-1">112</span>
            </button>
            <button onclick="incrementEmojiReaction('genius')" class="p-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 flex flex-col items-center justify-center hover:scale-[1.03] active:scale-90 transition-all outline-none">
                <span class="text-base">💡</span>
                <span id="react-genius-count" class="text-[9.5px] font-bold text-slate-550 dark:text-slate-405 mt-1">62</span>
            </button>
            <button onclick="incrementEmojiReaction('rocket')" class="p-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 flex flex-col items-center justify-center hover:scale-[1.03] active:scale-90 transition-all outline-none">
                <span class="text-base">🚀</span>
                <span id="react-rocket-count" class="text-[9.5px] font-bold text-slate-550 dark:text-slate-405 mt-1">37</span>
            </button>
        </div>
    </div>

    <!-- VALUE CONVERSION OPT-IN CTA CARD -->
    <div class="mt-8 p-5 rounded-3xl bg-gradient-to-br from-indigo-950 via-slate-950 to-indigo-950 border border-indigo-900/35 shadow-xl text-white select-none">
        <div class="flex items-center gap-2 mb-3">
            <span class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs">
                <i class="fas fa-crown"></i>
            </span>
            <h4 class="text-[9px] font-black uppercase tracking-wider text-sky-400">Direct Publisher Asset Access</h4>
        </div>
        
        <h3 class="font-display font-extrabold text-xs mb-1.5 text-white">
            <?= htmlspecialchars($ctaHeadline) ?>
        </h3>
        <p class="text-[10px] text-slate-400 font-semibold leading-relaxed mb-4">
            <?= htmlspecialchars($ctaText) ?>
        </p>
        
        <a href="<?= htmlspecialchars($ctaProductLink) ?>" class="w-full py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-black text-[10px] uppercase text-center block shadow-lg active:scale-95 transition-all outline-none">
            <?= htmlspecialchars($ctaButtonText) ?>
        </a>
    </div>

    <!-- RELATED READINGS (SAME CATEGORY SYSTEM) -->
    <?php if (count($relatedBlogs) > 0): ?>
    <div class="mt-8 pt-5 border-t border-slate-200/50 dark:border-slate-800/60 select-none">
        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-440 dark:text-slate-500 mb-4 flex items-center gap-1">
            <i class="fas fa-link text-sky-505"></i> Co-Categorized Guides
        </h4>
        
        <div class="space-y-3">
            <?php foreach ($relatedBlogs as $b): ?>
            <a href="blog_detail.php?id=<?= $b['id'] ?>" class="flex items-center gap-3 p-2.5 rounded-2.5xl bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-white/5 shadow-sm group hover:scale-[1.01] transition-all">
                <img src="<?= htmlspecialchars($b['image']) ?>" class="w-12 h-12 rounded-xl object-cover" loading="lazy">
                <div class="min-w-0 flex-1">
                    <h5 class="text-[11px] font-bold text-slate-800 dark:text-slate-250 truncate group-hover:text-sky-505 transition-colors">
                        <?= htmlspecialchars($b['title']) ?>
                    </h5>
                    <span class="text-[8px] text-slate-400 mt-1 block font-black uppercase tracking-wider text-sky-505 flex items-center gap-1">
                        Read full guide <i class="fas fa-arrow-right text-[7px]"></i>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- COMMENTS DISCUSSIONS DIRECT BOARD -->
    <div class="mt-8 pt-5 border-t border-slate-200/50 dark:border-slate-800/60 select-none">
        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-440 dark:text-slate-550 mb-3 flex items-center gap-1">
            <i class="fa-regular fa-comments text-sky-505"></i> Discussion Feed
        </h4>

        <!-- Submit Comment card -->
        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/5 mb-5 flex flex-col gap-3">
            <div class="flex gap-2">
                <input type="text" id="comm-name" placeholder="Name or alias..." class="flex-1 px-3 py-2 text-[10.5px] font-semibold rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-250/50 dark:border-white/5 text-slate-800 dark:text-slate-200 outline-none focus:ring-1 focus:ring-sky-500">
                <div class="px-2.5 py-2 text-[8px] text-center font-bold text-emerald-500 bg-emerald-500/10 rounded-xl leading-snug flex items-center gap-1">
                    <i class="fa-solid fa-shield"></i> Verified Reader
                </div>
            </div>
            
            <textarea id="comm-text" rows="2" placeholder="Share your insights or express constructive feedback..." class="w-full px-3 py-2 text-[10.5px] font-semibold rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-250/50 dark:border-white/5 text-slate-800 dark:text-slate-200 outline-none focus:ring-1 focus:ring-sky-550 resize-none"></textarea>
            
            <button onclick="submitDiscussionComment()" class="w-full py-2 bg-sky-500 hover:bg-sky-600 text-white font-black text-[9.5px] uppercase tracking-wider rounded-xl transition-all outline-none">
                Post comment
            </button>
        </div>

        <!-- Comments list feeds -->
        <div id="comments-list-box" class="space-y-4">
            <!-- Feed static list -->
            <div class="p-3.5 rounded-2.5xl bg-slate-50 dark:bg-slate-950/40 border border-slate-200/50 dark:border-white/5">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold text-slate-800 dark:text-slate-250 flex items-center gap-1">Aarav Sharma <i class="fa-solid fa-snowflake text-sky-400 text-[8px]"></i></span>
                    <span class="text-[8px] text-slate-400 font-bold">2 days ago</span>
                </div>
                <p class="text-[10.5px] text-slate-455 dark:text-slate-400 leading-normal mt-1.5 font-semibold">
                    Highly actionable points! The formula revision checklist and resume structures saved my application screening errors immediately.
                </p>
            </div>
            <div class="p-3.5 rounded-2.5xl bg-slate-50 dark:bg-slate-950/40 border border-slate-200/50 dark:border-white/5">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold text-slate-800 dark:text-slate-250 flex items-center gap-1">Aditi Roy <i class="fa-solid fa-snowflake text-sky-400 text-[8px]"></i></span>
                    <span class="text-[8px] text-slate-400 font-bold">1 week ago</span>
                </div>
                <p class="text-[10.5px] text-slate-455 dark:text-slate-400 leading-normal mt-1.5 font-semibold">
                    These ChatGPT stacking models prompt tricks work flawlessly. Structured outputs in system layers was what I missed. Excellent manual Mohan!
                </p>
            </div>
        </div>
    </div>

</main>

<script>
const activeBlogId = <?= $id ?>;
let bodyTextMultiplierFraction = 1.0; // multiplier tracker
let ttsPlaying = false;
let ttsTimerInterval = null;
let simulatedTtsTick = 0;
let isVoted = false;

document.addEventListener('DOMContentLoaded', () => {
    // Scroll progress bar logic
    window.addEventListener('scroll', updateArticleReadingTracer);
    
    // Check bookmark state initially
    updateDetailBookmarkButtonsState();
    
    // Load persisted comments
    loadLocallyStoredComments();
    
    // Load Reactions count from state or set mock defaults
    loadReactionDeckCounters();
    
    if (localStorage.getItem(`digitalmohan_voted_${activeBlogId}`) === 'true') {
        document.getElementById('voting-initial-state').classList.add('hidden');
        document.getElementById('voting-thanks-state').classList.remove('hidden');
        isVoted = true;
    }
});

function updateArticleReadingTracer() {
    const mainScreen = document.getElementById('blog-detail-screen');
    const borderBar = document.getElementById('reading-bar');
    if (!mainScreen || !borderBar) return;
    
    const scrolledY = window.scrollY;
    const documentHeight = mainScreen.offsetHeight - window.innerHeight;
    if (documentHeight <= 0) {
        borderBar.style.width = '100%';
        return;
    }
    const percent = Math.min(100, Math.max(0, (scrolledY / documentHeight) * 100));
    borderBar.style.width = percent + '%';
}

// Bookmark Vault utilities
function getSavedBlogIds() {
    return JSON.parse(localStorage.getItem('sellora_saved_blogs') || '[]');
}

function updateDetailBookmarkButtonsState() {
    const saved = getSavedBlogIds();
    const btnIcon = document.getElementById('detail-bookmark-icon');
    if (saved.includes(activeBlogId)) {
        btnIcon.className = "fa-solid fa-bookmark text-pink-500 text-xs animate-bounce";
    } else {
        btnIcon.className = "fa-regular fa-bookmark text-xs text-slate-500";
    }
}

function toggleDetailBookmark() {
    triggerVibe(35);
    let saved = getSavedBlogIds();
    const idx = saved.indexOf(activeBlogId);
    
    if (idx !== -1) {
        saved.splice(idx, 1);
        Toast.success("Article removed from Saved Bookmarks.");
    } else {
        saved.push(activeBlogId);
        Toast.success("Article bookmarked to Reading Bank!");
    }
    localStorage.setItem('sellora_saved_blogs', JSON.stringify(saved));
    updateDetailBookmarkButtonsState();
}

// Sizing modifier engine
function changeFontSizeMultiplier(action) {
    triggerVibe(15);
    const contentBody = document.getElementById('narrative-content-body');
    if (!contentBody) return;

    if (action === 'plus') {
        bodyTextMultiplierFraction = Math.min(1.4, bodyTextMultiplierFraction + 0.1);
    } else {
        bodyTextMultiplierFraction = Math.max(0.85, bodyTextMultiplierFraction - 0.1);
    }

    // Set CSS parameters dynamically
    contentBody.style.fontSize = (12 * bodyTextMultiplierFraction) + "px";
    contentBody.style.lineHeight = (1.75 * bodyTextMultiplierFraction) + "rem";
    
    Toast.success("Text scaled to " + Math.round(bodyTextMultiplierFraction * 100) + "% layout index comfort.");
}

// Share active link
function shareActiveArticle() {
    triggerVibe(40);
    const title = <?= json_encode($ssrTitle) ?>;
    const shareUrl = window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: title,
            url: shareUrl
        })
        .catch(() => {});
    } else {
        navigator.clipboard.writeText(shareUrl)
            .then(() => {
                Toast.success("Copied secure article URL to clipboard.");
            });
    }
}

// Helpfulness Vote Logger
function submitHelpfulnessVote(wasValuable) {
    if (isVoted) return;
    triggerVibe(30);
    isVoted = true;
    localStorage.setItem(`digitalmohan_voted_${activeBlogId}`, 'true');
    
    document.getElementById('voting-initial-state').classList.add('hidden');
    document.getElementById('voting-thanks-state').classList.remove('hidden');
    
    Toast.success(wasValuable ? "Thank you for upvoting this guide!" : "Thank you! We will adjust content parameters.");
}

// Simulated Reading Deck comments copy handler
function copySnippetTextBlock(elementId) {
    triggerVibe(45);
    const code = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(code)
        .then(() => {
            const ind = document.getElementById('copy-text-indicator');
            ind.innerText = "Copied! ✓";
            setTimeout(() => {
                ind.innerText = "Copy Snippet";
            }, 3000);
            Toast.success("Prompts system code copied successfully!");
        });
}

// Media Audio narration visual stimulation engine (TTS Audio podcast companion)
function toggleTtsNarrator() {
    triggerVibe(35);
    const playBtn = document.getElementById('tts-play-btn');
    const playIcon = document.getElementById('tts-icon');
    const statusText = document.getElementById('tts-audio-headline');
    const visualizer = document.getElementById('soundwave-bars-container');
    
    ttsPlaying = !ttsPlaying;
    
    if (ttsPlaying) {
        playBtn.className = "w-10 h-10 rounded-full bg-pink-500 hover:bg-pink-600 text-white flex items-center justify-center shadow-lg active:scale-90 transition-all outline-none flex-shrink-0";
        playIcon.className = "fa-solid fa-pause text-sm";
        statusText.innerText = "AI TTS Stream active - Listening Mode";
        
        // Turn visual waves into jumping nodes
        visualizer.innerHTML = `
            <div class="audio-bar-node w-[3.5px] rounded-full bg-gradient-to-t from-sky-500 to-indigo-505 h-2"></div>
            <div class="audio-bar-node w-[3.5px] rounded-full bg-gradient-to-t from-sky-500 to-indigo-505 h-3"></div>
            <div class="audio-bar-node w-[3.5px] rounded-full bg-gradient-to-t from-sky-500 to-indigo-505 h-4"></div>
            <div class="audio-bar-node w-[3.5px] rounded-full bg-gradient-to-t from-sky-500 to-indigo-505 h-2"></div>
            <div class="audio-bar-node w-[3.5px] rounded-full bg-gradient-to-t from-sky-500 to-indigo-505 h-5"></div>
            <div class="audio-bar-node w-[3.5px] rounded-full bg-gradient-to-t from-sky-500 to-indigo-505 h-1.5"></div>
            <div class="audio-bar-node w-[3.5px] rounded-full bg-gradient-to-t from-sky-500 to-indigo-505 h-3"></div>
            <div class="audio-bar-node w-[3.5px] rounded-full bg-gradient-to-t from-sky-500 to-indigo-505 h-2"></div>
            <div class="audio-bar-node w-[3.5px] rounded-full bg-gradient-to-t from-sky-500 to-indigo-505 h-4"></div>
            <div class="audio-bar-node w-[3.5px] rounded-full bg-gradient-to-t from-sky-500 to-indigo-505 h-1.5"></div>
            <div class="audio-bar-node w-[3.5px] rounded-full bg-gradient-to-t from-sky-500 to-indigo-505 h-3.5"></div>
        `;
        
        // Timer simulation loop
        ttsTimerInterval = setInterval(() => {
            simulatedTtsTick++;
            const minutes = Math.floor(simulatedTtsTick / 60);
            const seconds = simulatedTtsTick % 60;
            document.getElementById('tts-curr-timer').innerText = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        }, 1000);
        
        Toast.success("AI Synthesis speaker stream active. Maximize audio volume.");
    } else {
        pauseTtsAudioStream();
    }
}

function pauseTtsAudioStream() {
    const playBtn = document.getElementById('tts-play-btn');
    const playIcon = document.getElementById('tts-icon');
    const statusText = document.getElementById('tts-audio-headline');
    const visualizer = document.getElementById('soundwave-bars-container');
    
    playBtn.className = "w-10 h-10 rounded-full bg-sky-500 hover:bg-sky-600 text-white flex items-center justify-center shadow-lg active:scale-90 transition-all outline-none flex-shrink-0";
    playIcon.className = "fa-solid fa-play ml-0.5 text-sm";
    statusText.innerText = "AI Narrator - Paused Broadcast";
    
    // Inactivate audio nodes waves
    visualizer.innerHTML = `
        <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-2"></div>
        <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-3"></div>
        <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-4"></div>
        <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-2"></div>
        <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-4"></div>
        <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-1.5"></div>
        <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-3"></div>
        <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-2"></div>
        <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-4"></div>
        <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-2"></div>
        <div class="w-[3px] rounded-full bg-slate-200 dark:bg-slate-800 h-3"></div>
    `;
    
    clearInterval(ttsTimerInterval);
    ttsPlaying = false;
}

function changeTtsSpeed() {
    triggerVibe(15);
    const speed = document.getElementById('audio-speed-selector').value;
    Toast.success("Synthesis pacing adjusted to " + speed + "x pace factor.");
}

// React deck system counters loader
function getReactionsKey() {
    return `sellora_reactions_${activeBlogId}`;
}

function loadReactionDeckCounters() {
    let raw = localStorage.getItem(getReactionsKey());
    let defaults = { thumbsup: 24, claps: 112, genius: 62, rocket: 37 };
    
    if (raw) {
        defaults = JSON.parse(raw);
    } else {
        localStorage.setItem(getReactionsKey(), JSON.stringify(defaults));
    }
    
    for (let key in defaults) {
        const el = document.getElementById(`react-${key}-count`);
        if (el) el.innerText = defaults[key];
    }
}

function incrementEmojiReaction(type) {
    triggerVibe(25);
    const raw = localStorage.getItem(getReactionsKey());
    const reactionsObj = JSON.parse(raw);
    
    reactionsObj[type] = reactionsObj[type] + 1;
    localStorage.setItem(getReactionsKey(), JSON.stringify(reactionsObj));
    
    // Increment on frontend
    const el = document.getElementById(`react-${type}-count`);
    if (el) {
        el.innerText = reactionsObj[type];
        el.classList.add('text-sky-505', 'scale-110');
        setTimeout(() => {
            el.classList.remove('scale-110');
        }, 300);
    }
}

// Comment Discussions persistence
function submitDiscussionComment() {
    triggerVibe(50);
    const nameInput = document.getElementById('comm-name');
    const textInput = document.getElementById('comm-text');
    
    const nameVal = nameInput.value.trim();
    const textVal = textInput.value.trim();
    
    if (!nameVal || !textVal) {
        Toast.error("Discussions: Input both displayed name and advise message.");
        return;
    }
    
    const commentsContainer = document.getElementById('comments-list-box');
    
    const commentHtml = `
        <div class="p-3.5 rounded-2.5xl bg-white border border-l-4 border-l-sky-500 border-slate-200 dark:bg-slate-855 dark:border-white/5 shadow-sm transform translate-y-2 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black text-slate-800 dark:text-white">${htmlEscape(nameVal)}</span>
                <span class="text-[8px] text-sky-500 font-extrabold uppercase select-none">Just now</span>
            </div>
            <p class="text-[10px] text-slate-650 dark:text-slate-300 leading-normal mt-1.5 font-semibold">
                ${htmlEscape(textVal)}
            </p>
        </div>
    `;
    
    commentsContainer.insertAdjacentHTML('afterbegin', commentHtml);
    
    // Save locally
    saveCommentToLocalStore(activeBlogId, nameVal, textVal);
    
    nameInput.value = '';
    textInput.value = '';
    Toast.success("Your review comment was registered successfully!");
}

function saveCommentToLocalStore(blogId, name, text) {
    let key = `digitalmohan_comments_${blogId}`;
    let list = JSON.parse(localStorage.getItem(key) || '[]');
    list.unshift({ name, text, date: 'Just now' });
    localStorage.setItem(key, JSON.stringify(list));
}

function loadLocallyStoredComments() {
    let key = `digitalmohan_comments_${activeBlogId}`;
    let list = JSON.parse(localStorage.getItem(key) || '[]');
    const commentsContainer = document.getElementById('comments-list-box');
    
    list.forEach(c => {
        const h = `
            <div class="p-3.5 rounded-2.5xl bg-white border border-l-4 border-l-sky-500 border-slate-200 dark:bg-slate-855 dark:border-white/5 shadow-sm mb-4">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-slate-800 dark:text-white">${htmlEscape(c.name)}</span>
                    <span class="text-[8px] text-sky-500 font-extrabold uppercase select-none">${c.date}</span>
                </div>
                <p class="text-[10px] text-slate-650 dark:text-slate-300 leading-normal mt-1.5 font-semibold">
                    ${htmlEscape(c.text)}
                </p>
            </div>
        `;
        commentsContainer.insertAdjacentHTML('afterbegin', h);
    });
}

function htmlEscape(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
