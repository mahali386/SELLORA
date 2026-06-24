<?php
// Sellora - Refund and Replacement Policy Page
require_once __DIR__ . '/common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    <!-- Back Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="index.php" class="w-9 h-9 rounded-xl flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:scale-105 transition-all outline-none" onclick="triggerVibe(15)">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <div>
            <h1 class="font-display font-extrabold text-lg text-slate-900 dark:text-white">Refund & Replacement</h1>
            <p class="text-[10px] text-slate-500 font-medium tracking-wide uppercase">Policy & Protection</p>
        </div>
    </div>

    <!-- Layout banner -->
    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-6 text-white mb-6 shadow-xl relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 text-emerald-400/20 text-8xl font-black">
            <i class="fas fa-shield-heart"></i>
        </div>
        <h2 class="font-display font-extrabold text-xl mb-1.5">7-Day Guarantee</h2>
        <p class="text-[11px] leading-relaxed text-emerald-50 opacity-90">
            For peace of mind: If your content formats, prompts, or templates have syntax bugs or fail to load correctly, you are protected by automatic instant replacements.
        </p>
    </div>

    <!-- Fine clauses list -->
    <div class="space-y-4">
        
        <!-- Section 1 -->
        <div class="bg-white dark:bg-slate-850 rounded-2xl p-5 border border-slate-100 dark:border-white/5 shadow-sm space-y-2">
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-7 h-7 rounded-lg bg-sky-500/10 text-sky-500 flex items-center justify-center text-xs">
                    <i class="fas fa-circle-down"></i>
                </div>
                <h3 class="font-bold text-xs text-slate-900 dark:text-white">Digital Product Nature</h3>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Since our premium assets (e.g., Prompt files, Canva links, study sheets) are downloadable digital materials opened instantly upon credit clearance, traditional physical returns do not apply. 
            </p>
        </div>

        <!-- Section 2 -->
        <div class="bg-white dark:bg-slate-850 rounded-2xl p-5 border border-slate-100 dark:border-white/5 shadow-sm space-y-2">
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-xs">
                    <i class="fas fa-rotate"></i>
                </div>
                <h3 class="font-bold text-xs text-slate-900 dark:text-white">Immediate Replacement Cases</h3>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                You qualify for immediate file link replacement or a full purchase balance correction credit under the following instances:
            </p>
            <ul class="list-disc list-inside text-xs text-slate-500 dark:text-slate-400 space-y-1 pl-1">
                <li>Defective package ZIP or corrupted sheets download</li>
                <li>Mismatched product contents vs catalog description</li>
                <li>Dead/broken external hyperlinks (e.g., broken Canva access)</li>
            </ul>
        </div>

        <!-- Section 3 -->
        <div class="bg-white dark:bg-slate-850 rounded-2xl p-5 border border-slate-100 dark:border-white/5 shadow-sm space-y-2">
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center text-xs">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
                <h3 class="font-bold text-xs text-slate-900 dark:text-white">Claiming Process</h3>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Submit a simple support ticket describing the defect via our <a href="help.php" class="text-sky-500 underline font-medium">FAQ & Support Desk</a>. Be sure to list your Registered Email and secure Order ID. Our customer success team reviews queries within 4 hours.
            </p>
        </div>

        <!-- Section 4 -->
        <div class="bg-white dark:bg-slate-850 rounded-2xl p-5 border border-slate-100 dark:border-white/5 shadow-sm space-y-2">
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-7 h-7 rounded-lg bg-red-500/10 text-red-500 flex items-center justify-center text-xs">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <h3 class="font-bold text-xs text-slate-900 dark:text-white">Non-Eligible Claims</h3>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Exceptions where refunds/replacements will be denied include accidental purchases where the files have already been fully downloaded, or changes of mind after review.
            </p>
        </div>

    </div>
</main>

<?php include __DIR__ . '/common/footer.php'; ?>
<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
