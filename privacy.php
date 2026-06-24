<?php
// Sellora - Privacy Policy Page
require_once __DIR__ . '/common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    <div class="flex items-center gap-3 mb-6">
        <a href="index.php" class="w-9 h-9 rounded-xl flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:scale-105 transition-all outline-none" onclick="triggerVibe(15)">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <div>
            <h1 class="font-display font-extrabold text-lg text-slate-900 dark:text-white">Privacy Policy</h1>
            <p class="text-[10px] text-slate-500 font-medium tracking-wide uppercase">Information Security</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-850 rounded-2xl p-6 border border-slate-100 dark:border-white/5 shadow-sm space-y-6 text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
        
        <div class="space-y-2">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm">1. Data We Collect</h3>
            <p>We collect basic user credentials upon registration and purchase checkouts. This includes your Name, Verified Email Address, and Phone Number. This data is handled with strict confidentiality.</p>
        </div>

        <div class="space-y-2">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm">2. Use of Collected Data</h3>
            <p>Your details are used key-wise to:</p>
            <ul class="list-disc list-inside space-y-1 pl-1">
                <li>Instantly deliver purchase download items and PDF invoices</li>
                <li>Send automated push notifications or abandoned wishlist reminders</li>
                <li>Dispatch promotional newsletter launches (if subscribed)</li>
            </ul>
        </div>

        <div class="space-y-2">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm">3. Local Cookies & Storage</h3>
            <p>Your browsing experience uses standard local storage protocols to manage active dark mode preferences, wishlist logs, and current verified user sessions locally on your system.</p>
        </div>

        <div class="space-y-2">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm">4. Information Sharing</h3>
            <p>We do not rent, trade, or sell customer emails to secondary third-party analytics hubs or advertisers. All transactions remain completely anonymous and end-to-end encrypted.</p>
        </div>

        <p class="text-[10px] text-slate-400 text-center pt-4">Last Updated: June 14, 2026</p>
    </div>
</main>

<?php include __DIR__ . '/common/footer.php'; ?>
<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>
