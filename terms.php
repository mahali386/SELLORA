<?php
// Sellora - Terms of Service Page
require_once __DIR__ . '/common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    <div class="flex items-center gap-3 mb-6">
        <a href="index.php" class="w-9 h-9 rounded-xl flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:scale-105 transition-all outline-none" onclick="triggerVibe(15)">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <div>
            <h1 class="font-display font-extrabold text-lg text-slate-900 dark:text-white">Terms of Service</h1>
            <p class="text-[10px] text-slate-500 font-medium tracking-wide uppercase">User Agreement</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-850 rounded-2xl p-6 border border-slate-100 dark:border-white/5 shadow-sm space-y-6 text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
        
        <div class="space-y-2">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm">1. Acceptance of Terms</h3>
            <p>By registering on or downloading layouts, ChatGPT Prompts, or reference materials from DigitalMohan, you agree to comply with and be bound by these formal service guidelines.</p>
        </div>

        <div class="space-y-2">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm">2. Use License</h3>
            <p>We grant you a single-user, non-transferable, revocable license to access and open the purchased materials for personal or educational workflows. You may modify layouts for personal application, but you are strictly forbidden from distributing, reselling, or repackaging DigitalMohan bundles in any public directory.</p>
        </div>

        <div class="space-y-2">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm">3. Accountability & Billing</h3>
            <p>Prices are detailed lists in Indian Rupees (INR). Payments are handled via securely simulated Razorpay processing gateways. We are not accountable for any bank charge discrepancies during payment verification.</p>
        </div>

        <div class="space-y-2">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm">4. Limitation of Liability</h3>
            <p>Product packages are checked and validated. However, our layouts are provided "as is". In no event shall DigitalMohan or its directors be liable for secondary system malfunctions or missed exam grades stemming from formula revisions.</p>
        </div>

        <p class="text-[10px] text-slate-400 text-center pt-4">Last Updated: June 14, 2026</p>
    </div>
</main>

<?php include __DIR__ . '/common/footer.php'; ?>
<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
